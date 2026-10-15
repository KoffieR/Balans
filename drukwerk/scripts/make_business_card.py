"""Printklaar visitekaartje EQ5 (85x55 mm, 3 mm afloop, snijtekens, CMYK, fonts ingesloten)."""
import os
from fontTools.ttLib import TTFont
from fontTools.pens.basePen import BasePen
from fontTools.pens.boundsPen import BoundsPen
from reportlab.pdfgen import canvas
from reportlab.lib.units import mm
from reportlab.lib.colors import CMYKColor
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont as RLFont

OUT = "/app/drukwerk/visitekaartje"
FD = "/app/lettertypes"
TRIM_W, TRIM_H, BLEED = 85 * mm, 55 * mm, 3 * mm

TEAL = CMYKColor(0.75, 0.38, 0.42, 0.18)
TEAL_LIGHT = CMYKColor(0.45, 0.18, 0.22, 0.0)
TEAL_SOFT = CMYKColor(0.66, 0.31, 0.36, 0.10)
CREAM = CMYKColor(0.03, 0.05, 0.09, 0.0)
INK = CMYKColor(0.50, 0.60, 0.65, 0.72)
INK_SOFT = CMYKColor(0.40, 0.50, 0.55, 0.40)
REG = CMYKColor(1, 1, 1, 1)

CARD = {
    "naam": "Remco Sas",
    "functie": "Balansbrenger",
    "telefoon": "+31 6 23 988 488",
    "website": "www.eq5.nl",
    "slogan": "Van vicieuze cirkel naar balans",
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
    """Zelfde staart als in make_logos.py (y omhoog in font units)."""
    top, bottom = y0 + 0.035 * CAP, y0 - 0.34 * CAP
    wt, wb = 0.065 * CAP, 0.035 * CAP
    mid = top + (bottom - top) * 0.35
    end = bottom + wb
    k = 0.5523 * wb
    pen.moveTo((cx - wt, top))
    pen.lineTo((cx + wt, top))
    pen.qCurveTo((cx + wb * 1.05, mid), (cx + wb, end))
    pen.curveTo((cx + wb, end - k), (cx + k, bottom), (cx, bottom))
    pen.curveTo((cx - k, bottom), (cx - wb, end - k), (cx - wb, end))
    pen.qCurveTo((cx - wb * 1.05, mid), (cx - wt, top))
    pen.closePath()


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
    w = 46 * mm
    draw_eq5(c, (TRIM_W - w) / 2, 27 * mm, w, TEAL)
    c.setFillColor(INK_SOFT)
    c.setFont("Fraunces-Italic", 8.5)
    c.drawCentredString(TRIM_W / 2, 12.5 * mm, CARD["slogan"])


def back(c):
    c.setFillColor(TEAL)
    c.rect(-BLEED, -BLEED, TRIM_W + 2 * BLEED, TRIM_H + 2 * BLEED, stroke=0, fill=1)
    c.saveState()
    p = c.beginPath()
    p.rect(-BLEED, -BLEED, TRIM_W + 2 * BLEED, TRIM_H + 2 * BLEED)
    c.clipPath(p, stroke=0, fill=0)
    draw_q(c, TRIM_W - 30 * mm, 9 * mm, 30 * mm, TEAL_SOFT)
    c.restoreState()
    left = 7 * mm
    c.setFillColor(CREAM)
    c.setFont("Fraunces-Medium", 13)
    c.drawString(left, 37 * mm, CARD["naam"])
    tracked(c, left, 32 * mm, CARD["functie"].upper(), "InstrumentSans-Medium", 6.5, 1.2, TEAL_LIGHT)
    c.setStrokeColor(TEAL_LIGHT)
    c.setLineWidth(0.4)
    c.line(left, 26.5 * mm, left + 10 * mm, 26.5 * mm)
    c.setFillColor(CREAM)
    c.setFont("InstrumentSans", 8)
    c.drawString(left, 17 * mm, CARD["telefoon"])
    c.drawString(left, 12.5 * mm, CARD["website"])


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


def build(filename, marks):
    slug = 8 * mm if marks else BLEED
    pw, ph = TRIM_W + 2 * slug, TRIM_H + 2 * slug
    c = canvas.Canvas(os.path.join(OUT, filename), pagesize=(pw, ph))
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


def previews(filename):
    import pymupdf as fitz
    doc = fitz.open(os.path.join(OUT, filename))
    for i, page in enumerate(doc):
        clip = page.trimbox
        page.get_pixmap(dpi=300, clip=clip).save(os.path.join(OUT, f"preview-{'voorkant' if i == 0 else 'achterkant'}.png"))


if __name__ == "__main__":
    os.makedirs(OUT, exist_ok=True)
    for fn, marks in (("visitekaartje-EQ5-drukklaar-snijtekens.pdf", True),
                      ("visitekaartje-EQ5-drukklaar-afloop.pdf", False)):
        set_boxes(fn, *build(fn, marks))
    previews("visitekaartje-EQ5-drukklaar-afloop.pdf")
    print("klaar")
