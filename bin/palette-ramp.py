#!/usr/bin/env python3
"""Print or check the tint and shade ramp for a theme.json palette color.

  palette-ramp.py SLUG "Display Name" "#rrggbb"   print the 19 palette entries
  palette-ramp.py --check theme.json              recompute every ramp in the file

Each ramp runs {slug}-90-lighter ... {slug} ... {slug}-90-darker in 10% steps.
Per channel (0-255), with p = N * 0.01:
  darker:  floor(c * (1 - p) + 0.5)
  lighter: floor(c + (255 - c) * p + 0.5)

Keep p as the float N * 0.01. Dividing (N / 100) rounds exact .5 ties
differently and changes some existing entries (primary-70-darker was the
one that exposed it), so the ramps already in theme.json would stop matching.
"""
import json
import math
import re
import sys

STEPS = range(10, 100, 10)


def channel(c, n, kind):
    p = n * 0.01
    value = c * (1 - p) if kind == "darker" else c + (255 - c) * p
    return math.floor(value + 0.5)


def shade(base_hex, n, kind):
    rgb = [int(base_hex[i:i + 2], 16) for i in (1, 3, 5)]
    return "#" + "".join("%02x" % channel(c, n, kind) for c in rgb)


def ramp(slug, name, base_hex):
    entries = []
    for n in reversed(STEPS):
        entries.append((f"{slug}-{n}-lighter", f"{name} {n}% Lighter", shade(base_hex, n, "lighter")))
    entries.append((slug, name, base_hex.lower()))
    for n in STEPS:
        entries.append((f"{slug}-{n}-darker", f"{name} {n}% Darker", shade(base_hex, n, "darker")))
    return entries


def check(path):
    palette = json.load(open(path))["settings"]["color"]["palette"]
    by_slug = {p["slug"]: p for p in palette}
    pattern = re.compile(r"^(.+)-(\d+)-(lighter|darker)$")
    bases = sorted({m.group(1) for s in by_slug if (m := pattern.match(s))})
    problems, checked = [], 0
    for base in bases:
        if base not in by_slug:
            problems.append(f"{base}: ramp has no base color entry")
            continue
        order = [s for s, _, _ in ramp(base, "", by_slug[base]["color"])]
        present = [p["slug"] for p in palette if p["slug"] in order]
        if present != order:
            problems.append(f"{base}: ramp is incomplete or out of order")
        for slug, name, color in ramp(base, by_slug[base]["name"], by_slug[base]["color"]):
            if slug == base or slug not in by_slug:
                continue
            checked += 1
            if by_slug[slug]["color"].lower() != color:
                problems.append(f"{slug}: file {by_slug[slug]['color']}, formula {color}")
    print(f"ramps: {', '.join(bases)}; {checked} entries checked, {len(problems)} problems")
    for line in problems:
        print("  " + line)
    return 1 if problems else 0


def main(argv):
    if len(argv) == 3 and argv[1] == "--check":
        return check(argv[2])
    if len(argv) == 4 and re.fullmatch(r"#[0-9a-fA-F]{6}", argv[3]):
        for slug, name, color in ramp(argv[1], argv[2], argv[3]):
            print('\t\t\t\t{\n\t\t\t\t\t"color": "%s",\n\t\t\t\t\t"name": "%s",\n\t\t\t\t\t"slug": "%s"\n\t\t\t\t},' % (color, name, slug))
        return 0
    print(__doc__)
    return 2


if __name__ == "__main__":
    sys.exit(main(sys.argv))
