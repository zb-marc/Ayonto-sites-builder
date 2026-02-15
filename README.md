# Ayonto Sites Builder

**Version:** 0.2.0 Build 081  
**Status:** ✅ Production Ready - Security Grade A (94/100) - WCAG 2.1 AA Compliant  
**Company:** Ayonto UG (Haftungsbeschränkt) — https://ayon.to

Professional WordPress plugin for battery management with Elementor integration and enterprise-grade security.

## 🎉 Latest: Complete Rebranding (v0.2.0)

The plugin has been completely rebranded from "Voltrana Sites Builder" to **"Ayonto Sites Builder"**. All namespaces, constants, function prefixes, CSS classes, option keys, and text domains have been updated. CPT and taxonomy prefixes (`vt_`) remain unchanged for backward compatibility with existing data.

### Recent Improvements (Build 075-081)

**Mobile & UI Optimizations:**
- ✅ Responsive battery comparison tables with mobile card layout
- ✅ Complete mobile gap fixes in battery tables
- ✅ Improved sidebar navigation with sticky TOC
- ✅ Enhanced admin help page with tabbed interface

**Security & Performance:**
- ✅ Security Grade A (94/100) - Production Ready
- ✅ All WCAG 2.1 Level AA accessibility requirements met
- ✅ Comprehensive CSRF protection and input validation
- ✅ Redis-compatible cache management

**Admin Experience:**
- ✅ Modern settings interface with 5-tab organization
- ✅ Professional dashboard with statistics & quick actions
- ✅ Ayonto brand design system across all admin pages
- ✅ CSV/XLSX import with validation and dry-run mode

**Technical Excellence:**
- ✅ Parent page system for flexible URL structures
- ✅ RankMath SEO integration with breadcrumbs
- ✅ Schema.org Product JSON-LD structured data
- ✅ Elementor Custom Query hooks and Dynamic Tags

See `UPDATE.md` for complete changelog and technical details.

## 🏗️ Architecture

### SINGLE SOURCE OF TRUTH
`ayonto-sites-builder.config.json` is the central configuration.

### Key Design Decision
**ONE Taxonomy, Everything Else as Meta Fields!**

```
✅ Taxonomy:
- vt_category (Categories only!)

✅ Meta Fields (NOT taxonomies!):
- brand
- series
- technology
- voltage_v
- capacity_ah
- cca_a
- dimensions_mm
- weight_kg
- ... and more
```

**Why?**
- Faster queries (fewer JOINs)
- Cleaner admin UI
- Best practice: Taxonomies only for real categorization

## 📦 Installation

1. Upload `ayonto-sites-builder` folder to `/wp-content/plugins/`
2. Activate via WordPress Plugins menu
3. Configure via Ayonto menu

## 🚀 Features

### Core Functionality
- ✅ Custom Post Type: `vt_battery` with comprehensive meta fields
- ✅ Single Taxonomy: `vt_category` (optimized architecture)
- ✅ Meta Fields for all technical specifications (brand, series, technology, voltage, capacity, etc.)
- ✅ Parent page system for flexible hierarchical URL structures
- ✅ 5 specialized metaboxes in the post editor
- ✅ CSV/XLSX import with validation, dry-run mode, and error handling

### Integrations
- ✅ **Elementor**: Custom Query hooks + 20+ Dynamic Tags for battery data
- ✅ **Rank Math SEO**: Auto-generated titles, descriptions, and breadcrumbs
- ✅ **Schema.org**: Product JSON-LD structured data for rich snippets
- ✅ **Redis**: Object cache support for high-performance setups

### Frontend
- ✅ Responsive battery comparison tables with mobile card layout
- ✅ GLightbox integration for product images
- ✅ Shortcodes: `[vt_battery_table]`, `[vt_battery_list]`, `[vt_spec_table]`
- ✅ Technology badges with color coding (AGM, GEL, EFB, LiFePO4)
- ✅ WCAG 2.1 Level AA accessibility compliant

### Admin Experience
- ✅ Professional dashboard with statistics and quick actions
- ✅ Modern 5-tab settings interface (General, Schema.org, Design, Colors, Frontend)
- ✅ Comprehensive help documentation system
- ✅ Ayonto brand design system with consistent UI/UX

### Security & Compliance
- ✅ **Grade A (94/100)** security score
- ✅ CSRF protection with nonce verification on all forms
- ✅ Input sanitization and output escaping throughout
- ✅ SQL injection protection with prepared statements
- ✅ File upload validation (MIME type + extension check)
- ✅ GDPR compliant - no external data transmission
- ✅ WordPress Coding Standards compliant

## 📚 Documentation

Comprehensive documentation available:
- **Single Source of Truth**: `ayonto-sites-builder.config.json`
- **Complete Changelog**: `UPDATE.md` (4000+ lines)
- **Testing Guide**: `TESTING.md`
- **Security Audit**: See project documentation

## 📄 License

GPL-2.0+ - See LICENSE file for details.
