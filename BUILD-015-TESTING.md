# Build 015 - Testing Checklist

## 🧪 Testing für Battery Table Shortcode

### ✅ Test 1: Shortcode-Basis
**Test:**
```
[vt_battery_table]
```

**Erwartetes Ergebnis:**
- Zeigt alle Batterien an
- Default Spalten: model, brand, capacity_ah, voltage_v, technology, properties
- Desktop: Normale Tabelle
- Mobile (<768px): Card-Layout

**Status:** ⬜ Nicht getestet | ✅ Erfolgreich | ❌ Fehlgeschlagen

---

### ✅ Test 2: Kategorie-Filter
**Test:**
```
[vt_battery_table category="golfcarts"]
```

**Erwartetes Ergebnis:**
- Zeigt nur Batterien der Kategorie "golfcarts"
- Wenn keine Batterien: "Keine Batterien gefunden."

**Status:** ⬜ Nicht getestet | ✅ Erfolgreich | ❌ Fehlgeschlagen

---

### ✅ Test 3: Spalten-Auswahl
**Test:**
```
[vt_battery_table columns="model,brand,capacity_ah,voltage_v,circuit_type,properties"]
```

**Erwartetes Ergebnis:**
- Zeigt nur die angegebenen 6 Spalten
- Properties als Badges/Tags
- circuit_type wird angezeigt (wenn vorhanden)

**Status:** ⬜ Nicht getestet | ✅ Erfolgreich | ❌ Fehlgeschlagen

---

### ✅ Test 4: Sortierung
**Test:**
```
[vt_battery_table orderby="capacity_ah" order="DESC"]
```

**Erwartetes Ergebnis:**
- Batterien sortiert nach Kapazität (höchste zuerst)
- Numerische Sortierung (nicht alphabetisch)

**Status:** ⬜ Nicht getestet | ✅ Erfolgreich | ❌ Fehlgeschlagen

---

### ✅ Test 5: Responsive - Desktop
**Test:**
- Öffne Seite mit Shortcode auf Desktop (>1200px)

**Erwartetes Ergebnis:**
- Normale Tabelle mit blauem Header
- Hover-Effekt auf Zeilen
- Links zu Batterie-Details funktionieren
- Properties als blaue Badges angezeigt

**Status:** ⬜ Nicht getestet | ✅ Erfolgreich | ❌ Fehlgeschlagen

---

### ✅ Test 6: Responsive - Mobile
**Test:**
- Öffne Seite mit Shortcode auf Mobile (<768px)

**Erwartetes Ergebnis:**
- Card-Layout statt Tabelle
- Jede Zeile ist eine Card mit Border und Shadow
- Data-Labels vor jedem Wert (z.B. "Marke: Q-Batteries")
- Erste Zeile (Modell) mit blauem Hintergrund

**Status:** ⬜ Nicht getestet | ✅ Erfolgreich | ❌ Fehlgeschlagen

---

### ✅ Test 7: Responsive - Tablet
**Test:**
- Öffne Seite mit Shortcode auf Tablet (768-1023px)

**Erwartetes Ergebnis:**
- Normale Tabelle
- Horizontaler Scroll wenn nötig
- Kleinere Schrift (13px)

**Status:** ⬜ Nicht getestet | ✅ Erfolgreich | ❌ Fehlgeschlagen

---

## 🧪 Testing für neue Meta Fields

### ✅ Test 8: Meta Fields im Admin
**Test:**
1. Gehe zu Voltrana → Batterien → Neue Batterie
2. Prüfe ob neue Felder sichtbar sind

**Erwartetes Ergebnis:**
- Felder sollten in Meta Boxes vorhanden sein:
  - Schaltung (circuit_type)
  - Produktgruppe (product_group)
  - Anwendungsbereich (application_area)
  - Eigenschaften (properties)

**Hinweis:** Meta Boxes werden erst in Build 016 vollständig implementiert.
Felder sind aber bereits als Meta registriert und können über REST API gesetzt werden.

**Status:** ⬜ Nicht getestet | ✅ Erfolgreich | ❌ Fehlgeschlagen

---

## 🧪 Testing für CSV-Import

### ✅ Test 9: CSV-Import mit neuen Feldern
**Test:**
1. Gehe zu Voltrana → Datenimport
2. Lade die bereitgestellte CSV hoch (mit Schaltung, Prod.grp. Bez., War.grp. Bez.)
3. Führe Import aus

**Erwartetes Ergebnis:**
- Import erfolgreich
- Neue Felder werden gemappt:
  - "Schaltung" → circuit_type
  - "Prod.grp. Bez." → product_group
  - "War.grp. Bez." → application_area
- Properties werden aus "Art.bez.1" extrahiert

**Status:** ⬜ Nicht getestet | ✅ Erfolgreich | ❌ Fehlgeschlagen

---

### ✅ Test 10: Property-Extraktion
**Test:**
1. Importiere CSV mit "Art.bez.1" Spalte
2. Prüfe ob Properties extrahiert wurden

**Beispiel:**
```
Art.bez.1: "12V - 50Ah Blei Akku Zyklentyp AGM - Deep Cycle VRLA"
→ Properties sollten enthalten: ["Deep Cycle", "VRLA", "AGM"]
```

**Erwartetes Ergebnis:**
- Properties-Array enthält extrahierte Werte
- Keine Duplikate
- Wird in Tabelle als Tags angezeigt

**Status:** ⬜ Nicht getestet | ✅ Erfolgreich | ❌ Fehlgeschlagen

---

## 🧪 Testing für Frontend-Display

### ✅ Test 11: Properties-Tags
**Test:**
- Öffne Batterie-Tabelle mit Properties-Spalte

**Erwartetes Ergebnis:**
- Properties werden als blaue Badges angezeigt
- Design: Hellblauer Hintergrund (#dbeafe), dunkelblaue Schrift (#1e40af)
- Border-Radius: 4px
- Padding: 4px 8px

**Status:** ⬜ Nicht getestet | ✅ Erfolgreich | ❌ Fehlgeschlagen

---

### ✅ Test 12: Datenblatt-Link
**Test:**
- Batterie mit datasheet_url in Tabelle

**Erwartetes Ergebnis:**
- Link "Datenblatt ↗" angezeigt
- Öffnet in neuem Tab (target="_blank")
- rel="noopener" für Sicherheit

**Status:** ⬜ Nicht getestet | ✅ Erfolgreich | ❌ Fehlgeschlagen

---

### ✅ Test 13: Dimensions Display
**Test:**
- Batterie mit dimensions_mm in Tabelle

**Erwartetes Ergebnis:**
- Format: "198 × 166 × 171" (L × W × H)
- Nur angezeigt wenn alle 3 Werte vorhanden

**Status:** ⬜ Nicht getestet | ✅ Erfolgreich | ❌ Fehlgeschlagen

---

## 🧪 Performance Testing

### ✅ Test 14: Große Tabellen
**Test:**
- Shortcode mit >100 Batterien

**Erwartetes Ergebnis:**
- Seite lädt in <3 Sekunden
- Kein Memory-Limit Error
- Horizontal Scroll funktioniert auf Desktop

**Status:** ⬜ Nicht getestet | ✅ Erfolgreich | ❌ Fehlgeschlagen

---

## 🧪 Browser-Kompatibilität

### ✅ Test 15: Browser-Tests
**Browsers:**
- [ ] Chrome/Edge
- [ ] Firefox
- [ ] Safari (Desktop)
- [ ] Safari (iOS)
- [ ] Chrome (Android)

**Erwartetes Ergebnis:**
- Table rendert korrekt in allen Browsern
- Responsive funktioniert in allen Browsern
- CSS-Properties werden unterstützt

**Status:** ⬜ Nicht getestet | ✅ Erfolgreich | ❌ Fehlgeschlagen

---

## 🧪 Edge Cases

### ✅ Test 16: Leere Werte
**Test:**
- Batterie mit fehlenden Meta-Werten

**Erwartetes Ergebnis:**
- Leere Felder zeigen "—" (Em Dash)
- Keine PHP-Warnings
- Keine leeren Spalten

**Status:** ⬜ Nicht getestet | ✅ Erfolgreich | ❌ Fehlgeschlagen

---

### ✅ Test 17: Keine Batterien
**Test:**
```
[vt_battery_table category="nonexistent"]
```

**Erwartetes Ergebnis:**
- Nachricht: "Keine Batterien gefunden."
- Keine Tabelle gerendert
- Keine PHP-Errors

**Status:** ⬜ Nicht getestet | ✅ Erfolgreich | ❌ Fehlgeschlagen

---

## 📋 Gesamtergebnis

**Tests bestanden:** ___ / 17

**Kritische Issues:**
- [ ] Keine

**Bekannte Probleme:**
- [ ] Keine

**Nächste Schritte:**
- Build 016: Elementor Widget
- Build 017: JavaScript-Sortierung
- Build 018: Filter-Dropdowns

---

## 🚀 Deployment-Checklist

- [ ] Alle Tests bestanden
- [ ] Version auf 0.1.0 Build 015
- [ ] UPDATE.md aktualisiert
- [ ] readme.txt aktualisiert
- [ ] ZIP erstellt: `voltrana-sites-builder-v0.1.0-build015.zip`
- [ ] Keine PHP-Warnings im Debug-Mode
- [ ] Plugin auf Staging getestet
- [ ] Backup vor Deployment erstellt

---

**Build 015 Status:** ✅ Ready for Testing

**Erstellt:** November 6, 2025  
**Getestet von:** ___________  
**Deployment-Datum:** ___________
