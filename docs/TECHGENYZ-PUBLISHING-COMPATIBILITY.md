# Techgenyz Publishing Compatibility

Development 0.0.9 migrates the editorial Elementor boundary from `Techgenyz Master v1.0.5`. Version 1.0.2 is historical evidence only. The fixture and registry are source-derived; no production `_elementor_data` was available.

## Profile and scope

The adapters register only for the explicit `techgenyz` profile. Blissz retains its 0.0.8 adapters and avatar policy. An unconfigured profile registers neither compatibility layer.

This phase excludes products, comparisons, companies, brands, deals, REST APIs, API-key authentication, Firebase, IP controls, Amazon integrations, post-view ingestion and Content Analytics.

## Widget inventory

| Widget ID | Source behavior | Current boundary |
| --- | --- | --- |
| `post_terms` | Editorial category/tag terms | Read-only WordPress terms adapter |
| `tgm-date-widget` | Published date formatting | `DateFormatter`, server-rendered |
| `tgm-post-author-widget` | Author identity and media | `AuthorData` using `profile_picture` |
| `tgm-post-content` | Post body plus legacy insertion systems | `ArticleBodyRenderer`, exactly one content pass |
| `tgm_post_search` | Search UI with legacy popup/script modes | Accessible native GET search form |

## Control totals

| Measure | Count |
| --- | ---: |
| Direct controls | 178 |
| Responsive controls | 93 |
| Group controls | 65 |
| Controls with explicit defaults | 43 |
| Controls with options | 40 |
| Controls with conditions | 21 |
| Active translated selector controls | 127 |
| Intentionally inactive controls | 19 |
| Persistence-only controls | 8 |
| Repeaters / repeater subcontrols | 2 / 9 |

Every direct control has `SUPPORTED`, `NORMALIZED`, `INTENTIONALLY_INACTIVE`, or `PERSISTENCE_ONLY` status in `legacy-control-map.json`. The corrected map stores actual source-derived defaults, option maps, `condition`/`conditions` arrays, selectors and repeater subcontrols. A missing default remains distinct from an explicitly empty default. Definition hashes cover the complete evaluated Elementor argument array.

The earlier literal-call inventory reported 100 direct controls, 39 responsive controls and 21 groups. Evaluating the actual registration methods exposed the dynamically generated post-content tag/media controls, producing the corrected totals above.

## Functional matrix

| Widget | Supported | Normalized | Inactive / persistence-only |
| --- | --- | --- | --- |
| `post_terms` | Taxonomy, include/exclude, selection order, order, separator and links | Saved post type resolves against the current editorial post | Unsupported presentation-only values remain persisted |
| `tgm-date-widget` | Verified presets, date/time views and style selectors | Client-relative display becomes stable server-rendered output | AJAX and inline formatter scripts retired |
| `tgm-post-author-widget` | Override, avatar, name, prefix, bio, count and styles | Name formats/tags/custom links use the shared safe author archive presentation | No Person schema |
| `tgm-post-content` | Body and base text styles | One shared content pass | Related templates/insertion and advertisements intentionally inactive; values preserved |
| `tgm_post_search` | Placeholder, accessible label and input/form styles | Popup/display modes become a native crawlable GET form | Decorative icon-only controls persist without script/duplicate UI |

## CSS compatibility

Verified source controls use `add_control()`, `add_responsive_control()` and `add_group_control()` according to their original top-level registration. Selector-bearing controls are translated to `.elementor-post-terms-wrapper`, `.elementor-post-date-wrapper`, `.tgm-post-author-widget`, `.tgm-post-content`, or `.tgm-post-search` adapter markup. Normal Elementor wrappers remain enabled.

Static tests instantiate the adapters with narrow Elementor doubles and verify control methods, group registration, selectors and actual render targets. **LIVE ELEMENTOR CSS REGENERATION: NOT RUN.**

## Inactive style isolation

Related-post behavior, alternate templates and advertisements remain intentionally inactive. Their saved control definitions, conditions, options and repeater fields remain structurally representable, while frontend selectors are removed. Seven related-post selector-bearing controls and two related-post group selectors cannot style `.tgm-post-content` or its article body.

The retired search-icon controls remain persistence-only. Five selector-bearing icon controls have no active compatibility selectors and cannot style the surviving search form. No fake icon or hidden related-post DOM was added.

The two inactive repeaters preserve nine named subcontrols and source-derived representative default rows in the fixture. Static tests verify subcontrol keys, ordering, values and registration fields. **STATIC STRUCTURAL ROUND-TRIP VERIFIED. LIVE ELEMENTOR EDIT/SAVE ROUND-TRIP NOT RUN.**

## Author media

Techgenyz reads, writes and removes only `profile_picture`. It does not give `tbm_profile_picture` precedence and does not synchronize either key. Removal deletes only the relationship, never the Media Library attachment. WordPress avatar fallback remains active. Blissz continues to read `tbm_profile_picture` first with `profile_picture` fallback and writes only its Blissz key.

## Retired behavior and ownership boundaries

The adapters emit no schema, microdata, JSON-LD, canonical, robots, social metadata or sitemap override. Rank Math remains authoritative. `tgm_get_local_post_date` and `tgm_count_post_view` are not registered. No tracking, view increment, persistent migration, remote request, REST route, Firebase credential or product/platform runtime is introduced.

## UAT carry-forward

Later UAT must use real saved Techgenyz Elementor templates and verify all five widget IDs, saved control population, CSS regeneration, desktop/tablet/mobile output, terms, author avatars, dates, search accessibility, single-article and listing composition, console output, `debug.log`, PHP logs, and Rank Math metadata/schema. Static compatibility is not production approval.

Current environment status: live WordPress/Elementor not run; real `_elementor_data` not verified; live CSS regeneration not run; browser comparison and runtime log review not run.
