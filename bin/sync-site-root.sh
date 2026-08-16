#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
HTDOCS="${HTDOCS:-/var/www/krivoshein.site/htdocs}"
SRC="${ROOT}/site-root"

cp "${SRC}/llms.txt" "${HTDOCS}/llms.txt"
cp "${SRC}/ai.txt" "${HTDOCS}/ai.txt"
cp "${SRC}/humans.txt" "${HTDOCS}/humans.txt"
cp "${SRC}/robots.txt" "${HTDOCS}/robots.txt"
cp "${SRC}/agents.md" "${HTDOCS}/agents.md"
cp "${SRC}/agents.txt" "${HTDOCS}/agents.txt"
cp "${SRC}/agents.json" "${HTDOCS}/agents.json"
cp "${SRC}/openapi.json" "${HTDOCS}/openapi.json"
cp "${SRC}/auth.md" "${HTDOCS}/auth.md"
mkdir -p "${HTDOCS}/.well-known"
cp "${SRC}/.well-known/api-catalog" "${HTDOCS}/.well-known/api-catalog"
cp "${SRC}/.well-known/security.txt" "${HTDOCS}/.well-known/security.txt"
cp "${SRC}/.well-known/agent.json" "${HTDOCS}/.well-known/agent.json"
cp "${SRC}/.well-known/oauth-authorization-server" "${HTDOCS}/.well-known/oauth-authorization-server"
cp "${SRC}/.well-known/oauth-protected-resource" "${HTDOCS}/.well-known/oauth-protected-resource"
chown www-data:www-data \
  "${HTDOCS}/llms.txt" \
  "${HTDOCS}/ai.txt" \
  "${HTDOCS}/humans.txt" \
  "${HTDOCS}/robots.txt" \
  "${HTDOCS}/agents.md" \
  "${HTDOCS}/agents.txt" \
  "${HTDOCS}/agents.json" \
  "${HTDOCS}/openapi.json" \
  "${HTDOCS}/auth.md" \
  "${HTDOCS}/.well-known/api-catalog" \
  "${HTDOCS}/.well-known/security.txt" \
  "${HTDOCS}/.well-known/agent.json" \
  "${HTDOCS}/.well-known/oauth-authorization-server" \
  "${HTDOCS}/.well-known/oauth-protected-resource"

echo "Synced site-root -> ${HTDOCS}"