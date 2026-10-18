"""Printklaar visitekaartje EQ5 (85x55 mm, 3 mm afloop, snijtekens, CMYK, fonts ingesloten)."""
import os
from fontTools.ttLib import TTFont
from fontTools.pens.basePen import BasePen
from fontTools.pens.boundsPen import BoundsPen
from reportlab.pdfgen import canvas
from make_logos import tail_commands
from reportlab.lib.units import mm
from reportlab.lib.colors import CMYKColor
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont as RLFont

OUT = "/app/drukwerk/visitekaartje"
FD = "/app/lettertypes"
TRIM_W, TRIM_H, BLEED = 85 * mm, 55 * mm, 3 * mm

TEAL = CMYKColor(0.74, 0.30, 0.38, 0.12)  # #3F7F83, logo-teal van de website
TEAL_LIGHT = CMYKColor(0.45, 0.18, 0.22, 0.0)
TEAL_SOFT = CMYKColor(0.66, 0.31, 0.36, 0.10)
CREAM = CMYKColor(0.03, 0.05, 0.09, 0.0)
INK = CMYKColor(0.50, 0.60, 0.65, 0.72)
INK_SOFT = CMYKColor(0.40, 0.50, 0.55, 0.40)
REG = CMYKColor(1, 1, 1, 1)
BRONZE = CMYKColor(0.25, 0.40, 0.72, 0.10)      # #B08D57 ("naar balans")
BRONZE_SOFT = CMYKColor(0.08, 0.14, 0.27, 0.0)
BROWN = CMYKColor(0.35, 0.50, 0.70, 0.25)       # #8B6B47
DEEP = CMYKColor(0.55, 0.62, 0.65, 0.75)        # #2B2420, footer website
DEEP_SOFT = CMYKColor(0.50, 0.58, 0.62, 0.60)
BEIGE = CMYKColor(0.07, 0.09, 0.17, 0.0)        # #ECE4D4
TEAL_PALE = CMYKColor(0.34, 0.07, 0.17, 0.0)

CARD = {
    "naam": "Remco Sas",
    "functie": "Balansbrenger",
    "telefoon": "+31 6 23 988 488",
    "website": "www.eq5.nl",
    "diensten": ("meditaties · consulten · events", "trainingen · workshops"),
    "slogan": ("Van vicieuze cirkel", "naar balans"),
}

for name, file in {
    "Fraunces": "Fraunces/Fraunces-Regular.ttf",
    "Fraunces-Medium": "Fraunces/Fraunces-Medium.ttf",
    "Fraunces-Italic": "Fraunces/Fraunces-Italic.ttf",
    "InstrumentSans": "InstrumentSans/InstrumentSans-Regular.ttf",
    "InstrumentSans-Medium": "InstrumentSans/InstrumentSans-Medium.ttf",
}.items():
    pdfmetrics.registerFont(RLFont(name, os.path.join(FD, file)))

GFONT = TTFont(os.path.join(FD, "Fraunces/Fraunces-SemiBold.ttf"))
GS = GFONT.getGlyphSet()
CMAP = GFONT.getBestCmap()
CAP = GFONT["OS/2"].sCapHeight


class RLPen(BasePen):
    """fontTools-pen die tekent in een reportlab-path (font units -> pt)."""

    def __init__(self, path, x, y, s):
        super().__init__(GS)
        self.p, self.x, self.y, self.s = path, x, y, s

    def t(self, pt):
        return self.x + pt[0] * self.s, self.y + pt[1] * self.s

    def _moveTo(self, pt):
        self.p.moveTo(*self.t(pt))

    def _lineTo(self, pt):
        self.p.lineTo(*self.t(pt))

    def _curveToOne(self, a, b, c):
        self.p.curveTo(*self.t(a), *self.t(b), *self.t(c))

    def _closePath(self):
        self.p.close()


def glyph_bounds(ch):
    bp = BoundsPen(GS)
    GS[CMAP[ord(ch)]].draw(bp)
    return bp.bounds


def draw_tail(pen, cx, y0):
    for op, pts in tail_commands(cx, y0):
        {"M": pen.moveTo, "L": pen.lineTo, "C": lambda *p: pen.curveTo(*p),
         "Z": pen.closePath}[op](*pts)


def eq5_width():
    track = CAP * 0.04
    return sum(GS[CMAP[ord(c)]].width for c in "EO5") + 2 * track


def draw_eq5(c, x, y, width, color):
    s = width / eq5_width()
    track = CAP * 0.04
    path = c.beginPath()
    cur = 0.0
    for ch in "EO5":
        g = GS[CMAP[ord(ch)]]
        pen = RLPen(path, x + cur * s, y, s)
        g.draw(pen)
        if ch == "O":
            qx = x + cur * s
        cur += g.width + track
    c.setFillColor(color)
    c.drawPath(path, stroke=0, fill=1)
    tail = c.beginPath()
    x0, y0, x1, _ = glyph_bounds("O")
    draw_tail(RLPen(tail, qx, y, s), (x0 + x1) / 2, y0)
    c.drawPath(tail, stroke=0, fill=1)


def draw_q(c, x, y, height, color):
    s = height / CAP
    path = c.beginPath()
    GS[CMAP[ord("O")]].draw(RLPen(path, x, y, s))
    c.setFillColor(color)
    c.drawPath(path, stroke=0, fill=1)
    tail = c.beginPath()
    x0, y0, x1, _ = glyph_bounds("O")
    draw_tail(RLPen(tail, x, y, s), (x0 + x1) / 2, y0)
    c.drawPath(tail, stroke=0, fill=1)


def tracked(c, x, y, text, font, size, spacing, color):
    c.saveState()
    t = c.beginText(x, y)
    t.setFont(font, size)
    t.setCharSpace(spacing)
    t.setFillColor(color)
    t.textOut(text)
    c.drawText(t)
    c.restoreState()


def front(c):
    c.setFillColor(CREAM)
    c.rect(-BLEED, -BLEED, TRIM_W + 2 * BLEED, TRIM_H + 2 * BLEED, stroke=0, fill=1)
    w = 44 * mm
    draw_eq5(c, (TRIM_W - w) / 2, 30 * mm, w, TEAL)
    size = 8.5 * 1.3
    c.setFillColor(INK)
    c.setFont("Fraunces", size)
    c.drawCentredString(TRIM_W / 2, 13.2 * mm, CARD["slogan"][0])
    c.setFillColor(BRONZE)
    c.setFont("Fraunces-Italic", size)
    c.drawCentredString(TRIM_W / 2, 8.6 * mm, CARD["slogan"][1])


def full_bg(c, color):
    c.setFillColor(color)
    c.rect(-BLEED, -BLEED, TRIM_W + 2 * BLEED, TRIM_H + 2 * BLEED, stroke=0, fill=1)


def back_text(c, name, role, text, line):
    left = 7 * mm
    c.setFillColor(name)
    c.setFont("Fraunces-Medium", 13)
    c.drawString(left, 42 * mm, CARD["naam"])
    tracked(c, left, 37 * mm, CARD["functie"].upper(), "InstrumentSans-Medium", 6.5, 1.2, role)
    c.setFillColor(text)
    c.setFont("Fraunces-Italic", 7.5)
    c.drawString(left, 31 * mm, CARD["diensten"][0])
    c.drawString(left, 27.6 * mm, CARD["diensten"][1])
    c.setStrokeColor(line)
    c.setLineWidth(0.5)
    c.line(left, 22.5 * mm, left + 10 * mm, 22.5 * mm)
    c.setFillColor(text)
    c.setFont("InstrumentSans", 8)
    c.drawString(left, 17 * mm, CARD["telefoon"])
    c.drawString(left, 12.5 * mm, CARD["website"])


def big_q(c, color):
    c.saveState()
    p = c.beginPath()
    p.rect(-BLEED, -BLEED, TRIM_W + 2 * BLEED, TRIM_H + 2 * BLEED)
    c.clipPath(p, stroke=0, fill=0)
    draw_q(c, TRIM_W - 30 * mm, 9 * mm, 30 * mm, color)
    c.restoreState()


def back_a(c):
    """A: crème, bruine tekst, bronzen accenten."""
    full_bg(c, CREAM)
    big_q(c, BRONZE_SOFT)
    back_text(c, DEEP, BRONZE, BROWN, BRONZE)


def back_b(c):
    """B: donker warmbruin (footer website), crème tekst, brons."""
    full_bg(c, DEEP)
    big_q(c, DEEP_SOFT)
    back_text(c, CREAM, BRONZE, CREAM, BRONZE)


def back_c(c):
    """C: zacht beige met teal EQ5-Q als accent."""
    full_bg(c, BEIGE)
    big_q(c, TEAL_PALE)
    back_text(c, DEEP, TEAL, BROWN, BRONZE)


BACKS = {"A-creme": back_a, "B-donkerbruin": back_b, "C-beige-teal": back_c}


def crop_marks(c, slug):
    c.setStrokeColor(REG)
    c.setLineWidth(0.25)
    off, ln = BLEED + 1 * mm, slug - 1 * mm
    for x in (0, TRIM_W):
        for y in (0, TRIM_H):
            dx = -1 if x == 0 else 1
            dy = -1 if y == 0 else 1
            c.line(x + dx * off, y, x + dx * (off + ln - BLEED), y)
            c.line(x, y + dy * off, x, y + dy * (off + ln - BLEED))


def build(filename, marks, back):
    slug = 8 * mm if marks else BLEED
    pw, ph = TRIM_W + 2 * slug, TRIM_H + 2 * slug
    c = canvas.Canvas(os.path.join(OUT, filename), pagesize=(pw, ph), initialFontName="InstrumentSans", initialFontSize=8)
    c.setTitle("Visitekaartje EQ5 - Remco Sas")
    c.setAuthor("EQ5 / Balans")
    for side in (front, back):
        c.saveState()
        c.translate(slug, slug)
        side(c)
        c.restoreState()
        if marks:
            c.saveState()
            c.translate(slug, slug)
            crop_marks(c, slug)
            c.restoreState()
        c.showPage()
    c.save()
    return slug, pw, ph


def set_boxes(filename, slug, pw, ph):
    from pypdf import PdfReader, PdfWriter
    from pypdf.generic import RectangleObject
    path = os.path.join(OUT, filename)
    r, w = PdfReader(path), PdfWriter()
    for page in r.pages:
        page.trimbox = RectangleObject([slug, slug, pw - slug, ph - slug])
        page.bleedbox = RectangleObject([slug - BLEED, slug - BLEED, pw - slug + BLEED, ph - slug + BLEED])
        w.add_page(page)
    w.write(path)


def previews(filename, key):
    import pymupdf as fitz
    doc = fitz.open(os.path.join(OUT, filename))
    for i, page in enumerate(doc):
        name = "preview-voorkant.png" if i == 0 else f"preview-achterkant-{key}.png"
        page.get_pixmap(dpi=300, clip=page.trimbox).save(os.path.join(OUT, name))


def overview():
    from PIL import Image, ImageDraw, ImageFont
    files = ["preview-voorkant.png"] + [f"preview-achterkant-{k}.png" for k in BACKS]
    ims = [Image.open(os.path.join(OUT, f)).convert("RGB") for f in files]
    w, h = ims[0].size
    gap, lab = 60, 90
    sheet = Image.new("RGB", (2 * w + 3 * gap, 2 * (h + lab) + 3 * gap), (236, 228, 212))
    d = ImageDraw.Draw(sheet)
    font = ImageFont.truetype(os.path.join(FD, "InstrumentSans/InstrumentSans-Medium.ttf"), 44)
    labels = ["Voorkant"] + [f"Achterkant optie {k.replace('-', ' — ', 1).replace('creme', 'crème')}" for k in BACKS]
    for i, (im, label) in enumerate(zip(ims, labels)):
        x = gap + (i % 2) * (w + gap)
        y = gap + (i // 2) * (h + lab + gap)
        d.text((x, y), label, fill=(43, 36, 32), font=font)
        sheet.paste(im, (x, y + lab))
    sheet.save(os.path.join(OUT, "overzicht-opties.png"))


if __name__ == "__main__":
    os.makedirs(OUT, exist_ok=True)
    for f in os.listdir(OUT):
        if f.endswith((".pdf", ".png")):
            os.remove(os.path.join(OUT, f))
    for key, back in BACKS.items():
        for kind, marks in (("snijtekens", True), ("afloop", False)):
            fn = f"visitekaartje-EQ5-optie-{key}-drukklaar-{kind}.pdf"
            set_boxes(fn, *build(fn, marks, back))
        previews(f"visitekaartje-EQ5-optie-{key}-drukklaar-afloop.pdf", key)
    overview()
    print("klaar")
