# AGENTS.md — Site Master Codex Operating Procedure

## Purpose

This file defines the mandatory operating procedure for Codex when working on the **Site Master** WordPress plugin.

Site Master is the shared plugin being developed for:

- **Techgenyz**
- **The Blissz**

It consolidates and replaces the future development path of:

- Blissz Master v1.1.0
- Techgenyz Master v1.0.5

Techgenyz Master v1.0.2 is historical/reference material only and must never be shipped inside Site Master.

The core objective is to build one production-grade plugin with shared components and site profiles while preserving existing data, URLs, approved functionality, Rank Math behavior, Elementor compatibility, SEO behavior, and production safety.

---

# 1. Permanent Document Responsibilities

These files have different authorities and MUST NOT be treated as interchangeable.

## `AGENTS.md`

Defines **HOW Codex must operate**.

This file controls:

- development workflow
- production workflow
- required reading
- scope discipline
- version handling
- validation
- stop conditions
- packaging behavior
- reporting behavior

## `CODEX_HANDOFF.md`

Defines **WHAT has been implemented and why**.

It records:

- current architecture
- approved implementation decisions
- migration context
- locked behavior
- compatibility constraints
- development history
- completed development-version changes

It is NOT authoritative for the current Development Version or Last Production Version.

## `PRODUCTION_HANDOFF.md`

Defines **WHAT a production release must satisfy**.

It controls:

- production release contract
- permanent plugin identity
- production ZIP structure
- production acceptance gates
- SEO / Rank Math / schema checks
- security and data-safety checks
- performance gates
- regression gates
- packaging rules
- production release ledger
- Last Production Version

## Version authority

The current Development Version is authoritative from the actual Site Master source:

```text
SITE_MASTER_DEVELOPMENT_VERSION
```

inside:

```text
site-master/site-master.php
```

The Last Production Version is authoritative ONLY from:

```text
PRODUCTION_HANDOFF.md
```

If any documentation conflicts with the current source Development Version, the source wins.

If any document conflicts with the Production Version ledger, `PRODUCTION_HANDOFF.md` wins.

If an authoritative value is missing, malformed, or ambiguous, STOP. Never guess.

---

# 2. Project Identity

Permanent project name:

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

Do not version the internal WordPress plugin folder.

Correct:

```text
Site Master v1.0.0 Production.zip
└── site-master/
    └── site-master.php
```

Incorrect:

```text
site-master-v1.0.0/
site-master-1.0.0/
site-master/site-master/
```

---

# 3. Mandatory Site Profiles

Site Master uses one shared codebase with explicit site profiles.

Supported profiles:

```text
techgenyz
blissz
```

Profile selection must not rely only on hostname because staging domains may differ.

Preferred priority:

```text
deployment constant
→ saved option
→ safe setup/default flow
```

Example:

```php
define( 'SITE_MASTER_PROFILE', 'techgenyz' );
```

or:

```php
define( 'SITE_MASTER_PROFILE', 'blissz' );
```

Shared code must remain shared.

Do not duplicate the same renderer/service into two site-specific implementations unless the behavior is genuinely site-specific.

---

# 4. Mandatory SEO / Schema Boundary

This rule is permanent unless the user explicitly changes the architecture.

## Rank Math owns

- JSON-LD / structured data
- NewsArticle / Article / BlogPosting selection
- Product / Review / Organization / Brand schema integration
- WebPage / WebSite / Organization
- ProfilePage / Person
- AboutPage / ContactPage
- BreadcrumbList
- canonical
- robots
- SEO title
- meta description
- Open Graph
- Twitter metadata
- XML sitemap
- News sitemap

## Site Master owns

- visible semantic HTML
- `<main>`
- `<article>`
- `<section>`
- `<aside>`
- `<nav>`
- `<header>`
- `<footer>`
- `<figure>`
- `<figcaption>`
- `<time>`
- heading hierarchy
- visible breadcrumbs
- post cards
- article layout/components
- product listing/detail HTML
- company/brand listing/detail HTML
- author/date/category/content rendering
- image markup/loading behavior
- Elementor components
- data services
- performance behavior

## Site Master MUST NOT output

- Article JSON-LD
- NewsArticle JSON-LD
- BlogPosting JSON-LD
- Product JSON-LD
- Review JSON-LD
- Organization JSON-LD
- BreadcrumbList JSON-LD
- `itemscope`
- `itemtype`
- `itemprop`
- canonical tags
- robots tags
- Open Graph tags
- Twitter tags

Any custom schema enrichment must go through the Rank Math integration layer.

---

# 5. Locked Semantic Architecture

These decisions are approved and should not be changed casually.

## Editorial Post Card

Root:

```html
<article>
```

Rules:

- normal thumbnail does not require `<figure>`
- use `<figure>` only when the image is genuinely a figure/caption-credit unit
- homepage section card headline normally H3
- archive/search card headline normally H2
- no H1 in reusable post cards
- crawlable standard links
- visible author/date where configured
- `<time datetime="">`
- no schema/microdata

## Single Post / News Article

One complete semantic article must contain:

- category
- H1
- summary/dek where used
- author
- published date
- updated date where used
- featured image
- article body
- article footer/topics

The sidebar normally remains outside the primary `<article>`.

After-article related stories normally remain outside the primary `<article>`.

Do not build separate Article Start and Article End widgets.

## Static Pages

About, Contact, Advertise, Editorial Policy, Corrections, Privacy, Terms and similar pages do not require `<article>` by default.

Use:

```text
main
→ breadcrumb
→ header/H1
→ meaningful sections
```

## Product Listing

Product listing cards normally use:

```text
<ul>
  <li>...</li>
</ul>
```

Product cards are entity/list cards, not editorial articles.

## Brand / Company Listing

Brand/company directory cards normally use:

```text
<ul>
  <li>...</li>
</ul>
```

Do not use `<article>` simply because each company has a page.

## Product Detail

Product/specification/entity pages normally use a product section/entity structure, not editorial `<article>`.

A separate editorial product review may use `<article>`.

## Company Detail

Company/brand profile pages normally use entity/profile structure, not `<article>`.

A genuine editorial company review may use `<article>`.

---

# 6. Persistent Data Is Sacred

Site Master development must preserve existing production data unless the user explicitly authorizes a migration.

Do not rename or delete existing identifiers merely to make the new architecture cleaner.

Preserve where applicable:

- post IDs
- page IDs
- product IDs
- company IDs
- CPT slugs
- taxonomy slugs
- term IDs
- post meta keys
- options
- user meta
- stored settings
- Elementor widget IDs
- Elementor control IDs where saved templates depend on them
- Elementor saved data
- URLs
- redirects
- Rank Math metadata
- product specifications
- product gallery data
- ratings
- offers
- company/brand relationships
- existing taxonomies
- existing custom fields

When a new architecture is needed, prefer:

```text
legacy persisted identifier
→ compatibility adapter
→ new Site Master service/component
```

rather than destructive renaming.

---

# 7. Source Plugin Roles

## Blissz Master v1.1.0

Use as the primary reference for:

- existing Blissz Elementor behavior
- post cards
- single-post components
- SVG sanitization
- related content
- breadcrumbs
- author/date/media behavior

Do not preserve old BlogPosting microdata.

## Techgenyz Master v1.0.5

Use as the primary Techgenyz migration source.

Important modules include:

- Products
- Companies / Brands
- Comparisons
- Deals
- REST/API
- Firebase notifications
- post content
- post author
- post date
- search
- attachment handling
- post views
- geo/IP controls
- media/product widgets

## Techgenyz Master v1.0.2

Historical/reference only.

Never package it inside Site Master.

The supplied audited v1.0.5 package did not contain a nested v1.0.2 plugin copy; it did contain `.git` repository metadata. Never ship repository metadata or any nested historical plugin copy encountered in a future source/package.

---

# 8. Development Version Track

Site Master development starts at:

```text
0.0.1
```

The authoritative Development Version is:

```text
SITE_MASTER_DEVELOPMENT_VERSION
```

inside:

```text
site-master.php
```

Example development progression:

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

Development numbering represents implementation state only.

It is independent from production numbering.

`CODEX_HANDOFF.md` records what was implemented in each completed development version, but it is not the authority for the current version value.

---

# 9. Development Version Advancement Rule

Do not increment the Development Version merely because work started.

For each authorized development task:

1. Read this `AGENTS.md`.
2. Read the relevant current source.
3. Read `CODEX_HANDOFF.md`.
4. Read the current task/prompt.
5. Inspect the existing implementation before changing it.
6. Identify the exact scope.
7. Preserve unrelated approved behavior.
8. Implement the requested change.
9. Run applicable syntax/static/tests/regression checks.
10. Review the diff for unintended changes.
11. If validation fails:
    - do not mark the phase complete
    - do not advance the Development Version
    - do not create a completed development-history entry
    - report blockers
12. If validation passes:
    - advance `SITE_MASTER_DEVELOPMENT_VERSION` to the intended next development version
    - append a concise completed-version record to `CODEX_HANDOFF.md`
    - include implementation summary, preserved behavior, validation and known limitations
13. Do not modify the Production Version ledger during normal development.
14. Do not create a production ZIP during normal development.

---

# 10. Development History Rule

Every successfully completed development version must be recorded in:

```text
CODEX_HANDOFF.md
```

Each entry should include:

```text
Development Version
Date
Purpose
Implemented
Architecture/decisions
Compatibility preserved
Files/modules materially affected
Validation performed
Known limitations / follow-up
```

Historical version entries are context only.

The actual current version remains authoritative from:

```text
SITE_MASTER_DEVELOPMENT_VERSION
```

---

# 11. Initial Development Map

The currently approved development map is:

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

This map may evolve if later development reveals a safer sequence.

Do not silently merge major phases simply to reduce version count.

---

# 12. Mandatory Production Trigger

Treat this user request as the production-release command:

```text
Give me production ready zip file
```

Minor natural-language variations with the same intent also count.

When production intent is clear, DO NOT merely create a ZIP.

Execute the complete production workflow in:

```text
AGENTS.md
+
PRODUCTION_HANDOFF.md
```

and use `CODEX_HANDOFF.md` for implementation context.

---

# 13. Production Version Track

Production versions are independent from Development Versions.

Initial state:

```text
Last Production Version: NONE
```

The first successful production candidate is always:

```text
1.0.0
```

After that, normal production progression is:

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

Major-version changes require explicit user approval.

The Development Version must NEVER be used to calculate the Production Version.

Example:

```text
Development Version:     0.1.9
Last Production Version: NONE
Candidate Production:    1.0.0
```

Later:

```text
Development Version:     0.4.7
Last Production Version: 1.0.0
Candidate Production:    1.0.1
```

This is valid.

---

# 14. Production Release Copy Rule

Production packaging MUST NOT rewrite the working development source simply to assign the Production Version.

Required process:

```text
working development source
        ↓
clean temporary production staging copy
        ↓
apply Candidate Production Version to release metadata in staging copy only
        ↓
preserve SITE_MASTER_DEVELOPMENT_VERSION
        ↓
run full production audit
        ↓
build ZIP
        ↓
inspect actual ZIP
```

Example staged production copy:

```text
Plugin header Version:             1.0.0
SITE_MASTER_VERSION:               1.0.0
SITE_MASTER_DEVELOPMENT_VERSION:   0.1.9
```

The working development tree remains at:

```text
SITE_MASTER_DEVELOPMENT_VERSION = 0.1.9
```

unless a separate development task intentionally changes it.

---

# 15. Production Release Workflow

When the user requests a production-ready ZIP:

1. Read `AGENTS.md`.
2. Read `PRODUCTION_HANDOFF.md` completely.
3. Read `CODEX_HANDOFF.md`.
4. Inspect the complete current Site Master source.
5. Determine the Development Version from `SITE_MASTER_DEVELOPMENT_VERSION`.
6. Read the Last Production Version only from the production ledger.
7. Determine the Candidate Production Version.
8. STOP if either authoritative version value is missing or ambiguous.
9. Run the complete production audit.
10. Do not modify unrelated runtime functionality merely to make the audit pass.
11. If code defects are found during an audit-only production request, report them as blockers unless the user explicitly authorizes fixing them.
12. Create a clean temporary release copy.
13. Apply Candidate Production Version only to the staged copy.
14. Preserve the current Development Version in the staged copy.
15. Remove forbidden development/repository artifacts from the staged copy.
16. Build the production ZIP.
17. Inspect the actual generated ZIP.
18. Run final production gates.
19. If any mandatory blocker remains:
    - report `NOT READY FOR PRODUCTION`
    - do not update the production ledger
20. If all mandatory gates pass:
    - report `READY FOR PRODUCTION`
    - update the Production Version ledger
    - provide the production ZIP
    - provide the release report

---

# 16. Git Safety

Codex MUST NOT automatically:

- commit
- push
- rewrite Git history
- force-push
- create tags

unless the user explicitly asks.

Production packaging can be completed without Git mutation.

If a tag/commit is recommended, report the suggested action without executing it unless authorized.

---

# 17. Elementor Rules

Site Master must preserve Elementor compatibility.

Rules:

- minimize wrappers where safe
- use `has_widget_inner_wrapper(): false` where compatible and regression-tested
- do not remove wrapper-dependent CSS without fixing selectors
- do not split one conceptual component into many tiny widgets merely for design control
- preserve legacy widget IDs through adapters until templates are migrated
- keep current-post/product/user widgets dynamic unless caching safety is proven
- conditionally load widget CSS/JS
- do not duplicate desktop/mobile DOM merely for responsive design
- allow Elementor Theme Builder to own layout where appropriate
- keep PHP fallback templates only when necessary

---

# 18. Performance Rules

Do not introduce expensive per-request behavior without justification.

Avoid:

- per-pageview WordPress DB writes
- per-widget AJAX requests
- repeated inline JavaScript per widget instance
- remote API requests in normal render paths
- repeated metadata queries inside large loops
- unnecessary `found_rows`
- unconditional loading of Product/Company/Deal modules on The Blissz
- globally disabling Rank Math sitemap caching without proven requirement

Prefer:

- cached/batched analytics
- shared scripts
- view models/services
- conditional modules
- conditional assets
- WordPress responsive image APIs
- query caching where safe
- bounded queries
- `no_found_rows` where appropriate

---

# 19. Image Rules

Use WordPress attachment APIs whenever possible.

Required:

- width/height when known
- responsive `srcset`
- appropriate `sizes`
- descriptive alt
- correct loading behavior
- no blanket lazy-loading of primary LCP images

Contexts:

```text
primary hero / LCP candidate
→ eager or normal priority as appropriate
→ fetchpriority=high only when justified

card / offscreen image
→ loading=lazy
```

Publisher content should support large representative source images suitable for large preview use.

Do not create image schema in Site Master.

---

# 20. Product / Review Rules

All gadget families share one Product architecture:

- mobile
- camera
- drone
- headphones
- earphones
- other gadgets

Do not create separate renderer architectures for each category.

Product entity data may vary by category.

Do not treat editorial score as `AggregateRating`.

Editorial review score belongs conceptually to:

```text
Review.reviewRating
```

`AggregateRating` is only appropriate when genuine aggregated ratings exist.

Do not present Techgenyz as the seller/merchant when products are sold by third parties.

---

# 21. Security Rules

Mandatory principles:

- capability checks for privileged actions
- nonce/authentication protection where appropriate
- sanitize input
- escape output by context
- protect uploads
- sanitize SVG
- no frontend secret exposure
- intentional REST permissions
- no unsafe filesystem includes
- prepared SQL when direct SQL is required
- no destructive deactivation
- uninstall cleanup only when explicitly designed and protected
- no accidental debug output in production

---

# 22. Stop Conditions

STOP and report the issue when:

- Development Version authority is missing/ambiguous
- Last Production Version ledger is ambiguous
- source identity is ambiguous
- plugin root identity would change
- a migration could overwrite existing stored data unexpectedly
- a task conflicts with an approved architecture decision
- Rank Math/schema ownership becomes ambiguous
- a requested change would create duplicate SEO/schema output
- a production build contains missing runtime files
- a production ZIP contains unintended nested plugin copies
- a production audit reveals a real security/data-loss/SEO/functional blocker
- required validation cannot be performed and the missing validation is itself a release blocker

Do not guess through authoritative ambiguity.

---

# 23. Final Development Task Report

At the end of every completed development task, report:

- Development Version before
- Development Version after
- files materially changed
- implementation summary
- compatibility preserved
- validation/tests run
- failures or warnings
- known limitations
- next authorized phase

---

# 24. Content Analytics Guardrails

Site Master Content Analytics is a shared capability whose runtime begins in Development 0.1.0. It must preserve verified legacy lifetime totals without fabricating historical analytics. Migration and custom-table schema upgrades must be versioned, idempotent, non-destructive and protected against duplicate baseline import or double counting.

Analytics must use no per-view postmeta update, raw-IP storage, permanent visitor profile, cross-site tracking, blocking remote geolocation per view, or synchronous multi-counter fan-out. Raw events require bounded retention. Bot and staff exclusions must be documented and tested. Dashboard/report/export access must be capability protected, and aggregate data/exports must never expose visitor hashes. Analytics failure must not break frontend rendering.

# 25. Core Principle

The Site Master development and release process must remain:

```text
PRESERVE
INSPECT
IMPLEMENT
VALIDATE
DOCUMENT
AUDIT
STAGE
PACKAGE
INSPECT
REPORT
RELEASE
```

The goal is a repeatable and auditable system where the user does not need to restate the complete development or production procedure every time.
