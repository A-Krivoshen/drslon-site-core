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
cp "${SRC}/.well-known/agent-index.json" "${HTDOCS}/.well-known/agent-index.json"
mkdir -p "${HTDOCS}/en"
cp "${SRC}/en/ai.txt" "${HTDOCS}/en/ai.txt"
cp "${SRC}/en/llms.txt" "${HTDOCS}/en/llms.txt"
cp "${SRC}/en/llms-full.txt" "${HTDOCS}/en/llms-full.txt"
cp "${SRC}/en/agents.md" "${HTDOCS}/en/agents.md"
cp "${SRC}/en/agents.txt" "${HTDOCS}/en/agents.txt"
cp "${SRC}/en/index.html" "${HTDOCS}/en/index.html"
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
  "${HTDOCS}/.well-known/oauth-protected-resource" \
  "${HTDOCS}/.well-known/agent-index.json" \
  "${HTDOCS}/en/ai.txt" \
  "${HTDOCS}/en/llms.txt" \
  "${HTDOCS}/en/llms-full.txt" \
  "${HTDOCS}/en/agents.md" \
  "${HTDOCS}/en/agents.txt" \
  "${HTDOCS}/en/index.html"

echo "Synced site-root -> ${HTDOCS}"
