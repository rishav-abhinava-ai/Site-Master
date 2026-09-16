# Site Master Compatibility Contract

Development version: `0.0.1`  
Audit date: 2026-09-14

Site Master must preserve existing records, IDs, URLs, settings, saved templates and API consumers. The mandatory pattern is `legacy persisted identifier → adapter/service → Site Master`. Destructive renaming requires a separately approved, reversible migration.

## Protected Techgenyz identifiers

- Post types: `tgm-products`, `tgm-comparisons`, `tgm_company`, `tgm_deal`.
- Rewrites: `product`, `tgm-comparisons`, `product-category`, `brand`, `companies`, `deals`.
- Taxonomies: `tgm_brands`, `tgm_cats`, `tgm_groups`, `tgm_attributes`, `tgm_filters`, `tgm_rating_bars`, generated dynamic filters, `tgm_deal_cat` (`deal-category`), `tgm_deal_tag` (`deal-tag`).
- Product post meta: `tgm-product-*`, `aps-product-*`, `tgm-attr-group-*`, `aps-attr-group-*`; historical `pro_*`, `ft_*`, `at_*`, `rt_*`, `img_url_*`, `no_of_pro_image`, `pros_string`, `cons_string`; `_tgm_source_url`.
- Product/brand term meta: `brand-logo`, `brand-order`, `cat-image`, `cat-display`, `cat-features`, `cat-groups`, `cat-filters`, `cat-bars`, `group-icon`, `group-attrs`, `attribute-meta`, `rating-bar-value`, `tgm_company_id`.
- Comparisons: `aps-product-comparison` and referenced product IDs.
- Deals: `tgm_deal_url`, `tgm_marketplace`, `tgm_amazon_asin`, `tgm_clicks`; `tgm_deal_link`, `tgm_deals`, `tgm_amz_box`; `tgm-deal-url`.
- Options: `tgm_products_settings`, `tgm_master_settings`, `tgm_master_ipinfo_settings`, `tgm_master_api_settings`, `tgm_firebase_notifications_settings`, `tgm_amazon_settings`, `tgm-constants`, and historical `category_{id}`, `cat_feature_{id}`, `cat_group_{id}`, `cat_rating_{id}`, `group_att_{id}` families.
- Notifications: `_tgm_fcm_sent_at`, `_tgm_fcm_last_error`, `_tgm_fcm_message_name`; credentials stay server-side.
- Author media: user meta `profile_picture` and its attachment IDs.
- REST: `tgm/v1`, `tgm/v1/ext`; routes `posts`, `categories`, `tags`, `authors`, `products`, `url`, `load-more-posts`; `X-TGM-App-Key`; existing response fields.
- AJAX: `tgm_get_local_post_date`, `tgm_count_post_view`, `tgm-product-search`, `tgm_thumb` while clients depend on them.
- Elementor widgets: `post_terms`, `tgm-date-widget`, `tgm-post-author-widget`, `tgm-post-content`, `tgm_post_search`, `tgm-product-title`, `tgm-product-image`, `tgm-product-features`, `tgm-product-specifications`, `tgm-product-rating`, `tgm-product-brand`, `tgm-product-category`, `tgm-product-filter`.
- Elementor dynamic tags: `tgm-meta`, `tgm-deal-url`.
- All control keys persisted in `_elementor_data`; exact control fixtures must be captured before replacing each widget.

## Protected Blissz identifiers

- Author media: user meta `tbm_profile_picture` and its referenced attachment IDs.
- AJAX `tbm_get_local_post_date` while templates depend on it.
- Widgets `tbm_breadcrumb`, `tbm-date-widget`, `tbm-post-author-widget`, `tbm-post-content`, `tbm_post_search`, `tbz_loop_post_block`, `blissz_loop_category_block`, `tbz_loop_single_post_block`.
- Every saved Elementor control key for query, layout, headings, images, author/date, excerpt, category and styling.
- Category metadata used by loop modules; Elementor template IDs/types; `rank_math_primary_category`; `_yoast_wpseo_primary_category` fallback.
- Visible breadcrumb, author/date, related-content, card and search behavior, except prohibited schema/microdata.

## Distinct legacy author-media identifiers

`profile_picture` (Techgenyz) and `tbm_profile_picture` (Blissz) are distinct protected persisted identifiers. Neither key nor its referenced attachment IDs may be destructively renamed, deleted, copied in bulk or rewritten merely to unify Site Master internals. Compatibility follows `legacy persisted identifier -> compatibility adapter/service -> Site Master`. Development `0.0.8` implements this Blissz policy: read `tbm_profile_picture` first, fall back to `profile_picture`, write/delete only the `tbm_profile_picture` relationship, and never synchronize the keys or delete the referenced attachment.

## WordPress, SEO and lifecycle guarantees

- Post, page, term, user, media and relationship IDs are immutable migration inputs.
- Site Master does not rewrite existing Rank Math post/term/user metadata, canonical/index settings, primary categories or sitemap configuration.
- Rank Math alone owns canonical, robots, social metadata, sitemaps and JSON-LD.
- Legacy microdata, forced SearchAction and global sitemap-cache disabling are behavior, not protected data, and are not migrated by default.
- Existing product, comparison, company/brand, deal, archive, taxonomy, REST and shortcode URLs must continue or receive an explicitly approved redirect/migration plan.
- Activation/reactivation never overwrites populated values with defaults. Deactivation is non-destructive.
- Migrations are idempotent, preserve populated values, record state, and are tested against representative copies.
- Profile resolution is deployment constant, then saved option, then safe setup/default; hostname alone is not authoritative.

## Adapter retirement and fixtures

An adapter may be removed only when an inventory proves no template, client, shortcode, hook, stored identifier or URL depends on it, and a rollback-capable migration passes both-site regression tests.

Before relevant implementation, capture sanitized fixtures for Blissz loop/single templates; Techgenyz publishing/product widgets; multiple product categories; comparisons; brand-company mappings; deals; REST responses; notifications; both `tgm-*` and `aps-*` product records; and legacy option-based specifications.

## Content Analytics / Legacy Post Views

Existing Techgenyz lifetime view totals are protected. Development 0.1.0 must re-inspect the source and verify the exact authoritative meta key or keys before migration. The `tgm_count_post_view` action and visible count behavior remain compatible while consumers depend on them.

No total may reset to zero. Import the genuine legacy lifetime value once as an immutable, auditable cutover baseline; baseline plus new accepted views equals displayed lifetime. Never fabricate historical time series, unique readers, referrers, devices, countries or campaigns. Migration must be idempotent, must not import the baseline twice, and must prevent the legacy and new collectors from double counting.
