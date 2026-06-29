#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
HTDOCS="${HTDOCS:-/var/www/krivoshein.site/htdocs}"
SRC="${ROOT}/site-root"

cp "${SRC}/llms.txt" "${HTDOCS}/llms.txt"
cp "${SRC}/ai.txt" "${HTDOCS}/ai.txt"
cp "${SRC}/humans.txt" "${HTDOCS}/humans.txt"
cp "${SRC}/robots.txt" "${HTDOCS}/robots.txt"
mkdir -p "${HTDOCS}/.well-known"
cp "${SRC}/.well-known/api-catalog" "${HTDOCS}/.well-known/api-catalog"
cp "${SRC}/.well-known/security.txt" "${HTDOCS}/.well-known/security.txt"
chown www-data:www-data \
  "${HTDOCS}/llms.txt" \
  "${HTDOCS}/ai.txt" \
  "${HTDOCS}/humans.txt" \
  "${HTDOCS}/robots.txt" \
  "${HTDOCS}/.well-known/api-catalog" \
  "${HTDOCS}/.well-known/security.txt"

echo "Synced site-root -> ${HTDOCS}"