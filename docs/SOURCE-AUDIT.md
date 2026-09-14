# Site Master Legacy Source Audit

Development version: `0.0.1`  
Audit date: 2026-09-14

## Executive summary

The three supplied ZIPs were extracted to a temporary inspection directory and left unchanged. Internal headers verify Blissz Master 1.1.0, Techgenyz Master 1.0.5, and Techgenyz Master 1.0.2. Techgenyz 1.0.5 is the migration baseline; 1.0.2 is regression reference only. The planned shared-core/profile architecture fits the sources, but copying legacy runtime wholesale would preserve duplicate schema, costly request behavior, weak SVG handling and a large persistence surface.

The supplied 1.0.5 ZIP has no nested 1.0.2 plugin copy. It does contain a complete `.git` directory, which is forbidden from future release staging.

## Blissz Master 1.1.0

- Procedural bootstrap coordinated by `TBM_Master`.
- Shared author/avatar, RSS, local-date, Rank Math, SVG and Elementor functionality.
- Breadcrumb, date, author, post-content and search widgets plus Loop Post, Loop Category and Loop Single modules.
- Loop Post uses semantic `<article>` and visible `<time>`, but Loop Post/Single also emit BlogPosting/Person microdata. Single-post composition can leave content outside the header article.
- Breadcrumbs use Rank Math output. Rank Math hooks force SearchAction and globally disable sitemap caching.
- SVG uploads use `enshrined/svg-sanitize`; this is the stronger migration baseline.
- Date rendering exposes public/private AJAX and can produce per-widget overhead.

No custom CPT, custom database table, independent REST API or destructive uninstall routine was found.

## Techgenyz Master 1.0.5

- Procedural, feature-configured bootstrap.
- Publishing core includes avatar, dates, content/search/term widgets, RSS, comments/taxonomy presentation, attachment redirects, IP blocking, Firebase and post views.
- Products register `tgm-products`, `tgm-comparisons`, fixed and dynamic taxonomies, admin pages, templates, widgets, shortcodes and AJAX helpers. Both current and APS/historical metadata forms are supported.
- Companies link brand terms to company posts via `tgm_company_id`; live relationship completeness remains unproven.
- Deals provide public entities/taxonomies, redirects, click tracking, shortcodes, a dynamic tag and Amazon settings. Each click writes post meta.
- REST namespaces `tgm/v1` and `tgm/v1/ext` expose publishing/product routes. 1.0.5 adds API-key authorization with `X-TGM-App-Key`; response compatibility is binding.
- Firebase stores service-account JSON. It must remain server-side, capability-protected and absent from debug output.
- Public view counting writes WordPress post meta per counted request.
- Rank Math hooks force SearchAction and disable sitemap caching. SVG code permits the MIME but lacks the Blissz sanitizer strength.
- Product image/template fallbacks need responsive-image and LCP correction in the media phase.

No custom database table or destructive uninstall routine was found.

## Techgenyz Master 1.0.2 comparison

The layout is substantially the same. Version 1.0.5 adds `core/post-views.php` and changes bootstrap, APIs, date scripts, Firebase, IP controls, settings, Rank Math, Elementor content/search registration, deals, and product admin/runtime. These changes include later sanitization, bounded-query, settings and authenticated-API work. Therefore 1.0.2 must not replace 1.0.5; it is useful only for regression comparison.

## Shared functionality

Both migration sources duplicate date, avatar, RSS, Rank Math, search/content widgets and SVG enablement under different prefixes. These should become shared services with legacy adapters. Blissz supplies editorial loop/single components and the stronger sanitizer. Techgenyz supplies products, comparisons, companies, deals, APIs, notifications, IP controls and views.

## Duplicate functionality

The `tbm_*` and `tgm_*` date, author, content, search, RSS, Rank Math and SVG paths solve substantially the same problems. Site Master should consolidate their implementation while preserving externally persisted IDs through adapters.

## Migration risks

Primary functional risks are incomplete saved-widget mapping, changing dynamic query results, breaking rewrite/redirect behavior, losing product fields across competing meta families, altering API response shapes, and enabling profile-specific modules on the wrong site.

## SEO / Rank Math findings

Both sources force SearchAction through `rank_math/json_ld/disable_search` and disable sitemap caching through `rank_math/sitemap/enable_caching`. Neither override is approved for automatic migration. Blissz Loop Post/Single directly contain `itemscope`, `itemtype`, `itemprop` and BlogPosting/Person metadata; Site Master must omit all of it. Rank Math remains sole metadata/schema/sitemap authority.

## Schema / microdata findings

Direct BlogPosting and nested Person microdata appears in Blissz visual widgets. It must be deleted from the Site Master equivalents, not adapted. No audited legacy feature justifies a second JSON-LD graph alongside Rank Math.

## Elementor findings

The compatibility contract records discovered IDs. Large loop, author, breadcrumb, product and filter widgets have many persisted controls, so adapters must be fixture-driven. Techgenyz also has `tgm-meta` and `tgm-deal-url` dynamic tags and an Elementor query filter. Wrapper reduction requires selector and old-template regression tests.

## Product, company and brand findings

One product CPT serves all gadget categories. Data spans `tgm-*`, APS aliases and older option/meta conventions. Comparisons persist product relationships. Brand terms may point to company posts, but absent links must not be invented. Preserve first, normalize through adapters later. Editorial scores must not be represented as AggregateRating.

## Media findings

Blissz provides the safer SVG pipeline. Both plugins use WordPress attachments in several paths, while product and fallback image output is inconsistent about responsive sources, dimensions and loading priority. Later renderers must distinguish hero/LCP images from lazy offscreen cards.

## Performance findings

- Post views and deal clicks perform per-event post-meta writes.
- Local-date widgets can fan out AJAX/script work.
- Rank Math sitemap caching is globally disabled.
- Product loops need N+1 and `no_found_rows` review.
- Remote Amazon, Firebase and IP calls require caching/retry boundaries and must not block normal rendering.
- Profile gating must prevent Techgenyz entity modules loading on Blissz.

## Security findings

- Blissz SVG sanitization is the shared baseline; Techgenyz MIME-only handling is insufficient.
- v1.0.5 API routes use an API-key permission callback; API/Firebase secrets must be rotatable and never exposed.
- Several admin paths visibly sanitize and check capabilities/nonces, but every migrated mutation still requires targeted verification.
- Public AJAX, redirect/click and view endpoints need abuse and cache analysis.
- Attachment/IP redirects require validated targets and must not become blanket homepage redirects.
- Legacy archives include repository metadata and cannot serve as release trees.

## Data-compatibility risks

Highest-risk areas are saved Elementor JSON, dual product meta families, dynamic taxonomies, option-based specifications, term media, comparisons, brand-company mappings, REST contracts, Firebase credentials and author attachment IDs. Defaults must never overwrite populated legacy values.

## Recommended migration order

Follow the approved phase map: bootstrap/profiles; shared SEO boundary/SVG/author/date/breadcrumb; cards; complete article; pages/accessibility; media; Blissz adapters; Techgenyz publishing; product/company/comparison; Rank Math entity bridge; deals/crawl; performance; security; QA/build; both-site UAT.

## Items requiring later decisions

- Evidence for forced SearchAction or uncached sitemaps.
- Which attachment, IP, comment and taxonomy-label behavior remains required.
- Deal indexability and analytics replacement.
- View-count storage and continuity.
- Canonical company URL/base and missing brand-company remediation using production data.
- REST version/deprecation rollout.
- Complete saved-control fixtures during implementation.
- Whether APS/option product formats remain writable or become read-only inputs.
