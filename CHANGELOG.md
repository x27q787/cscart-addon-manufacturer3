# Changelog

## 3.0.0 — 2026-10-08

### Major architectural overhaul

#### Breaking changes
- **Full drop of the CMS Pages dependency.** The addon no longer uses `?:pages`
  (`page_type = 'M'`), the `manufacturer_elem_type` column or any of the core
  page hooks/controllers/templates. Removed:
  - `controllers/backend/pages.pre.php`, `controllers/backend/pages.post.php`
  - `controllers/frontend/pages.post.php`
  - `design/backend/templates/addons/manufacturer/hooks/pages/*`
  - `design/backend/templates/addons/manufacturer/overrides/views/pages/*`
  - `schemas/clone/*`, `schemas/seo/canonical_urls.php` (old pages-based)
  - RSS/`sanitize_html`/`generate_rss_feed` page integration
- **Independent database structure.** New tables created on install:
  - `cscart_nomenclature_nodes` — the nomenclature (manufacturer catalog) tree:
    `node_id`, `parent_id`, `id_path`, `status`, `position`, `timestamp`
  - `cscart_nomenclature_node_descriptions` — multilingual node data:
    `name`, `description`, `seo_name`
  - `cscart_nomenclature_links` — many-to-many `product_id` ↔ `node_id` links
- The old `manufacturer_elem_type` page column is not touched anymore: on
  upgrade it is simply left alone (no ALTER, no drop).

#### New admin area
- New independent controller `controllers/backend/nomenclature.php` with
  `manage`, `update`, `delete`, `delete_range` modes (no pages inheritance).
- New menu item under Website → «Nomenclature» (`nomenclature.manage`), fully
  decoupled from the Pages menu.
- New backend templates `design/backend/templates/addons/manufacturer/views/nomenclature/`:
  - `manage.tpl` — tree with statuses, product counts, bulk delete
  - `update.tpl` — clean form: node name, parent node, SEO name (ЧПУ), status,
    position, description, image, and a «Linked products» tab with the product
    picker (many-to-many)

#### Dynamic storefront
- `controllers/frontend/nomenclature.php` + `views/nomenclature/view.tpl`.
- The product group page renders a **dynamic table**: article (SKU), technical
  parameters (product features), price and an «Add to cart» button per row.
- Features and their values are resolved at runtime from `product_features`,
  `product_features_values` and `product_feature_variants_descriptions` for the
  products linked to the node (including child nodes) — **no static JSON tables**.
- Existing UniTheme2 / responsive tile grid, sidebox list and scroller LESS/TPL
  blocks are kept and now link to `nomenclature.view?node_id=...`.

#### Misc
- Addon version bumped to `3.0.0` in `addon.xml`.
- Demo data (`database/demo.sql`) now seeds the new nomenclature tables instead
  of `?:pages`.
- New language variables (RU/EN) for the nomenclature admin and storefront.