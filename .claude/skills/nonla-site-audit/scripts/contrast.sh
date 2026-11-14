#!/usr/bin/env bash
# WCAG 2.x contrast ratio between two 6-digit hex colours.
#   bash contrast.sh '#B85520' '#FAF5EC'
set -euo pipefail
[ $# -eq 2 ] || { echo "usage: contrast.sh '#RRGGBB' '#RRGGBB'" >&2; exit 2; }
awk -v a="$1" -v b="$2" '
function ch(c) { c = c / 255; return (c <= 0.03928) ? c / 12.92 : ((c + 0.055) / 1.055) ^ 2.4 }
function lum(h) { sub(/^#/, "", h)
  return 0.2126 * ch(strtonum("0x" substr(h,1,2))) + 0.7152 * ch(strtonum("0x" substr(h,3,2))) + 0.0722 * ch(strtonum("0x" substr(h,5,2))) }
BEGIN {
  if (a !~ /^#?[0-9A-Fa-f]{6}$/ || b !~ /^#?[0-9A-Fa-f]{6}$/) { print "need two 6-digit hex colours" > "/dev/stderr"; exit 2 }
  x = lum(a); y = lum(b); if (x < y) { t = x; x = y; y = t }
  r = (x + 0.05) / (y + 0.05)
  v = (r >= 4.5) ? "passes AA for all text" : (r >= 3) ? "AA for large text and UI only" : "fails AA"
  printf "%s on %s  %.2f:1  %s\n", a, b, r, v
}'
