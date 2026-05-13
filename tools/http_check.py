import urllib.request
import urllib.error

urls = [
    'https://admin.alumni-steman.my.id/grafana/',
    'https://admin.alumni-steman.my.id/grafana/api/health',
]

for url in urls:
    try:
        req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
        resp = urllib.request.urlopen(req, timeout=10)
        print(f"[OK {resp.status}] {url}")
    except urllib.error.HTTPError as e:
        print(f"[HTTP {e.code}] {url}")
    except Exception as e:
        print(f"[ERROR] {url}: {e}")
