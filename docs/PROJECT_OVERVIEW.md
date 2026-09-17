# Site Master — Project Overview

## Purpose

Build one shared production-grade WordPress plugin for Techgenyz and The Blissz.

## Migration sources

- Blissz Master v1.1.0
- Techgenyz Master v1.0.5
- Techgenyz Master v1.0.2 — historical/reference only

## Core goals

- one shared codebase
- explicit site profiles
- preserve existing data and persisted identifiers
- Rank Math as sole SEO/schema authority
- low-DOM semantic HTML
- Elementor compatibility
- responsive media
- high performance
- strong security
- repeatable development and production workflows

## Site Master Analytics / Content Analytics

Content Analytics is a shared first-party editorial analytics capability with dedicated, profile-partitioned storage. Development 0.1.0 replaces Techgenyz per-view postmeta writes with a tiny beacon, append-only ingestion and scheduled aggregation while preserving `post_views_count` once as an immutable baseline. Blissz tracking remains disabled by default and unconfigured sites cannot track. See `CONTENT-ANALYTICS-SPEC.md` and `CONTENT-ANALYTICS-ARCHITECTURE.md`.

## Version model

Development starts at `0.0.1`.

First successful production release is `1.0.0`.

The tracks are independent.

## Current foundation scope

Development 0.0.6 added the shared single-main page shell, opt-in theme-compatible skip link, semantic sidebar, non-article static-page renderer and author archive/profile composition to the existing article and listing architecture.

Feature implementation belongs to later authorized development phases.

Development 0.0.7 hardens the shared attachment-image policy across cards, articles, static pages, custom avatars and RSS. See `MEDIA-POLICY.md` for normalized options, layout responsibilities and validation limits.

Development 0.0.8 adds the profile-gated Blissz Elementor compatibility layer. See `BLISSZ-COMPATIBILITY.md` for the source-derived widget/control inventory, avatar-key precedence, article composition requirement, retired behavior, and validation boundary.

Development 0.0.9 adds the profile-gated Techgenyz editorial publishing compatibility layer. See `TECHGENYZ-PUBLISHING-COMPATIBILITY.md` for its source-derived widget/control inventory, selector translations, author policy, inactive legacy behavior, and deferred live-UAT requirements.
