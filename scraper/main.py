import sys
import time
import re
import requests
sys.stdout.reconfigure(encoding='utf-8')

API_URL = "http://127.0.0.1:8000/api/save-lead"
STATUS_URL = "http://127.0.0.1:8000/api/status"
TOTAL_URL = "http://127.0.0.1:8000/api/update-total"
STOP_URL = "http://127.0.0.1:8000/api/stop"

SEARCH_ID = int(sys.argv[2]) if len(sys.argv) > 2 else 1
print("SEARCH_ID:", SEARCH_ID)

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager

query = sys.argv[1] if len(sys.argv) > 1 else "restaurants in hyderabad"
search_query = query.replace(" ", "+")

def create_driver():
    options = Options()

    options.add_argument("--headless=new")
    options.add_argument("--disable-gpu")
    options.add_argument("--window-size=1920,1080")

    options.add_argument("--disable-blink-features=AutomationControlled")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")

    options.add_argument(
        "user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36"
    )

    driver = webdriver.Chrome(
        service=Service(ChromeDriverManager().install()),
        options=options
    )

    driver.execute_script("Object.defineProperty(navigator, 'webdriver', {get: () => undefined})")

    return driver

driver = create_driver()
progress = 0

def check_status():
    try:
        res = requests.get(f"{STATUS_URL}/{SEARCH_ID}", timeout=5)
        status = res.json()

        if status.get("stopped"):
            print("STOPPED BY USER")
            driver.quit()
            sys.exit()

        if status.get("paused"):
            print("PAUSED...")
            while True:
                time.sleep(3)
                res = requests.get(f"{STATUS_URL}/{SEARCH_ID}", timeout=5)
                status = res.json()
                if not status.get("paused"):
                    print("RESUMED")
                    break
    except Exception as e:
        print("STATUS ERROR:", e)

try:
    print("Opening Google Maps...")
    driver.get(f"https://www.google.com/maps/search/{search_query}")
    time.sleep(6)

    print("Zooming out...")
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

    print("Scrolling results...")

    for i in range(80):

        driver.execute_script(
            'arguments[0].scrollTop += 1000',
            scrollable_div
        )
        time.sleep(2)

        driver.execute_script(
            'arguments[0].dispatchEvent(new Event("scroll"))',
            scrollable_div
        )
        time.sleep(2)

        places = driver.find_elements(By.XPATH, '//a[contains(@href, "/maps/place/")]')
        current_count = len(places)

        print(f"Scroll {i+1}: {current_count}")

        if current_count == prev_count:
            same_count += 1

            driver.execute_script(
                'arguments[0].scrollTop = arguments[0].scrollHeight',
                scrollable_div
            )
            time.sleep(3)

            if same_count >= 6:
                print("✅ End reached")
                break
        else:
            same_count = 0

        prev_count = current_count

    print("Collecting links...")

    links = list(set([
        p.get_attribute("href")
        for p in driver.find_elements(By.XPATH, '//a[contains(@href, "/maps/place/")]')
        if p.get_attribute("href")
    ]))

    total_places = len(links)
    print("🔥 TOTAL PLACES FOUND:", total_places)

    try:
        requests.post(
            TOTAL_URL,
            json={"search_id": SEARCH_ID, "total_places": total_places},
            headers={"Content-Type": "application/json"},
            timeout=5
        )
    except Exception as e:
        print("TOTAL ERROR:", e)

    if total_places == 0:
        driver.quit()
        sys.exit()

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
            rating = "0"

            try:
                address = driver.find_element(
                    By.XPATH,
                    '//button[contains(@aria-label,"Address")]'
                ).get_attribute("aria-label").replace("Address: ", "")
            except: pass

            try:
                phone = driver.find_element(
                    By.XPATH,
                    '//button[contains(@data-item-id,"phone")]'
                ).text
            except: pass

            try:
                website = driver.find_element(
                    By.XPATH,
                    '//a[@data-item-id="authority"]'
                ).get_attribute("href")
            except: pass

            try:
                rating = driver.find_element(
                    By.XPATH,
                    '//span[contains(@class,"MW4etd")]'
                ).text
            except: pass

            # 🔥 NEW: EXTRACT AREA + PINCODE
            main_area = ""
            pincode = ""

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
                print("AREA/PIN ERROR:", e)

            data = {
                "search_id": SEARCH_ID,
                "name": name,
                "phone": re.sub(r'[^0-9+]', '', phone),
                "email": "",
                "website": website,
                "address": address,
                "main_area": main_area,
                "pincode": pincode,
                "maps_url": link,
                "rating": float(rating) if rating else 0,
            }

            try:
                requests.post(API_URL, json=data, headers={"Content-Type": "application/json"}, timeout=10)
            except:
                continue

            progress += 1
            print(f"PROGRESS: {progress}/{total_places}")

        except Exception as e:
            print("SCRAPE ERROR:", e)

    # 🔥 AUTO STOP
    try:
        requests.post(f"{STOP_URL}/{SEARCH_ID}", timeout=5)
        print("✅ AUTO STOP SENT")
    except Exception as e:
        print("STOP ERROR:", e)

except Exception as e:
    print("FATAL ERROR:", e)

finally:
    print("Scraping completed.")
    driver.quit()
    sys.exit()