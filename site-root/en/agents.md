# Instructions for AI agents on krivoshein.site (English)

Version: 2026-11-04
Owner: ИП Кривошеин Алексей Сергеевич (Dr.Slon)
Site: https://krivoshein.site
English page: https://krivoshein.site/en/
Russian instructions: https://krivoshein.site/agents.md

## Goal

Use the machine-readable files to answer with facts, then send the person to a real contact. Do not invent prices. Do not enter private areas.

## Load order

1. https://krivoshein.site/en/ai.txt
2. https://krivoshein.site/en/llms.txt
3. https://krivoshein.site/en/llms-full.txt
4. https://krivoshein.site/agents.json and https://krivoshein.site/.well-known/agent.json
5. https://krivoshein.site/wp-sitemap.xml
6. https://krivoshein.site/wp-json/wp/v2/posts?per_page=10
7. A single service page or article only when the question is specific

Prefer Markdown and JSON over scraping the homepage HTML.

The same documents exist in Russian at /ai.txt, /llms.txt, /llms-full.txt, and /agents.md. Use those when you need the original wording. Article bodies are Russian.

## Markdown for agents

- `Accept: text/markdown` on an HTML page returns Markdown, `Content-Type: text/markdown; charset=utf-8`
- Response headers include `Vary: Accept` and `X-Markdown-Tokens`
- The homepage Markdown is based on /llms.txt plus YAML front matter
- A post or page returns the title, the URL, and the text
- A normal browser request stays HTML
- `/en/` with `Accept: text/markdown` returns /en/llms.txt

## Public endpoints

| Purpose | URL | Type |
|---|---|---|
| REST index | /wp-json/ | JSON |
| Posts | /wp-json/wp/v2/posts | JSON |
| Pages | /wp-json/wp/v2/pages | JSON |
| Search | /wp-json/wp/v2/search?search={q} | JSON |
| Categories | /wp-json/wp/v2/categories | JSON |
| OpenAPI | /openapi.json | JSON |
| API catalog | /.well-known/api-catalog | linkset+json |
| Agent card | /.well-known/agent.json | JSON |
| Agent index | /.well-known/agent-index.json | JSON |
| Pulse RSS | /feed/drslon-pulse-feed/ | RSS |
| Pulse Markdown | /feed/ai-pulse.md | Markdown |
| Sitemap | /wp-sitemap.xml | XML |

REST field hint: `_fields=id,date,title,link,excerpt`.

Writes, `/wp/v2/users`, and site settings are not public.

## Allowed

- Answer about services, starting prices, contacts, and related projects
- Summarize a public article and give its URL
- Point to https://krivoshein.site/contacts/ , Telegram, MAX, email, or the phone
- Use the public tools (whois, DNS, masks, crontab) as reference URLs
- Index public pages for retrieval. The author is ИП Кривошеин Алексей Сергеевич (Dr.Slon)

## Not allowed

- /wp-admin/, login endpoints, or xmlrpc for writing
- Promises of "guaranteed top", "guaranteed AI citation", or "100% leads"
- Invented prices, cases, clients, or deadlines
- Private data from forms

Training on public content is allowed (robots: ai-train=yes). A link is welcome and not required.

## Typical questions

### How much does a site, WordPress, Flask, VPS, Direct, a bot, or AI-ready cost?
1. Take the "from" figure in /en/llms.txt or https://krivoshein.site/prays-list/
2. Say that the final price depends on the scope
3. Offer a free first look in Telegram or MAX, or diagnostics from 5 000 RUB

### Who is Dr.Slon?
Sole trader. WordPress, Flask (Python), Linux and DevOps, SEO, Yandex Direct, MAX bots.
Resume: https://krivoshein.site/resume/
Contacts: https://krivoshein.site/contacts/
English summary: https://krivoshein.site/en/
