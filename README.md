# Ayonto Sites Builder

**Version:** 0.1.37 Build 057  
**Status:** ✅ Production Ready - Security Grade A-

Professional WordPress plugin for battery management with Elementor integration.

## 🎉 Latest Updates (Build 057)

**CRITICAL HOTFIX:**
- ✅ **PHP Parse Error behoben** - Build 056 wurde sofort ersetzt
- ✅ **Fehlende schließende Klammer** in class-frontend.php gefixt
- ⚠️ **Build 056 nicht verwenden** - nur Build 057 ist stabil

**Previous Updates (Build 056):**
- ✅ **Doppelte Featured Image Ausgabe** behoben
- ✅ **Filter post_thumbnail_html** unterdrückt automatische Theme-Ausgabe
- ✅ **Kontrolle über Bilder** nur noch über Elementor Templates

**Major Features (Build 055):**
- ✅ **Produktbild-Upload** in Metabox "Batterien für diese Lösung"
- ✅ **GLightbox Integration** für Touch-freundliche Bildvergrößerung (Zoom, Drag)
- ✅ **Responsive Thumbnails** in Battery Tables
- ✅ **product_image Spalte** im [vt_battery_table] Shortcode
- ✅ **Fallback-Icon 📷** wenn kein Produktbild vorhanden

**Security Improvements (Build 054):**
- ✅ **Security Score: A- (90/100)** - von C+ (72/100) verbessert
- ✅ **MIME-Type Validation** für File Uploads
- ✅ **Path Traversal Protection** im Autoloader
- ✅ **GDPR-konforme** Datenschutz-Hinweise
- ✅ **WordPress Privacy API** Integration
- ✅ **uninstall.php** für saubere Deinstallation

**Admin Settings System (Build 045-047):**
- ✅ **5 Tabs** - General, Schema.org, Design, Colors, Frontend
- ✅ **Konfigurierbare Firmenangaben** (Name, URL, Logo, Marke)
- ✅ **4 Farben mit Color Picker** für Design-Anpassung
- ✅ **White-Label ready** - alle "Ayonto" Werte konfigurierbar
- ✅ **CSS-Variablen** für Farben im Frontend
- ✅ **Settings speichern** korrekt (Merge-Logik ohne Datenverluste)

**Content Features (Build 048-053):**
- ✅ **Additional Content Meta Field** mit HTML-Editor
- ✅ **Helper-Buttons** für HTML-Tags (H2-H6, P, Strong, Listen, Links, Tabellen)
- ✅ **Elementor Dynamic Tag** "Zusätzlicher Inhalt"
- ✅ **Shortcode [vt_additional_content]** für formatierte Inhalte
- ✅ **Tabellen-Support** mit vt-battery-table Styling
- ✅ **Custom List Icons** mit Ayonto-Logo
- ✅ **HTML-Sanitization** (wp_kses) für sichere Ausgabe

**RankMath Integration (Build 042-043):**
- ✅ **Schema Sync** - Batterien automatisch in RankMath
- ✅ **ItemList Schema** automatisch eingefügt
- ✅ **Schema-Duplikate** entfernt (sauberes JSON-LD)
- ✅ **Admin Notice** zeigt synchronisierte Batterien

**Previous Major Updates:**
- ✅ **Root-Level URLs** ohne `/batterie/` Präfix (Build 008)
- ✅ **Parent-Seiten System** für flexible URLs (Build 007)
- ✅ **5 Metaboxen** im Editor (Build 030-040)

## 🏗️ Architecture

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
- terminals
- warranty_months
- datasheet_url
- product_image (NEW in Build 055)
- additional_content (NEW in Build 048)
- ... and more
```

**Why?**
- Faster queries (fewer JOINs)
- Cleaner admin UI
- Best practice: Taxonomies only for real categorization

## 📦 Installation

**Requirements:**
- WordPress: 5.8 or higher
- PHP: 7.4 or higher
- Tested up to: WordPress 6.4

**Steps:**
1. Upload `ayonto-sites-builder` folder to `/wp-content/plugins/`
2. Activate via WordPress Plugins menu
3. Configure via Ayonto → Einstellungen menu
4. Set up company info, colors, and branding

## 🚀 Features

### Core Features
- ✅ Custom Post Type: `vt_battery`
- ✅ One Taxonomy: `vt_category`
- ✅ Meta Fields for technical data
- ✅ **Parent-Seiten System** (flexible URLs)
- ✅ **5 Metaboxen** im Editor mit Grid-Layout

### Import & Data Management
- ✅ CSV/XLSX Import with validation
- ✅ Dry-run mode for testing
- ✅ Normalization & term synonyms
- ✅ Duplicate detection (EAN/Model)
- ✅ Batch processing with locking

### Elementor Integration
- ✅ Custom Query Hook for Filters
- ✅ 35+ Dynamic Tags for Meta Fields
- ✅ Loop Support for Battery Listings
- ✅ Responsive Templates

### SEO & Schema
- ✅ Rank Math SEO Integration
- ✅ Breadcrumbs with Parent Pages
- ✅ Schema.org Product JSON-LD
- ✅ ItemList Schema für Kategorien
- ✅ Organization Schema (configurable)
- ✅ No duplicate schemas

### Frontend & Design
- ✅ Responsive Battery Tables
- ✅ **Produktbilder** mit GLightbox
- ✅ Additional Content Support
- ✅ Custom List Icons (SVG)
- ✅ Technology Badges (colored)
- ✅ Property Tags
- ✅ Mobile Card Layout (<768px)
- ✅ **Configurable Colors** (4 brand colors)

### Admin & Settings
- ✅ **5-Tab Settings Panel**
  - General (Company Info)
  - Schema.org (Organization)
  - Design (Typography)
  - Colors (4 Color Pickers)
  - Frontend (Display Options)
- ✅ Settings Helper Class
- ✅ White-Label Ready
- ✅ WordPress Media Library Integration
- ✅ Helper Buttons for HTML

### Performance & Security
- ✅ Redis Cache Support
- ✅ Cache Invalidation Hooks
- ✅ **Security Grade: A- (90/100)**
- ✅ CSRF Protection (Nonces)
- ✅ File Upload Validation
- ✅ Path Traversal Protection
- ✅ GDPR Compliant
- ✅ WordPress Privacy API

### Code Quality
- ✅ WordPress Coding Standards
- ✅ PHPDoc Complete
- ✅ Internationalization (i18n)
- ✅ Text Domain: 'ayonto-sites'
- ✅ Sanitization & Escaping
- ✅ Prepared SQL Statements

## 🎨 Shortcodes

### Battery Table
```php
[vt_battery_table]
[vt_battery_table category="starter" limit="10"]
[vt_battery_table columns="model,technology,capacity_ah,voltage_v,product_image"]
```

### Additional Content
```php
[vt_additional_content] // Displays additional content field
```

## 🔧 Dynamic Tags (Elementor)

Available in **Ayonto** group:
- Model, EAN, Brand, Series
- Technology, Capacity, Voltage, CCA
- Dimensions (L, W, H), Weight
- Terminals, Warranty
- Category (Name & URL)
- **Product Image** (NEW)
- **Additional Content** (NEW)
- Composed Tags (Dimensions Compact)
- HTML Renderer (Spec Table)

## 📚 Documentation

Full changelog available in `readme.txt`.

## 🐛 Known Issues

**None** - All critical bugs fixed in Build 057!

**Build 056** should NOT be used (PHP Parse Error).

## 📝 What's Next?

Planned features for future builds:
- More Elementor Dynamic Tags
- WP-CLI Commands (vt import, vt import:preview)
- Landing Pages Auto-Creation
- Advanced Filtering
- Extended Schema Types
- PDF Generation

## 🔐 Security

**Current Grade: A- (90/100)**

Security measures:
- ✅ MIME-Type Validation
- ✅ File Extension Checks
- ✅ Path Traversal Protection
- ✅ CSRF Nonce Verification
- ✅ Input Sanitization
- ✅ Output Escaping
- ✅ Prepared Statements
- ✅ Capability Checks

## 📄 License

GPL-2.0 or later

## 👨‍💻 Author

**Marc Mirschel**  
Website: [https://ayon.to](https://ayon.to)

---

**Tags:** battery, elementor, batteries, meta-fields, custom-post-type, seo, schema-org, rankmath, wordpress-plugin

**Contributors:** marcmirschel
