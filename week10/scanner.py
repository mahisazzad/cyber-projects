# scanner.py
import os

# Define signatures directly here
signatures = [
    {"name": "SuspiciousAgent", "pattern": b"User-Agent: EvilBot"},
    {"name": "Fake_EICAR_Test", "pattern": b"FAKE-EICAR-STRING"}
]

def scan_file(path):
    with open(path, "rb") as f:
        data = f.read()
    matches = []
    for sig in signatures:
        if sig["pattern"] in data:
            matches.append(sig["name"])
    return matches

def scan_dir(directory):
    results = {}
    for root, _, files in os.walk(directory):
        for fname in files:
            fpath = os.path.join(root, fname)
            try:
                hits = scan_file(fpath)
            except Exception:
                continue
            if hits:
                results[fpath] = hits
    return results

if __name__ == "__main__":
    target = "samples"
    results = scan_dir(target)
    if results:
        for file, hits in results.items():
            print(f"[MATCH] {file} -> {hits}")
    else:
        print("No matches found.")