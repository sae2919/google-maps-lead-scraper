import sys
import time
import re
import requests

sys.stdout.reconfigure(encoding='utf-8')

API_URL    = "http://127.0.0.1:8000/api/save-lead"
STATUS_URL = "http://127.0.0.1:8000/api/status"
TOTAL_URL  = "http://127.0.0.1:8000/api/update-total"
STOP_URL   = "http://127.0.0.1:8000/api/stop"

# ── Arguments ──────────────────────────────────────────────────────────────────
query     = sys.argv[1] if len(sys.argv) > 1 else "restaurants in hyderabad"
SEARCH_ID = int(sys.argv[2]) if len(sys.argv) > 2 else 1
OFFSET    = int(sys.argv[3]) if len(sys.argv) > 3 and sys.argv[3].strip() != '' else 0

# ── Safe print ─────────────────────────────────────────────────────────────────
def safe_print(*args, **kwargs):
    try:
        print(*args, **kwargs)
    except OSError:
        pass

safe_print(f"SEARCH_ID: {SEARCH_ID} | OFFSET: {OFFSET}")

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager


def detect_types(name, address, html):

    text = (name + " " + address + " " + html).lower()

    if any(x in text for x in [
        "restaurant", "food", "biryani",
        "tiffin", "mess", "cafe", "dining"
    ]):
        return ["restaurant"]

    elif any(x in text for x in [
        "hospital", "clinic", "doctor",
        "medical", "health"
    ]):
        return ["hospital"]

    elif any(x in text for x in [
        "hotel", "lodge", "resort",
        "rooms", "stay"
    ]):
        return ["lodging"]

    elif any(x in text for x in [
        "gym", "fitness", "workout",
        "training"
    ]):
        return ["gym"]

    else:
        return ["business"]


def create_driver():

    options = Options()

    # 🔥 HEADLESS MODE
    options.add_argument("--headless=new")

    options.add_argument("--disable-gpu")
    options.add_argument("--window-size=1920,1080")
    options.add_argument("--disable-blink-features=AutomationControlled")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")

    # 🔥 EXTRA HEADLESS STABILITY
    options.add_argument("--disable-extensions")
    options.add_argument("--disable-infobars")
    options.add_argument("--disable-popup-blocking")
    options.add_argument("--start-maximized")

    options.add_argument(
        "user-agent=Mozilla/5.0 "
        "(Windows NT 10.0; Win64; x64) "
        "AppleWebKit/537.36 "
        "Chrome/120 Safari/537.36"
    )

    driver = webdriver.Chrome(
        service=Service(
            ChromeDriverManager().install()
        ),
        options=options
    )

    driver.execute_script(
        "Object.defineProperty("
        "navigator, 'webdriver', "
        "{get: () => undefined})"
    )

    return driver


driver = create_driver()

progress = 0


def check_status():

    try:

        res = requests.get(
            f"{STATUS_URL}/{SEARCH_ID}",
            timeout=5
        )

        status = res.json()

        if status.get("stopped"):

            safe_print("⛔ STOPPED BY USER")

            driver.quit()

            sys.exit(0)

        if status.get("paused"):

            safe_print("⏸ PAUSED — waiting...")

            while True:

                time.sleep(3)

                try:

                    res = requests.get(
                        f"{STATUS_URL}/{SEARCH_ID}",
                        timeout=5
                    )

                    status = res.json()

                except Exception:
                    continue

                if status.get("stopped"):

                    safe_print("⛔ STOPPED")

                    driver.quit()

                    sys.exit(0)

                if not status.get("paused"):

                    safe_print("▶ RESUMED")

                    break

    except Exception as e:

        safe_print(f"STATUS ERROR: {e}")


search_query = query.replace(" ", "+")

try:

    safe_print("Opening Google Maps...")

    driver.get(
        f"https://www.google.com/maps/search/{search_query}"
    )

    time.sleep(8)

    safe_print("Zooming out...")

    for _ in range(5):

        try:

            driver.execute_script(
                "document.querySelector("
                "'button[aria-label=\"Zoom out\"]'"
                ").click();"
            )

            time.sleep(2)

        except:
            break

    scrollable_div = driver.find_element(
        By.XPATH,
        '//div[@role="feed"]'
    )

    prev_count = 0
    same_count = 0

    # ─────────────────────────────────────────────────────────────
    # 🔥 SCROLL + COLLECT LINKS LIVE
    # ─────────────────────────────────────────────────────────────

    safe_print("Scrolling results...")

    links = set()

    for i in range(500):

        driver.execute_script(
            'arguments[0].scrollTop = arguments[0].scrollHeight',
            scrollable_div
        )

        time.sleep(5)

        driver.execute_script(
            "arguments[0].scrollBy(0, 5000);",
            scrollable_div
        )

        time.sleep(5)

        places = driver.find_elements(
            By.CSS_SELECTOR,
            'a.hfpxzc'
        )

        current_count = len(places)

        safe_print(
            f"Scroll {i+1}: "
            f"{current_count} visible places"
        )

        # 🔥 SAVE LINKS DURING SCROLL
        for p in places:

            href = p.get_attribute("href")

            if not href:
                continue

            clean = href.split("&")[0]

            links.add(clean)

        safe_print(
            f"🔥 TOTAL COLLECTED LINKS: {len(links)}"
        )

        # 🔥 LIVE TOTAL UPDATE — send to Laravel on every scroll iteration
        try:
            requests.post(
                TOTAL_URL,
                json={
                    "search_id": SEARCH_ID,
                    "total_places": len(links)
                },
                headers={"Content-Type": "application/json"},
                timeout=3
            )
        except Exception:
            pass

        # 🔥 END DETECTION
        if current_count == prev_count:

            same_count += 1

            if same_count >= 5:

                safe_print(
                    "✅ End of results reached"
                )

                break

        else:

            same_count = 0

        prev_count = current_count

    # ─────────────────────────────────────────────────────────────
    # 🔥 FINAL LINKS
    # ─────────────────────────────────────────────────────────────

    links = list(links)

    total_places = len(links)

    safe_print(
        f"FINAL UNIQUE LINKS: {total_places}"
    )

    safe_print(
        f"🔥 TOTAL PLACES FOUND FIRST: "
        f"{total_places} | OFFSET: {OFFSET}"
    )

    # ─────────────────────────────────────────────────────────────
    # 🔥 SEND TOTAL FIRST TO LARAVEL
    # ─────────────────────────────────────────────────────────────

    try:

        requests.post(
            TOTAL_URL,
            json={
                "search_id": SEARCH_ID,
                "total_places": total_places
            },
            headers={
                "Content-Type": "application/json"
            },
            timeout=10
        )

        safe_print(
            "✅ TOTAL COUNT SENT TO UI"
        )

    except Exception as e:

        safe_print(
            f"TOTAL UPDATE ERROR: {e}"
        )

    # 🔥 WAIT SO UI SHOWS TOTAL FIRST
    time.sleep(3)

    if total_places == 0:

        safe_print("No places found.")

        driver.quit()

        sys.exit(0)

    # ─────────────────────────────────────────────────────────────
    # 🔥 RESUME SUPPORT
    # ─────────────────────────────────────────────────────────────

    if OFFSET > 0:

        safe_print(
            f"⏩ Skipping first "
            f"{OFFSET} links"
        )

        links = links[OFFSET:]

    # ─────────────────────────────────────────────────────────────
    # 🔥 PROCESS EACH BUSINESS
    # ─────────────────────────────────────────────────────────────

    for i, link in enumerate(links):

        if i % 10 == 0:

            check_status()

        try:

            driver.get(link)

            time.sleep(3)

            try:

                name = driver.find_element(
                    By.TAG_NAME,
                    "h1"
                ).text

            except:
                continue

            address = ""
            phone   = ""
            website = ""
            rating  = "0"

            try:

                address = driver.find_element(
                    By.XPATH,
                    '//button[contains(@aria-label,"Address")]'
                ).get_attribute(
                    "aria-label"
                ).replace(
                    "Address: ",
                    ""
                )

            except:
                pass

            try:

                phone = driver.find_element(
                    By.XPATH,
                    '//button[contains(@data-item-id,"phone")]'
                ).text

            except:
                pass

            try:

                website = driver.find_element(
                    By.XPATH,
                    '//a[@data-item-id="authority"]'
                ).get_attribute("href")

            except:
                pass

            try:

                rating = driver.find_element(
                    By.XPATH,
                    '//span[contains(@class,"MW4etd")]'
                ).text

            except:
                pass

            main_area = ""
            pincode   = ""

            try:

                pin_match = re.search(
                    r'\b\d{6}\b',
                    address
                )

                if pin_match:

                    pincode = pin_match.group()

                parts = address.split(',')

                if len(parts) >= 3:

                    main_area = parts[-3].strip()

                elif len(parts) >= 2:

                    main_area = parts[-2].strip()

            except Exception as e:

                safe_print(
                    f"AREA ERROR: {e}"
                )

            types = detect_types(
                name,
                address,
                driver.page_source
            )

            data = {

                "search_id": SEARCH_ID,

                "name": name,

                "phone": re.sub(
                    r'[^0-9+]',
                    '',
                    phone
                ),

                "email": "",

                "website": website,

                "address": address,

                "main_area": main_area,

                "pincode": pincode,

                "maps_url": link,

                "rating": float(rating)
                if rating else 0,

                "types": types,
            }

            try:

                requests.post(
                    API_URL,
                    json=data,
                    headers={
                        "Content-Type":
                        "application/json"
                    },
                    timeout=10
                )

            except:
                continue

            progress += 1

            actual_total = OFFSET + len(links)

            safe_print(
                f"PROGRESS: "
                f"{OFFSET + progress}/"
                f"{actual_total}"
            )

        except Exception as e:

            safe_print(
                f"SCRAPE ERROR: {e}"
            )

    # ─────────────────────────────────────────────────────────────
    # 🔥 AUTO STOP
    # ─────────────────────────────────────────────────────────────

    try:

        requests.post(
            f"{STOP_URL}/{SEARCH_ID}",
            timeout=5
        )

        safe_print(
            "✅ SCRAPING COMPLETE"
        )

    except Exception as e:

        safe_print(
            f"STOP ERROR: {e}"
        )

except Exception as e:

    safe_print(f"FATAL ERROR: {e}")

finally:

    safe_print("Scraping session ended.")

    driver.quit()

    sys.exit(0)