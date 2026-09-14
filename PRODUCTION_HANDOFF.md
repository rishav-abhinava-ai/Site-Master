# PRODUCTION_HANDOFF.md — Site Master Production Contract

## Purpose

This file is the mandatory production-release contract for the **Site Master** WordPress plugin.

Before creating ANY production ZIP, Codex MUST read this file in full and follow it.

This document defines:

- permanent plugin identity
- production version authority
- production ZIP rules
- release staging rules
- production acceptance gates
- SEO / Rank Math / schema checks
- semantic HTML checks
- Product / Company / Brand checks
- Elementor checks
- performance checks
- security checks
- data-safety checks
- regression checks
- production release ledger

This document is a release gate.

It does not replace inspection of the actual current source.

---

# 1. Permanent Plugin Identity

Permanent plugin name:

```text
Site Master
```

Permanent WordPress plugin directory:

```text
site-master/
```

Permanent main plugin file:

```text
site-master/site-master.php
```

Permanent plugin basename:

```text
site-master/site-master.php
```

The internal WordPress plugin directory MUST NOT contain the release version.

Correct:

```text
Site Master v1.0.0 Production.zip
└── site-master/
    ├── site-master.php
    ├── assets/
    ├── src/
    └── modules/
```

Incorrect:

```text
site-master-v1.0.0/
site-master-1.0.0/
site-master/site-master/
v1.0.0/site-master/
```

The version belongs in release metadata and outer ZIP name, not the internal plugin directory.

---

# 2. Version Tracks

Site Master intentionally maintains TWO independent version tracks:

1. Development Version
2. Production Version

They MUST NOT be treated as the same value.

---

# 3. Development Version Authority

The Development Version represents the current development/source state.

Its ONLY authority is:

```text
SITE_MASTER_DEVELOPMENT_VERSION
```

inside:

```text
site-master/site-master.php
```

Initial development version:

```text
0.0.1
```

Example progression:

```text
0.0.1
0.0.2
0.0.3
...
0.0.9
0.1.0
0.1.1
...
```

`CODEX_HANDOFF.md` contains historical implementation entries but is not version authority.

If `SITE_MASTER_DEVELOPMENT_VERSION` is missing or ambiguous, STOP production packaging.

---

# 4. Production Version Authority

The Last Production Version is authoritative ONLY from the release ledger in this file.

Initial state:

```text
Last Production Version: NONE
Last Production ZIP: NONE
```

The first successful production candidate is:

```text
1.0.0
```

After the first release, normal progression is:

```text
1.0.0
1.0.1
1.0.2
...
1.0.9
1.1.0
1.1.1
...
```

Major-version changes require explicit approval.

The Development Version MUST NOT be used to calculate the Candidate Production Version.

---

# 5. Candidate Production Version Rule

When:

```text
Last Production Version = NONE
```

the Candidate Production Version is:

```text
1.0.0
```

After the first release:

```text
Last Production Version 1.0.0
→ Candidate 1.0.1
```

Then:

```text
1.0.9
→ 1.1.0
```

A failed candidate does not advance the ledger.

The same candidate version may be rebuilt after blockers are fixed until a successful release is approved.

---

# 6. Production Release Copy Rule

Never modify the working development source merely to assign the Production Version.

Required flow:

```text
current reviewed development source
        ↓
clean temporary production staging copy
        ↓
apply Candidate Production Version only to release metadata in staged copy
        ↓
preserve SITE_MASTER_DEVELOPMENT_VERSION
        ↓
run complete production audit
        ↓
build production ZIP
        ↓
inspect actual ZIP
```

Example:

```text
Development Version = 0.1.9
Last Production = NONE
Candidate Production = 1.0.0
```

The staged production copy should contain:

```text
Plugin header Version = 1.0.0
SITE_MASTER_VERSION = 1.0.0
SITE_MASTER_DEVELOPMENT_VERSION = 0.1.9
```

The working development source remains unchanged by packaging.

---

# 7. Mandatory Production Trigger

When the user says:

```text
Give me production ready zip file
```

or clearly requests a production-ready release, Codex must:

1. Read `AGENTS.md`.
2. Read this `PRODUCTION_HANDOFF.md` completely.
3. Read `CODEX_HANDOFF.md`.
4. Inspect the actual current source.
5. Read the Development Version from source.
6. Read the Last Production Version from this ledger.
7. Determine the Candidate Production Version.
8. Stop if authoritative version information is missing/ambiguous.
9. Run the complete production audit.
10. Create a clean temporary production staging copy.
11. Apply Candidate Production Version only to the staged copy.
12. Preserve the Development Version inside the staged copy.
13. Remove forbidden development/repository artifacts.
14. Build the production ZIP.
15. Inspect the actual generated ZIP.
16. Run final validation.
17. If any mandatory blocker remains:
    - report `NOT READY FOR PRODUCTION`
    - do not update the production ledger
18. If every mandatory gate passes:
    - report `READY FOR PRODUCTION`
    - update the production ledger
    - provide the ZIP
    - provide the production-readiness report

Do not simply compress the repository.

---

# 8. Production ZIP Structure

The final ZIP must contain exactly one top-level plugin directory:

```text
site-master/
```

Outer production filename:

```text
Site Master vX.Y.Z Production.zip
```

Expected structure:

```text
Site Master vX.Y.Z Production.zip
└── site-master/
    ├── site-master.php
    ├── assets/
    ├── src/
    ├── modules/
    └── other runtime-required files/directories
```

The exact runtime subdirectories may evolve.

Permanent rules:

- exactly one top-level plugin directory
- root must be `site-master/`
- exactly one main plugin file at `site-master/site-master.php`
- no versioned internal folder
- no double nesting
- no outer version directory inside ZIP
- required runtime files must be present
- actual ZIP must be inspected after creation

---

# 9. Production Artifact Exclusions

Exclude non-runtime material unless it is explicitly required:

- `.git/`
- `.github/` when not needed at runtime
- old plugin ZIPs
- old nested Techgenyz plugin copies
- tests
- fixtures
- coverage
- PHPUnit configuration not needed at runtime
- PHPCS config not needed at runtime
- PHPStan config not needed at runtime
- IDE metadata
- local environment files
- logs
- screenshots
- audit PDFs
- caches
- temp files
- source maps
- debug artifacts
- development-only tooling
- node_modules
- Composer dev dependencies
- developer-only handoff/reference files unless intentionally included

Do not remove a file solely because its name appears development-related.

Verify runtime need first.

---

# 10. Mandatory ZIP Inspection

After building the ZIP, inspect the ZIP itself.

Verify:

```text
Exactly one top-level directory = YES
Top-level directory = site-master = YES
Main plugin file = site-master/site-master.php = YES
Main plugin file count = 1
Nested old plugin copy = NO
Versioned plugin root = NO
Double nesting = NO
Required runtime assets = PRESENT
Required Elementor files = PRESENT
Required templates/fallbacks = PRESENT
.git = 0
Old ZIPs = 0
Tests = 0
Fixtures = 0
Audit PDFs = 0
Debug artifacts = 0
Composer dev dependencies = 0
```

If any required runtime file is missing:

```text
NOT READY FOR PRODUCTION
```

If an unwanted artifact is included:

- correct the staging copy
- rebuild the ZIP
- inspect again

Do not manually patch/edit the ZIP after creation.

---

# 11. Data-Safety Gate

Production release preparation must never delete, reset, rename, recreate or overwrite existing live data merely to package/install an update.

Protected data includes, where applicable:

- posts/pages
- authors/users
- products
- companies
- brands
- comparisons
- deals
- taxonomies
- term assignments
- post meta
- options
- user meta
- Elementor templates
- Elementor saved widget data
- Rank Math metadata
- ratings
- offers
- specifications
- product gallery data
- company/brand relationships
- API settings
- notification settings
- site-profile settings
- media relationships

A normal plugin update must preserve all existing stored data unless an explicitly approved, tested and reversible migration requires otherwise.

---

# 12. Persistent-Identifier Gate

Verify that production release does not unintentionally change:

- plugin folder
- main plugin filename
- CPT slugs
- taxonomy slugs
- rewrite bases
- meta keys
- option keys
- Elementor widget IDs
- Elementor control IDs that are persisted
- REST namespaces/routes where compatibility matters
- AJAX action names where compatibility matters
- shortcodes where compatibility matters

If a new internal architecture replaces old runtime code, compatibility adapters may preserve stored identifiers.

---

# 13. Deactivation Safety

Deactivation must be non-destructive.

Verify that deactivation does not delete:

- posts
- products
- companies
- terms
- settings
- plugin tables if any
- post meta
- product data
- Elementor data
- Rank Math data
- media
- logs/analytics data intended to persist
- profile settings

Legitimate deactivation housekeeping such as scheduled-hook cleanup or rewrite refresh handling may be allowed.

---

# 14. Uninstall Safety

If Site Master implements uninstall cleanup, destructive cleanup must only occur through the genuine WordPress uninstall lifecycle and only according to explicit documented user intent/settings.

Production audit must verify that destructive cleanup cannot be triggered through:

- activation
- deactivation
- update
- settings save
- frontend load
- normal admin load
- AJAX
- REST
- cron
- plugin bootstrap
- manual file removal

Do not introduce duplicate delete-data mechanisms.

Never test destructive uninstall against real production data.

---

# 15. SEO / Rank Math Ownership Gate

Rank Math must remain the sole SEO/schema authority.

Verify Site Master does not generate duplicate:

- `<title>`
- meta description
- canonical
- robots
- Open Graph
- Twitter metadata
- XML sitemap
- News sitemap
- JSON-LD graphs

Verify Site Master does not globally override Rank Math without a documented requirement.

Particular checks:

- no unnecessary global sitemap-cache disable
- no unnecessary forced SearchAction override
- no custom canonical override unless explicitly approved
- no custom robots override unless explicitly approved

---

# 16. Schema Gate

Site Master must not output schema directly from visual components.

Search production source and rendered representative pages for:

```text
itemscope
itemtype
itemprop
application/ld+json
```

Classify legitimate Rank Math output separately from Site Master output.

Site Master must not produce:

- BlogPosting microdata in cards
- BlogPosting microdata in single-post widgets
- Article microdata
- NewsArticle microdata
- Product microdata
- Organization microdata
- duplicate JSON-LD

Rank Math schema must match visible values where applicable:

- headline
- author
- datePublished
- dateModified
- image
- product name
- brand
- review score
- offers

---

# 17. Article / Post Semantic Gate

Representative single-post pages must verify:

- one primary `<main>`
- one page H1
- one complete primary `<article>`
- article header inside article
- featured image inside article where intended
- article body inside the same article
- article footer/topics inside article
- sidebar outside article
- normal after-article related stories outside article
- visible author linked where appropriate
- visible `<time datetime="">`
- exact visible publication date/time for Techgenyz news
- no schema/microdata from Site Master

---

# 18. Homepage / Archive Semantic Gate

Verify:

## Homepage

- stable page-level H1
- meaningful H2 sections
- Post Card titles use H3 under section headings
- Post Cards use `<article>`
- no card schema/microdata

## Archive / Search

- archive/query H1
- cards use H2
- pagination is navigation
- sidebar semantics correct
- no card schema/microdata

---

# 19. Static Page Gate

Representative:

- About
- Contact
- Advertise
- Editorial Policy
- Corrections
- Privacy
- Terms

Verify:

- one H1
- meaningful sections
- no unnecessary Article semantic wrapper
- Rank Math page schema remains authoritative
- breadcrumb output does not duplicate schema

---

# 20. Author Page Gate

Verify:

- author name is H1
- visible author profile/bio
- article listing uses shared Post Card component
- Rank Math ProfilePage/Person remains authoritative
- no duplicate Person/ProfilePage graph from Site Master

---

# 21. Product Listing Gate

Representative Product archive/category pages must verify:

- H1
- optional description
- product list/grid semantics
- Product Card root normally `<li>`
- product thumbnail does not require `<figure>` unless caption/credit is rendered
- no Product schema from cards
- pagination works
- filters do not create uncontrolled indexable URL sprawl
- responsive images are present
- offscreen product images may lazy-load

---

# 22. Product Detail Gate

Representative products across multiple categories:

- mobile
- camera
- drone
- headphones
- earphones
- another gadget category

Verify:

- same shared Product architecture
- product H1
- brand relationship
- gallery
- features
- specifications
- rating
- offers/prices when applicable
- product data preserved from legacy implementation
- responsive image markup
- primary product/LCP image not blindly lazy-loaded
- no Site Master Product JSON-LD

---

# 23. Product Schema Integration Gate

Rank Math Product/Review integration must be validated on representative product/review URLs.

Verify:

- Product entity is generated through Rank Math where configured
- editorial score is not falsely used as AggregateRating
- AggregateRating exists only when genuine aggregated rating data exists
- Review.reviewRating reflects editorial review score where appropriate
- Techgenyz is not falsely marked as merchant/seller when purchase occurs on third-party sites
- Offer data is accurate and maintained if included
- ISO currency code is used where applicable
- product name/brand/image/rating visible values match structured data

---

# 24. Brand / Company Listing Gate

Verify:

- entity list uses `<ul><li>` or equivalent intentional list structure
- brand/company card root is not forced to `<article>`
- no Organization JSON-LD emitted per card by Site Master
- links resolve to canonical company/brand URLs
- images/logos use responsive/known-dimension markup where appropriate

---

# 25. Company / Brand Detail Gate

Verify:

- H1 entity name
- visible company/brand information
- related products/news components
- profile/entity structure
- editorial review pages are distinguishable from profile pages
- Rank Math Organization/Brand integration remains authoritative
- no duplicate schema from Site Master

---

# 26. Deals / Crawl Hygiene Gate

Audit:

- Deal CPT visibility
- Deal redirect behavior
- Deal archive
- Deal categories
- Deal tags
- affiliate/redirect-only URLs
- filter taxonomies
- attachment pages
- feeds
- parameters
- legacy rewrites

Do not allow thin/redirect-only utility URLs to become a new indexable crawl ecosystem.

Do not blanket redirect unrelated 404s to homepage.

---

# 27. Sitemap Gate

Rank Math remains sitemap authority.

Verify:

- sitemap caching is not unnecessarily disabled
- only canonical/indexable URLs are present
- no redirects in sitemap
- no 404s in sitemap
- no 5xx URLs in sitemap
- no private/filter utility taxonomies
- no thin Deal URLs if not intended for indexing
- News sitemap behavior remains correct for Techgenyz

Runtime/staging sitemap verification is required where static inspection cannot prove actual output.

---

# 28. Breadcrumb Gate

Verify:

- visible breadcrumbs are correct
- breadcrumb links are canonical
- no duplicate breadcrumb JSON-LD
- Rank Math remains schema authority
- breadcrumb output does not expose utility/redirect URLs

---

# 29. Image / Core Web Vitals Gate

Verify:

- width/height present where known
- `srcset` preserved
- `sizes` appropriate
- primary LCP image not blindly lazy-loaded
- `fetchpriority=high` not assigned excessively
- offscreen/card images lazy-load appropriately
- no obvious CLS caused by image markup
- representative publisher hero images meet intended source-quality guidance
- no unnecessary oversized downloads
- WebP/AVIF delivery remains compatible

---

# 30. Elementor Gate

Verify:

- Elementor widgets register correctly
- Elementor absent: plugin fails gracefully where expected
- Elementor Pro absent: fallback paths remain safe
- legacy widget compatibility where required
- saved templates do not break unexpectedly
- `has_widget_inner_wrapper(): false` only where tested
- CSS selectors remain correct after wrapper optimization
- no accidental duplicate desktop/mobile DOM
- dynamic widgets are not incorrectly cached
- static output caching only where safe
- widget assets are scoped/conditional

---

# 31. Performance Gate

Production release must not introduce or preserve known high-cost behavior without explicit acceptance.

Verify:

- no per-pageview WordPress DB write counter unless explicitly redesigned/accepted
- no per-date AJAX fan-out
- no repeated per-widget inline date script
- no blocking remote API call in ordinary rendering
- external API results cached where appropriate
- conditional module loading
- conditional CSS/JS
- `no_found_rows` where safe
- bounded queries
- no obvious N+1 term/meta query pattern
- no unnecessary Product/Company/Deal bootstrap on The Blissz
- uncached TTFB reviewed where environment allows
- PHP memory reviewed where environment allows

---

# 32. Security Gate

Audit:

- PHP direct-access protection where applicable
- nonce validation
- capability checks
- REST permissions
- input sanitization
- contextual output escaping
- upload handling
- SVG sanitizer
- media deletion ownership/capability controls
- SQL preparation
- filesystem paths/includes
- remote requests
- redirects
- API credentials
- Firebase secrets
- debug output
- admin custom HTML trust boundary

No critical/high unmitigated security defect may remain for production release.

---

# 33. Site Profile Gate

Verify both profiles independently.

## Techgenyz

Expected enabled functionality may include:

- Products
- Companies / Brands
- Comparisons
- Deals if retained
- APIs
- Firebase notifications
- news-specific date behavior

## The Blissz

Verify:

- Product/Company/Deal modules are not unnecessarily loaded
- shared publishing components work
- lifestyle/blog behavior works
- Rank Math BlogPosting/Article selection remains external to Site Master

Staging tests should confirm profile selection is explicit and safe.

---

# 34. Migration / Upgrade Gate

Verify:

- existing Blissz data remains intact
- existing Techgenyz data remains intact
- existing Techgenyz Product data remains intact
- existing Company/Brand data remains intact
- existing Elementor templates are compatible or have an approved migration path
- migration scripts are idempotent
- migrations do not overwrite existing saved values with defaults
- activation does not reset existing settings
- reactivation does not reset existing settings
- plugin replacement/update does not reset existing settings

Pattern:

```text
saved database value exists
→ preserve it

no saved value exists
→ use documented default
```

---

# 35. Validation Gate

Run where supported:

## PHP

- lint all PHP files

## JavaScript

- syntax validation where Node is available
- report NOT RUN when unavailable; do not claim PASS

## Static quality

Where configured:

- PHPCS
- PHPStan
- PHPUnit

## Git/worktree

Where repository context exists:

```text
git diff --check
```

Do not commit/push automatically.

## Static searches

Search for:

- old nested Techgenyz plugin copies
- `itemscope`
- `itemtype`
- `itemprop`
- direct JSON-LD generation
- legacy BlogPosting microdata
- sitemap cache disable filters
- forced SearchAction filters
- debug statements
- hard-coded local paths
- secrets
- test artifacts
- old ZIP files

---

# 36. Runtime / Staging Verification

If staging WordPress environments are available, verify representative:

## The Blissz

- homepage
- category/archive
- single post
- static page
- author page
- search

## Techgenyz

- homepage
- news category/archive
- single NewsArticle
- static page
- author page
- search
- Product listing
- Product detail
- Company/Brand listing
- Company/Brand detail
- Comparison
- Deal behavior
- APIs/notifications where applicable

Verify rendered:

- title/meta/canonical/robots
- Rank Math JSON-LD
- breadcrumbs
- headings
- semantic article boundaries
- image loading
- HTTP status
- asset loading
- JavaScript errors
- PHP logs

If staging/live access is unavailable, clearly separate static PASS results from runtime checks still required.

Never claim a runtime test passed when it was not run.

---

# 37. Content Analytics Production Gate

When Content Analytics is present, verify legacy lifetime totals and the recorded cutover baseline, including that an idempotent upgrade cannot import the baseline twice. Confirm no per-view postmeta update path, no legacy/new double counting, and no tracking when the profile or setting disables it; The Blissz must not track by default.

Verify known-bot and configured staff exclusion, absence of raw IP storage, absence of visitor hashes from dashboards/exports, and no blocking remote geolocation per view. Dashboard, report API and CSV permissions/nonces must work. Validate realtime, top and trending calculations; scheduled aggregation; bounded-retention cleanup; idempotent custom-table upgrades; full-page-cache compatibility; and that the primary response does not block on analytics processing. Collector/aggregation failure must not break frontend rendering.

# 38. Production Verdict

Use:

```text
READY FOR PRODUCTION
```

only when no genuine blocker remains.

Use:

```text
NOT READY FOR PRODUCTION
```

when a genuine blocker exists in:

- security
- data safety
- plugin identity
- SEO/schema
- Rank Math
- semantic rendering
- product/company migration
- Elementor compatibility
- performance
- packaging
- required runtime behavior

Do not reject solely because a non-critical optional staging check is unavailable, but clearly list outstanding checks.

---

# 39. Production Ledger Update Rule

Update the production ledger only AFTER:

1. complete required audit passes
2. production staging copy is prepared
3. ZIP is built
4. actual ZIP is inspected
5. final verdict is `READY FOR PRODUCTION`

If any mandatory gate fails:

```text
DO NOT update the ledger
```

A failed candidate is not a production release.

---

# 40. Mandatory Production Report

Every production build request must report:

```text
Executive Summary
Development Version
Previous Production Version
Candidate Production Version
Plugin Identity
Production ZIP Structure
Data-Safety Audit
Migration/Upgrade Audit
SEO Audit
Rank Math Audit
Schema Audit
Article/Post Semantic Audit
Homepage/Archive Audit
Static Page Audit
Author Audit
Product Listing Audit
Product Detail Audit
Product/Review Schema Integration Audit
Brand/Company Audit
Deals/Crawl Hygiene Audit
Sitemap Audit
Breadcrumb Audit
Image/CWV Audit
Elementor Audit
Performance Audit
Security Audit
Site Profile Audit
Validation Results
Runtime/Staging Verification
Issues Found
Final Verdict
Production ZIP Path
SHA-256 / artifact hash where available
```

For every issue use:

```text
Severity:
Impact:
Recommendation:
Production blocker: YES/NO
```

---

# 41. Production Release Ledger

## Current Record

```text
Last Production Version: NONE
Last Production ZIP: NONE
```

## First Production Rule

When the current record is `NONE`, the first Candidate Production Version is:

```text
1.0.0
```

## Historical Releases

No Site Master production release has been approved yet.

Do not add a historical release entry until the first production package passes all mandatory gates.

---

# 42. Final Principle

The Site Master production process must be:

```text
AUDITABLE
NON-DESTRUCTIVE
VERSION-INDEPENDENT
SEO-SAFE
SCHEMA-SAFE
DATA-SAFE
PACKAGE-SAFE
PERFORMANCE-AWARE
REGRESSION-SAFE
```

The user should only need to say:

```text
Give me production ready zip file
```

and Codex should independently execute the documented production workflow.
