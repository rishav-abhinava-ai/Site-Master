# Tests

`blissz-compatibility-smoke.php` verifies the Development 0.0.8 Blissz-only adapter, legacy widget/control resolution, representative rendering, article composition, avatar-key coexistence policy, profile isolation, and prohibited-output boundaries against the source-derived fixture in `tests/fixtures/`.

`techgenyz-publishing-compatibility-smoke.php` verifies the Development 0.0.9 Techgenyz-only editorial adapters, source-derived control registry, responsive/group registration, selector translation, terms/date/author/content/search behavior, cross-profile author policy and prohibited-runtime boundaries.

Run the Development 0.0.2 scaffold checks with:

```text
php tests/scaffold-smoke.php
```

The suite validates constant and option profile resolution, safe invalid/unconfigured behavior, optional dependency absence and the non-booting reserved module registry.

Run Development 0.0.3 shared-core coverage with:

```text
php tests/shared-core-smoke.php
```

Run Development 0.0.4 Post Card/listing coverage with:

```text
php tests/post-card-listing-smoke.php
```

This covers normalized data, semantic/escaped low-DOM markup, heading contexts,
images and figures, category fallbacks, author/date/excerpt options, safe query
normalization, pagination, and global-post isolation.

Run Development 0.0.5 single-article coverage with:

```text
php tests/single-article-smoke.php
```

This verifies one real parent article/H1, nested header/hero/body/footer,
single-pass WordPress content processing, meaningful modified dates, responsive
unlinked hero output, topics, escaping, optional-wrapper omission, and the
Article Start/End prohibition.

Run Development 0.0.6 page/accessibility coverage with:

```text
php tests/page-accessibility-smoke.php
```

This verifies non-article static pages, one-main/H1 composition, breadcrumb and
skip-link placement, author H1/H2/H3 hierarchy, shared author/query/card/listing
and pagination reuse, empty states, and sibling article/sidebar semantics.

Automated QA infrastructure is introduced in a later authorized development phase.

Do not treat the absence of the future test suite in Development 0.0.1 as permission to skip validation.

Run Development 0.0.7 media/static performance coverage with `php tests/media-cwv-smoke.php`. This suite uses controlled WordPress stubs and DOM assertions; it does not measure browser CWV or prove live WordPress output.
`content-analytics-smoke.php` verifies Development 0.1.0 profile defaults, baseline key/normalization, daily hash rotation, privacy normalization, bot/device classification, trend/comparison helpers, append-only hygiene, browser-storage prohibition, bounded SQL, transactional cursor/lock behavior, legacy AJAX registration, and collector size/allowlist gates.

`schema-recovery-smoke.php` verifies physical-table readiness independently from the schema-version option, healthy/stale/missing-table/failed-repair behavior, version persistence after successful verification only, and non-destructive repeated installation.
