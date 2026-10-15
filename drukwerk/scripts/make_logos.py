"""Genereert logo-opties (Q-beeldmerk en EQ5-woordmerk) als SVG + overzicht-PNG."""
import os
import cairosvg
from fontTools.ttLib import TTFont
from fontTools.pens.svgPathPen import SVGPathPen
from fontTools.pens.boundsPen import BoundsPen
from fontTools.pens.transformPen import TransformPen

ROOT = "/app/drukwerk"
FONT = "/app/lettertypes/Fraunces/Fraunces-SemiBold.ttf"
COLORS = {"brons": "#8B6B47", "salie": "#6B7F5C", "teal": "#3C6E71"}
INK = "#2B2420"
CREAM = "#F5F0E8"

font = TTFont(FONT)
gs = font.getGlyphSet()
cmap = font.getBestCmap()
CAP = font["OS/2"].sCapHeight


def glyph(ch, x=0.0, s=1.0):
    name = cmap[ord(ch)]
    pen = SVGPathPen(gs)
    gs[name].draw(TransformPen(pen, (s, 0, 0, -s, x, 0)))
    bp = BoundsPen(gs)
    gs[name].draw(bp)
    return pen.getCommands(), gs[name].width * s, bp.bounds


def tail_commands(cx, y0):
    """Staart in font units (y omhoog): vloeiende uitloop uit de ring, smalle steel, ronde punt."""
    flare_w, flare_y = 0.16 * CAP, y0 + 0.032 * CAP
    neck_w, neck_y = 0.046 * CAP, y0 - 0.10 * CAP
    bot_w, bottom = 0.036 * CAP, y0 - 0.34 * CAP
    end = bottom + bot_w
    k = 0.5523 * bot_w
    cmds = [("M", ((cx - flare_w, flare_y),)), ("L", ((cx + flare_w, flare_y),))]
    for sgn, seq in ((1, "down"), (-1, "up")):
        fl = (cx + sgn * flare_w, flare_y)
        nk = (cx + sgn * neck_w, neck_y)
        en = (cx + sgn * bot_w, end)
        c_fl = (cx + sgn * (neck_w + (flare_w - neck_w) * 0.30), y0 + 0.004 * CAP)
        c_nk = (cx + sgn * neck_w, y0 - 0.035 * CAP)
        if seq == "down":
            cmds += [("C", (c_fl, c_nk, nk)), ("L", (en,)),
                     ("C", ((cx + bot_w, end - k), (cx + k, bottom), (cx, bottom))),
                     ("C", ((cx - k, bottom), (cx - bot_w, end - k), (cx - bot_w, end)))]
        else:
            cmds += [("L", (nk,)), ("C", (c_nk, c_fl, fl))]
    cmds.append(("Z", ()))
    return cmds


def q_mark(x=0.0, s=1.0):
    """Q = Fraunces-O met een verticale staart midden onder de ring."""
    d, adv, (x0, y0, x1, y1) = glyph("O", x, s)
    cx = (x0 + x1) / 2
    tail = " ".join(op + " ".join(f"{x + px * s:.1f},{-py * s:.1f}" for px, py in pts)
                    for op, pts in tail_commands(cx, y0))
    return d, tail, adv


def svg_q(color):
    d, tail, adv = q_mark()
    h = CAP * 1.45
    vb = f"0 {-CAP * 1.08:.0f} {adv:.0f} {h:.0f}"
    return (f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="{vb}">'
            f'<g fill="{color}"><path d="{d}"/><path d="{tail}"/></g></svg>')


def svg_eq5(color, q_color=None):
    q_color = q_color or color
    e, ea, _ = glyph("E")
    track = CAP * 0.04
    qd, qt, qa = q_mark(ea + track)
    f, fa, _ = glyph("5", ea + qa + 2 * track)
    total = ea + qa + fa + 2 * track
    vb = f"0 {-CAP * 1.08:.0f} {total:.0f} {CAP * 1.45:.0f}"
    return (f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="{vb}">'
            f'<path fill="{color}" d="{e}"/>'
            f'<g fill="{q_color}"><path d="{qd}"/><path d="{qt}"/></g>'
            f'<path fill="{color}" d="{f}"/></svg>')


def inner(svg):
    return svg.split(">", 1)[1].rsplit("</svg>", 1)[0], svg.split('viewBox="')[1].split('"')[0]


def overview():
    W, H = 1800, 1250
    parts = [f'<svg xmlns="http://www.w3.org/2000/svg" width="{W}" height="{H}" viewBox="0 0 {W} {H}">',
             f'<rect width="{W}" height="{H}" fill="{CREAM}"/>',
             f'<text x="80" y="110" font-family="Fraunces" font-size="54" fill="{INK}">Logo-opties Balans</text>',
             f'<text x="80" y="230" font-family="Instrument Sans" font-size="30" fill="{INK}">Optie A — Q-beeldmerk</text>',
             f'<text x="80" y="720" font-family="Instrument Sans" font-size="30" fill="{INK}">Optie B — EQ5-woordmerk</text>']
    for i, (name, col) in enumerate(COLORS.items()):
        x = 80 + i * 560
        body, vb = inner(svg_q(col))
        parts.append(f'<svg x="{x + 130}" y="270" width="260" height="330" viewBox="{vb}">{body}</svg>')
        parts.append(f'<text x="{x + 260}" y="650" text-anchor="middle" font-family="Instrument Sans" '
                     f'font-size="24" fill="{INK}">{name} {col}</text>')
        body, vb = inner(svg_eq5(INK if i == 0 else col, col))
        parts.append(f'<svg x="{x}" y="760" width="500" height="330" viewBox="{vb}">{body}</svg>')
        label = f"{name} (Q) + donker" if i == 0 else f"{name} {col}"
        parts.append(f'<text x="{x + 250}" y="1140" text-anchor="middle" font-family="Instrument Sans" '
                     f'font-size="24" fill="{INK}">{label}</text>')
    parts.append("</svg>")
    return "".join(parts)


if __name__ == "__main__":
    out = os.path.join(ROOT, "logo-opties")
    os.makedirs(out, exist_ok=True)
    for name, col in COLORS.items():
        open(f"{out}/optie-A-Q-{name}.svg", "w").write(svg_q(col))
        open(f"{out}/optie-B-EQ5-{name}.svg", "w").write(svg_eq5(col))
    open(f"{out}/optie-B-EQ5-donker-Q-brons.svg", "w").write(svg_eq5(INK, COLORS["brons"]))
    ov = overview()
    open(f"{out}/overzicht.svg", "w").write(ov)
    cairosvg.svg2png(bytestring=ov.encode(), write_to=f"{out}/overzicht.png")
    print("klaar")
