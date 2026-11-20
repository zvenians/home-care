from playwright.sync_api import sync_playwright
from pathlib import Path
root=Path(__file__).resolve().parents[1]
with sync_playwright() as p:
 browser=p.chromium.launch(headless=True, executable_path='/usr/bin/chromium', args=['--no-sandbox'])
 page=browser.new_page(viewport={'width':1440,'height':960},device_scale_factor=1,accept_downloads=True)
 errors=[]
 page.on('pageerror',lambda e:errors.append(str(e)))
 page.goto('http://127.0.0.1:8765/')
 page.wait_for_timeout(600)
 print('title', page.title())
 print('hero',page.locator('.hero-copy h1').all_text_contents())
 page.screenshot(path='/mnt/data/home-desktop.png',full_page=True)
 page.get_by_role('button',name='Pesan layanan',exact=False).first.click()
 page.wait_for_timeout(180)
 print('booking',page.locator('.booking-head h1').all_text_contents())
 page.screenshot(path='/mnt/data/booking-desktop.png',full_page=True)
 print('errors',errors[:10])
 browser.close()
