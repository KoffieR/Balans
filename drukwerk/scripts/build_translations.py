"""Bouwt balans.pot en de .po/.mo-bestanden (EN/FR) voor het thema."""
import json
import polib
from translations import T

LANG_DIR = "/app/balans-theme/languages"
strings = json.load(open("/app/drukwerk/scripts/strings.json"))
assert len(strings) == len(T), (len(strings), len(T))

LOCALES = {"en_US": 0, "en_GB": 0, "fr_FR": 1, "fr_BE": 1}
META = {
    "Project-Id-Version": "Balans / EQ5 theme",
    "MIME-Version": "1.0",
    "Content-Type": "text/plain; charset=UTF-8",
    "Content-Transfer-Encoding": "8bit",
    "X-Domain": "balans",
}

pot = polib.POFile()
pot.metadata = dict(META)
for msgid, ref in strings.items():
    pot.append(polib.POEntry(msgid=msgid, msgstr="", occurrences=[(ref, "")]))
pot.save(f"{LANG_DIR}/balans.pot")

for locale, idx in LOCALES.items():
    po = polib.POFile()
    po.metadata = dict(META, Language=locale, **{"Plural-Forms": "nplurals=2; plural=(n != 1);" if idx == 0 else "nplurals=2; plural=(n > 1);"})
    for (msgid, ref), pair in zip(strings.items(), T):
        po.append(polib.POEntry(msgid=msgid, msgstr=pair[idx], occurrences=[(ref, "")]))
    po.save(f"{LANG_DIR}/{locale}.po")
    po.save_as_mofile(f"{LANG_DIR}/{locale}.mo")
print("klaar:", len(strings), "strings x", len(LOCALES), "locales")
