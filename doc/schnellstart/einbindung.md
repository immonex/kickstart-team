# Frontend-Komponenten

Das Team-Add-on stellt folgende Elemente bereit, die im Website-Frontend auf unterschiedliche Art eingebunden werden können:

- [Agentur-Listen](#Agentur-Listen)
- [Agentur-Details](#Agentur-Details) (Einzelansicht + Widget)
- [Kontaktpersonen-Listen](#Kontaktpersonen-Listen)
- [Kontaktpersonen-Details](#Kontaktpersonen-Details) (Einzelansicht + Widget)

> Voraussetzungen für die Verfügbarkeit aller nachfolgend genannten **Standard-Archiv- und Einzelansichten** sind, dass diese **nicht** in den [Plugin-Optionen](einrichtung?id=add-on-optionen) deaktiviert wurden und im WordPress-Backend unter ***Einstellungen → Permalinks*** die Option *Beitragsname* o. vgl. für die Permalink-Struktur festgelegt wurde. (Die Shortcode- und Widget-Varianten sind hiervon nicht betroffen.)

## Agentur-Listen

### Archivseite (Standard-Listenansicht)

`https://[WEBSITE.TLD]/immobilienmakler-agenturen/`<sup>1</sup> bzw. `https://[WEBSITE.TLD]/real-estate-agencies/`<sup>1</sup>

### Shortcode

`[inx-team-agency-list]`

Der Shortcode dient zur Einbindung von Agentur-Listen in beliebige Seiten oder Page-Builder-Inhaltselemente. Zur Filterung und Begrenzung der Elemente können die [allgemeinen Listen-Attribute](listen-attribute#Shortcodes) genutzt werden.

#### Beispiel

Agenturliste mit maximal vier Einträgen, denen der WordPress-Benutzer mit der ID *128* als Autor zugeordnet ist
`[inx-team-agency-list author=128 limit=4]`

Details: [Komponenten → Agentur-Listen](../komponenten/agentur-listen)

## Agentur-Details

### Standard-Einzelansicht (Vollansicht)

`https://[WEBSITE.TLD]/immobilienmakler-agenturen/[AGENTUR-SLUG]/`<sup>1</sup> bzw. `https://[WEBSITE.TLD]/real-estate-agencies/[AGENTUR-SLUG]/`<sup>1</sup>

Hierbei handelt es sich um eine **komplette Seite**, die folgende Abschnitte umfasst:

- Logo, Firma und Kontaktdaten
- Team-Übersicht (Kontaktpersonen, die der Agentur zugeordnet sind)
- Immobilien-Übersicht (Immobilienangebote, die der Agentur über die primäre Kontaktperson zugeordnet sind)

### Widget

`immonex Kickstart: Agentur`

Mit diesem Widget kann eine "Kompaktansicht" der Agentur-Kontaktdatendaten (auswählbar) inkl. Formular in einem immobilienbezogenen Sidebar-Bereich (z. B. *Immobilien-Detailseite*) via ***Design → Widgets*** oder (bei geöffneter Immobilien-Seite) ***Customizer → Widgets*** eingebunden werden.

Die Agentur wird automatisch anhand der **primären Kontaktperson** ermittelt, die der betr. Immobilie zugewiesen ist.

> Das nachfolgend beschriebene [Kontaktpersonen-Widget](#Widget-1) ist die gängigere Variante der Kontaktdaten-Einbindung.

### Shortcode

`[inx-team-agency]`

Per Shortcode können sowohl die o. g. Inhalte der Standard-Einzelansicht (ohne Rahmenseite) als auch das Widget in beliebige Seiten oder Page-Builder-Inhaltselemente eingebunden werden. Typ und Umfang der Darstellung sind mit **Shortcode-Attributen** bestimmbar, nachfolgend die gängigsten (alle optional):

- **Darstellungsart** (nur bei Widget-Einbindung): `type="widget"`
- **anzuzeigende Elemente**: `elements="[ELEMENT1, ELEMENT2, ELEMENT3...]"`
- **ID des Agentur-Beitrags** (nur bei separater Einbindung ohne Immobilienbezug): `id=[ID]`

#### Beispiele

Agentur-Details (Beitrags-ID: 1234) inkl. Team- und Angebots-Übersicht in eine Seite einfügen
`[inx-team-agency id=1234]`

Agentur-Widget mit Standardumfang innerhalb einer Immobilien-Detailseite einfügen
`[inx-team-agency type="widget"]`

Agentur-Widget nur mit angegebenen Elementen rendern
`[inx-team-agency type="widget" elements="logo, company, email, phone"]`

Details und vollständige Attribut-/Elementliste: [Komponenten → Agentur-Details](../komponenten/agentur-details)

## Kontaktpersonen-Listen

### Archivseite (Standard-Listenansicht)

`https://[WEBSITE.TLD]/immobilienmakler/`<sup>1</sup> bzw. `https://[WEBSITE.TLD]/real-estate-agents/`<sup>1</sup>

### Shortcode

`[inx-team-agent-list]`

Auch dieser Shortcode dient der Einbindung von Listen – in diesem Fall von Kontaktpersonen – in beliebige Seiten bzw. Page-Builder-Elemente. Hier sind ebenfalls zur Filterung und Begrenzung der Elemente die [allgemeinen Listen-Attribute](listen-attribute#Shortcodes) verfügbar, zudem ein weiterer Parameter:

- **ID des zugehörigen Agentur-Beitrags**: `agency=[ID]`

#### Beispiel

Ansprechpartnerliste mit maximal vier Einträgen, denen die Agentur mit der ID 128 zugeordnet ist
`[inx-team-agent-list agency=128 limit=4]`

Details: [Komponenten → Kontaktpersonen-Listen](../komponenten/kontaktpersonen-listen)

## Kontaktpersonen-Details

### Standard-Einzelansicht (Vollansicht)

`https://[WEBSITE.TLD]/immobilienmakler/[KONTAKTPERSONEN-SLUG]/`<sup>1</sup> bzw. `https://[WEBSITE.TLD]/real-estate-agent/[KONTAKTPERSONEN-SLUG]/`<sup>1</sup>

Die **Detailseite** der Kontaktpersonen enthält folgende Inhalte:

- Foto, Name und Kontaktdaten
- Immobilien-Übersicht (Immobilienangebote, die der Person zugeordnet sind)

### Widget

`immonex Kickstart: Kontaktperson`

Analog zu den Agenturen kann mit diesem Widget die "Kompaktansicht" der Ansprechpartner-Kontaktdatendaten (auswählbar) inkl. [Formular](../komponenten/kontaktformular) in einem immobilienbezogenen Sidebar-Bereich (z. B. *Immobilien-Detailseite*) via ***Design → Widgets*** oder (bei geöffneter Immobilien-Seite) ***Customizer → Widgets***) eingebunden werden.

### Shortcode

`[inx-team-agent]`

Auch mit diesem Shortcode können sowohl die o. g. Inhalte der Standard-Einzelansicht (ohne Rahmenseite) als auch das Widget in beliebige Seiten oder Page-Builder-Inhaltselemente eingebunden werden. Die gängigsten **Shortcode-Attribute** (alle optional) für die Definition von Darstellungsart und Umfang in diesem Fall:

- **Darstellungstyp** (nur bei Widget-Einbindung): `type="widget"`
- **anzuzeigende Elemente**: `elements="[ELEMENT1, ELEMENT2, ELEMENT3...]"`
- **ID des Kontaktpersonen-Beitrags** (nur bei separater Einbindung ohne Immobilienbezug): `id=[ID]`

#### Beispiele

Ansprechpartner-Details (Beitrags-ID: 1234) inkl. Angebots-Übersicht in eine Seite einfügen
`[inx-team-agent id=1234]`

Kontaktpersonen-Widget mit Standardumfang innerhalb einer Immobilien-Detailseite einfügen
`[inx-team-agent type="widget"]`

Kontaktpersonen-Widget nur mit angegebenen Elementen rendern
`[inx-team-agent type="widget" elements="photo, full_name_incl_title, position, email_auto_select, phone_auto_select"]`

Details und vollständige Attribut-/Elementliste: [Komponenten → Kontaktpersonen-Details](../komponenten/kontaktpersonen-details)

---

<sup>1</sup> abhängig von der aktuellen Website-Sprache (→ [Übersetzungen & Mehrsprachigkeit](../anpassung-erweiterung/uebersetzung-mehrsprachigkeit))