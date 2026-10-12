import urllib.request
import os

os.makedirs('assets/images/banner', exist_ok=True)
os.makedirs('assets/images/brand', exist_ok=True)
os.makedirs('assets/images/feedback', exist_ok=True)
os.makedirs('assets/images/mechanism', exist_ok=True)

headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'}

def download_file(url, path):
    try:
        req = urllib.request.Request(url, headers=headers)
        with urllib.request.urlopen(req, timeout=10) as response, open(path, 'wb') as out_file:
            out_file.write(response.read())
        print(f"Downloaded: {url} -> {path}")
        return True
    except Exception as e:
        print(f"Failed {url}: {e}")
        return False

# Download .com banner
download_file('https://fractional-cc.com/cdn/shop/files/FCC_Banner-1_40c1e127-5031-41c7-a1a9-7ee13b3a9947.jpg', 'assets/images/banner/fcc-hero-banner.jpg')
download_file('https://fractional-cc.com/cdn/shop/files/logo-fcc-16_5698486c-7370-4068-9425-14324d37dfbf_32x32.png', 'assets/images/brand/logo-favicon.png')

# Download key visual assets from vn site
vn_downloads = [
    ('https://static.ladipage.net/61b976d64622180014834a72/artboard-7-20260824072049-bslvq.jpg', 'assets/images/banner/artboard-7.jpg'),
    ('https://w.ladicdn.com/s768x543/61b976d64622180014834a72/artboard-1-20260225105808-cbzyt.jpg', 'assets/images/banner/hero-main.jpg'),
    ('https://w.ladicdn.com/s768x1588/61b976d64622180014834a72/artboard-3-20260612103258-qxs8j.jpg', 'assets/images/mechanism/mechanism-infographic.jpg'),
    ('https://w.ladicdn.com/s768x818/61b976d64622180014834a72/artboard-4-20260824075822-en1hb.jpg', 'assets/images/banner/feature-routine.jpg'),
    ('https://w.ladicdn.com/s768x821/61b976d64622180014834a72/artboard-2-20260226021450-ut_fj.jpg', 'assets/images/banner/combo-visual.jpg'),
    ('https://w.ladicdn.com/s550x600/61b976d64622180014834a72/snapinstapp_454866985_1013148220137487_7599832557328149774_n_1080-20250120024411-33n2c.jpg', 'assets/images/feedback/feedback-1.jpg'),
    ('https://w.ladicdn.com/s550x600/61b976d64622180014834a72/1787563819389_2903489445515391041_2903489445515391041_d4c1165e0d2f6b5f21ea8f4c90cf9f9d-20260824093113-t_hrh.jpg', 'assets/images/feedback/feedback-2.jpg'),
    ('https://w.ladicdn.com/s550x600/61b976d64622180014834a72/1787563819409_2903489445515391041_2903489445515391041_7831cc46f7740f229efbb96a42d90cf8-20260824093045-t_eij.jpg', 'assets/images/feedback/feedback-3.jpg'),
    ('https://w.ladicdn.com/s550x800/61b976d64622180014834a72/1787562985822_2067565089174011579_7952800762099278676_2b31ee5dfe40e48f1d4b7f2c9518a96f-20260824091645-0_xhi.jpg', 'assets/images/feedback/feedback-4.jpg'),
]

for url, path in vn_downloads:
    download_file(url, path)
