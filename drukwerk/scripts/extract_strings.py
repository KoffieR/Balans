"""Haalt alle vertaalbare strings (textdomain 'balans') uit het thema."""
import re, glob, json
ROOT = "/app/balans-theme"
PAT = re.compile(r"\b(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*'((?:[^'\\]|\\.)*)'\s*,\s*'balans'\s*\)")
strings = {}
for f in sorted(glob.glob(ROOT + "/**/*.php", recursive=True)):
    for m in PAT.finditer(open(f).read()):
        s = m.group(1).replace("\\'", "'")
        strings.setdefault(s, f.replace(ROOT + "/", ""))
other = re.findall(r"\b(_n|_x|_nx|_ex|esc_html_x|esc_attr_x)\(", "".join(open(f).read() for f in glob.glob(ROOT + "/**/*.php", recursive=True)))
json.dump(strings, open("/app/drukwerk/scripts/strings.json", "w"), ensure_ascii=False, indent=0)
print(len(strings), "strings; other funcs:", set(other))
