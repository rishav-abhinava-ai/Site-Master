# The Blissz Compatibility Layer

Development 0.0.8 provides a Blissz-only Elementor adapter over Site Master's shared renderers. The adapter is registered only when the resolved profile is `blissz`; Techgenyz and unconfigured installations do not register the category or widgets.

## Evidence and fixtures

The compatibility inventory was derived from `Blissz Master v1.1.0 Production.zip`. `tests/fixtures/blissz-elementor-controls.json` records each legacy widget source path, source hash, section names, direct control IDs, group-control names, representative saved settings, and the classification assigned to every control. `LegacyControlInventory` is the executable copy checked against that fixture by the compatibility suite.

No production database or exported `_elementor_data` fixture was available. Consequently the suite proves source-derived saved-ID resolution and representative rendering with synthetic saved settings; it does not claim a live template migration test.

## Widget mapping

| Legacy widget ID | Compatibility behavior | Shared boundary |
| --- | --- | --- |
| `tbm_breadcrumb` | Renders visible breadcrumbs | `BreadcrumbRenderer` |
| `tbm-date-widget` | Preserves legacy date/time presets and view modes without AJAX | `DateFormatter` |
| `tbm-post-author-widget` | Renders the selected author, avatar, archive link, optional bio and post count | `AuthorData` and shared image output |
| `tbm-post-content` | Processes current post content once | `ArticleBodyRenderer` |
| `tbm_post_search` | Preserves the source widget's native GET search form | WordPress search URL and query variable |
| `tbz_loop_post_block` | Renders the current loop post as a Post Card; saved H1 is normalized to H3 | `PostCardViewModel` and `PostCardRenderer` |
| `blissz_loop_category_block` | Renders the current category entity from existing term metadata | `ImageRenderer` |
| `tbz_loop_single_post_block` | Renders the article header/media fragment with one H1 | `SingleArticleViewModel`, `ArticleHeaderRenderer`, and `ArticleHeroRenderer` |

All source-derived direct control IDs are registered so saved settings remain recognized. The structured `legacy-control-map.json` registry records source type, responsive status, default, option/condition presence, translated selectors, source-definition hash, classification, and one of `SUPPORTED`, `NORMALIZED`, `INTENTIONALLY_INACTIVE`, or `PERSISTENCE_ONLY`. Responsive controls use `add_responsive_control()`, confirmed top-level group controls use `add_group_control()`, and selector-bearing controls target current adapter/shared markup. Hidden controls are limited to intentional persistence-only entries. Normal Elementor wrappers remain enabled.

Post-content settings for alternate templates, inline related-post insertion, and advertisements are recognized but intentionally inactive. Restoring those source implementations would duplicate query, placement, and advertisement behavior outside the current shared architecture. The single-post `enable_schema` setting is recognized and ignored because Rank Math exclusively owns schema and SEO metadata.

## Article boundary

The single-post adapter emits the header/media fragment and the content adapter emits the body. A saved Elementor layout must place them in the same native parent container configured with HTML tag `article`. This produces one article containing one H1 and one body without cross-widget opening or closing tags. The adapters do not create invalid cross-widget markup or a second article. Static fixture composition is covered; live legacy template structure remains unverified without exported `_elementor_data`.

## Avatar compatibility

Legacy Blissz source stores the custom author attachment ID in `tbm_profile_picture`. Under the Blissz profile, reads use this order:

1. `tbm_profile_picture`
2. `profile_picture` as a non-destructive fallback

Writes update only `tbm_profile_picture`. The adapter never copies, synchronizes, deletes, or rewrites either key. If both keys exist, the legacy Blissz key wins. Attachment validation and normal avatar fallback remain in `AuthorData`.

## Retired source behavior

The adapter does not restore legacy microdata, JSON-LD, Rank Math overrides, per-instance scripts, global frontend assets, or the date widget's AJAX fan-out. The old `tbm_get_local_post_date` endpoint has no migrated caller and is not registered. Registration and rendering perform no persistent migration or production-data write.

## Control-registry totals

| Measure | Verified count |
| --- | ---: |
| Direct top-level controls | 408 |
| Responsive direct controls | 178 |
| Top-level group controls | 65 |
| Selector-bearing style controls | 263 |
| Functional non-style controls with explicit treatment | 67 |
| Intentionally inactive controls | 20 |
| Persistence-only controls | 78 |

The raw source contains additional control calls inside repeater definitions. Those are repeater subfields, not independent widget-level controls, and are not counted or registered as top-level saved controls.

## CSS regeneration compatibility

| Widget | Style / responsive / group controls | Selector translation | Status |
| --- | --- | --- | --- |
| `tbm_breadcrumb` | 22 / 15 / 5 | `.sm-breadcrumbs` and descendants | STATICALLY VERIFIED |
| `tbm-date-widget` | 6 / 4 / 2 | `.elementor-post-date-wrapper` and `time` | STATICALLY VERIFIED |
| `tbm-post-author-widget` | 24 / 17 / 9 | `.tbm-post-author`, avatar, name and bio | STATICALLY VERIFIED |
| `tbm-post-content` | 15 / 11 / 4 | `.tbm-post-content-wrapper` | STATICALLY VERIFIED; inactive inserted-content styles excluded |
| `tbm_post_search` | 37 / 24 / 8 | form, search input and submit button | STATICALLY VERIFIED |
| `tbz_loop_post_block` | 77 / 57 / 18 | `.sm-post-card.tbz-lpb` component families | STATICALLY VERIFIED |
| `blissz_loop_category_block` | 19 / 14 / 4 | `.blcb`, media and badge | STATICALLY VERIFIED |
| `tbz_loop_single_post_block` | 63 / 36 / 15 | `.tbz-lspb` article component families | STATICALLY VERIFIED |

Static verification exercises actual adapter registration methods with narrow Elementor doubles and proves control method, selector presence, current DOM target, and group registration. **LIVE ELEMENTOR CSS REGENERATION: NOT RUN.**

## Functional compatibility matrix

| Widget | Supported | Normalized | Intentionally inactive / persistence-only |
| --- | --- | --- | --- |
| Breadcrumb | Shared visible breadcrumb rendering and selector styles | Rank Math output is sanitized | Separator/content truncation controls remain persistence-only where shared output cannot reproduce them safely |
| Date | Presets, date/time view and style controls | Synchronous `DateFormatter` output | AJAX and per-widget scripts retired |
| Author | Override, avatar, name/prefix, bio, count and styles | Shared author/archive/avatar boundaries | Unsupported legacy link/name variants remain persistence-only |
| Content | One-pass content and base content styles | Shared `ArticleBodyRenderer` | Alternate templates, related insertion and advertisements intentionally inactive; values preserved |
| Search | Placeholder, accessible label, submit visibility/text and styles | Deterministic instance-specific input ID | Decorative icon controls without adapter markup remain persistence-only |
| Loop Post | Image, category, title visibility/link, excerpt, author/avatar, date format, reading time, read-more button and styles | H1 normalized to reusable-card H3; category source uses non-writing primary-category resolution | Decorative icon/layout controls without shared equivalents remain persistence-only |
| Loop Category | Image/link, badge/link and styles | Existing protected category metadata is read only | Decorative icon controls without adapter markup remain persistence-only |
| Single Post | Image, category, summary, author/avatar, date format, reading time, optional link button and styles | Title remains the canonical H1; schema disabled; category source is read-only; top-meta/divider placement follows shared header | Legacy schema intentionally inactive; unsupported decorative/layout controls remain persistence-only |

Every inactive or persistence-only entry remains registered in the structured registry with widget ID, control ID, legacy classification, status, and source-definition hash. Saved `_elementor_data` is not rewritten.

## Validation boundary

Automated coverage verifies profile isolation, all eight widget IDs, the complete source-derived control inventory, representative rendering, avatar precedence and write-key selection, native article composition, one content-filter pass, no legacy AJAX registration, and absence of schema/microdata output. Live WordPress, Elementor editor loading, actual saved-template rendering, responsive browser comparison, and runtime log review require a working WordPress database and remain not run in this environment.
