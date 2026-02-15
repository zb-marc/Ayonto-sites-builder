=== Ayonto Sites Builder ===
Contributors: ayonto
Tags: battery, elementor, batteries, meta-fields, custom-post-type
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 0.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Professional battery management system with Elementor integration. Architecture: ONE taxonomy (vt_category), everything else as Meta Fields!

== Description ==

Ayonto Sites Builder is a professional WordPress plugin for managing battery products with deep Elementor integration.

**Key Architecture Decision:**
* Only ONE taxonomy: vt_category (Categories)
* Brand, Series, Technology, Voltage are Meta Fields (NOT taxonomies!)
* Better performance, cleaner admin UI

**Features:**
* Custom Post Type: vt_battery
* One Taxonomy: vt_category
* Meta Fields for all technical data
* CSV/XLSX Import with normalization
* Elementor Custom Query & Dynamic Tags
* Rank Math SEO Integration
* Schema.org JSON-LD
* Redis Cache Support

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/ayonto-sites-builder/`
2. Activate the plugin through the 'Plugins' screen
3. Use Ayonto menu to configure settings

== Changelog ==

= 0.2.0 Build 081 - Complete Rebranding: Voltrana → Ayonto =
* REBRANDING: Complete plugin rename from "Voltrana Sites Builder" to "Ayonto Sites Builder"
* REBRANDING: All namespaces changed from Voltrana\Sites to Ayonto\Sites
* REBRANDING: All constants changed from VOLTRANA_SITES_ to AYONTO_SITES_
* REBRANDING: All function prefixes changed from voltrana_sites_ to ayonto_sites_
* REBRANDING: Text domain changed from voltrana-sites to ayonto-sites
* REBRANDING: All CSS classes changed from voltrana-* to ayonto-*
* REBRANDING: All option keys changed from voltrana_ to ayonto_
* REBRANDING: Plugin slug changed to ayonto-sites-builder
* REBRANDING: Domain changed to https://ayon.to
* REBRANDING: Company changed to Ayonto UG (Haftungsbeschränkt)
* REBRANDING: Admin menu slug changed from voltrana-root to ayonto-root
* NOTE: CPT (vt_battery) and taxonomy (vt_category) prefixes remain unchanged for data compatibility
* NOTE: Meta field keys (vt_*) remain unchanged for data compatibility
* MIGRATION: Existing installations require database option migration (see UPDATE.md)

= 0.1.61 Build 080 - Mobile Table Gap Fix (Complete) =
* FIXED: Large gap after battery table specifically on mobile devices
* IMPROVED: Removed margin-bottom from last table row in mobile view
* IMPROVED: Enhanced wpautop protection with placeholder system
* CSS: Set margin to 0 for .vt-battery-table-wrapper on mobile
* CSS: Added :last-child selector for tr elements to remove bottom margin
* CSS: Enhanced shortcode protection CSS for mobile devices
* JS: Added automatic cleanup of empty paragraphs and breaks around tables
* JS: Dynamic margin removal for mobile devices (< 768px)
* TECHNICAL: Improved protect_shortcodes_from_wpautop() with placeholder replacement
* RESULT: No gaps after tables on both desktop and mobile views

= 0.1.60 Build 079 - Battery Table Gap Fix =
* FIXED: Large gap after [vt_battery_table] shortcode caused by unwanted whitespace
* IMPROVED: Removed trailing whitespace in table rendering output
* IMPROVED: Added trim() to remove unnecessary spacing from shortcode output
* IMPROVED: Protected shortcodes from wpautop to prevent extra paragraph tags
* CSS: Adjusted .vt-battery-table-wrapper margins (removed bottom margin)
* CSS: Added .vt-shortcode-protect class to prevent wpautop interference
* TECHNICAL: Added protect_shortcodes_from_wpautop() filter at priority 9
* RESULT: Clean table display without unwanted gaps below the table

= 0.1.59 Build 078 - Help Page UI Improvement =
* UI IMPROVEMENT: Moved "Inhaltsverzeichnis" (Table of Contents) from content area to sidebar
* CHANGED: TOC now appears in sidebar below Plugin Information for better navigation
* IMPROVED: TOC styling updated to match sidebar design (consistent with vt-help-info)
* IMPROVED: More compact TOC design for better space utilization in sidebar
* REASON: Better overview and navigation - TOC always visible while reading documentation
* NOTE: TOC only shows for documents with 3+ headings

= 0.1.58 Build 077 - Help Page Critical Fixes =
* CRITICAL FIX: Fixed PHP Fatal Error in Help page (Parsedown::blockSetextHeader() missing)
* CRITICAL FIX: Added missing blockSetextHeader() method to Parsedown library
* UI IMPROVEMENT: Help page now shows only 3 main documents instead of all BUILD files
* CHANGED: Help documentation now displays: Overview (README), Changelog (UPDATE), Test Guide (TESTING)
* REMOVED: BUILD-*.md files from Help page (they are development artifacts)
* REASON: Cleaner UI and better user experience - users don't need to see every build file
* NOTE: All BUILD information is still available in UPDATE.md (Changelog)

= 0.1.57 Build 076 - EAN Display Bugfix =
* CRITICAL FIX: Removed duplicate 'case ean:' in Shortcodes class (dead code at line 485-487)
* FIX: Changed EAN output from <code> to <span> for semantic correctness
* REASON: <code> is meant for programming code, not for product identifiers
* NOTE: This fixes the incomplete implementation from Build 075
* INCLUDES: All improvements from Build 075

= 0.1.56 Build 075 - EAN Readability Improvements =
* IMPROVED: Enhanced EAN number readability in battery tables
* IMPROVED: Increased EAN font size from 10px to 13px for better visibility
* IMPROVED: Better contrast with darker text color (#181818 instead of #6b7280)
* IMPROVED: Stronger border color (#004B61) for EAN fields
* IMPROVED: Added specific CSS class (vt-value-ean) to EAN column output
* IMPROVED: Increased EAN column width from 130px to 145px
* REMOVED: Font-family definitions from frontend CSS (now using theme fonts)
* STYLE: Optimized EAN display with better padding and letter-spacing

= 0.1.55 Build 074 - Help Class Property Fix =
* CRITICAL FIX: Fixed "Cannot redeclare Help::$instance" fatal error
* FIX: Removed duplicate property declaration in Help class (line 38)
* INCLUDES: All fixes from Builds 071-073
* NOTE: Help page now fully functional

= 0.1.54 Build 073 - Help Page Fix =
* FIX: Fixed "permission denied" error when accessing Help page
* FIX: Converted Help class to use proper namespace (Ayonto\Sites\Admin\Help)
* FIX: Integrated Help initialization into main plugin init
* IMPROVED: Help class now uses singleton pattern like other admin classes
* INCLUDES: All fixes from Builds 071-072
* NOTE: Help page now accessible at Ayonto → Hilfe

= 0.1.53 Build 072 - CRITICAL HOTFIX #2 =
* CRITICAL FIX: Fixed undefined constant AYONTO_SITES_FILE error in Data_Retention class
* FIX: Corrected constant reference to use AYONTO_SITES_PLUGIN_FILE with proper namespace
* INCLUDES: All fixes from Build 071
* NOTE: Second critical hotfix - resolves all issues from Build 070

= 0.1.52 Build 071 - CRITICAL HOTFIX =
* CRITICAL FIX: Fixed autoloader namespace validation preventing main namespace classes from loading
* CRITICAL FIX: Resolved PHP Fatal Error "Class Post_Type not found"
* FIX: Adjusted namespace whitelist logic to allow root namespace classes
* FIX: Classes like Post_Type, Shortcodes, Activator, Deactivator now load correctly
* NOTE: This is a critical hotfix for Build 070 - all users must update immediately

= 0.1.51 Build 070 =
* SECURITY: Fixed XSS vulnerability in JavaScript context using wp_json_encode()
* SECURITY: Implemented rate limiting for import function (10 imports/hour)
* SECURITY: Added explicit namespace whitelist to autoloader
* SECURITY: Enhanced file size validation (max 10MB, min 100 bytes)
* NEW: Data Retention Policy for GDPR compliance (90-day auto-cleanup)
* NEW: Security Audit Logger for tracking critical actions
* IMPROVED: Better input validation and output escaping
* IMPROVED: Session security enhancements
* IMPROVED: Overall security score increased from B+ (87%) to A (94%)
* FIX: All critical security vulnerabilities resolved
* COMPLIANCE: Full GDPR/DSGVO compliance with automatic data cleanup
* DOCUMENTATION: Added security audit documentation

= 0.1.46 Build 065 =
* NEW: Moderne Settings-Page CSS (280+ Zeilen)
* NEW: Settings Enhancements JavaScript (Logo-Preview, Field-Icons)
* NEW: Section-Cards mit Gradient-Headers
* NEW: 2-Spalten-Layout für Settings auf Desktop
* NEW: Logo-Upload mit Live-Preview
* NEW: Moderne Input-Fields mit besseren Focus-States
* NEW: Field-Icons werden automatisch hinzugefügt
* NEW: Upload-Button-Group mit Remove-Option
* IMPROVED: Besseres Spacing und visuelle Hierarchie
* IMPROVED: Responsive Design für alle Screen-Größen
* IMPROVED: Color-Picker mit visueller Anzeige

= 0.1.45 Build 064 =
* REMOVED: Statistiken-Widget aus Dashboard entfernt
* REMOVED: Datenqualität-Widget aus Dashboard entfernt
* IMPROVED: Dashboard fokussiert auf Schnellaktionen, Recent Activity und System Status
* IMPROVED: Klareres, fokussierteres Dashboard-Layout
* IMPROVED: Reduzierte Animation-Delays (nur noch 3 Widgets)

= 0.1.44 Build 063 =
* NEW: Erweiterte Dashboard-Statistiken mit echten Daten
* NEW: Top 5 Marken-Statistik (fehlte komplett!)
* NEW: Kapazitätsbereich (Durchschnitt, Min, Max)
* NEW: Entwürfe-Zähler im Dashboard
* NEW: Datenqualität-Widget mit Debug-Informationen
* NEW: Post-Status-Übersicht (Publish, Draft, etc.)
* NEW: Fehlende Daten-Checker (Ohne Technologie, Ohne Marke, Ohne Kapazität)
* IMPROVED: Alle Statistiken verwenden jetzt echte Daten aus der Datenbank
* IMPROVED: Bessere SQL-Queries für Performance
* IMPROVED: Visuelle Warnung bei fehlenden Daten
* IMPROVED: Hilfreiche Tipps zur Datenqualität

= 0.1.43 Build 062 =
* NEW: Einheitliches Design-System für alle Ayonto Admin-Seiten
* NEW: Zentrale admin.css mit Ayonto Brand Colors und CSS Variables
* IMPROVED: Settings-Seite mit Ayonto-Branding (Cards, Tabs, Buttons)
* IMPROVED: Import-Seite mit Ayonto-Branding
* IMPROVED: Dashboard nutzt jetzt einheitliches Design-System
* IMPROVED: Konsistente Button-Styles auf allen Admin-Seiten
* IMPROVED: Konsistente Tab-Navigation mit Ayonto-Akzentfarbe
* IMPROVED: Alle Formulare mit Ayonto-Farben und Focus-States
* IMPROVED: Notice-Boxen im Ayonto-Style (Info, Success, Warning, Error)
* IMPROVED: Responsive Design für alle Admin-Seiten
* ADDED: CSS Variables für Ayonto Brand Colors (Primary: #004B61, Accent: #F79D00)
* ADDED: Utility-Klassen für konsistentes Spacing und Layout
* ADDED: Animationen für Card-Elemente (fadeIn mit staggered delay)
* ADDED: Hover-Effekte für Buttons und Interactive Elements
* TECHNICAL: Neue zentrale assets/css/admin.css (22KB)
* TECHNICAL: Dashboard lädt jetzt admin.css + admin-dashboard.css
* TECHNICAL: Settings und Import laden admin.css
* UX: Einheitliche Optik über alle Ayonto-Menüpunkte
* UX: Professionelleres Erscheinungsbild

= 0.1.41 Build 061 =
* NEW: Dashboard-Seite mit Statistiken, Quick Actions, Recent Activity und System Status
* NEW: Zentrale Übersichtsseite als Einstiegspunkt im Admin-Bereich
* IMPROVED: Optimierte Admin-Menüstruktur - CPT "Lösungen" jetzt unter Ayonto-Menü
* IMPROVED: Konsistente Parent-Slug-Struktur (alle Submenüs unter 'ayonto-root')
* IMPROVED: Dashboard zeigt Statistiken nach Technologie und Spannung
* IMPROVED: Dashboard zeigt letzte 5 bearbeitete Lösungen
* IMPROVED: Dashboard zeigt System-Status (PHP, WordPress, Plugins, Permalinks)
* IMPROVED: Quick Actions für häufige Aufgaben (Neue Lösung, Import, Einstellungen)
* FIXED: Statistiken filtern jetzt leere/Null-Werte bei Technologie und Spannung
* FIXED: "Neue Lösung" wird automatisch als Submenu-Item hinzugefügt falls fehlend
* FIXED: Dashboard immer als erstes Submenu-Item (Menü-Reihenfolge korrigiert)
* ADDED: Neues Dashboard-CSS mit Ayonto Branding (Primary: #004B61, Accent: #F79D00)
* ADDED: Responsive Widget-Grid-Layout mit Animationen
* TECHNICAL: Neue Klasse \Ayonto\Sites\Admin\Dashboard
* TECHNICAL: Custom CSS assets/css/admin-dashboard.css
* TECHNICAL: Menü-Reihenfolge-Fix-Funktion ayonto_sites_fix_admin_menu_order()
* UX: Dashboard als erste Submenu-Page (Position 0)
* UX: Verbesserte Admin-Navigation und Übersichtlichkeit

= 0.1.40 Build 060 =
* FIXED: CRITICAL - aria-hidden Console Warning komplett behoben (Build 059 fix unvollständig)
* FIXED: document.activeElement.blur() wird explizit aufgerufen beim Öffnen der Lightbox
* IMPROVED: Doppelte Absicherung via Click Event Listener auf alle .glightbox Links
* IMPROVED: Reduziertes setTimeout Delay (100ms → 50ms) für schnellere Focus-Verschiebung
* TECHNICAL: onOpen Event blur() + setTimeout focus() Kombination
* TECHNICAL: Zusätzlicher Click Handler mit 10ms Delay für sofortigen Focus-Removal
* RESULT: Keine aria-hidden Warnings mehr in Browser Console

= 0.1.39 Build 059 =
* FIXED: CRITICAL - Accessibility Warning in Browser Console behoben
* FIXED: "Blocked aria-hidden on element" Warnung beim Öffnen der Lightbox
* IMPROVED: Focus Management - Close Button erhält automatisch Focus beim Öffnen
* IMPROVED: Keyboard Navigation - Sichtbarer Focus Outline (orange) für Close Button
* IMPROVED: WCAG 2.1 Compliance - Screen Reader freundlich
* TECHNICAL: onOpen Event Handler in GLightbox für Focus-Verschiebung
* TECHNICAL: :focus und :focus-visible Styles für Close Button

= 0.1.38 Build 058 =
* IMPROVED: GLightbox Overlay Background jetzt in Ayonto Brand Color rgba(0, 75, 97, 0.70)
* IMPROVED: Close Button mit CSS-basiertem X-Icon (größer, besser sichtbar)
* IMPROVED: Close Button Hover-Effekt mit Rotation und Ayonto Accent Color (#F79D00)
* IMPROVED: Navigation Buttons (gnext/gprev) komplett ausgeblendet (nur 1 Bild pro Batterie)
* IMPROVED: Mobile-optimierte Close Button Größe (40px auf Mobile, 44px auf Desktop)
* TECHNICAL: GLightbox SVGs durch CSS ::before/::after ersetzt für bessere Kontrolle
* TECHNICAL: frontend.css erweitert um GLightbox Custom Styles Section

= 0.1.37 Build 057 =
* CRITICAL HOTFIX: PHP Parse Error in Build 056 behoben
* FIXED: Fehlende schließende geschweifte Klammer in class-frontend.php
* NOTE: Build 056 wurde sofort durch Build 057 ersetzt (nicht produktiv verwenden)

= 0.1.36 Build 056 =
* FIXED: Doppelte Featured Image Ausgabe auf Single Battery Pages
* NEW: Filter post_thumbnail_html für vt_battery Posts unterdrückt automatische Theme-Ausgabe
* IMPROVED: Featured Images werden nur noch über Elementor Templates/Content kontrolliert ausgegeben
* NOTE: Theme's automatisches Featured Image wird in main loop unterdrückt, bleibt in Widgets/Templates erhalten

= 0.1.35 Build 055 =
* NEW: Produktbild-Upload in Metabox "Batterien für diese Lösung"
* NEW: GLightbox Integration für Bildvergrößerung (Touch-freundlich, Zoom, Drag)
* NEW: product_image Spalte in [vt_battery_table] Shortcode
* IMPROVED: "Eigenschaften"-Spalte standardmäßig ausgeblendet (via columns Attribut)
* IMPROVED: Responsive Bild-Thumbnails in Battery Tables
* IMPROVED: Fallback-Icon 📷 wenn kein Produktbild vorhanden
* CHANGED: Metabox-Feld "Datenblatt-URL" durch "Produktbild" ersetzt
* ADDED: assets/css/glightbox.min.css
* ADDED: assets/js/glightbox-init.js
* NOTE: GLightbox JS aktuell von CDN (für Production lokal hosten)

= 0.1.34 Build 054 =
* SECURITY: MIME-Type Validation für File Uploads implementiert
* SECURITY: Autoloader Path Traversal Protection hinzugefügt
* SECURITY: Direct $_POST Access in Settings gesichert (sanitize_text_field)
* NEW: uninstall.php für saubere Plugin-Deinstallation
* NEW: WordPress Privacy API Integration
* NEW: PHP Version Requirement (7.4) in readme.txt
* IMPROVED: Vollständige Datei-Upload-Sicherheit (Extension + MIME-Type Check)
* IMPROVED: Defense-in-Depth für Autoloader (Regex Sanitization)
* IMPROVED: GDPR-konforme Datenschutz-Hinweise
* FIXED: Alle Medium-Risk Sicherheitslücken behoben
* Security Score: Von C+ (72/100) auf A- (90/100) verbessert

= 0.1.33 Build 053 =
* BUGFIX: CRITICAL - Additional Content Listen-Layout repariert
* BUGFIX: Strong-Text bricht nicht mehr in neue Zeile um
* BUGFIX: Entfernt display:flex von li-Elementen (verursachte Layout-Probleme)
* BUGFIX: Leerzeichen vor/nach Strong-Tags werden jetzt korrekt angezeigt
* IMPROVED: Inline-Display für Strong-Elemente forciert

= 0.1.33 Build 052 =
* FEATURE: Additional Content Styling - Custom List Icons mit Ayonto-Logo
* FEATURE: Listen verwenden jetzt SVG-Logo statt Standard-Bullet-Points
* IMPROVED: Konsistente Abstände für <ul> und <p> Elemente (20px)
* IMPROVED: Strong-Text Styling in Listen (#004B61, 600 font-weight)
* IMPROVED: Mobile-Responsive Anpassungen für Additional Content
* IMPROVED: Optimierte Lesbarkeit mit professioneller Typografie

= 0.1.32 Build 051 =
* BUGFIX: CRITICAL - Hover-Effekt bei Battery-Tables wieder lesbar
* BUGFIX: CRITICAL - Strong-Elemente auf Mobile jetzt lesbar (color: #fff)
* BUGFIX: Entfernt opacity: 0.1 die Text unsichtbar machte
* IMPROVED: Hover nutzt jetzt rgba(0, 75, 97, 0.05) für leichten blauen Effekt
* IMPROVED: Mobile-Ansicht: Strong-Texte jetzt weiß für bessere Lesbarkeit

= 0.1.31 Build 050 =
* IMPROVED: Battery-Table-Styling für Additional Content Tabellen
* IMPROVED: Tabellen nutzen vt-battery-table CSS-Klassen (kein Inline-CSS)
* IMPROVED: Konsistentes Design mit [vt_battery_table] Shortcode
* IMPROVED: Dunkler Header (#004B61), Box-Shadow, Hover-Effekte
* IMPROVED: Responsive Wrapper für optimale Mobile-Darstellung
* IMPROVED: Admin-Hinweise aktualisiert mit Styling-Info

= 0.1.30 Build 049 =
* IMPROVED: Tabellen-Support für Additional Content Field (table, thead, tbody, tr, th, td)
* IMPROVED: Helper-Button "📊 Tabelle" mit fertiger Vorlage
* IMPROVED: Tabellen nutzen vt-battery-table Styling (dunkler Header, Box-Shadow, Hover-Effekte)
* IMPROVED: Responsive Wrapper für mobile Ansicht
* IMPROVED: Vollständige HTML-Sanitization für Tabellen-Tags
* IMPROVED: Konsistentes Design mit [vt_battery_table] Shortcode

= 0.1.29 Build 048 =
* NEW: Additional Content Meta Field mit HTML-Editor
* NEW: Metabox "Zusätzlicher Inhalt" mit Textarea + Helper-Buttons
* NEW: Elementor Dynamic Tag "Zusätzlicher Inhalt" (Gruppe: Ayonto)
* NEW: Shortcode [vt_additional_content] für formatierte Inhalte
* NEW: HTML-Sanitization (wp_kses) für sichere Ausgabe
* IMPROVED: Stabiler HTML-Editor (kein wp_editor wegen DOM-Problemen)
* IMPROVED: Helper-Buttons für HTML-Tags (H2-H6, P, Strong, Listen, Links)
* IMPROVED: Elementor Integration mit Dynamic Tags Infrastructure
* IMPROVED: Content-Filter-Support (apply_filters 'the_content')

= 0.1.28 Build 047 =
* BUGFIX: CRITICAL - Settings werden jetzt korrekt gespeichert (keine Datenverluste mehr)
* BUGFIX: Tab-Wechsel überschreibt keine Daten mehr
* IMPROVED: Merge-Logik in sanitize_settings() - nur geänderte Felder werden überschrieben
* IMPROVED: Checkbox-Handling für Import- und Frontend-Tabs

= 0.1.28 Build 046 =
* IMPROVED: Schema.org Organization jetzt auf ALLEN Seiten (nicht nur spezifische)
* IMPROVED: Intelligente RankMath-Integration - keine Duplikate
* IMPROVED: Organization wird zu RankMath JSON-LD hinzugefügt wenn nicht vorhanden
* IMPROVED: Konsistente Firmenidentität über die gesamte Website
* NEW: add_organization_to_rankmath() Methode für RankMath Filter

= 0.1.28 Build 045 =
* NEW: Vollständiges Admin Settings System mit 5 Tabs
* NEW: Konfigurierbare Firmenangaben (Name, URL, Logo, Marke)
* NEW: Schema.org Organisation komplett konfigurierbar (inkl. ContactPoint)
* NEW: Design-Einstellungen (4 Farben mit Color Picker)
* NEW: Import-Einstellungen konfigurierbar
* NEW: Frontend-Optionen konfigurierbar
* NEW: Settings_Helper Klasse für einfachen Zugriff
* IMPROVED: Alle hart kodierten "Ayonto" Werte durch Settings ersetzt
* IMPROVED: CSS-Variablen für Farben im Frontend
* IMPROVED: White-Label ready durch konfigurierbare Firmendaten

= 0.1.26 Build 043 =
* BUGFIX: Schema-Duplikate entfernt (ItemList erschien mehrfach)
* IMPROVED: Nur noch Filter-basierte Schema-Ausgabe (keine Meta-Speicherung)
* IMPROVED: Sauberes JSON-LD ohne redundante Einträge
* Schema wird jetzt nur noch via rank_math/json_ld Filter eingefügt
* Kompatibel mit RankMath 1.x und WordPress 6.x

= 0.1.25 Build 042 =
* NEW: RankMath Schema Sync - Batterien aus Metabox automatisch in RankMath
* NEW: Metabox "Batterien für diese Lösung" → RankMath Schema Generator
* NEW: ItemList Schema automatisch in RankMath JSON-LD eingefügt
* NEW: Admin Notice zeigt Anzahl synchronisierter Batterien
* IMPROVED: Alte Schema-Klasse nur als Fallback (wenn RankMath inaktiv)
* IMPROVED: Product Schema mit allen technischen Daten aus Metabox
* Schema wird automatisch bei jedem Speichern synchronisiert
* Kompatibel mit RankMath 1.x und WordPress 6.x

= 0.1.24 Build 041 =
* NEW: Vollständige Schema.org JSON-LD Implementierung
* NEW: Product Schema für einzelne Batterien (mit additionalProperty)
* NEW: CollectionPage + ItemList für Übersichtsseiten/Landing Pages
* NEW: BreadcrumbList Schema (falls RankMath nicht aktiv)
* NEW: Organization Schema auf allen Seiten
* IMPROVED: Automatische Erkennung von Landing Pages (via Shortcodes)
* IMPROVED: Schema wird nur in Production ausgegeben (oder bei WP_DEBUG)
* IMPROVED: @graph Format mit allen Schemas zusammen
* IMPROVED: Technische Spezifikationen als PropertyValue in Product Schema
* Schema-Typen: Product, CollectionPage, ItemList, BreadcrumbList, Organization
* Optimiert für Google Rich Results (Product + CollectionPage)

= 0.1.23 Build 040 =
* CRITICAL FIX: RankMath Integration korrigiert - Title/Description Filter ENTFERNT
* CHANGED: RankMath nutzt jetzt seine EIGENEN manuell eingetragenen Felder
* Plugin überschreibt NICHT mehr Title & Description automatisch
* KEPT: Breadcrumbs-Filter (mit Parent-Page-Support via vt_parent_page_id)
* KEPT: Canonical-URL-Filter (mit Parent-Page-Support)
* Meta Fields werden NUR für Schema.org JSON-LD verwendet
* Config aktualisiert: rank_math.integration_scope = "breadcrumbs_canonical_only"
* Config aktualisiert: rank_math.filters_enabled (title/description = false)
* Dokumentation in class-rank-math.php aktualisiert

= 0.1.14 Build 031 =
* NEW: 3 Section-Headers nebeneinander: Grunddaten | Maße & Gewicht | Sonstiges
* NEW: PDF-Upload aus WordPress Mediathek für Datenblatt-URL
* NEW: Nur PDF-Dateien erlaubt für Datenblatt
* IMPROVED: Datenblatt-URL mit "PDF wählen" Button und "✕" zum Entfernen
* IMPROVED: Technische Spezifikationen: Felder nutzen jetzt volle Breite
* NEW: CSS-Klassen vt-section-header-third, vt-section-header-double
* NEW: CSS-Klasse vt-media-field für Media-Button-Layout
* NEW: CSS-Klasse vt-tech-field für volle Breite
* NEW: JavaScript für WordPress Media Uploader Integration
* RESULT: Professionelle PDF-Auswahl und optimale Feld-Breiten

= 0.1.13 Build 030 =
* FIXED: Layout EXAKT nach Screenshot umgesetzt
* IMPROVED: "Grunddaten" und "Maße & Gewicht" Headers nebeneinander (2-Spalten-Layout)
* IMPROVED: Links: Modell, EAN, Serie (vertikal unter Grunddaten)
* IMPROVED: Rechts: L×B×H inline, Gewicht, Eigenschaften, Datenblatt-URL (unter Maße & Gewicht)
* IMPROVED: Technische Spezifikationen über volle Breite mit allen 7 Feldern auf 2 Zeilen
* RESULT: Perfekt wie im Screenshot - 2-Spalten oben, volle Breite unten

= 0.1.12 Build 029 =
* IMPROVED: Layout perfektioniert - alle Felder genau wie gewünscht gruppiert
* IMPROVED: Grunddaten (Modell, EAN, Serie) - ALLE 3 in einer Zeile
* IMPROVED: Maße & Gewicht - L×B×H + Gewicht in einer Zeile (span 3+1)
* IMPROVED: Technische Spezifikationen - ALLE 7 Felder auf 2 kompakte Zeilen (4+3)
* NEW: CSS-Klasse vt-field-triple für 3-Spalten-Felder
* RESULT: Perfekte einzeilige Gruppierung aller verwandten Felder

= 0.1.11 Build 028 =
* IMPROVED: Layout komplett neu strukturiert - maximale Platzeffizienz
* NEW: Section-Headers "Grunddaten" und "Maße & Gewicht" nebeneinander (je span 2)
* IMPROVED: Grunddaten (Modell, EAN, Serie) links, Maße + Gewicht rechts
* IMPROVED: Technische Spezifikationen auf 2 kompakte Zeilen verteilt (4+3 Felder)
* IMPROVED: Garantie-Label verkürzt auf "Garantie (Mon.)" für kompaktere Darstellung
* IMPROVED: Maße-Label verkürzt auf "L × B × H" statt "Länge × Breite × Höhe"
* RESULT: Deutlich kompakteres Layout, alle wichtigen Daten auf einen Blick

= 0.1.10 Build 027 =
* IMPROVED: Feld-Breiten drastisch reduziert für kompaktere Darstellung
* IMPROVED: Modell-Feld von 50% auf 25% Breite reduziert (span 2 → span 1)
* IMPROVED: Text-Inputs max-width: 180px (Modell, EAN, Serie)
* IMPROVED: Number-Inputs max-width: 100px (vorher 120px)
* IMPROVED: URL-Inputs max-width: 300px
* RESULT: Felder nehmen jetzt nur noch die notwendige Breite ein

= 0.1.9 Build 026 =
* IMPROVED: Metabox vertikale Höhe deutlich reduziert
* IMPROVED: Padding von 15px auf 10px reduziert
* IMPROVED: Grid-Gap von 12px auf 8px reduziert
* IMPROVED: Label-Margin von 4px auf 2px reduziert
* IMPROVED: Input-Padding von 4px/8px auf 3px/6px optimiert
* IMPROVED: Textarea min-height von 60px auf 40px reduziert
* IMPROVED: Section-Header-Margins von 10px auf 6px reduziert
* IMPROVED: Dimensions-Gruppe kompakter mit optimierten × Separatoren
* IMPROVED: Font-Sizes reduziert für bessere Übersichtlichkeit
* RESULT: ~30% weniger vertikale Höhe pro Batterie-Row

= 0.1.8 Build 025 =
* FIXED: SVG-Icon wird jetzt korrekt als Background-Image angezeigt
* IMPROVED: SVG als Data-URI in CSS statt Inline-HTML
* IMPROVED: Funktioniert mit wp_kses_post() Filterung
* TECHNICAL: Background-image mit url('data:image/svg+xml;utf8,...')

= 0.1.7 Build 024 =
* NEW: Professionelles PDF-Icon als Inline-SVG statt Emoji
* IMPROVED: SVG-Icon (File-Document) mit sauberen Linien
* IMPROVED: Icon perfekt zentriert im Button (18×18px)
* IMPROVED: Konsistentes Erscheinungsbild über alle Browser
* CSS: SVG-Styling für optimale Darstellung

= 0.1.6 Build 023 =
* REMOVED: Kaltstartstrom (CCA) aus Standard-Spalten entfernt
* IMPROVED: Kompakteres Layout für bessere Übersichtlichkeit
* IMPROVED: Reduzierte Padding (10px statt 12px/15px)
* IMPROVED: Kleinere Schriftgrößen (13px statt 14px)
* IMPROVED: Property-Tags kompakter (11px, 3px 7px padding)
* IMPROVED: Technology-Badges kompakter (11px, 3px 8px padding)
* IMPROVED: PDF-Icon kleiner (32×32px statt 36×36px)
* IMPROVED: EAN kompakter (10px font-size, 3px 6px padding)
* IMPROVED: Model-Name kleiner (13px statt 14px)
* IMPROVED: Optimierte Spaltenbreiten für kompaktere Darstellung
* IMPROVED: Line-height überall auf 1.3-1.4 reduziert
* CSS: Alle Elemente für einzeilige Darstellung optimiert

= 0.1.5 Build 022 =
* NEW: Markenname "Ayonto" automatisch vor Modell in Tabelle
* NEW: EAN-Spalte zu Standard-Spalten hinzugefügt
* NEW: PDF-Icon (📄) statt Text für Datenblatt-Link
* IMPROVED: Datenblatt als kompakter Icon-Button (36×36px) mit Hover-Effekt
* IMPROVED: Technology-Badges mit korrektem Umlaut-Handling (Blei-Säure → blei-saure CSS-Klasse)
* IMPROVED: EAN mit Border und größerem Padding für bessere Lesbarkeit
* IMPROVED: Spaltenbreiten optimiert (model, ean, technology, etc.)
* IMPROVED: Numerische Spalten (Kapazität, Spannung, CCA, Gewicht) zentriert
* IMPROVED: Datenblatt-Spalte zentriert und schmal (60px)
* IMPROVED: Properties-Spalte breiter (min-width: 200px)
* CSS: Column-specific widths mit data-column Selektoren
* CSS: Model-Name Styling (.vt-model-name)
* CSS: EAN mit Border (.vt-value-ean)

= 0.1.4 Build 021 =
* IMPROVED: [vt_battery_table] mit deutlich mehr Informationen
* NEW: Standard-Spalten erweitert auf 9 Felder (model, technology, capacity_ah, voltage_v, cca_a, dimensions_mm, weight_kg, terminals, properties)
* NEW: Model als Link zum Datenblatt (wenn vorhanden)
* NEW: Technologie als farbige Badges (AGM blau, GEL gelb, EFB pink, LiFePO4 grün, etc.)
* NEW: Datenblatt-Link als gelber Button mit Icon
* NEW: Garantie in Jahren statt Monaten (wenn möglich: 12 Monate = 1 Jahr)
* NEW: EAN als Monospace-Code formatiert
* IMPROVED: Bessere Zahlenformatierung (keine unnötigen Dezimalstellen bei ganzen Zahlen)
* IMPROVED: Properties in flex-Container für bessere Darstellung
* IMPROVED: Alle Werte mit semantischen CSS-Klassen (vt-value-*)
* CSS: Technology Badges mit Farbschema pro Technologie
* CSS: Datasheet-Link als prominenter Button in Accent-Farbe (#F79D00)

= 0.1.3 Build 020 =
* IMPROVED: Kompakte und übersichtliche Admin-Metabox mit 4-Spalten-Layout
* IMPROVED: Felder logisch in Sektionen gruppiert (Grunddaten, Technische Spezifikationen, Maße & Gewicht, Eigenschaften)
* NEW: Maße (L×B×H) in einer kompakten Zeile mit visueller Trennung
* NEW: Brand immer automatisch auf "Ayonto" gesetzt (hidden field)
* REMOVED: application_area und product_group Felder entfernt
* IMPROVED: Kleinere Inputs und Labels für bessere Übersichtlichkeit
* IMPROVED: Section Headers mit Markenfarbe (#004B61)

= 0.1.2 Build 019 =
* FIXED: Fatal error in Admin metabox when properties field contains array
* IMPROVED: Array-to-string conversion for properties field display
* Arrays are now properly converted to comma-separated strings in textarea fields

= 0.1.1 Build 018 =
* IMPROVED: Battery Table CSS mit Ayonto-Markenfarben
* NEW: Header-Hintergrund #004B61 (Secondary)
* NEW: Link-Farbe #004B61, Hover #F79D00 (Accent)
* NEW: Text-Farbe #333333
* NEW: Property-Tags mit transparentem Blau (#004B61B3)
* IMPROVED: Konsistente Markenfarben in allen Tabellen-Elementen
* IMPROVED: Mobile- und Desktop-Ansicht harmonisiert

= 0.1.0 Build 015 =
* NEW: Battery Table Shortcode [vt_battery_table] with responsive design
* NEW: 4 Meta Fields: circuit_type, product_group, application_area, properties
* NEW: Mobile card layout (<768px) with data labels
* NEW: Desktop table with horizontal scroll
* NEW: Property tags/badges display
* NEW: CSV import extended for new fields (Schaltung, Prod.grp. Bez., War.grp. Bez.)
* NEW: Auto-extract properties from Art.bez.1 description
* IMPROVED: Frontend CSS with complete responsive styles
* IMPROVED: Shortcode supports all meta fields including new ones

= 0.1.0 Build 014 =
* STABLE: Production-ready release
* Complete permalink system with parent pages
* Import functionality with dry-run mode
* All previous fixes integrated

= 0.1.0 Build 004 =
* FIXED: Rank Math primary category hook disabled (compatibility issues)
* Primary category can now be set manually in Rank Math metabox
* More defensive error handling

= 0.1.0 Build 003 =
* FIXED: Autoloader simplified and working correctly
* All classes now load properly
* Activator/Deactivator improved

= 0.1.0 Build 002 =
* FIXED: Import class render_page → render_import_page
* FIXED: Cache Manager wp_cache_delete_group() → wp_cache_flush()
* FIXED: Rank Math set_primary_category() now accepts 2 parameters
* FIXED: REST API schema for array/object meta fields
* Initial release - Production ready!
