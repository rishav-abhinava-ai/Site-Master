# Site Master

Site Master is the shared WordPress plugin being developed for **Techgenyz** and **The Blissz**.

## Current development version

`0.1.0`

The authoritative value is `SITE_MASTER_DEVELOPMENT_VERSION` in `site-master.php`.

## First production release

The first production-ready release is reserved as `1.0.0`.

Development and production versions are independent.

## Profiles

- `techgenyz`
- `blissz`

Example deployment configuration:

```php
define( 'SITE_MASTER_PROFILE', 'techgenyz' );
```

## Required operating documents

Before significant Codex work, read:

1. `AGENTS.md`
2. `CODEX_HANDOFF.md`
3. the current authorized development prompt
4. `PRODUCTION_HANDOFF.md` for production release work

## Architecture boundary

**Rank Math** owns SEO metadata and structured data.

**Site Master** owns visible semantic HTML, Elementor components, media rendering, content data, site profiles, performance, and compatibility.

See `docs/PROJECT_OVERVIEW.md`.

## Content Analytics

`src/Analytics/` implements privacy-conscious first-party editorial analytics: conditional beacon collection, append-only events, scheduled aggregation, immutable Techgenyz baseline preservation, aggregate reports and protected export. See `docs/CONTENT-ANALYTICS-ARCHITECTURE.md`.
