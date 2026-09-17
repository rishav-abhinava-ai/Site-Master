# Site Master Content Analytics Architecture

Implemented in Development `0.1.0`. Version authority remains `SITE_MASTER_DEVELOPMENT_VERSION` in `site-master.php`.

## Flow and profiles

An eligible cached editorial post enqueues `assets/js/content-analytics.min.js`. It sends one first-party POST to `/wp-json/site-master/v1/analytics/collect`; ingestion validates profile, post, token, origin, staff/bot policy and a bounded allowlist, then performs one append-only event insert. WP-Cron aggregates bounded batches. Failure is isolated from article rendering.

Techgenyz defaults enabled and supports baseline import plus the `tgm_count_post_view` adapter. Blissz defaults disabled but can be enabled. Unconfigured profiles can never collect. Every event, aggregate, baseline and state row includes the profile.

## Storage, indexes and aggregation

Schema version `1` creates the prefixed tables `sm_analytics_events`, `sm_analytics_hourly`, `sm_analytics_daily`, `sm_analytics_dimensions`, `sm_analytics_post_totals`, `sm_analytics_legacy_baseline`, and `sm_analytics_state`. Event indexes cover profile/time, profile/post/time and profile/day/reader; a unique profile/dedupe key collapses concurrent retries. Aggregate primary keys match report buckets. Upgrades are non-destructive and run on activation or admin upgrade checks.

The five-minute worker obtains a database advisory lock and starts a transaction. It reads at most 500 events after the durable cursor, writes hourly/daily/post/dimension aggregates, advances the cursor in the same transaction, and rolls back on failure. Dimensions are author, category, tag, referrer, UTM source/medium/campaign, device and country; dimensions report views only.

Past-day readers are finalized with `COUNT(DISTINCT reader_hash)` before raw rows become prune-eligible. Raw retention defaults to 72 hours (configurable 48–168); deletion is capped at 5,000 and requires aggregation and day finalization. Aggregate, cumulative and baseline rows remain retained in V1. Events and hour buckets use UTC; report days and administrator ranges use `wp_timezone()`.

## Privacy and collection

No cookie, browser storage, fingerprint, reader account, raw IP, complete User-Agent, full referrer URL or precise location is stored. A binary 128-bit reader estimate is HMAC(`transient IP|normalized UA`, HMAC(site auth salt, UTC day)); it rotates daily. A separate HMAC binds profile, post, identity and a 60-second bucket for dedupe. Inputs and hashes are never reported or exported.

Referrers become bounded hostnames. Only five bounded UTM fields are accepted. Device is desktop/mobile/tablet/unknown. Country is `ZZ` unless an administrator configures a trusted server/edge header; forwarding headers are not trusted by default. Known bots, empty UAs, HEAD/purpose requests and logged-in readers (by default) are rejected. Bot filtering is best-effort.

The cache-safe token is an HMAC of profile and post ID and is not reader-specific. Payloads are capped at 2 KiB. The script uses `sendBeacon` with keepalive `fetch` fallback and fails silently.

## Baseline, compatibility and metrics

Reinspection of Techgenyz Master 1.0.5 established `post_views_count` as the only editorial lifetime key. Techgenyz imports published-post values in batches of 250 with `INSERT IGNORE`; invalid/negative values become zero, duplicate rows resolve to the greatest valid count, source metadata remains untouched, and durable state prevents inflation. The first run records the UTC cutover. Version 1.0.2 contains no editorial view counter or fallback key.

Both `tgm_count_post_view` actions remain Techgenyz-only. They preserve `nonce`, `post_id`, the JSON envelope and `counted`/`views` fields, but share event persistence and dedupe with the beacon and never mutate postmeta. An apparent old tracker produces an administrator warning; Site Master does not disable third-party code.

Lifetime is immutable baseline plus measured cumulative views. Realtime derives 5/30/60-minute counts from retained events. Trending excludes lifetime: `((recent + 1) / (previous + 1)) * log10(day views + 1)`. Comparisons use equal periods and report an undefined percentage when a zero prior period grows. Launch metrics are unavailable before cutover and partial until the requested window closes.

## Administration and limits

Site Master → Analytics contains Overview, Content, Realtime, Authors, Taxonomies, Acquisition and Settings plus a per-post box. Reports default to `edit_others_posts` (filterable), settings require `manage_options`, and per-post data requires `edit_post`. CSV is aggregate-only, nonce/capability protected, limited to 366 days and 100 content rows, and guards formula injection. Reader hashes and raw rows are never exported.

Rank Math remains the only schema/SEO owner. Analytics does not mutate editorial content, terms, users, dates, media, Elementor or Rank Math data. V1 has no visitor journey, historical reconstruction, product/deal tracking, remote enrichment or third-party analytics. UAT must cover real baselines, cache delivery, dedupe, WP-Cron, timezone/DST, permissions, growth and logs.
