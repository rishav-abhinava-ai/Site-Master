# Tests

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

Automated QA infrastructure is introduced in a later authorized development phase.

Do not treat the absence of the future test suite in Development 0.0.1 as permission to skip validation.
