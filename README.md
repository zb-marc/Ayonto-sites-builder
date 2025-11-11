# Voltrana Sites Builder

**Version:** 0.1.0 Build 008  
**Status:** ✅ Production Ready - Root-Level URLs!

Professional WordPress plugin for battery management with Elementor integration.

## 🎉 Latest Updates (Build 008)

**Root-Level URLs & Simplified Breadcrumbs:**
1. ✅ **Root-Level URLs** ohne `/batterie/` Präfix
2. ✅ **Kürzere URLs:** `/golfcarts` statt `/batterie/golfcarts`
3. ✅ **Breadcrumbs ohne Kategorie** - nur Parent-Seite
4. ✅ **Cleaner Navigation** für bessere UX

**URL-Struktur:**
```
Ohne Parent: /golfcarts
Mit Parent:  /loesungen/golfcarts
```

**Previous Updates (Build 007):**
5. ✅ **Wählbare Parent-Seite** für flexible URLs
6. ✅ **Batterie-Icon** sichtbar

**Previous Updates (Build 005):**
7. ✅ **Fixed tote Links** im Admin-Menü
8. ✅ **Renamed:** "Batterien" → "Lösungen"

## 🏗️ Architecture

### SINGLE SOURCE OF TRUTH
`voltrana-sites-builder.config.json` is the central configuration.

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

1. Upload `voltrana-sites-builder` folder to `/wp-content/plugins/`
2. Activate via WordPress Plugins menu
3. Configure via Voltrana menu

## 🚀 Features

- ✅ Custom Post Type: `vt_battery`
- ✅ One Taxonomy: `vt_category`
- ✅ Meta Fields for technical data
- ✅ **Parent-Seiten System** (flexible URLs)
- ✅ **5 Metaboxen** im Editor
- ✅ CSV/XLSX Import with validation
- ✅ Elementor Custom Query + Dynamic Tags
- ✅ Rank Math SEO Integration (mit Parent-Seiten Breadcrumbs)
- ✅ Schema.org Product JSON-LD
- ✅ Redis Cache Support
- ✅ Responsive Frontend
- ✅ WordPress Coding Standards

## 📚 Documentation

See `voltrana-sites-builder.config.json` for complete specifications.

## 🐛 Bug Reports

All known critical bugs have been fixed in Build 006!

Icons are now properly visible in the admin menu.

See `UPDATE.md` for detailed changelog.

## 📝 What's Next?

- Shortcodes vollständig implementieren
- Mehr Elementor Dynamic Tags (35+)
- WP-CLI Commands
- Landing Pages System

## 📄 License

GPL-2.0+
