# Site Master Migration Map

Development version: `0.0.1`  
Audit date: 2026-09-14

Classification vocabulary: **SHARED CORE**, **TECHGENYZ PROFILE**, **BLISSZ PROFILE**, **LEGACY COMPATIBILITY**, **DEPRECATE AFTER MIGRATION**, **DO NOT MIGRATE**, and **REQUIRES DECISION**.

| Legacy plugin | Source | Feature / persistent identifiers | Target | Classification | Migration and validation | Phase |
|---|---|---|---|---|---|---|
| Both | bootstrap, `core/core.php` | Feature loading; legacy basenames and `tbm_*`/`tgm_*` hooks | `src/Core/`, `src/Profiles/` | SHARED CORE | Profile-aware bootstrap; activation and both-profile smoke tests | 0.0.2 |
| Techgenyz | `core/user-avatar.php` | Custom avatar; user meta `profile_picture` | `Content/AuthorData` | SHARED CORE + LEGACY COMPATIBILITY | Preserve existing attachment IDs; shared `profile_picture` service was implemented in 0.0.3; test nonce, capability, deletion and fallback | 0.0.3 |
| Blissz | `core/user-avatar.php` | Custom avatar; user meta `tbm_profile_picture` | `Content/AuthorData` / Blissz compatibility adapter | SHARED CORE + BLISSZ LEGACY COMPATIBILITY | Preserve existing attachment IDs; add a non-destructive adapter after sanitized fixture inspection; do not imply 0.0.3 compatibility | 0.0.8 |
| Both | local-date code/widgets | AJAX `tbm_get_local_post_date`, `tgm_get_local_post_date`; widget IDs | `Support/DateFormatter` | SHARED CORE + LEGACY COMPATIBILITY | One formatter/script; retain adapters; avoid AJAX fan-out | 0.0.3 |
| Both | `core/rss.php` | Feed featured image filters | content/media | SHARED CORE | Preserve after duplicate-output/feed regression | 0.0.3 |
| Both | `core/rankmath.php` | Forced SearchAction; disabled sitemap cache | `SEO/RankMathBridge` | REQUIRES DECISION / DO NOT MIGRATE by default | Rank Math remains sole owner; require sitemap/schema evidence | 0.0.3, 0.1.2 |
| Both | `core/svg.php` | SVG upload behavior | `Security/SvgSanitizer` | SHARED CORE | Use Blissz sanitizer baseline; malicious SVG tests | 0.0.3 |
| Blissz | standard Elementor widgets | `tbm_breadcrumb`, `tbm-post-author-widget`, `tbm-date-widget`, `tbm-post-content`, `tbm_post_search`; saved controls | `src/Elementor/` | BLISSZ PROFILE + LEGACY COMPATIBILITY | Exact-ID adapters; saved-template and selector regression | 0.0.3–0.0.8 |
| Techgenyz | editorial Elementor widgets | `post_terms`, `tgm-date-widget`, `tgm-post-author-widget`, `tgm-post-content`, `tgm_post_search`; saved controls | `src/Elementor/Compatibility/Techgenyz/` | TECHGENYZ PROFILE + LEGACY COMPATIBILITY | 0.0.9 source-derived exact-ID/control adapters; live templates and CSS regeneration deferred to UAT | 0.0.9 |
| Blissz | loop post/category/single modules | `tbz_loop_post_block`, `blissz_loop_category_block`, `tbz_loop_single_post_block`; category metadata; `_elementor_template_type`; `rank_math_primary_category` | Components/Content | SHARED CORE + LEGACY COMPATIBILITY | Preserve query/display behavior; rebuild approved semantics | 0.0.4–0.0.8 |
| Blissz | loop post/single renderers | BlogPosting/Person `itemscope`, `itemtype`, `itemprop` | none | DO NOT MIGRATE | Remove; scan source and rendered output | 0.0.4–0.0.5 |
| Techgenyz | products | CPT `tgm-products`; rewrite `product` | `modules/products/` | TECHGENYZ PROFILE + LEGACY COMPATIBILITY | Preserve IDs/URLs; archive/admin/upgrade tests | 0.1.1 |
| Techgenyz | comparisons | CPT `tgm-comparisons`; rewrite `tgm-comparisons`; `aps-product-comparison`; AJAX `tgm-product-search`, `tgm_thumb` | `modules/comparisons/` | TECHGENYZ PROFILE + LEGACY COMPATIBILITY | Preserve relations/actions; harden mutations | 0.1.1 |
| Techgenyz | product taxonomies | `tgm_brands` (`brand`), `tgm_cats` (`product-category`), `tgm_groups`, `tgm_attributes`, `tgm_filters`, `tgm_rating_bars`; dynamic filters | products/companies | TECHGENYZ PROFILE + LEGACY COMPATIBILITY | Preserve slugs, term IDs and visibility; rewrite/crawl tests | 0.1.1, 0.1.3 |
| Techgenyz | product metadata/admin | `tgm-*`, `aps-*`, group prefixes; historical `pro_*`, `ft_*`, `at_*`, `rt_*`, `img_url_*`; brand/category/group/attribute/rating term meta | ProductData/products | TECHGENYZ PROFILE + LEGACY COMPATIBILITY | Dual-read; write only via approved idempotent migration; fixture round-trip | 0.1.1 |
| Techgenyz | product widgets | `tgm-product-title`, `-image`, `-features`, `-specifications`, `-rating`, `-brand`, `-category`, `-filter`; saved controls | Elementor/products | LEGACY COMPATIBILITY | Exact-ID adapters; old-template and responsive-image regression | 0.1.1 |
| Techgenyz | companies | CPT `tgm_company`; rewrite `companies`; brand term meta `tgm_company_id` | `modules/companies/` | TECHGENYZ PROFILE + LEGACY COMPATIBILITY | Preserve posts/mappings; never infer absent links | 0.1.1 |
| Techgenyz | deals | CPT `tgm_deal` (`deals`); taxonomies `tgm_deal_cat` (`deal-category`), `tgm_deal_tag` (`deal-tag`); deal meta; `tgm_amazon_settings`; three shortcodes; tag `tgm-deal-url` | `modules/deals/` | TECHGENYZ PROFILE + REQUIRES DECISION | Preserve data/contracts; redesign writes/indexability; redirect/API/crawl tests | 0.1.3 |
| Techgenyz | APIs | `tgm/v1`, `tgm/v1/ext`; `posts`, `categories`, `tags`, `authors`, `products`, `url`, `load-more-posts`; `X-TGM-App-Key` | `modules/api/` | TECHGENYZ PROFILE + LEGACY COMPATIBILITY | Preserve response/auth contracts; security and consumer tests | 0.0.9 |
| Techgenyz | Firebase | `tgm_firebase_notifications_settings`; `_tgm_fcm_*` | `modules/notifications/` | TECHGENYZ PROFILE | Preserve settings/meta; keep secrets server-side; duplicate-send tests | 0.0.9 |
| Techgenyz | module/IP/API settings | `tgm_master_settings`, `tgm_master_ipinfo_settings`, `tgm_master_api_settings`, `tgm-constants` | profile/config | TECHGENYZ PROFILE + LEGACY COMPATIBILITY | Never overwrite populated options; admin security/upgrade tests | 0.0.9 |
| Techgenyz | post views | public AJAX `tgm_count_post_view`; verified meta `post_views_count` | `src/Analytics/` | SHARED CORE CAPABILITY + TECHGENYZ LEGACY COMPATIBILITY | Immutable batched baseline/cutover; append-only events; shared 60-second beacon/AJAX dedupe; no legacy postmeta mutation; aggregate reports | 0.1.0 |
| Techgenyz | attachment/IP/comments/taxonomy-label | Redirect/access/presentation hooks | profile services | REQUIRES DECISION | Confirm business/URL effects first | 0.0.9, 0.1.3 |
| Techgenyz 1.0.5 ZIP | `.git` directory | Repository metadata | none | DO NOT MIGRATE | Exclude and scan package | 0.1.7 |
| Techgenyz 1.0.2 | whole plugin | Historical behavior; lacks 1.0.5 post-view file/hardening | none | DO NOT MIGRATE | Compare only; never bootstrap/package | all migration phases |

## Development 0.0.8 author-media requirement

The Blissz migration recognizes existing `tbm_profile_picture` values, preserves their referenced attachment IDs, reuses the shared AuthorData/avatar presentation path, retains WordPress avatar fallback and avoids destructive user-meta migration. `tbm_profile_picture` and `profile_picture` remain separate persisted histories. The implemented 0.0.8 policy reads `tbm_profile_picture` first, falls back to `profile_picture`, writes or removes only the Blissz relationship, and performs no synchronization or attachment deletion. This policy is covered by source-derived synthetic fixtures; a sanitized production database fixture was unavailable.

## Scaffold conclusion

The existing Core, Profiles, Content, Components, Elementor, Media, SEO, Security, Support and profile-gated module boundaries fit the audited capabilities. No additional top-level runtime boundary is justified during 0.0.1.
