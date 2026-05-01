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
OFFSET    = int(sys.argv[3]) if len(sys.argv) > 3 else 0

# ── Safe print (Windows stdout fix) ───────────────────────────────────────────
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
    if any(x in text for x in ["restaurant", "food", "biryani", "tiffin", "mess", "cafe", "dining"]):
        return ["restaurant"]
    elif any(x in text for x in ["hospital", "clinic", "doctor", "medical", "health"]):
        return ["hospital"]
    elif any(x in text for x in ["hotel", "lodge", "resort", "rooms", "stay"]):
        return ["lodging"]
    elif any(x in text for x in ["gym", "fitness", "workout", "training"]):
        return ["gym"]
    else:
        return ["business"]


def create_driver():
    options = Options()
    options.add_argument("--headless=new")
    options.add_argument("--disable-gpu")
    options.add_argument("--window-size=1920,1080")
    options.add_argument("--disable-blink-features=AutomationControlled")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument(
        "user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
        "AppleWebKit/537.36 Chrome/120 Safari/537.36"
    )
    driver = webdriver.Chrome(
        service=Service(ChromeDriverManager().install()),
        options=options
    )
    driver.execute_script(
        "Object.defineProperty(navigator, 'webdriver', {get: () => undefined})"
    )
    return driver


driver = create_driver()
progress = 0


def check_status():
    try:
        res    = requests.get(f"{STATUS_URL}/{SEARCH_ID}", timeout=5)
        status = res.json()

        if status.get("stopped"):
            safe_print("⛔ STOPPED BY USER")
            driver.quit()
            sys.exit(0)

        if status.get("paused"):
            safe_print("⏸  PAUSED — waiting to resume...")
            while True:
                time.sleep(3)
                try:
                    res    = requests.get(f"{STATUS_URL}/{SEARCH_ID}", timeout=5)
                    status = res.json()
                except Exception:
                    continue

                if status.get("stopped"):
                    safe_print("⛔ STOPPED WHILE PAUSED")
                    driver.quit()
                    sys.exit(0)

                if not status.get("paused"):
                    safe_print("▶  RESUMED")
                    break

    except Exception as e:
        safe_print(f"STATUS CHECK ERROR: {e}")


search_query = query.replace(" ", "+")

try:
    safe_print("Opening Google Maps...")
    driver.get(f"https://www.google.com/maps/search/{search_query}")
    time.sleep(6)

    safe_print("Zooming out...")
    for _ in range(5):
        try:
            driver.execute_script(
                'document.querySelector(\'button[aria-label="Zoom out"]\').click();'
            )
            time.sleep(2)
        except:
            break

    scrollable_div = driver.find_element(By.XPATH, '//div[@role="feed"]')
    prev_count = 0
    same_count = 0

    safe_print("Scrolling results...")
    for i in range(80):
        driver.execute_script('arguments[0].scrollTop += 1000', scrollable_div)
        time.sleep(2)
        driver.execute_script(
            'arguments[0].dispatchEvent(new Event("scroll"))', scrollable_div
        )
        time.sleep(2)

        places        = driver.find_elements(By.XPATH, '//a[contains(@href, "/maps/place/")]')
        current_count = len(places)
        safe_print(f"Scroll {i+1}: {current_count} places found")

        if current_count == prev_count:
            same_count += 1
            driver.execute_script(
                'arguments[0].scrollTop = arguments[0].scrollHeight', scrollable_div
            )
            time.sleep(3)
            if same_count >= 6:
                safe_print("✅ End of results reached")
                break
        else:
            same_count = 0

        prev_count = current_count

    safe_print("Collecting links...")
    links = list(set([
        p.get_attribute("href")
        for p in driver.find_elements(By.XPATH, '//a[contains(@href, "/maps/place/")]')
        if p.get_attribute("href")
    ]))

    total_places = len(links)
    safe_print(f"🔥 TOTAL PLACES FOUND: {total_places} | OFFSET: {OFFSET}")

    # ── Send total to Laravel ──────────────────────────────────────────────────
    try:
        requests.post(
            TOTAL_URL,
            json={"search_id": SEARCH_ID, "total_places": total_places},
            headers={"Content-Type": "application/json"},
            timeout=5
        )
    except Exception as e:
        safe_print(f"TOTAL UPDATE ERROR: {e}")

    if total_places == 0:
        safe_print("No places found — exiting.")
        driver.quit()
        sys.exit(0)

    # ── Skip already-processed links (resume support) ─────────────────────────
    if OFFSET > 0:
        safe_print(f"⏩ Skipping first {OFFSET} links (already saved in previous run)")
        links = links[OFFSET:]
        safe_print(f"▶  Resuming from link {OFFSET + 1}")

    # ── Process each link ─────────────────────────────────────────────────────
    for i, link in enumerate(links):

        if i % 10 == 0:
            check_status()

        try:
            driver.get(link)
            time.sleep(2)

            try:
                name = driver.find_element(By.TAG_NAME, "h1").text
            except:
                continue

            address = phone = website = ""
            rating  = "0"

            try:
                address = driver.find_element(
                    By.XPATH, '//button[contains(@aria-label,"Address")]'
                ).get_attribute("aria-label").replace("Address: ", "")
            except:
                pass

            try:
                phone = driver.find_element(
                    By.XPATH, '//button[contains(@data-item-id,"phone")]'
                ).text
            except:
                pass

            try:
                website = driver.find_element(
                    By.XPATH, '//a[@data-item-id="authority"]'
                ).get_attribute("href")
            except:
                pass

            try:
                rating = driver.find_element(
                    By.XPATH, '//span[contains(@class,"MW4etd")]'
                ).text
            except:
                pass

            main_area = ""
            pincode   = ""
            try:
                pin_match = re.search(r'\b\d{6}\b', address)
                if pin_match:
                    pincode = pin_match.group()
                parts = address.split(',')
                if len(parts) >= 3:
                    main_area = parts[-3].strip()
                elif len(parts) >= 2:
                    main_area = parts[-2].strip()
            except Exception as e:
                safe_print(f"AREA/PIN ERROR: {e}")

            types = detect_types(name, address, driver.page_source)

            data = {
                "search_id": SEARCH_ID,
                "name":      name,
                "phone":     re.sub(r'[^0-9+]', '', phone),
                "email":     "",
                "website":   website,
                "address":   address,
                "main_area": main_area,
                "pincode":   pincode,
                "maps_url":  link,
                "rating":    float(rating) if rating else 0,
                "types":     types,
            }

            try:
                requests.post(
                    API_URL,
                    json=data,
                    headers={"Content-Type": "application/json"},
                    timeout=10
                )
            except:
                continue

            progress += 1
            actual_total = OFFSET + len(links)
            safe_print(f"PROGRESS: {OFFSET + progress}/{actual_total}")

        except Exception as e:
            safe_print(f"SCRAPE ERROR: {e}")

    # ── Auto-stop when finished ────────────────────────────────────────────────
    try:
        requests.post(f"{STOP_URL}/{SEARCH_ID}", timeout=5)
        safe_print("✅ AUTO STOP SENT — scraping complete")
    except Exception as e:
        safe_print(f"STOP SEND ERROR: {e}")

except Exception as e:
    safe_print(f"FATAL ERROR: {e}")

finally:
    safe_print("Scraping session ended.")
    driver.quit()
    sys.exit(0)