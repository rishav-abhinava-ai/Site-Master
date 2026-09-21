# CODEX_HANDOFF.md — Site Master Implementation Context

Last updated: 2026-09-17

## Version Authority Notice

This file records implementation context, approved technical decisions, migration constraints, and development history.

It does **not** define the authoritative current Development Version.

Current Development Version authority:

```text
SITE_MASTER_DEVELOPMENT_VERSION
```

inside:

```text
site-master/site-master.php
```

It also does **not** define the authoritative Last Production Version.

Production authority:

```text
PRODUCTION_HANDOFF.md
```

Historical versions in this file are context only.

---

# 1. Project Purpose

Site Master is a shared WordPress plugin being developed for:

- **Techgenyz**
- **The Blissz**

The project consolidates future development from:

- Blissz Master v1.1.0
- Techgenyz Master v1.0.5

Techgenyz Master v1.0.2 is historical/reference only.

The project objective is one production-grade plugin with:

- shared semantic publishing components
- site profiles
- Elementor integration
- Rank Math integration
- Techgenyz Product/Company/Brand infrastructure
- migration compatibility
- minimum practical DOM
- high performance
- strong security
- production-safe packaging and release discipline

---

# 2. Current Project State

Initial Site Master development begins at:

```text
0.0.1
```

The first successful production release is reserved as:

```text
1.0.0
```

Development and production version tracks are independent.

Initial production ledger state:

```text
Last Production Version: NONE
```

The production ledger itself lives only in `PRODUCTION_HANDOFF.md`.

---

# 3. Core Architectural Decision

## Rank Math is the sole SEO/schema authority

Site Master must not become a second SEO/schema system.

Rank Math owns:

- Article / NewsArticle / BlogPosting schema
- Product / Review / Organization / Brand schema
- WebPage / WebSite / Organization
- ProfilePage / Person
- AboutPage / ContactPage
- BreadcrumbList
- canonical
- robots
- meta title
- meta description
- Open Graph
- Twitter
- XML sitemap
- News sitemap

Site Master owns:

- visible semantic HTML
- component rendering
- Elementor widgets
- authors
- dates
- categories
- article body
- breadcrumbs HTML
- images
- Product/Company visible content
- site profiles
- performance behavior
- data services
- migration compatibility

Site Master must not output:

```text
itemscope
itemtype
itemprop
Article JSON-LD
NewsArticle JSON-LD
BlogPosting JSON-LD
Product JSON-LD
Review JSON-LD
Organization JSON-LD
BreadcrumbList JSON-LD
```

Custom structured-data enrichment, if required, must be performed through Rank Math integration.

---

# 4. Site Profiles

Site Master uses one shared codebase with two profiles:

```text
techgenyz
blissz
```

## Shared core

Expected shared areas:

- post cards
- article components
- authors
- dates
- breadcrumbs
- media/image rendering
- SVG sanitization
- accessibility helpers
- Elementor integration
- Rank Math bridge
- shared services/renderers/view models

## Techgenyz profile

Expected site-specific modules:

- Products
- Companies / Brands
- Comparisons
- Deals where retained
- API modules where retained
- Firebase notifications where retained
- news-oriented visible date/time behavior
- product/company data services

## The Blissz profile

Expected site-specific behavior:

- lifestyle/blog presentation defaults
- reading-time behavior
- Blissz-specific display defaults
- Products/Companies/Deals disabled unless explicitly enabled later

---

# 5. Source Plugin Baseline

## Blissz Master v1.1.0

Useful implementation references:

- Loop Post Block already outputs semantic `<article>`
- visible author/date rendering
- `<time datetime="">`
- featured image handling
- post content
- related content
- breadcrumbs
- custom avatar support
- RSS helpers
- stronger SVG sanitization

Known migration issue:

- Loop Post Block and Single Post Block output BlogPosting microdata
- this must not be carried into Site Master

Known semantic issue:

- current single-post architecture can place title/meta/image in one `<article>` while actual Post Content renders as a sibling
- Site Master must guarantee one complete article boundary

## Techgenyz Master v1.0.5

Primary Techgenyz migration source.

Useful infrastructure includes:

- Products
- Comparisons
- Companies / Brands
- Deals
- REST APIs
- Firebase notifications
- post views
- geo/IP controls
- post content
- post author
- post date
- post search
- attachment handling
- media/product Elementor widgets

Known issues to address during migration:

- the supplied audited v1.0.5 package contains no nested v1.0.2 plugin copy, but it does contain `.git` metadata
- `.git` metadata and any nested historical plugin copy encountered in a future package must never be shipped
- SVG handling is weaker than Blissz sanitizer
- post views use per-view AJAX/database writes
- date logic can create excessive per-widget script/AJAX behavior
- product hero/card image rendering needs stronger responsive-image handling
- Rank Math sitemap caching is globally disabled in legacy code
- forced SearchAction behavior should not be carried without a requirement
- company ↔ brand relationship implementation is incomplete
- Deals may generate crawl/index bloat if redirect/thin URLs remain publicly indexable

## Techgenyz Master v1.0.2

Historical/reference only.

Do not ship.

---

# 6. Approved Semantic Component Rules

## 6.1 Editorial Post Card

Root:

```html
<article class="sm-post-card">
```

Default image wrapper:

```html
<a class="sm-post-card__media" href="...">
    <img ...>
</a>
```

Do not force `<figure>` around every thumbnail.

Use `<figure>` when:

- image caption is rendered
- image credit is rendered
- media is genuinely treated as an independent figure

Do not place unrelated category badges inside `<figure>`.

Headline hierarchy:

```text
homepage section → H3
archive/search → H2
```

Reusable post card must not casually output H1.

No card-level schema/microdata.

## 6.2 Homepage

Preferred structure:

```text
main
→ stable H1
→ sections with H2
→ editorial cards with H3
```

Do not use a rotating lead-story headline as the page H1 by default.

One responsive DOM.

## 6.3 Archive / Category / Tag / Search

Preferred structure:

```text
main
→ breadcrumb
→ archive header/H1
→ content layout
   → results section
      → Post Cards with H2
      → pagination
   → sidebar aside
```

## 6.4 Single Post / News Article

One primary `<article>` must contain:

```text
header
category
H1
summary/dek
author
published time
updated time
featured image
article body
article footer/topics
```

Sidebar is normally outside.

After-article related stories are normally outside.

Do not use separate Article Start/Article End widgets.

Use a true parent semantic container.

## 6.5 Static Pages

Default:

```text
main
→ breadcrumb
→ header/H1
→ meaningful sections
```

No `<article>` required by default.

## 6.6 Author Page

Default:

```text
main
→ breadcrumb
→ author profile header
→ H1 author name
→ bio
→ H2 "Articles by ..."
→ Post Cards
```

## 6.7 Product Listing

Default:

```text
main
→ breadcrumb/header/H1
→ filters/toolbar if required
→ ul.product-grid
   → li.product-card
→ pagination
→ optional aside
```

Product Card root normally:

```html
<li class="sm-product-card">
```

not `<article>`.

No Product schema emitted by card renderer.

## 6.8 Product Detail

Product/specification pages are entity pages.

Default root may be:

```html
<section class="sm-product" aria-labelledby="product-name">
```

with:

- H1
- brand
- gallery
- summary
- features
- specifications
- rating
- prices/offers

Use `<article>` only for a separate editorial review article.

## 6.9 Brand / Company Listing

Default:

```text
ul.brand-grid
→ li.brand-card
```

Do not output Organization schema for every listing card.

## 6.10 Company / Brand Detail

Entity/profile structure, not `<article>` by default.

Editorial company review may use `<article>`.

---

# 7. Approved Card Families

Site Master should not force one universal semantic root for all content.

Approved families:

| Component | Root | Purpose |
|---|---|---|
| Post Card | `<article>` | news/blog/editorial content |
| Review Card | `<article>` | editorial reviews |
| Product Card | `<li>` | product directory/listing |
| Brand/Company Card | `<li>` | brand/company directory |

Internal presentation primitives may be shared:

- Media
- Title
- Meta
- Badge
- Rating
- Excerpt

---

# 8. Product Schema/Data Direction

Site Master provides visible data and Rank Math integration.

Expected schema direction through Rank Math:

| Content | Schema direction |
|---|---|
| Current factual news | NewsArticle |
| General editorial content | Article |
| Lifestyle/blog post | BlogPosting or Article |
| Technical tutorial | Article |
| TechArticle | optional only; not relied on for Google Article features |
| Product detail | Product |
| Editorial gadget review | Product + Review / Review relationship |
| Company detail | Organization or Brand where appropriate |
| Editorial company review | Review → Organization where appropriate |
| Listing/archive | WebPage / CollectionPage; optional ItemList |

Important:

- editorial score is not automatically AggregateRating
- AggregateRating requires genuine aggregated rating data
- Techgenyz must not be represented as seller/merchant when purchase occurs on third-party sites
- offers should only be represented when accurate and maintained

---

# 9. Product Data Improvements Planned

During Product migration, preserve existing data and add normalized fields where appropriate.

Planned additions include:

- model
- MPN
- GTIN
- manufacturer
- ISO currency code
- availability
- condition
- release date
- official product URL
- seller per offer
- offer currency
- offer availability
- offer validity
- review author
- review date
- pros/cons where useful

Future scope:

```text
ProductGroup
Product variants
```

Variant support is not required for the first production release unless later approved.

---

# 10. Performance Decisions

Approved direction:

- no per-view postmeta update for analytics
- no synchronous analytics multi-counter fan-out or analytics work that unnecessarily blocks the primary page response
- a lightweight append-only write through a dedicated post-render collector may be used when the approved analytics design requires it
- no per-date AJAX fan-out
- no repeated inline script per date widget
- use one shared date formatter when relative/localized dates are required
- use WordPress responsive image APIs
- hero image must not be blindly lazy-loaded
- offscreen/card images may be lazy
- conditionally load CSS/JS
- conditionally load site-specific modules
- use `no_found_rows` for fixed-size non-paginated queries where safe
- avoid repeated metadata/term queries in loops
- cache remote/API results
- do not globally disable Rank Math sitemap caching without demonstrated need

---

# 11. Elementor Decisions

Approved direction:

- preserve existing saved Elementor data
- use compatibility adapters for legacy widget IDs where required
- introduce shared Site Master widgets/components gradually
- use `has_widget_inner_wrapper(): false` where safe and tested
- do not create one tiny widget per label/value
- prefer compact configurable components such as Post Meta
- Theme Builder owns layout where possible
- PHP templates may remain fallback
- dynamic current-content widgets should not be incorrectly cached
- static output caching may be evaluated where safe

---

# 12. Image Decisions

Approved rules:

- use `wp_get_attachment_image()` or equivalent WordPress attachment APIs where possible
- preserve width/height
- preserve responsive `srcset`
- appropriate `sizes`
- meaningful alt
- optional caption/credit
- hero/LCP context distinct from card/offscreen context
- `fetchpriority="high"` only when appropriate
- normal cards/product thumbnails do not need `<figure>` unless caption/credit is shown

Publisher image validation may warn when a featured source image is below 1200px wide.

This is a content/publishing warning only and does not generate Discover schema.

---

# 13. Accessibility Decisions

Planned shared behavior includes:

- skip link to `#main`
- one `<main>` per page
- correct heading hierarchy
- standard crawlable links
- `rel="author"` for author links where appropriate
- visible/valid `<time datetime="">`
- sidebar labeled with `<aside>`
- nav used for actual navigation
- ads labeled clearly
- no duplicate mobile/desktop content trees where avoidable

---

# 14. Crawl / Indexability Decisions

Site Master must not create unnecessary public/indexable URL ecosystems.

Particular review areas:

- Deals
- Deal Categories
- Deal Tags
- filter/attribute taxonomies
- attachment pages
- feeds
- query parameters
- legacy rewrite rules

Do not blanket-redirect unrelated 404s to homepage.

Sitemaps must contain canonical/indexable URLs only.

Rank Math remains responsible for sitemap output.

---

# 15. Security Direction

Approved requirements:

- stronger Blissz SVG sanitizer becomes shared implementation
- nonces/authentication where appropriate
- capability checks
- sanitization
- contextual escaping
- protected media deletion
- protected REST exposure
- no secrets in frontend output
- intentional trust boundary for admin-entered custom HTML/ad code
- no destructive deactivation
- uninstall safety must be explicit and tested

---

# 16. Development Phase Map

Approved current map:

```text
0.0.1  Foundation / governance / source audit / handoff files
0.0.2  Site Master scaffold / profiles / bootstrap / version system
0.0.3  Shared core / Rank Math boundary / SVG / author / dates / breadcrumbs
0.0.4  Low-DOM editorial Post Card + listing components
0.0.5  Modular single-article architecture
0.0.6  Static pages / author pages / sidebar / accessibility
0.0.7  Media / image rendering / Core Web Vitals
0.0.8  The Blissz migration
0.0.9  Techgenyz shared publishing migration
0.1.0  Site Master Content Analytics
0.1.1  TG Products / Companies / Brands / Comparisons
0.1.2  Rank Math Product / Review / Organization bridge
0.1.3  Deals / crawl control / URL hygiene
0.1.4  Performance optimization
0.1.5  Security / production hardening
0.1.6  Automated QA / CI / compatibility
0.1.7  Build tooling / release packaging system
0.1.8  Full UAT on The Blissz + Techgenyz
0.1.9+ UAT fixes / release stabilization if required
```

The first successful production package is:

```text
1.0.0
```

---

# 17. Site Master Content Analytics Architecture

Content Analytics is a shared first-party editorial analytics module reserved at `modules/analytics/`. Techgenyz will enable it during migration and preserve verified lifetime view totals; The Blissz remains disabled until explicitly enabled; unconfigured installations never track. Runtime, tables, collector, dashboard and migrations belong exclusively to planned Development 0.1.0.

The locked design uses a small conditional post-render first-party beacon, narrowly validated ingestion, bounded short-retention events, scheduled/batched hourly/daily/dimension aggregation, UTC storage and WordPress-timezone reporting. It forbids per-view postmeta updates, synchronous counter fan-out, raw IP, permanent visitor profiles, cross-site tracking and blocking per-view geolocation. Daily reader estimates use rotating keyed one-way identifiers and are always labeled estimates.

Legacy cutover requires source re-inspection to verify the exact meta key, immutable one-time baseline import, a recorded cutover, idempotency and protection from double counting. It cannot fabricate historical time series or dimensions. `tgm_count_post_view` remains compatible while consumers depend on it. Reports and aggregate CSV exports are capability protected and never expose hashes or raw visitor-event dumps. The complete approved scope is in `docs/CONTENT-ANALYTICS-SPEC.md`.

# 18. Development History

## Development Version 0.0.1 — Initial Foundation

Status:

```text
COMPLETED — 2026-09-14
```

Purpose:

- establish Site Master governance
- establish development/production separation
- audit source plugins
- create migration map
- create compatibility contract
- create the three handoff files
- prevent accidental implementation before architecture and persistence contracts are understood

Approved starting rules:

- development starts at `0.0.1`
- first production release is `1.0.0`
- Rank Math is sole SEO/schema authority
- one shared plugin with Techgenyz and Blissz profiles
- current persistent identifiers must be preserved unless an explicit migration approves a change
- Techgenyz Master v1.0.5 is primary Techgenyz migration source
- Techgenyz Master v1.0.2 is historical/reference only
- Blissz Master v1.1.0 is the Blissz migration source
- production packaging must use a clean temporary copy

Implemented:

- verified the supplied headers for Blissz Master 1.1.0, Techgenyz Master 1.0.5 and Techgenyz Master 1.0.2
- audited publishing, Elementor, SEO/Rank Math, media, Products, Comparisons, Companies/Brands, Deals, REST, Firebase, IP, attachment, view, performance and security surfaces
- created `docs/SOURCE-AUDIT.md` and completed the migration map and compatibility contract
- confirmed the supplied 1.0.5 archive has no nested 1.0.2 plugin, but contains forbidden `.git` metadata

Architecture / Decisions:

- existing shared-core plus profile-gated module boundaries fit the audit
- 1.0.5 remains the Techgenyz baseline; 1.0.2 is comparison-only
- legacy microdata, forced SearchAction, global sitemap-cache disabling, per-view writes and per-widget date AJAX are not approved migration behavior

Compatibility Preserved:

- no runtime source or legacy ZIP was changed
- no persistent identifier was renamed and no data migration was performed
- adapters and fixtures required by later phases are documented

Files / Modules Materially Affected:

- `docs/SOURCE-AUDIT.md`
- `docs/MIGRATION-MAP.md`
- `docs/COMPATIBILITY-CONTRACT.md`
- `CODEX_HANDOFF.md`

Validation:

- PHP lint: 17/17 current Site Master files passed
- legacy PHP lint: Blissz 33/33, Techgenyz 1.0.5 44/44, Techgenyz 1.0.2 43/43 passed
- executable Site Master hygiene scan found no schema/microdata, JSON-LD, legacy path, or debug matches
- nested artifact scan found no legacy plugin or ZIP inside Site Master
- Git diff validation was unavailable because this directory is not a Git worktree; files were reviewed directly

Known Limitations / Follow-up:

- runtime WordPress, Elementor, Rank Math and production-data validation was outside this foundation audit
- complete saved-control and representative production-data fixtures must be captured in their implementation phases
- decisions in `docs/SOURCE-AUDIT.md` remain intentionally unresolved
- next authorized phase is Development 0.0.2; it has not been started

---

## Development Version 0.0.2 — Scaffold / Profiles / Bootstrap / Version System

Date: 2026-09-14

Purpose:

- complete the safe shared bootstrap and explicit profile foundation
- reserve and document Site Master Content Analytics without implementing its runtime

Implemented:

- explicit `techgenyz`, `blissz`, and safe `unconfigured` profile resolution with constant-over-option priority
- declarative, non-booting module registry including reserved Analytics
- optional Elementor, Elementor Pro and Rank Math detection
- capability/nonce-protected Site Master setup and status screen with version/channel, profile, dependency and module reporting
- durable Content Analytics specification and placeholder module boundary
- corrected stale v1.0.5 nested-copy statements and synchronized development/release maps

Architecture / Decisions:

- unconfigured or invalid profiles never default to Techgenyz and load no site-specific module
- every later-phase module remains disabled in 0.0.2
- Analytics runtime remains planned for 0.1.0; The Blissz tracking is disabled by default
- analytics uses dedicated storage and privacy-safe aggregation, never per-view postmeta updates

Compatibility Preserved:

- existing `site_master_profile` option and deployment constant precedence are retained
- legacy view totals and `tgm_count_post_view` are protected for the 0.1.0 migration design
- activation, deactivation and uninstall remain non-destructive

Files / Modules Materially Affected:

- `site-master.php`, `src/Core/`, `src/Profiles/`, `src/Admin/`, `tests/`
- `modules/analytics/`
- `AGENTS.md`, `CODEX_HANDOFF.md`, `PRODUCTION_HANDOFF.md`, `README.md`
- `docs/PROJECT_OVERVIEW.md`, `docs/MIGRATION-MAP.md`, `docs/COMPATIBILITY-CONTRACT.md`, `docs/CONTENT-ANALYTICS-SPEC.md`

Validation:

- PHP lint: 22/22 files passed before version advancement
- scaffold smoke suite: constant/option/unconfigured/invalid resolution, dependency absence/presence and reserved registry passed
- admin security: capability and nonce enforcement verified statically
- executable hygiene: no direct schema/microdata, forced Rank Math filters, analytics tables/endpoints/scripts or per-view analytics postmeta code
- frontend neutrality: admin registration is gated by `is_admin()`; no later module is booted
- Git diff check unavailable because the plugin directory is not a Git worktree

Post-completion verification correction:

- the profile-save handler now enforces `SITE_MASTER_PROFILE` as a deployment lock and leaves the saved option untouched
- the status screen reports plugin, development/runtime versions, channel, active profile, profile source, WordPress/PHP versions and optional-dependency availability
- module registry support is profile-aware: Techgenyz supports all reserved modules, Blissz supports only shared Analytics, and unconfigured supports none; every runtime `enabled` value remains false
- smoke coverage now includes profile-source reporting, all three module-support matrices and the deployment-lock regression with a preserved conflicting saved option

Known Limitations / Follow-up:

- no live WordPress/Elementor/Rank Math browser smoke environment was exercised
- the status page intentionally provides profile setup only; analytics settings/dashboard are deferred to 0.1.0
- next authorized phase is 0.0.3; planned Analytics remains 0.1.0

## Development Version 0.0.3 — Shared Core / Rank Math Boundary / SVG / Author / Dates / Breadcrumbs / RSS

Date: 2026-09-14

Purpose:

- provide production-oriented shared services for both site profiles without beginning widget or template migration

Implemented:

- centralized Rank Math availability and breadcrumb-output access with graceful no-op behavior
- reusable visible breadcrumb renderer that avoids nested navigation and strips unsupported attributes
- fail-closed SVG/SVGZ upload integration using the declared `enshrined/svg-sanitize` dependency
- shared `profile_picture` author service, secure profile mutation, WordPress image/avatar fallback and relationship-only removal
- server-rendered published/modified date data, WordPress-timezone formatting, ISO-8601 values and semantic `<time>` output
- one shared RSS featured-image filter implementation with feed-only and duplicate-output guards
- bootstrap registration of shared services without frontend assets or legacy widget/AJAX registration

Architecture / Decisions:

- Rank Math remains the sole SEO/schema owner; no SearchAction or sitemap-cache override is registered
- SVG MIME acceptance is disabled unless the trusted sanitizer is autoloadable and the user has normal upload plus elevated media-management capability
- Composer owns the sanitizer dependency; Site Master loads one root vendor autoloader when present and never falls back to MIME-only SVG acceptance
- exact dates require no JavaScript; legacy date AJAX actions remain documented compatibility contracts for later widget adapters
- both legacy RSS implementations were materially equivalent and are consolidated as shared behavior

Compatibility Preserved:

- `profile_picture`, existing attachment IDs, Rank Math data, saved Elementor data/widget IDs, and legacy date action contracts remain unchanged
- avatar removal deletes only the user-meta relationship, never the Media Library attachment
- no posts, users, media, SEO records or production data were migrated or rewritten

Files / Modules Materially Affected:

- `site-master.php`, `composer.json`, `README.md`, `docs/PROJECT_OVERVIEW.md`
- `src/Core/Plugin.php`, `src/SEO/RankMathBridge.php`, `src/Components/Breadcrumbs/BreadcrumbRenderer.php`
- `src/Security/SvgSanitizer.php`, `src/Content/AuthorData.php`, `src/Support/DateFormatter.php`, `src/Content/RssFeaturedImage.php`
- `tests/shared-core-smoke.php`, `tests/README.md`, `CODEX_HANDOFF.md`

Validation:

- PHP lint: 25/25 files passed before version advancement
- existing scaffold suite: 14/14 cases passed
- shared-core suite: Rank Math unavailable/mock, breadcrumb wrapper behavior, SVG unavailable/mock security payloads, author security/fallback/removal, date/time and RSS behavior passed
- Composer JSON structure parsed successfully; Composer CLI and dependency installation were unavailable
- executable hygiene: no Site Master schema/microdata, forbidden Rank Math filters, legacy date AJAX, view-count update, remote request, or frontend asset registration
- mutation review limited writes to protected admin profile settings, authorized author-meta relationship changes, and in-place temporary SVG upload sanitization

Known Limitations / Follow-up:

- the trusted SVG package is declared but not installed in this workspace; SVG upload therefore remains securely disabled until production dependencies are installed
- live WordPress, Rank Math breadcrumb, Media Library upload, profile-admin, feed and browser tests were not run; no `debug.log` was available
- no formal PHPCS, PHPStan or PHPUnit configuration exists yet
- next authorized phase is 0.0.4; it has not been started

## Development Version 0.0.4 — Low-DOM Editorial Post Card + Listing Components

Date: 2026-09-14

Purpose:

- establish one shared editorial card and listing foundation for Techgenyz and The Blissz
- make the approved homepage and archive heading hierarchies possible without migrating templates or legacy Elementor widget IDs

Implemented:

- normalized `PostCardViewModel` boundary for post identity, permalink, title, image, primary category, excerpt, author and published/modified dates
- one semantic `PostCardRenderer` rooted at `<article class="sm-post-card">` with optional image, caption, category, excerpt, author, avatar and date output
- safe homepage H3 and archive/category/tag/search H2 defaults with an H2–H6 allow-list and context-aware invalid-value fallback
- WordPress responsive attachment rendering with lazy card-image defaults, no blanket high fetch priority, no broken/empty media wrapper and conditional figure/figcaption output
- compatible Rank Math primary-category lookup, Yoast fallback and assigned WordPress-category fallback without metadata writes
- manual-first, shortcode-free, plain-text bounded excerpts without arbitrary content rendering
- bounded `EditorialQuery` service with allow-listed post type, taxonomy, ordering, status, IDs, author, pagination and sticky handling
- direct sibling-card listing output, meaningful section/heading behavior, empty-result suppression and crawlable pagination navigation
- render-from-normalized-data behavior that does not call `setup_postdata()` or mutate the global query/post

Architecture / Decisions:

- editorial Post Cards and future editorial review cards may share the `<article>` renderer; Product and Brand/Company cards must use their separate entity/list architecture
- layout differences belong to options/classes/CSS rather than duplicated desktop/mobile markup or separate site renderers
- Elementor registration is intentionally deferred because the shared architecture can be validated independently; legacy IDs remain reserved for Development 0.0.8 adapters
- the initial editorial query allow-list contains standard `post`; future editorial post types must be explicitly supplied by trusted integration code
- advanced hero/LCP media policy remains Development 0.0.7; 0.0.4 only adds the safe offscreen/card attachment path

Compatibility Preserved:

- `rank_math_primary_category`, `_yoast_wpseo_primary_category`, `profile_picture`, author/date services, posts, terms, users, media, URLs and Rank Math metadata are read without mutation
- no legacy Elementor widget/control ID was registered, removed or changed
- no migration, persistent write, global query mutation, analytics runtime, date AJAX action or frontend asset was introduced
- Rank Math remains the sole SEO/schema owner; Site Master card/listing output contains no schema or SEO metadata

Files / Modules Materially Affected:

- `src/Components/PostCard/PostCardViewModel.php`, `src/Components/PostCard/PostCardRenderer.php`
- `src/Components/Listing/PostListRenderer.php`, `src/Components/Listing/PaginationRenderer.php`
- `src/Content/PostData.php`, `src/Content/EditorialQuery.php`, `src/Media/ImageRenderer.php`
- `src/Components/README.md`, `src/Elementor/ElementorIntegration.php`
- `tests/post-card-listing-smoke.php`, `tests/README.md`, `tests/scaffold-smoke.php`
- `site-master.php`, `README.md`, `docs/PROJECT_OVERVIEW.md`, `CODEX_HANDOFF.md`

Validation:

- PHP lint: 31/31 Site Master PHP files passed before version advancement
- scaffold suite: 14/14 cases passed
- shared-core suite: 8/8 groups passed
- Post Card/listing suite passed view-model, heading, image/figure, category, author/date, excerpt, escaping, listing, query, pagination and global-state assertions
- `git diff --check` passed; line-ending conversion warnings were informational only
- executable hygiene scan found no schema/microdata, JSON-LD, prohibited Rank Math override, per-view write, analytics collector, legacy date AJAX, global-post setup, frontend asset or inline-script pattern
- performance was reviewed statically: queries are bounded, fixed collections disable found rows, paginated collections preserve them, and normal WordPress post/meta/term caches remain enabled

Known Limitations / Follow-up:

- live WordPress rendering could not run because the available CLI PHP lacks the MySQL extension; browser DOM, query-count profiling, both-profile output and WordPress log checks are therefore NOT RUN
- no PHPCS, PHPStan or PHPUnit configuration exists; the dedicated PHP smoke/render suites provide current automated coverage
- no frontend CSS is included; layout styling and Elementor adapters remain intentionally deferred
- advanced image/LCP work remains Development 0.0.7
- next authorized phase is 0.0.5; it has not been started

## Development Version 0.0.5 — Modular Single-Article Architecture

Date: 2026-09-14

Purpose:

- establish one shared complete editorial-article composition for Techgenyz news, general editorial content and The Blissz lifestyle/blog posts
- prevent the legacy structural failure where the article header closed before separately rendered post content

Implemented:

- normalized `SingleArticleViewModel` for post identity/type, permalink, headline, manual summary, primary category, shared author data, published/meaningfully modified dates, featured image, raw article content and topics
- coherent header renderer with one fixed H1, optional visible category/summary, shared author/avatar output and compact published/updated metadata
- hero renderer using the shared image boundary with responsive WordPress attachment output, non-linked default, optional real caption and a distinct eager hero context without blanket high fetch priority
- article-body renderer that invokes the normal `the_content` boundary once and leaves Gutenberg, approved shortcodes, embeds and internal content markup to WordPress
- optional topics footer using standard crawlable tag links and no empty footer when topics are unavailable/disabled
- `SingleArticleRenderer` that composes header, hero, body and footer inside one real `<article class="sm-article">`
- documented 60-second meaningful-modification threshold, preventing effectively identical timestamps from producing an Updated label
- non-invasive Elementor article-container convention helper requiring a native Container with HTML Tag `article`

Architecture / Decisions:

- the PHP renderer is the complete semantic fallback/test/future-rendering boundary; Elementor Theme Builder remains preferred for page layout
- Elementor components and legacy widget adapters are intentionally deferred because the complete PHP composition proves semantics without introducing saved-template risk
- no Article Start or Article End widget/class is permitted; Site Master does not rewrite arbitrary Elementor output or force nesting with buffering/regular expressions
- breadcrumbs, after-article related stories and the site sidebar remain outside the primary article; their full composition belongs to later authorized phases
- visible summaries use the manual WordPress excerpt only and never consume Rank Math descriptions
- Product entities, company profiles and static pages do not use this editorial article renderer

Compatibility Preserved:

- shared `AuthorData`, `DateFormatter`, `PostData` primary-category resolution and `ImageRenderer` are reused rather than duplicated
- post/content/image/category/tag/author IDs and relationships, `profile_picture`, Rank Math metadata, primary-category metadata, URLs and saved Elementor data remain unchanged
- normal body content is passed through the established WordPress content filter exactly once by the renderer
- no migration, persistent write, legacy widget-ID registration, analytics runtime, post-view increment or frontend asset was introduced
- Rank Math remains sole schema/SEO owner; Site Master article output contains no schema or SEO metadata

Files / Modules Materially Affected:

- `src/Components/Article/SingleArticleViewModel.php`, `SingleArticleRenderer.php`
- `src/Components/Article/ArticleHeaderRenderer.php`, `ArticleHeroRenderer.php`, `ArticleBodyRenderer.php`, `ArticleFooterRenderer.php`
- `src/Elementor/ArticleContainerConvention.php`, `src/Elementor/ElementorIntegration.php`
- `src/Media/ImageRenderer.php`, `src/Support/DateFormatter.php`, `src/Components/README.md`
- `tests/single-article-smoke.php`, `tests/README.md`, `tests/scaffold-smoke.php`
- `site-master.php`, `README.md`, `docs/PROJECT_OVERVIEW.md`, `CODEX_HANDOFF.md`

Validation:

- PHP lint: 39/39 Site Master PHP files passed before version advancement
- scaffold suite: 14/14 cases passed
- shared-core suite: 8/8 groups passed
- Post Card/listing regression suite passed
- single-article suite passed parent-article/H1 structure, old split-body regression, header options, shared author/category/date data, meaningful modified threshold, responsive unlinked hero, caption/absence, single-pass content filtering, topics/absence, escaping and Article Start/End prohibition assertions
- `git diff --check` passed with informational working-copy line-ending notices only
- executable hygiene scan found no schema/microdata, JSON-LD, custom canonical/robots, prohibited Rank Math overrides, per-view write, legacy date AJAX, Article Start/End class, inline script or frontend asset registration
- static performance review found no secondary article query, global query/post mutation, remote request or new asset/runtime overhead

Known Limitations / Follow-up:

- live WordPress rendering remains NOT RUN because the available CLI PHP lacks the MySQL extension; actual DOM, Rank Math graph comparison, both-profile output and WordPress log review remain unavailable
- no PHPCS, PHPStan or PHPUnit configuration is available; dedicated PHP smoke/semantic suites provide current automated coverage
- no new Elementor widgets or legacy adapters exist yet; templates must later follow the documented native article-container convention
- related-story engine, sidebar/page layout and static-page architecture remain intentionally outside this phase
- advanced hero/LCP policy remains Development 0.0.7
- next authorized phase is 0.0.6; it has not been started

## Development Version 0.0.6 — Static Pages / Author Pages / Sidebar / Accessibility

Date: 2026-09-14

Purpose:

- establish shared page-level semantics, author archives, supplementary sidebar boundaries and accessibility/navigation foundations for both profiles
- preserve the locked distinction between editorial articles and ordinary static pages

Implemented:

- one `PageShellRenderer` producing a single `main#main`, breadcrumb-first placement and optional sibling content/sidebar layout
- explicit opt-in `SkipLinkRenderer` targeting `#main`, allowing Theme Builder or fallback shells to add one link only when the active theme does not already provide it
- `SidebarRenderer` with an accessible label, clean empty/invalid omission and rejection of nested main landmarks
- normalized `StaticPageViewModel` and low-DOM `StaticPageRenderer` with one fixed H1, optional shared responsive image and single-pass normal WordPress content processing without an article root
- normalized public-only `AuthorPageViewModel` sourced entirely through `AuthorData`
- shared author-page composition with decorative adjacent avatar, sanitized public biography, H1 profile identity and H2 post-list section
- author queries through `EditorialQuery`, Post Cards through the existing card model/renderer, H3 card titles, and existing listing/pagination output
- valid configurable author empty state without an empty Post List wrapper
- safe additional listing classes so author pages can reuse `PostListRenderer` while exposing a stable `sm-author-posts` component class

Architecture / Decisions:

- the page shell rejects primary content containing another main element or duplicate `id="main"`; it never nests main landmarks
- skip-link emission is helper/renderer-driven rather than globally injected, preventing duplication with compliant themes without fragile DOM detection
- the opt-in skip link remains a normal visible/focusable anchor; no global focus CSS or outline suppression is introduced
- ordinary static pages use header/H1/content semantics and never inherit the editorial `<article>` renderer by default
- author hierarchy is H1 author name → H2 Articles by Author → H3 Post Card titles
- sidebars remain supplementary sibling asides; after-article related content and in-article supplementary content remain separate concepts
- Elementor registration remains intentionally deferred; Theme Builder owns page layout and may consume these shared boundaries later

Compatibility Preserved:

- `AuthorData`, `profile_picture`, `EditorialQuery`, Post Card/listing/pagination, breadcrumbs and shared image/content boundaries are reused without parallel implementations
- posts, pages, users, terms, media, URLs, Rank Math metadata, primary-category data and saved Elementor identifiers/data remain unchanged
- sidebar/page/author rendering introduces no migration, persistent write, legacy widget registration, global template interception or global frontend hook
- Site Master emits no Person/ProfilePage/WebPage/Breadcrumb/Article schema or SEO metadata; Rank Math remains authoritative
- 0.0.3 shared-core, 0.0.4 listing and 0.0.5 complete-article behavior remain regression-covered

Files / Modules Materially Affected:

- `src/Components/Accessibility/SkipLinkRenderer.php`
- `src/Components/Layout/PageShellRenderer.php`, `SidebarRenderer.php`
- `src/Components/Page/StaticPageViewModel.php`, `StaticPageRenderer.php`
- `src/Components/Author/AuthorPageViewModel.php`, `AuthorPageRenderer.php`
- `src/Components/Listing/PostListRenderer.php`, `src/Components/README.md`
- `src/Elementor/ElementorIntegration.php`, `src/Core/Plugin.php`
- `tests/page-accessibility-smoke.php`, `tests/README.md`, `tests/scaffold-smoke.php`
- `site-master.php`, `README.md`, `docs/PROJECT_OVERVIEW.md`, `CODEX_HANDOFF.md`

Validation:

- PHP lint: 47/47 Site Master PHP files passed before version advancement
- scaffold suite: 14/14 cases passed
- shared-core suite: 8/8 groups passed
- Post Card/listing and single-article regression suites passed
- 0.0.6 page/accessibility suite passed static page main/H1/no-article, breadcrumb order, one-pass content, optional image/empty wrappers, author H1/H2/H3 hierarchy, public bio/avatar, author query, pagination, empty archive, skip-link target and sibling article/sidebar assertions
- `git diff --check` passed with informational working-copy line-ending notices only
- executable hygiene scan found no schema/microdata, JSON-LD, custom canonical/robots, prohibited Rank Math override, per-view write, Article Start/End, inline script, JavaScript navigation or frontend asset registration
- static accessibility/DOM review confirmed crawlable links, one responsive tree, native landmarks, restrained ARIA and no nested interactive controls introduced by these renderers

Known Limitations / Follow-up:

- live WordPress and browser accessibility testing remain NOT RUN because the available CLI PHP lacks the MySQL extension and no usable runtime path is available
- actual keyboard/focus appearance depends on the active theme when it elects to style the visible opt-in skip-link class; Site Master adds no global theme override
- Rank Math runtime graph inspection, both-profile rendered output and WordPress log review remain unavailable
- no PHPCS, PHPStan or PHPUnit configuration is available; dedicated smoke/semantic suites provide current automated coverage
- no Elementor widgets or legacy adapters are registered yet
- next authorized phase is 0.0.7; it has not been started

# 19. How Future Entries Must Be Added

After every successful development phase, append a new section:

```md
## Development Version X.Y.Z — Short Name

Date:

Purpose:

Implemented:
- ...

Architecture / Decisions:
- ...

Compatibility Preserved:
- ...

Files / Modules Materially Affected:
- ...

Validation:
- PHP lint:
- PHPCS:
- PHPStan:
- PHPUnit:
- render tests:
- regression checks:

Known Limitations / Follow-up:
- ...
```

Do not remove historical entries merely because architecture evolves.

If a decision is superseded, mark it as superseded and state the new approved decision.

---

# 20. Current Locked Decisions Summary

The following are currently considered locked unless explicitly changed:

1. Plugin name is Site Master.
2. Shared codebase for Techgenyz + The Blissz.
3. Site profiles are explicit.
4. Development starts at 0.0.1.
5. First production is 1.0.0.
6. Development and production versions are independent.
7. Rank Math is sole schema/SEO authority.
8. Site Master emits no Article/Product/Organization JSON-LD.
9. Site Master emits no Article/Product microdata.
10. Single post has one complete `<article>`.
11. Sidebar normally sits outside the primary article.
12. Related stories normally sit outside the primary article.
13. Post Card root is `<article>`.
14. Product Card root is normally `<li>`.
15. Brand/Company Card root is normally `<li>`.
16. Ordinary card thumbnails do not require `<figure>`.
17. Products share one architecture across gadget categories.
18. Existing persistent identifiers and data are preserved.
19. Stronger SVG sanitization is shared.
20. Production packaging never modifies the working development source merely to assign a production release version.


## Development Version 0.0.7 � Media / Image Rendering / Core Web Vitals

Date: 2026-09-15

Purpose: Establish one safe shared attachment-image policy while preserving approved semantic components.

Implemented:
- Controlled semantic contexts, independent loading/priority intent, normalized allow-listed options and validated registered image sizes/dimension pairs.
- Explicit high priority forces eager; no default first-card or blanket hero high priority. WordPress retains responsive srcset, intrinsic dimensions and decoding policy.
- Cards/articles/static pages forward layout-specific sizes/loading/priority. Custom avatars and RSS reuse the shared renderer; avatars cannot receive high priority and feed hints are omitted.
- Attachment alt metadata, including empty alt, replaces the previously fabricated post-title fallback without writing metadata.

Architecture / Decisions:
- No custom preload, image proxy, conversion, remote render request, CSS crop policy, JavaScript loader or global optimization filter.
- Layout determines viewport knowledge, sizes and at most one explicit high-priority candidate. Auto leaves optimization to core.
- Read-only legacy ZIP inspection found tbm_profile_picture in Blissz versus profile_picture in Techgenyz; document the discrepancy for fixture-based Phase 0.0.8 compatibility work. No migration in this phase.

Compatibility Preserved:
- Existing attachment relationships, IDs, stored alt/captions, profile_picture, avatar fallback, feeds, URLs, Elementor data and Rank Math ownership.
- Editorial article/H1 boundaries, card links/headings, hero non-linking, figure/caption semantics and page/author/sidebar composition.
- SVG sanitizer dependency and fail-closed behavior unchanged. Production ledger unchanged; no ZIP or Git mutation.

Files / Modules Materially Affected:
- src/Media/ImageRenderer.php; PostCard renderer/view model; Article hero/renderer/view model; StaticPage renderer; AuthorData; RssFeaturedImage.
- tests/media-cwv-smoke.php (new), shared-core/scaffold suites and tests/README.md.
- site-master.php, src/Core/Plugin.php, README.md, docs/PROJECT_OVERVIEW.md, docs/MEDIA-POLICY.md (new), CODEX_HANDOFF.md.

Validation:
- PHP 8.2.29: all 48 PHP files linted, zero syntax failures.
- Scaffold, shared-core, post-card/listing, single-article, page/accessibility and media/CWV suites passed.
- Media DOM/stub assertions cover dimensions, responsive attributes, size validation, alt, unsafe options, priority conflicts, multi-card default priority, figures, static-page empty media, avatars and feed duplication.
- Executable hygiene scan and static review: no schema/social output, remote render fetch, global media filters, frontend assets, analytics or per-view postmeta writes introduced.
- git diff --check passed; Git may emit its configured LF/CRLF normalization warning.

Known Limitations / Follow-up:
- Composer CLI unavailable; dependency installation/Composer validation NOT RUN. Actual sanitizer unavailable; SVG fail-closed smoke passed, positive sanitizer suite uses a test double.
- Live WordPress, both-profile browser DOM/network output, Lighthouse/CWV measurement and runtime log correlation NOT RUN: available PHP has no mysqli/pdo_mysql and renderers are not yet connected to site layouts. No performance scores claimed.
- No PHPCS/PHPStan/PHPUnit configuration; dedicated suites supply static coverage.
- Confirm Blissz tbm_profile_picture and saved Elementor fixtures during Phase 0.0.8. Existing compatibility/source-audit documents were not rewritten.
- Next phase is 0.0.8 � The Blissz Migration; not started.


Post-completion verification correction (2026-09-16):
- Linked Post Card media with an empty attachment alt now receives a safely escaped accessible link label derived from the normalized article headline.
- Attachment alt behavior itself remains unchanged; meaningful image alt text does not receive a duplicate link label, and the same fallback applies inside caption figures.


Post-completion compatibility-contract correction (2026-09-16):
- Legacy source re-inspection confirmed Blissz author media uses `tbm_profile_picture`, while Techgenyz uses `profile_picture`.
- `COMPATIBILITY-CONTRACT.md`, `MIGRATION-MAP.md` and the durable media policy were corrected; both keys and referenced attachment IDs are protected persistent identifiers.
- No user meta or attachment data was migrated or rewritten, and the current `AuthorData` runtime remains unchanged at 0.0.7.
- The Blissz compatibility adapter, including evidence-based read precedence and write behavior, remains reserved for Development 0.0.8.

## Development Version 0.0.8 — The Blissz Migration

Date: 2026-09-17

Purpose: Replace the future runtime dependency on Blissz Master with a Blissz-profile compatibility layer that resolves its persisted Elementor widget/control identifiers through Site Master's shared components.

Implemented:
- Added a profile-gated Elementor category and adapters for all eight source-derived Blissz widget IDs: breadcrumb, date, author, content, search form, loop post, loop category and single-post fragment.
- Added a durable fixture containing exact source paths, hashes, sections, 408 direct control IDs, group-control names, representative settings and per-control compatibility classification.
- Reused shared breadcrumb, date, author, content, Post Card, article and image boundaries instead of copying legacy implementations.
- Added deterministic Blissz avatar compatibility: read `tbm_profile_picture` first, fall back to `profile_picture`, and write only the legacy Blissz key without copying or synchronizing data.
- Preserved the source search widget's actual GET-form behavior and current-post/current-term loop behavior; no invented query system was added.

Architecture / Decisions:
- Techgenyz and unconfigured profiles register no Blissz category, widgets or avatar-key filters.
- The single-post widget emits a header/media fragment and relies on the native Elementor parent container configured as `article`; the content widget emits the body. This avoids invalid cross-widget tags while retaining one article and one H1 when composed correctly.
- Saved single-post H1 remains the article H1; a saved loop-card H1 is normalized to H3 to preserve the reusable-card heading contract.
- Direct legacy control IDs remain recognized. Existing compiled Elementor CSS and retained legacy classes provide the style compatibility path; live CSS regeneration remains unverified.
- Alternate-template, inline related-post and advertisement settings are recognized but inactive. `enable_schema` is recognized and ignored. Legacy AJAX date fan-out, scripts, schema/microdata and Rank Math overrides are retired.

Compatibility Preserved:
- Existing posts, users, terms, attachments, URLs, Elementor widget/control IDs, saved Elementor data, both avatar keys, category metadata and Rank Math data are not migrated or rewritten.
- Attachment rendering, alt policy, responsive attributes, author fallback, one-pass content filtering, standard links, card semantics and article H1 behavior continue through shared components.
- No global template replacement, CSS/JavaScript asset, analytics, schema, microdata, JSON-LD or production-data migration was introduced.

Files / Modules Materially Affected:
- `src/Elementor/Compatibility/Blissz/*`, `src/Elementor/ElementorIntegration.php`, `src/Core/Plugin.php`, `src/Content/AuthorData.php`.
- `tests/blissz-compatibility-smoke.php`, `tests/fixtures/blissz-elementor-controls.json`, `tests/README.md`.
- `docs/BLISSZ-COMPATIBILITY.md`, `docs/PROJECT_OVERVIEW.md`, `README.md`, `site-master.php`, `CODEX_HANDOFF.md`.

Validation:
- PHP 8.2.29: all 60 Site Master PHP files linted with zero failures before version advancement.
- Scaffold, shared-core, Post Card/listing, single-article, page/accessibility, media/CWV and Blissz compatibility suites passed.
- Compatibility coverage verifies Blissz-only registration, all eight widget IDs, exact fixture/runtime control inventory, representative saved settings, avatar precedence/coexistence/write behavior, one-pass content, native article composition, no AJAX endpoint and no schema/microdata output.
- Fixture audit: 8 widgets, 408 unique direct control IDs, complete per-control classifications and valid source hashes.
- `git diff --check` passed; Git emitted only configured LF/CRLF normalization notices.

Known Limitations / Follow-up:
- Local MySQL was unavailable, so live WordPress/Elementor editor loading, actual production `_elementor_data`, category/user metadata fixtures, responsive browser comparison, Elementor CSS regeneration and runtime log review were NOT RUN. Source-derived synthetic fixtures are identified as such.
- Existing compiled Elementor CSS is expected to remain applicable through recognized settings, normal wrappers and retained classes; regenerated style-control output is not claimed as verified.
- Alternate-template, inline related-post and advertisement execution remains deferred rather than duplicating unsafe legacy behavior.
- Composer, PHPCS, PHPStan and PHPUnit configurations are unavailable; dedicated executable smoke suites provide current automated coverage.
- Production ledger remains `NONE`; no production ZIP or Git mutation was performed. Next authorized phase is 0.0.9 and has not been started.

Post-completion verification correction (2026-09-17 — Elementor compatibility):
- Replaced blanket hidden placeholders with a structured source-derived control registry: 408 direct controls, 178 responsive controls, 65 confirmed top-level group controls and 263 translated selector-bearing style controls.
- Responsive and group controls now use Elementor's responsive/group registration paths; persistence-only controls alone fall back to hidden registration. Selector families target current adapter/shared DOM rather than restoring legacy wrapper trees.
- Added explicit `SUPPORTED`, `NORMALIZED`, `INTENTIONALLY_INACTIVE` and `PERSISTENCE_ONLY` treatment for every direct control. Alternate templates, related insertion, advertisements and legacy schema remain inactive with saved values untouched.
- Loop Post now respects title visibility/linking, bounded date formats, content-derived reading time and crawlable read-more button settings. Single Post keeps its canonical H1 while mapping date, reading-time and optional button behavior.
- Search input IDs are deterministic per Elementor widget instance, and labels reference the matching unique field.
- Reconciled durable avatar documentation with runtime policy: Blissz reads `tbm_profile_picture` first, falls back to `profile_picture`, writes/removes only `tbm_profile_picture`, performs no synchronization and never deletes attachments.
- Static registration/selector behavior is verified with narrow Elementor doubles. Live Elementor CSS regeneration, production `_elementor_data`, browser comparison and runtime log review remain NOT RUN because the local WordPress database was unavailable.
- Development and plugin versions remain 0.0.8; channel remains development; production ledger remains NONE; no ZIP or Git mutation was performed.

## Development Version 0.0.9 — Techgenyz Shared Publishing Migration

Date: 2026-09-17

Purpose: Resolve the persisted Techgenyz 1.0.5 editorial Elementor widget/control contracts through profile-gated adapters and shared Site Master publishing services without starting product, platform or analytics migrations.

Implemented:
- Inventoried and registered the five source-confirmed editorial widget IDs: `post_terms`, `tgm-date-widget`, `tgm-post-author-widget`, `tgm-post-content` and `tgm_post_search`.
- Added a source-derived registry/fixture with 100 direct controls, 39 responsive controls, 21 group controls and 61 selector-bearing style controls. Each direct control has explicit compatibility status and source-definition evidence.
- Added Techgenyz-only adapters for editorial terms, server-rendered published dates, author display/media, one-pass post content and accessible native search.
- Translated selector families to current adapter markup and retained normal Elementor wrappers. Responsive and group controls use their corresponding Elementor registration methods.
- Preserved `profile_picture` as the Techgenyz author-media key without Blissz precedence, synchronization, bulk migration or attachment deletion.

Architecture / Decisions:
- Techgenyz registration is explicit-profile only; Blissz retains its corrected 0.0.8 runtime; unconfigured sites register neither adapter set.
- Date output uses `DateFormatter`; the legacy AJAX/inline formatter path and `tgm_get_local_post_date` are not registered.
- Post content uses `ArticleBodyRenderer` exactly once. Related-template insertion and advertisements are intentionally inactive with saved values retained.
- Search popup/script modes normalize to a sanitized native GET form with deterministic per-instance IDs.
- The audited Techgenyz package contains no separate editorial listing-card or single-article Elementor widget in this scope. Existing shared Post Card/listing/single-article suites remain the semantic regression boundary.

Compatibility Preserved:
- Widget IDs, top-level control IDs, profile_picture attachments, editorial term relationships, posts, users, URLs and saved Elementor data remain unchanged.
- The corrected Blissz avatar and widget behavior remains regression-tested.
- Rank Math retains exclusive SEO/schema ownership. No legacy microdata, JSON-LD, metadata override or sitemap hook was migrated.
- Product, comparison, company, brand, deal, API, Firebase, IP, Amazon, post-view and Content Analytics protections remain intact and runtime-deferred.

Files / Modules Materially Affected:
- `src/Elementor/Compatibility/Techgenyz/*`, `src/Elementor/ElementorIntegration.php`.
- `tests/techgenyz-publishing-compatibility-smoke.php`, `tests/fixtures/techgenyz-elementor-controls.json`, Blissz isolation regression, scaffold version assertion and tests README.
- `docs/TECHGENYZ-PUBLISHING-COMPATIBILITY.md`, compatibility contract, migration map, project overview, README, `site-master.php`, and this handoff.

Validation:
- PHP 8.2.29 whole-tree lint and all existing/new smoke suites passed before version advancement.
- Techgenyz compatibility suite verifies exact widget/control IDs, responsive/group methods, translated selectors, terms filtering, server date, author key isolation, one-pass content, unique search IDs, hostile input handling and prohibited schema output.
- Blissz, media/CWV, page/accessibility, Post Card/listing, scaffold, shared-core and single-article suites passed.
- Executable hygiene found no schema/microdata, Rank Math override, date AJAX, post-view ingestion, persistent view write, Firebase/API credential, REST route or blocking remote request in the Techgenyz publishing adapters.

Known Limitations / Follow-up:
- LIVE WORDPRESS / ELEMENTOR: NOT RUN. REAL `_elementor_data`: NOT VERIFIED. LIVE CSS REGENERATION: NOT RUN. BROWSER COMPARISON: NOT RUN. RUNTIME LOGS: NOT RUN.
- Fixtures are source-derived from Techgenyz Master 1.0.5; no claim of production-template verification is made.
- CLI DOMDocument availability is an environment concern for later browser/render UAT; current suites use static markup and narrow WordPress/Elementor doubles.
- Production ledger remains NONE; no production ZIP, commit, push or tag was created.
- Next authorized phase is 0.1.0 — Site Master Content Analytics; it has not been started.

Post-completion Elementor control fidelity correction (2026-09-17):
- Rebuilt the Techgenyz control registry from evaluated 1.0.5 Elementor registration methods. Corrected inventory: 178 direct controls, 93 responsive controls and 65 group controls, including dynamically generated post-content definitions missed by literal-call inspection.
- Captured 43 explicit defaults, 40 option maps, 21 condition definitions, complete selector maps, two inactive repeaters with nine named subcontrols, and definition hashes covering the evaluated Elementor argument arrays.
- Registration now forwards real defaults, options, conditions, repeater fields and title fields. Explicitly missing defaults remain distinguishable from empty defaults.
- Enforced status semantics: intentionally inactive and persistence-only controls emit no compatibility selectors. Seven related-post style selectors, two related-post group selectors and five retired search-icon selectors were removed from active registration.
- Related-post, alternate-template and advertisement saved structures remain editor-representable but have no frontend behavior or CSS. Static fixture tests preserve repeater row keys, subcontrol keys, representative source defaults and ordering.
- All existing suites, the expanded Techgenyz fidelity assertions, whole-tree PHP lint and executable hygiene passed. Live Elementor, real `_elementor_data`, live edit/save round-trip and live CSS regeneration remain NOT RUN.
- Versions remain 0.0.9, channel remains development, production ledger remains NONE, and 0.1.0 was not started.

## Development Version 0.1.0 — Site Master Content Analytics

Date: 2026-09-17

Purpose: Replace Techgenyz's per-view postmeta counter with shared, privacy-conscious first-party editorial analytics while preserving genuine lifetime totals and profile isolation.

Implemented:
- Added conditional first-party beacon collection at `site-master/v1/analytics/collect`, a cache-safe post/profile HMAC, a 2 KiB payload cap, allowlisted acquisition fields, origin validation, staff/bot exclusion and silent frontend failure.
- Added schema-versioned tables for append-only events, hourly/daily/dimension aggregates, cumulative post totals, immutable legacy baselines and durable state.
- Added binary daily rotating reader estimates and one-minute keyed dedupe hashes without persisting raw IP, complete User-Agent, cookie ID, full referrer or permanent visitor identity.
- Added a five-minute advisory-locked, transaction/cursor-safe, 500-event worker, past-day distinct-reader finalization and bounded raw retention/pruning.
- Added Techgenyz-only idempotent baseline import from verified `post_views_count`, explicit UTC cutover state and a `tgm_count_post_view` adapter sharing beacon dedupe without legacy postmeta mutation.
- Added bounded reporting services, Overview/Content/Realtime/Authors/Taxonomies/Acquisition/Settings tabs, per-post analytics, profile settings and protected aggregate CSV.

Architecture / Decisions:
- Techgenyz defaults enabled; Blissz defaults disabled but opt-in; unconfigured profiles cannot enable or collect. Every stored row is profile-partitioned.
- Lifetime equals immutable baseline plus measured cumulative views. Historical granular data is never invented. Trending excludes lifetime and uses recent-to-previous velocity weighted by recent-day volume.
- Events/hour buckets use UTC; reporting days and administrator ranges use the WordPress timezone. Country remains `ZZ` unless a trusted server header is explicitly configured.
- Rank Math remains exclusive SEO/schema owner. Product, company, comparison, deal, Firebase and legacy REST work were not started.

Compatibility Preserved:
- Techgenyz Master 1.0.5 `core/post-views.php` was re-inspected: `post_views_count` is the lifetime key; AJAX requires `nonce` and `post_id`, registers logged-in and logged-out actions, and returns WordPress JSON envelopes with `counted`/`views` or an invalid-post error. Version 1.0.2 contains no editorial counter or fallback key.
- Existing postmeta remains untouched. Posts, terms, users, URLs, Elementor data and Rank Math metadata are not modified. Existing 0.0.8/0.0.9 behavior remains regression-tested.

Files / Modules Materially Affected:
- `src/Analytics/*`, `assets/js/content-analytics.min.js`, Core bootstrap/lifecycle/module status, `tests/content-analytics-smoke.php` and scaffold assertions.
- Analytics architecture/spec, project overview, migration map, compatibility contract, README and this handoff.

Validation:
- All PHP files linted with zero failures. Scaffold, shared-core, Post Card/listing, single-article, page/accessibility, media/CWV, Blissz, Techgenyz and Content Analytics suites passed.
- Analytics assertions cover profile defaults, exact baseline key/normalization, hash rotation, privacy normalization, comparison/trending helpers, browser-storage prohibition, append-only/no-remote-call hygiene, bounded SQL, transaction/lock/cursor structure, legacy actions and REST payload gates.
- Executable hygiene found no analytics `update_post_meta`, blocking remote request, cookie/browser storage, schema output or product/deal/Firebase/legacy-REST coupling. SQL uses controlled identifiers, prepared values and bounded raw/report queries. `git diff --check` passed with line-ending notices only.

Known Limitations / Follow-up:
- Local's PHP/MySQL runtime created and verified all seven prefixed schema-version-1 tables. Two repeat `dbDelta()` installs completed ready with no database error, after the live check identified and corrected compact primary-key syntax. The Local profile is currently unconfigured, so no baseline import or event ingestion was attempted. LIVE WORDPRESS UI, real ingestion/aggregation, runtime logs, LIVE BEACON BROWSER TEST, CACHE TEST and DEVTOOLS NETWORK TEST were NOT RUN. These remain mandatory UAT items. The Local PHP configuration also emitted a pre-existing missing `php_imagick.dll` startup warning.
- Bot detection and readers are estimates; trusted country accuracy depends on edge configuration; WP-Cron latency controls freshness. V1 omits visitor journeys, remote enrichment and product/deal analytics.
- Production ledger remains NONE. No production ZIP or Git mutation was performed. Next authorized phase is 0.1.1 and has not been started.

Post-completion referrer-attribution correction (2026-09-17):
- `Privacy::referrer_domain()` now accepts the beacon's privacy-preserving hostname-only value as well as an HTTP/HTTPS URL, while rejecting empty, malformed, credential-like/path/query/fragment-bearing hostname input and retaining only a validated, bounded lowercase host without the `www.` prefix.
- The frontend beacon remains unchanged and continues sending only `document.referrer`'s hostname. No path, query, fragment, search term or additional reader information is transmitted or persisted.
- Acquisition aggregation now has an explicit tested boundary: a normalized `google.com` value remains the `referrer` dimension; only an empty normalized value becomes `direct`.
- Development/plugin versions remain 0.1.0. Whole-tree PHP lint, Content Analytics tests, and all existing Site Master smoke/regression suites passed; no commit or push was performed.

Post-completion Blocker 2 correction (2026-09-17 — daily reader boundary):
- Collector calculates the WordPress-timezone `report_day` once and supplies that exact value to both event persistence and reader-hash derivation. Estimated-reader identity now rotates on the stored reporting-day boundary rather than UTC midnight.
- Event timestamps and hourly aggregation buckets remain UTC. Past-day finalization continues using `COUNT(DISTINCT reader_hash)` constrained to the stored `report_day`.
- The 60-second beacon/legacy dedupe derivation remains byte-compatible with 0.1.0: its UTC daily key, epoch bucket, profile, post ID and transient identity inputs are unchanged. Legacy AJAX continues through Collector with no separate hashing path.
- No schema change, stored-row rewrite or data migration was required. Development/plugin versions remain 0.1.0 and Development 0.1.1 was not started.

Post-completion schema recovery hardening (2026-09-17):
- Analytics schema version remains `1`. Installation now verifies all seven physical tables before persisting the current schema-version option, and full readiness still requires both the matching option and all tables.
- A matching schema version with any missing table now causes the existing admin upgrade path to rerun non-destructive `dbDelta()` definitions; a healthy schema avoids repeat installation.
- Repeated installation remains idempotent and preserves existing events, aggregates, cumulative totals, baselines, state and cursors. A failed physical repair returns false and leaves `Schema::ready()` false.
- No table definition, schema migration, analytics-row rewrite or frontend repair path was introduced. Development/plugin versions remain 0.1.0 and Development 0.1.1 was not started.
