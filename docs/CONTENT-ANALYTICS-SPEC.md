# Site Master Content Analytics Specification

Status: implemented in Development `0.1.0`; see `CONTENT-ANALYTICS-ARCHITECTURE.md` for the durable runtime contract. This document is not version authority.

## Purpose and profiles

Content Analytics is a shared, privacy-conscious, first-party editorial analytics capability, not a replacement for every GA4 feature. Techgenyz will enable it during migration and preserve its genuine historical lifetime post-view totals. The Blissz can opt in later but tracking is disabled by default. An unconfigured Site Master installation never tracks.

## V1 navigation and reports

The planned admin hierarchy is Site Master → Analytics → Overview, Content, Realtime, Authors, Taxonomies, Acquisition, Settings, plus a per-post **View Analytics** drill-down.

Overview supports views today, yesterday, last 7/30/90 days, period change, distinct posts viewed, estimated active readers and estimated daily unique readers. Charts use hourly buckets for today, daily buckets for 7/30/90 days, and adaptive custom ranges. Page views and estimated readers are distinct metrics.

Ranked content reports support post, views, estimated daily readers where applicable, today/24-hour/7-day/30-day values, change, publication date, author and category, filterable by date range, post, author, category, tag and explicitly supported post type.

Trending is recent traffic velocity, not lifetime rank. It considers 15-minute, 60-minute, 6-hour and 24-hour windows against an appropriate preceding/baseline period. Development 0.1.0 must define and test the formula before displaying a score.

Realtime remains aggregate: estimated active readers, views in the last 5/30/60 minutes, trending/recent posts, top categories and top referrer domains. It never exposes individual visitor timelines.

Post drill-down supports lifetime views, immutable legacy baseline where applicable, views since cutover, today/yesterday/7/30/90 days, first hour/6 hours/24 hours/7 days, peak hour/day, time series, referrers, device and country where available. Launch-window aggregates must remain calculable independently from lifetime traffic.

Author reports use WordPress authors and include published posts, selected-period total, average per post, top/trending posts and time series. Category/tag reports use existing relationships and include views, posts viewed, average, period change, top posts and top authors. Analytics never creates duplicate ownership or taxonomy metadata.

## Acquisition and dimensions

Store sanitized aggregate referrer host/domain by default, including direct/unknown, Google, Bing, Facebook, X, Reddit and other observed domains. HTTP referrers cannot reliably distinguish Google Search, Google News and Google Discover; Search Console integration is future scope.

V1 supports bounded, sanitized `utm_source`, `utm_medium` and `utm_campaign`. `utm_content` and `utm_term` are optional later dimensions; arbitrary unlimited query strings are forbidden.

Device classification is limited to desktop, mobile, tablet and other/unknown. Do not retain complete User-Agent strings indefinitely. Country is permitted only through an efficient approved source; otherwise use `unknown`. Never make a blocking remote geolocation request per view, store raw IP, or track precise location.

## Privacy model

V1 requires no analytics cookie. It stores no raw IP, permanent cross-site ID, permanent cross-day profile, cross-site tracking, session replay, fingerprint profile or precise location history.

An estimated daily unique reader may use a keyed one-way hash of a request network identifier, normalized client information, a site secret and the current analytics day. It rotates daily; inputs are not stored; hashes never appear in dashboards or exports. This is an estimate, not a human identity.

Events use UTC. Editor reporting and buckets use the WordPress site timezone with DST boundaries handled explicitly; browser-local time is not authoritative.

## Accepted views, bots and staff

Development 0.1.0 must formalize and test the accepted-view definition. Intended acceptance requires published supported editorial content, a valid first-party request, no excluded bot/staff classification, and no obvious retry. Do not count wp-admin, unrelated REST browsing, feeds, previews, drafts, revisions, known crawlers, health checks or monitoring requests.

A short retry/dedup window may suppress repeated beacons but is not the daily-unique definition. Bot filtering covers known search, SEO, monitoring, identifiable AI and spam/scanner agents. Classification changes require documented migration/reprocessing behavior where feasible rather than silent deletion.

Settings must support excluding administrators, editors, all logged-in users, or including logged-in users. Techgenyz defaults to excluding administrators and editors.

## Legacy Techgenyz preservation

Before implementation, re-inspect the legacy source and identify the exact authoritative view meta key or keys—never infer them from documentation. Import each genuine lifetime total once as an immutable baseline with cutover timestamp/version. Displayed lifetime equals baseline plus new accepted views.

Never reset totals or fabricate historical hourly/daily traffic, readers, referrers, device, country or campaign data. Migration is versioned, idempotent, auditable and cannot import a baseline twice.

Keep `tgm_count_post_view` while consumers depend on it. Development 0.1.0 must decide whether it forwards or remains a temporary adapter and must prove the legacy and new collectors cannot double count.

## Storage, ingestion and caching

Forbidden: per-view postmeta updates, synchronous multi-counter fan-out during page render, or analytics work that unnecessarily blocks the primary response.

Preferred flow: cached page render → tiny conditional first-party beacon → narrow lightweight append-only event ingestion → scheduled/batched aggregation → hourly/daily/dimension aggregates → dashboard queries. A persistent queue/object cache may optimize this, but Redis is not required.

Dedicated table names, fields and indexes are designed in 0.1.0. Concepts include short-retention events, hourly/daily/dimension aggregates, uniqueness/dedup data, schema version and cutover/baseline metadata. `dbDelta()`-appropriate upgrades must be versioned, idempotent, non-destructive and rollback-aware. Raw event retention is bounded.

The collector strictly validates supported published post IDs, rate/abuse limits and bot/staff policy; returns a minimal response; leaks no secret; and permits no arbitrary database write. Public collection exposes no reports. Tracking assets load only when the module is enabled and applicable, and analytics failure never breaks frontend rendering.

## Permissions and export

Settings require `manage_options`. Full site reports require a reviewed editorial capability such as `edit_others_posts`. A future own-post report may use `edit_post`. Report APIs are capability protected. CSV exports require capability, nonce and validated filters and contain aggregate top-post, daily, author, category, referrer or campaign reports only—never visitor hashes or raw event dumps.

## Locked V1 scope

Lifetime/baseline views; today/yesterday and 7/30/90-day comparisons; estimated daily readers; realtime 5/30/60; top/trending/post and launch-window analytics; author/category/tag analytics; referrer domains; UTM source/medium/campaign; device; efficient country or unknown; bot and staff exclusion; filters/comparisons; aggregate CSV.

## Future scope, not required for Site Master 1.0.0

Scroll depth, engaged time, outbound clicks, heatmaps, replay, persistent journeys, cross-device identity, ecommerce, funnels, automatic evergreen/breaking classification, spike notifications, normalized post comparison, Search Console, Google Discover/News attribution, search terms and browser/OS reports.
