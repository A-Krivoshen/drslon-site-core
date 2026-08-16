# auth.md

You are an agent. This file describes how to discover and obtain access to **krivoshein.site** (public site + WordPress REST API) on behalf of a human or for public discovery.

Two hosts are relevant (same origin for this deployment):

- **Resource server** — `https://krivoshein.site` — HTML pages, Markdown negotiation, and public WordPress REST at `https://krivoshein.site/wp-json/`
- **Authorization / registration surface** — `https://krivoshein.site` — discovery metadata under `/.well-known/` and human claim / contact flows

## Current state

Most **public read** endpoints (posts, pages, search, llms.txt, agent discovery files) do **not** require credentials. Prefer unauthenticated discovery first.

**Agentic OAuth registration (auth.md full automated ID-JAG / device claim against live token endpoints) is not enabled as a production OAuth product on this host.** Privileged or custom agent credentials (if ever needed) are provisioned **out of band** after a human claim / contact step. Do not invent accounts, do not ask the user to paste secrets into chat, and do not spam `POST /agent/auth`.

Authoritative machine-readable discovery:

- Protected Resource Metadata: `https://krivoshein.site/.well-known/oauth-protected-resource`
- Authorization Server Metadata: `https://krivoshein.site/.well-known/oauth-authorization-server`

If anything in this file conflicts with those JSON documents, **trust the JSON**.

## Step 1 — Discover

### 1a. Protected Resource Metadata (PRM)

```http
GET /.well-known/oauth-protected-resource HTTP/1.1
Host: krivoshein.site
Accept: application/json
```

Expected shape (honest stub — not a live OAuth resource server):

```json
{
  "resource": "https://krivoshein.site/wp-json/",
  "resource_name": "krivoshein.site WordPress REST API",
  "oauth_enabled": false,
  "error": "not_enabled",
  "authorization_servers": ["https://krivoshein.site"],
  "scopes_supported": ["public:read", "agent:discover"],
  "bearer_methods_supported": []
}
```

- `oauth_enabled` is **false**. Do not request or send a bearer for public GETs.
- `resource` — canonical API base for machine clients (WordPress REST).
- `authorization_servers` — discovery JSON lives on this host; it does **not** mean a working token issuer.
- `scopes_supported` — documentation only; public GETs need no bearer.

### 1b. Authorization Server Metadata + `agent_auth`

```http
GET /.well-known/oauth-authorization-server HTTP/1.1
Host: krivoshein.site
Accept: application/json
```

Read:

- `oauth_enabled` is **false**. `grant_types_supported` is **empty**. `response_types_supported` is `["none"]`.
- `error` / `note` — automated registration and token issuance are not enabled.
- `service_documentation` — this file.
- Advertised `token_endpoint`, `revocation_endpoint`, and `registration_endpoint` are **stubs**. POSTs return JSON `not_enabled` / `unsupported_grant_type`. Do not treat them as a working OAuth server.
- `agent_auth.skill` — this document (`https://krivoshein.site/auth.md`).
- `agent_auth.claim_uri` — humans use `https://krivoshein.site/contacts/`.
- `agent_auth.register_uri` — policy stub only; automated account creation is not offered.

If `oauth_enabled` is false (it is), **stop**. Do not mint JWT assertions, do not call `/oauth2/token`, and do not invent client_id / client_secret.

## Step 2 — Pick a method

| What you have | What to do |
| --- | --- |
| Only public content (blog, services, prices, contacts) | **No registration.** Call public GET APIs and Markdown discovery files. |
| Need privileged / custom integration | **Human claim / contact** — surface `https://krivoshein.site/contacts/` to the user; operator provisions access out of band. |
| ID-JAG or automated identity_assertion | **Not supported in production today.** Do not mint assertions for this audience unless this file and AS metadata explicitly enable them later. |

## Step 3 — Public access (no credential)

Prefer these **unauthenticated** discovery paths:

```http
GET /llms.txt HTTP/1.1
Host: krivoshein.site
Accept: text/markdown
```

```http
GET /wp-json/wp/v2/posts?per_page=10 HTTP/1.1
Host: krivoshein.site
Accept: application/json
```

```http
GET /.well-known/agent.json HTTP/1.1
Host: krivoshein.site
Accept: application/json
```

Related documents:

- [agents.md](https://krivoshein.site/agents.md) — agent instructions
- [llms.txt](https://krivoshein.site/llms.txt) — site index for LLMs
- [openapi.json](https://krivoshein.site/openapi.json) — OpenAPI sketch
- [api-catalog](https://krivoshein.site/.well-known/api-catalog) — linkset catalog

Accept-header content negotiation: `Accept: text/markdown` on HTML pages returns Markdown when supported.

## Step 4 — Human claim / provisioning (when a credential is required)

There is **no self-serve agent signup form** that mints production OAuth tokens.

1. Tell the user to open the claim / contact page: [Contacts](https://krivoshein.site/contacts/)
2. User describes the agent use-case (scopes, rate limits, contact email).
3. Operator may issue a scoped credential **out of band** (environment variable / secret store — never chat paste).
4. Resume only after the user confirms the secret is available in the environment (e.g. `KRIVOSHEIN_AGENT_TOKEN` if provisioned).

`register_uri` in AS metadata documents this policy surface. Passive scanners must not create accounts via POST.

## Step 5 — Use a credential (if provisioned)

If the user has provisioned a bearer token:

```http
GET /wp-json/wp/v2/posts HTTP/1.1
Host: krivoshein.site
Authorization: Bearer $KRIVOSHEIN_AGENT_TOKEN
Accept: application/json
```

Rules:

- Read the token from the environment or secret store at call time.
- Never echo it in logs, commits, PRs, or chat.
- On `401` / `403`, drop any cached value and ask the user to rotate/refresh out of band.

## Errors

| Situation | Meaning | Action |
| --- | --- | --- |
| Public GET returns 200 | No auth needed | Continue |
| `oauth_enabled: false` in AS/PRM JSON | No live OAuth | Use public APIs or human claim |
| `not_enabled` / `unsupported_grant_type` from `/oauth2/token`, `/oauth2/revoke`, `/agent/auth` | Stubs, not a token server | Do not retry; contacts/ for humans |
| `401` with bearer | Token invalid / revoked | Drop secret; user re-provisions |
| `429` | Rate limited | Back off; honor Retry-After |

## Revocation

- **Human-mediated:** user or operator revokes any issued token out of band (contact / operator process).
- **Discovery:** treat unexpected `401` on a previously working token as revocation.
- When `revocation_uri` appears in `agent_auth` and live revocation is enabled, follow [RFC 7009](https://datatracker.ietf.org/doc/html/rfc7009) only if documented as live; until then do not call speculative revoke endpoints.

## Operator / legal

- Site owner: ИП Кривошеин Алексей Сергеевич (Dr.Slon)
- Contacts: [https://krivoshein.site/contacts/](https://krivoshein.site/contacts/)
- Email: aleksey@krivoshein.site
- Offer / terms: [https://krivoshein.site/oferta/](https://krivoshein.site/oferta/)

## Related

- Protocol overview: [https://workos.com/auth-md](https://workos.com/auth-md)
- Spec repo: [https://github.com/workos/auth.md](https://github.com/workos/auth.md)
