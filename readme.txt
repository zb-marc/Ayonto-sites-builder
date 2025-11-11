=== Voltrana Sites Builder ===
Contributors: marcmirschel
Tags: battery, elementor, batteries, meta-fields, custom-post-type
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 0.1.37
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Professional battery management system with Elementor integration. Architecture: ONE taxonomy (vt_category), everything else as Meta Fields!

== Description ==

Voltrana Sites Builder is a professional WordPress plugin for managing battery products with deep Elementor integration.

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

1. Upload the plugin files to `/wp-content/plugins/voltrana-sites-builder/`
2. Activate the plugin through the 'Plugins' screen
3. Use Voltrana menu to configure settings

== Changelog ==

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
* FEATURE: Additional Content Styling - Custom List Icons mit Voltrana-Logo
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
* NEW: Elementor Dynamic Tag "Zusätzlicher Inhalt" (Gruppe: Voltrana)
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
* IMPROVED: Alle hart kodierten "Voltrana" Werte durch Settings ersetzt
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
* NEW: Markenname "Voltrana" automatisch vor Modell in Tabelle
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
* NEW: Brand immer automatisch auf "Voltrana" gesetzt (hidden field)
* REMOVED: application_area und product_group Felder entfernt
* IMPROVED: Kleinere Inputs und Labels für bessere Übersichtlichkeit
* IMPROVED: Section Headers mit Markenfarbe (#004B61)

= 0.1.2 Build 019 =
* FIXED: Fatal error in Admin metabox when properties field contains array
* IMPROVED: Array-to-string conversion for properties field display
* Arrays are now properly converted to comma-separated strings in textarea fields

= 0.1.1 Build 018 =
* IMPROVED: Battery Table CSS mit Voltrana-Markenfarben
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
