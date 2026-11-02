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
    try:
        palette = json.load(open(path))["settings"]["color"]["palette"]
    except (ValueError, KeyError, OSError) as err:
        print(f"cannot read palette from {path}: {err.__class__.__name__}")
        return 1
    by_slug = {p["slug"]: p for p in palette}
    slugs = [p["slug"] for p in palette]
    pattern = re.compile(r"^(.+)-(\d+)-(lighter|darker)$")
    bases = sorted({m.group(1) for s in by_slug if (m := pattern.match(s))})
    problems, checked = [], 0
    for slug in slugs:
        if slug not in ("white", "black") and not pattern.match(slug) and slug not in bases:
            problems.append(f"{slug}: color has no ramp")
    for needed in ("white", "black", "gray"):
        if needed not in by_slug:
            problems.append(f"{needed}: missing, it stays on every site")
    if "gray" not in bases:
        problems.append("gray: missing ramp, it stays on every site")
    for base in bases:
        if base not in by_slug:
            problems.append(f"{base}: ramp has no base color entry")
            continue
        color = by_slug[base]["color"]
        if not re.fullmatch(r"#[0-9a-fA-F]{6}", color):
            problems.append(f"{base}: base color is not #rrggbb")
            continue
        expected = ramp(base, by_slug[base]["name"], color)
        order = [e[0] for e in expected]
        members = [k for k, s in enumerate(slugs) if s in order or (s.startswith(base + "-") and pattern.match(s) and pattern.match(s).group(1) == base)]
        ours = [slugs[k] for k in members]
        if ours != order:
            problems.append(f"{base}: ramp is incomplete, off-grid or out of order")
        if members and members[-1] - members[0] + 1 != len(members):
            problems.append(f"{base}: ramp entries are not contiguous in the palette")
        for slug, name, shade_hex in expected:
            if slug == base or slug not in by_slug:
                continue
            checked += 1
            entry = by_slug[slug]
            if entry["color"].lower() != shade_hex:
                problems.append(f"{slug}: file {entry['color']}, formula {shade_hex}")
            if entry["name"] != name:
                problems.append(f"{slug}: name is {entry['name']!r}, expected {name!r}")
    print(f"ramps: {', '.join(bases)}; {checked} entries checked, {len(problems)} problems")
    for line in problems:
        print("  " + line)
    return 1 if problems else 0


def main(argv):
    if len(argv) == 3 and argv[1] == "--check":
        return check(argv[2])
    if len(argv) == 4 and re.fullmatch(r"#[0-9a-fA-F]{6}", argv[3]) and re.fullmatch(r"[a-z][a-z0-9]*(-[a-z0-9]+)*", argv[1]):
        for slug, name, color in ramp(argv[1], argv[2], argv[3]):
            print("\t\t\t\t{\n\t\t\t\t\t\"color\": %s,\n\t\t\t\t\t\"name\": %s,\n\t\t\t\t\t\"slug\": %s\n\t\t\t\t}," % (json.dumps(color), json.dumps(name), json.dumps(slug)))
        return 0
    print(__doc__)
    return 2


if __name__ == "__main__":
    sys.exit(main(sys.argv))
