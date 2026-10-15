"""Exporteert EQ5-logo, Q-accent en favicons naar het WordPress-thema."""
import os
import re
import cairosvg
from make_logos import svg_q, svg_eq5, glyph, CAP

IMG = "/app/balans-theme/assets/images"
TEAL, CREAM = "#3C6E71", "#F5F0E8"


def tighten(svg, x0, x1):
    top = -1460
    bottom = int(29 + 0.34 * CAP) + 10
    return re.sub(r'viewBox="[^"]+"', f'viewBox="{x0} {top} {x1 - x0} {bottom - top}"', svg, count=1)


def main():
    os.makedirs(IMG, exist_ok=True)
    _, _, (ox0, _, ox1, _) = glyph("O")
    q = tighten(svg_q("currentColor"), int(ox0) - 10, int(ox1) + 10)
    _, _, (ex0, _, _, _) = glyph("E")
    total = float(re.search(r'viewBox="\S+ \S+ (\S+)', svg_eq5("x")).group(1))
    _, fa, (_, _, fx1, _) = glyph("5")
    eq5 = tighten(svg_eq5("currentColor"), int(ex0) - 10, int(total - fa + fx1) + 10)
    for name, svg in (("logo-eq5.svg", eq5), ("q-accent.svg", q)):
        open(os.path.join(IMG, name), "w").write(svg.replace("<svg ", '<svg aria-hidden="true" focusable="false" '))

    body = q.split(">", 1)[1].rsplit("</svg>", 1)[0].replace("currentColor", CREAM)
    vb = re.search(r'viewBox="([^"]+)"', q).group(1)
    icon = (f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">'
            f'<rect width="64" height="64" rx="14" fill="{TEAL}"/>'
            f'<svg x="14" y="8" width="36" height="50" viewBox="{vb}">{body}</svg></svg>')
    open(os.path.join(IMG, "favicon.svg"), "w").write(icon)
    for size, fn in ((32, "favicon-32.png"), (180, "apple-touch-icon.png"), (512, "site-icon-512.png")):
        cairosvg.svg2png(bytestring=icon.encode(), write_to=os.path.join(IMG, fn), output_width=size, output_height=size)
    print("klaar")


if __name__ == "__main__":
    main()
