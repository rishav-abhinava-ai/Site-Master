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

Content Analytics is a shared first-party editorial analytics capability with dedicated analytics storage. It will preserve Techgenyz legacy lifetime post-view totals while replacing per-view postmeta updates. The Blissz can use the shared module later, but tracking is disabled by default and must be explicitly enabled. The architecture minimizes personal data and is specified in `CONTENT-ANALYTICS-SPEC.md`; runtime implementation is reserved for Development `0.1.0`.

## Version model

Development starts at `0.0.1`.

First successful production release is `1.0.0`.

The tracks are independent.

## Current foundation scope

Development 0.0.3 provides shared SVG security, author/avatar data, exact date formatting, the Rank Math boundary, visible breadcrumb integration, and RSS featured-image compatibility.

Feature implementation belongs to later authorized development phases.
