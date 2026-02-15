# Ayonto Sites Builder - Testing Checklist

**Version:** 0.2.0 Build 081  
**Testing Date:** ___________  
**Tester:** ___________  
**Environment:** WordPress ____ | PHP ____ | Browser _______

---

## 🎯 Quick Tests (5 Minuten)

### 1. Plugin Installation ✅
- [ ] ZIP hochgeladen ohne Fehler
- [ ] Aktivierung erfolgreich
- [ ] Keine PHP Errors im Log
- [ ] Admin Menu "Ayonto" erscheint

### 2. Mobile Table Rendering ✅ (Build 079-080 Fix)
- [ ] Seite mit `[vt_battery_table]` auf Mobile (<768px) öffnen
- [ ] **Keine große Lücke** nach der Tabelle
- [ ] Card-Layout wird korrekt angezeigt
- [ ] Browser DevTools: Wrapper hat `margin: 0`
- [ ] Letzte Zeile hat `margin-bottom: 0`

### 3. Console Check ✅
- [ ] Produktbild anklicken
- [ ] Lightbox öffnet
- [ ] Browser Console (F12) öffnen
- [ ] **KEINE Warnings** zu aria-hidden
- [ ] **KEINE JavaScript Errors**

### 4. GLightbox Basics ✅
- [ ] Overlay ist **Ayonto Blau** (nicht schwarz)
- [ ] Close Button ist **groß und weiß** (44px)
- [ ] X-Icon ist sichtbar
- [ ] Hover → **Orange Hintergrund** + Rotation
- [ ] **Keine Pfeil-Buttons** (gnext/gprev)

---

## 🎨 Frontend Tests

### Battery Table Shortcode
**Test:** Seite mit `[vt_battery_table]` aufrufen

- [ ] Tabelle wird angezeigt
- [ ] Header ist dunkelblau (#004B61)
- [ ] Produktbilder werden angezeigt
- [ ] Hover-Effekt auf Zeilen funktioniert
- [ ] Mobile (<768px): Card-Layout
- [ ] Technology Badges farbig (AGM blau, GEL gelb, etc.)

### GLightbox (Build 058/059)
**Test:** Produktbild in Tabelle anklicken

#### Overlay
- [ ] Hintergrund ist `rgba(0, 75, 97, 0.70)` (Ayonto Blau)
- [ ] Backdrop Blur sichtbar (optional)
- [ ] Bild zentriert
- [ ] Keine schwarzen Balken

#### Close Button
- [ ] Position: Oben rechts (20px)
- [ ] Größe: 44×44px (Desktop)
- [ ] Hintergrund: Weiß mit Border
- [ ] X-Icon sichtbar (CSS-basiert, nicht SVG)
- [ ] Hover:
  * Hintergrund → Orange (#F79D00)
  * Rotation 90°
  * Scale 115%
  * Box-Shadow erhöht

#### Focus & Accessibility (Build 059)
- [ ] Close Button erhält **automatisch Focus** beim Öffnen
- [ ] **Orange Outline** sichtbar (bei Keyboard-Navigation)
- [ ] ESC-Taste schließt Lightbox
- [ ] ENTER auf Close Button schließt
- [ ] Browser Console: **Keine aria-hidden Warnings**

#### Navigation
- [ ] **Keine Pfeil-Buttons** sichtbar (← →)
- [ ] gnext/gprev ausgeblendet

#### Interaktion
- [ ] Zoom funktioniert (Mausrad / Pinch)
- [ ] Drag funktioniert (Maustaste halten)
- [ ] Click außerhalb schließt Lightbox
- [ ] Close Button schließt Lightbox

### Additional Content
**Test:** Battery Single Page mit Additional Content

- [ ] HTML-Content wird angezeigt
- [ ] Überschriften (H2-H6) in Ayonto Blau
- [ ] Listen mit Ayonto-Logo Icon
- [ ] Strong-Text farbig (#004B61)
- [ ] Tabellen mit vt-battery-table Styling
- [ ] Responsive auf Mobile

---

## 📱 Mobile Tests (<768px)

### Battery Table
- [ ] Card-Layout statt Tabelle
- [ ] Labels vor jedem Wert
- [ ] Erste Zeile (Model) mit blauem Hintergrund
- [ ] Produktbild-Thumbnail sichtbar
- [ ] Touch funktioniert

### GLightbox
- [ ] Close Button: 40×40px (kleiner als Desktop)
- [ ] X-Icon proportional kleiner
- [ ] Touch zum Öffnen funktioniert
- [ ] Swipe zum Schließen funktioniert
- [ ] Pinch-Zoom funktioniert

---

## ⚙️ Admin Tests

### Settings Page
**Navigation:** Ayonto → Einstellungen

#### Tab: General
- [ ] Company Name speichert
- [ ] Company URL speichert
- [ ] Brand Name speichert
- [ ] Logo Upload funktioniert

#### Tab: Schema.org
- [ ] Organization Name speichert
- [ ] Logo URL speichert
- [ ] Contact Point speichert

#### Tab: Design
- [ ] Typography Einstellungen speichern

#### Tab: Colors
- [ ] Primary Color (#004B61) speichert
- [ ] Secondary Color speichert
- [ ] Accent Color (#F79D00) speichert
- [ ] Text Color speichert
- [ ] Color Picker funktioniert

#### Tab: Frontend
- [ ] Display Optionen speichern
- [ ] Änderungen werden im Frontend sichtbar

### Metaboxen
**Navigation:** Batterien → Batterie bearbeiten

#### Metabox: Grunddaten
- [ ] Modell-Feld funktioniert
- [ ] EAN-Feld funktioniert
- [ ] Serie-Feld funktioniert
- [ ] Speichern funktioniert

#### Metabox: Technische Spezifikationen
- [ ] Technologie Dropdown (AGM, GEL, EFB, LiFePO4)
- [ ] Kapazität (Ah) als Number
- [ ] Spannung (V) als Number
- [ ] Kaltstartstrom (CCA) als Number
- [ ] Alle Felder speichern

#### Metabox: Maße & Gewicht
- [ ] Länge × Breite × Höhe (mm)
- [ ] Gewicht (kg)
- [ ] Inline-Darstellung funktioniert

#### Metabox: Eigenschaften
- [ ] Pole/Klemmen Feld
- [ ] Garantie (Monate)
- [ ] Speichern funktioniert

#### Metabox: Batterien für diese Lösung
- [ ] Produkt bild-Upload (WordPress Media Library)
- [ ] Bild wird in Tabelle angezeigt
- [ ] Lightbox funktioniert
- [ ] Fallback-Icon 📷 wenn kein Bild

#### Metabox: Zusätzlicher Inhalt
- [ ] Textarea mit Helper-Buttons
- [ ] HTML-Tags funktionieren (H2-H6, P, Strong, Listen)
- [ ] Tabellen-Button funktioniert
- [ ] Content wird im Frontend angezeigt

### Import
**Navigation:** Ayonto → Datenimport

- [ ] CSV-Upload funktioniert
- [ ] XLSX-Upload funktioniert
- [ ] Validierung läuft
- [ ] Dry-Run Modus funktioniert
- [ ] Import erstellt Batterien
- [ ] Meta Fields korrekt gemappt

---

## 🔐 Security Tests

### Nonces
- [ ] Alle Forms haben Nonces
- [ ] Nonce-Verifikation funktioniert
- [ ] Expired Nonces werden abgelehnt

### File Uploads
- [ ] Nur erlaubte MIME-Types (CSV, XLSX, JPG, PNG)
- [ ] File Extension Check funktioniert
- [ ] Malicious Files werden abgelehnt

### Sanitization
- [ ] Inputs werden sanitized (text_field, textarea)
- [ ] SQL-Injection nicht möglich (prepared statements)
- [ ] XSS nicht möglich (esc_html, esc_attr)

---

## ♿ Accessibility Tests (WCAG 2.1)

### Keyboard Navigation
- [ ] TAB navigiert durch alle Elemente
- [ ] ENTER öffnet Lightbox
- [ ] ESC schließt Lightbox
- [ ] Focus Outline sichtbar (orange)
- [ ] Close Button mit TAB erreichbar

### Screen Reader (optional)
- [ ] NVDA/JAWS aktivieren
- [ ] Lightbox öffnen
- [ ] "Close Button" wird vorgelesen
- [ ] Keine Verwirrung durch aria-hidden
- [ ] Navigation klar

### WCAG 2.1 Compliance
- [ ] 4.1.2 Name, Role, Value (Level A) ✅
- [ ] 2.4.7 Focus Visible (Level AA) ✅
- [ ] Keine Console Warnings
- [ ] aria-hidden korrekt verwendet

---

## 🌐 Browser Compatibility

### Desktop
- [ ] Chrome/Edge (Chromium 90+)
- [ ] Firefox (88+)
- [ ] Safari (14+)
- [ ] Opera

### Mobile
- [ ] Chrome Mobile (Android)
- [ ] Safari Mobile (iOS)
- [ ] Samsung Internet
- [ ] Firefox Mobile

---

## ⚡ Performance Tests

### Page Load
- [ ] First Contentful Paint < 1.5s
- [ ] Largest Contentful Paint < 2.5s
- [ ] Total Blocking Time < 200ms
- [ ] Cumulative Layout Shift < 0.1

### Assets
- [ ] CSS minified und gecacht
- [ ] JS minified und gecacht
- [ ] GLightbox von lokal (nicht CDN)
- [ ] Keine unnecessary HTTP Requests

### Lightbox
- [ ] Öffnet instant (<100ms)
- [ ] Zoom smooth (60fps)
- [ ] Drag smooth
- [ ] Close smooth

---

## 🐛 Regression Tests

### Build 057 → 059 Migration
- [ ] Plugin-Update ohne Fehler
- [ ] Keine Daten verloren
- [ ] Alle Settings erhalten
- [ ] Keine broken Features

### Known Issues (Fixed)
- [ ] ✅ PHP Parse Error (Build 056/057) → Fixed
- [ ] ✅ Doppelte Featured Images (Build 056) → Fixed
- [ ] ✅ aria-hidden Warning (Build 058) → Fixed in 059

---

## 📊 Test Results Summary

| Category | Passed | Failed | Skipped |
|----------|--------|--------|---------|
| Frontend | __ / __ | __ | __ |
| Admin | __ / __ | __ | __ |
| Mobile | __ / __ | __ | __ |
| Accessibility | __ / __ | __ | __ |
| Browser | __ / __ | __ | __ |
| Performance | __ / __ | __ | __ |
| Security | __ / __ | __ | __ |

**Total:** __ / __ tests passed

---

## ✅ Sign-Off

**Tester:** ___________________________  
**Date:** ___________________________  
**Status:** ☐ Approved for Production  ☐ Needs Fixes  
**Notes:**

________________________________________
________________________________________
________________________________________

---

## 📝 Bug Report Template

Falls Issues gefunden werden:

```
**Bug Title:** [Kurze Beschreibung]

**Severity:** Critical | High | Medium | Low

**Steps to Reproduce:**
1. 
2. 
3. 

**Expected Result:**


**Actual Result:**


**Environment:**
- WordPress Version:
- PHP Version:
- Browser:
- Device:

**Screenshots:** [Falls vorhanden]

**Console Errors:** [Falls vorhanden]
```

---

**Happy Testing! 🔋⚡**
