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
        print(*args, **kwargs, flush=True)
    except OSError:
        pass

safe_print(f"SEARCH_ID: {SEARCH_ID} | OFFSET: {OFFSET}")

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.keys import Keys
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

    # HEADLESS
    options.add_argument("--headless=new")

    # PERFORMANCE
    options.page_load_strategy = 'eager'

    options.add_argument("--disable-gpu")
    options.add_argument("--window-size=1920,1080")
    options.add_argument("--start-maximized")

    # STABILITY
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument("--disable-extensions")
    options.add_argument("--disable-infobars")
    options.add_argument("--disable-popup-blocking")

    # ANTI DETECTION
    options.add_argument("--disable-blink-features=AutomationControlled")

    # FASTER LOADING
    prefs = {
        "profile.managed_default_content_settings.images": 2
    }

    options.add_experimental_option(
        "prefs",
        prefs
    )

    options.add_argument(
        "user-agent=Mozilla/5.0 "
        "(Windows NT 10.0; Win64; x64) "
        "AppleWebKit/537.36 "
        "(KHTML, like Gecko) "
        "Chrome/120 Safari/537.36"
    )

    driver = webdriver.Chrome(
        service=Service(
            ChromeDriverManager().install()
        ),
        options=options
    )

    driver.set_page_load_timeout(30)

    driver.execute_script("""
        Object.defineProperty(
            navigator,
            'webdriver',
            {
                get: () => undefined
            }
        )
    """)

    return driver


driver = create_driver()

progress = 0


def update_total(total):

    try:

        safe_print(
            f"UPDATING TOTAL: {total}"
        )

        response = requests.post(
            TOTAL_URL,
            json={
                "search_id": SEARCH_ID,
                "total_places": total
            },
            headers={
                "Content-Type": "application/json"
            },
            timeout=5
        )

        safe_print(
            f"TOTAL API STATUS: {response.status_code}"
        )

    except Exception as e:

        safe_print(
            f"TOTAL API ERROR: {e}"
        )


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

    safe_print("OPENING GOOGLE MAPS")

    driver.get(
        f"https://www.google.com/maps/search/{search_query}"
    )

    safe_print("MAPS PAGE OPENED")

    time.sleep(5)

    # ─────────────────────────────────────────────────────────────
    # FIND RESULTS PANEL
    # ─────────────────────────────────────────────────────────────

    scrollable_div = None

    safe_print("FINDING RESULTS PANEL")

    for attempt in range(15):

        try:

            scrollable_div = driver.find_element(
                By.XPATH,
                '//div[@role="feed"]'
            )

            safe_print(
                "RESULTS PANEL FOUND"
            )

            break

        except Exception as e:

            safe_print(
                f"WAITING RESULTS PANEL: {attempt+1}"
            )

            time.sleep(2)

    if not scrollable_div:

        safe_print(
            "RESULTS PANEL NOT FOUND"
        )

        driver.save_screenshot(
            "maps_error.png"
        )

        driver.quit()

        sys.exit(0)

    # ─────────────────────────────────────────────────────────────
    # SCROLLING
    # ─────────────────────────────────────────────────────────────

    links = set()

    prev_count = 0
    same_count = 0

    safe_print("STARTING SCROLL")

    for i in range(20):

        check_status()

        safe_print(
            f"SCROLL LOOP: {i+1}"
        )

        try:

            driver.execute_script(
                """
                arguments[0].scrollTop =
                arguments[0].scrollHeight
                """,
                scrollable_div
            )

        except Exception as e:

            safe_print(
                f"SCROLL ERROR: {e}"
            )

            break

        time.sleep(2)

        places = driver.find_elements(
            By.CSS_SELECTOR,
            'a.hfpxzc'
        )

        current_count = len(places)

        safe_print(
            f"VISIBLE PLACES: {current_count}"
        )

        for p in places:

            try:

                href = p.get_attribute("href")

                if href:

                    clean = href.split("&")[0]

                    if clean not in links:

                        links.add(clean)

                        # INSTANT TOTAL UPDATE
                        update_total(
                            len(links)
                        )

            except:
                pass

        safe_print(
            f"TOTAL LINKS: {len(links)}"
        )

        if current_count == prev_count:

            same_count += 1

            if same_count >= 4:

                safe_print(
                    "END OF RESULTS"
                )

                break

        else:

            same_count = 0

        prev_count = current_count

    # ─────────────────────────────────────────────────────────────
    # FINAL TOTAL
    # ─────────────────────────────────────────────────────────────

    links = list(links)

    total_places = len(links)

    safe_print(
        f"FINAL TOTAL: {total_places}"
    )

    update_total(total_places)

    if total_places == 0:

        safe_print(
            "NO PLACES FOUND"
        )

        driver.quit()

        sys.exit(0)

    # RESUME SUPPORT
    if OFFSET > 0:

        safe_print(
            f"SKIPPING FIRST {OFFSET}"
        )

        links = links[OFFSET:]

    # ─────────────────────────────────────────────────────────────
    # PROCESS BUSINESSES
    # ─────────────────────────────────────────────────────────────

    for i, link in enumerate(links):

        check_status()

        try:

            safe_print(
                f"OPENING BUSINESS {i+1}"
            )

            driver.get(link)

            time.sleep(2)

            try:

                name = driver.find_element(
                    By.TAG_NAME,
                    "h1"
                ).text

                safe_print(
                    f"BUSINESS: {name}"
                )

            except Exception as e:

                safe_print(
                    f"NAME ERROR: {e}"
                )

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
                rating = "0"

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

            except:
                pass

            try:

                clean_rating = re.findall(
                    r'\d+\.?\d*',
                    str(rating)
                )

                clean_rating = float(
                    clean_rating[0]
                ) if clean_rating else 0

            except:
                clean_rating = 0

            types = detect_types(
                name,
                address,
                driver.page_source
            )

            data = {

                "search_id": SEARCH_ID,

                "business_name": name,

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

                "rating": clean_rating,

                "types": types,
            }

            safe_print(
                "SAVING LEAD"
            )

            safe_print(data)

            try:

                response = requests.post(
                    API_URL,
                    json=data,
                    headers={
                        "Content-Type": "application/json"
                    },
                    timeout=10
                )

                safe_print(
                    f"SAVE STATUS: {response.status_code}"
                )

                safe_print(
                    f"SAVE RESPONSE: {response.text}"
                )

            except Exception as e:

                safe_print(
                    f"SAVE API ERROR: {e}"
                )

                continue

            progress += 1

            safe_print(
                f"PROGRESS: {progress}/{total_places}"
            )

        except Exception as e:

            safe_print(
                f"BUSINESS ERROR: {e}"
            )

    # ─────────────────────────────────────────────────────────────
    # STOP
    # ─────────────────────────────────────────────────────────────

    try:

        requests.post(
            f"{STOP_URL}/{SEARCH_ID}",
            timeout=5
        )

        safe_print(
            "SCRAPING COMPLETED"
        )

    except Exception as e:

        safe_print(
            f"STOP ERROR: {e}"
        )

except Exception as e:

    safe_print(
        f"FATAL ERROR: {e}"
    )

finally:

    safe_print(
        "SCRAPING SESSION ENDED"
    )

    driver.quit()

    sys.exit(0)