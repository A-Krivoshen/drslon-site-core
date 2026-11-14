# Contact brief

`includes/contact-brief.php` renders a browser-only composer on the services
homepage and contact widget. No endpoint, database, mail delivery or new consent
storage is added. Known legacy hero copy is adapted at render time; custom ACF
copy and tariffs are preserved. `hero_title` is an editable new ACF field.

Canonical shared assets:

- `assets/js/contact-brief.js`
- `assets/css/contact-brief.css`

Standalone copies in the six static landings and Flask are generated and checked
by `tools/sync_contact_assets.py` in the optimization audit workspace. They do not
fetch their UI from WordPress at runtime. Keep copies byte-identical.

The draft includes service, canonical source page, selected task/package and
optional project URL/description/contact preference. Query strings and URL
credentials are excluded. Fields are not serialized into messenger URLs or
stored. Clipboard denial selects the draft for manual copying. Direct personal
Telegram, existing `/max`, phone and email links work without JavaScript.

Contact clicks retain `cta_telegram` and `cta_max`. New microevents are
`contact_open`, `contact_phone_click`, `contact_email_click`, `brief_start`,
`brief_prepare`, `brief_copy`. Parameters contain only allowlisted service and
canonical page. No `brief_submit_success` is emitted. Existing consent is honored
when `krvConsent` is present; legacy landing policies need separate reconciliation.
Creating/mapping admin goals requires reviewing the existing counter settings.

Tests live in the optimization workspace: isolated Chromium/Firefox flows,
PHP/ACF render fixture, source preservation and generated-asset parity. A real
WordPress staging database and physical mobile/Safari checks remain release
gates. No production deployment, policy migration or tariff change is included.
