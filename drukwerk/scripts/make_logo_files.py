"""EQ5-logo in teal: los en met slogan (tekst omgezet naar contouren). SVG + PNG + PDF."""
import os
import re
import cairosvg
import uharfbuzz as hb
from fontTools.ttLib import TTFont
from fontTools.pens.svgPathPen import SVGPathPen
from fontTools.pens.transformPen import TransformPen

OUT = "/app/drukwerk/logo"
TEAL = "#3C6E71"
LOGO = "/app/balans-theme/assets/images/logo-eq5.svg"
SLOGANS = {
    "nl": "van vicieuze cirkel naar balans",
    "en": "from vicious cycle to balance",
    "fr": "du cercle vicieux \u00e0 l\u2019\u00e9quilibre",
}
THEME_IMG = "/app/balans-theme/assets/images"
SLOGAN_FONT = "/app/lettertypes/Fraunces/Fraunces-Italic.ttf"


def text_path(text, font_file, size, x, y):
    """Shape met HarfBuzz (kerning) en geef SVG-pad + breedte terug."""
    blob = hb.Blob.from_file_path(font_file)
    face = hb.Face(blob)
    font = hb.Font(face)
    buf = hb.Buffer()
    buf.add_str(text)
    buf.guess_segment_properties()
    hb.shape(font, buf, {"kern": True, "liga": True})
    tt = TTFont(font_file)
    gs, order, upm = tt.getGlyphSet(), tt.getGlyphOrder(), tt["head"].unitsPerEm
    s = size / upm
    pen = SVGPathPen(gs)
    cx = 0
    for info, pos in zip(buf.glyph_infos, buf.glyph_positions):
        gs[order[info.codepoint]].draw(TransformPen(pen, (s, 0, 0, -s, x + (cx + pos.x_offset) * s, y)))
        cx += pos.x_advance
    return pen.getCommands(), cx * s


def logo_parts():
    svg = open(LOGO).read()
    vb = [float(v) for v in re.search(r'viewBox="([^"]+)"', svg).group(1).split()]
    body = svg.split(">", 1)[1].rsplit("</svg>", 1)[0].replace("currentColor", TEAL)
    return body, vb


def write(name, svg):
    path = os.path.join(OUT, name)
    open(path + ".svg", "w").write(svg)
    cairosvg.svg2png(bytestring=svg.encode(), write_to=path + ".png", output_width=3000)
    cairosvg.svg2pdf(bytestring=svg.encode(), write_to=path + ".pdf")


def slogan_svg(body, x, y, w, h, pad, slogan, color):
    size = w * 0.115
    _, tw = text_path(slogan, SLOGAN_FONT, size, 0, 0)
    base = y + h + size * 1.35
    d, tw = text_path(slogan, SLOGAN_FONT, size, x + (w - tw) / 2, base)
    left, right = min(x, x + (w - tw) / 2), max(x + w, x + (w + tw) / 2)
    bottom = base + size * 0.3
    return (f'<svg xmlns="http://www.w3.org/2000/svg" '
            f'viewBox="{left - pad:.0f} {y - pad:.0f} {right - left + 2 * pad:.0f} {bottom - y + 2 * pad:.0f}">'
            f'{body}<path fill="{color}" d="{d}"/></svg>')


def main():
    os.makedirs(OUT, exist_ok=True)
    body, (x, y, w, h) = logo_parts()
    pad = w * 0.06
    write("EQ5-logo-teal", f'<svg xmlns="http://www.w3.org/2000/svg" '
          f'viewBox="{x - pad:.0f} {y - pad:.0f} {w + 2 * pad:.0f} {h + 2 * pad:.0f}">{body}</svg>')

    for lang, slogan in SLOGANS.items():
        svg = slogan_svg(body, x, y, w, h, pad, slogan, TEAL)
        write(f"EQ5-logo-teal-met-slogan-{lang.upper()}", svg)
        theme = slogan_svg(body.replace(TEAL, "currentColor"), x, y, w, h, w * 0.01, slogan, "currentColor")
        open(os.path.join(THEME_IMG, f"logo-eq5-slogan-{lang}.svg"), "w").write(
            theme.replace("<svg ", '<svg aria-hidden="true" focusable="false" '))
    print("klaar")


if __name__ == "__main__":
    main()
