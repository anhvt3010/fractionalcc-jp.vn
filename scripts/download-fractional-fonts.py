"""Vendor the existing Google Fonts families and licenses without changing typography."""
import pathlib
import re
import urllib.request

root = pathlib.Path(__file__).resolve().parent.parent / 'wp-content/themes/project-child/assets/fractional'
fonts = root / 'assets/fonts'
fonts.mkdir(parents=True, exist_ok=True)
url = ('https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,500..600'
       '&family=Plus+Jakarta+Sans:wght@300..800&display=swap')
request = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0 AppleWebKit/537.36 Chrome/131.0.0.0 Safari/537.36'})
css = urllib.request.urlopen(request).read().decode()
count = 0
for remote in set(re.findall(r'https://fonts\.gstatic\.com/[^)]+\.woff2', css)):
    name = remote.split('/')[-3] + '-' + remote.rsplit('/', 1)[1]
    (fonts / name).write_bytes(urllib.request.urlopen(remote).read())
    css = css.replace(remote, 'assets/fonts/' + name)
    count += 1
for family in ('lora', 'plusjakartasans'):
    license_url = 'https://raw.githubusercontent.com/google/fonts/main/ofl/' + family + '/OFL.txt'
    (fonts / (family + '-OFL.txt')).write_bytes(urllib.request.urlopen(license_url).read())
(root / 'fonts.css').write_text('/* Source: Google Fonts CSS API; SIL OFL licenses in assets/fonts/. */\n' + css, encoding='utf-8')
print(f'Vendored {count} WOFF2 files and two SIL OFL licenses.')
