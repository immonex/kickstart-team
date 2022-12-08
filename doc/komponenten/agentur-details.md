# Agentur-Details

Analog zu den Listen ist auch die Agentur-Detailansicht für Websites vorgesehen, in denen die Inhalte mehrerer Immobilienmakler-Agenturen kombiniert werden.

Es werden grundsätzlich zwei Arten der Ausgabe unterschieden: Der Umfang des **Standard-Templates** (*TeamQuiwi-Skin*) umfasst neben dem Namen und Logo (sofern vorhanden) des Unternehmens alle relevanten Kontaktdaten sowie zwei zusätzliche Abschnitte für Kontaktpersonen und Immobilienangebote, die der Agentur zugeordnet sind. Wir sprechen bei dieser Form daher auch von der **Vollansicht**.

Mit dem **Widget-Template** wird hingegen eine kompaktere Form der Agenturdaten generiert, die für die Darstellung als Teil einer Immobilien-Detailansicht optimiert ist.

## Vollansicht

Die Vollansicht einer Agentur kann "out of the box" über die Standard-Einzelansicht (im WP-Jargon *Single Template*) aufgerufen werden, sofern diese nicht in den [Plugin-Optionen](../schnellstart/einrichtung?id=agentur-einzelansicht) deaktiviert wurde und im WP-Backend unter ***Einstellungen → Permalinks*** die Option *Beitragsname* o. vgl. für die Permalink-Struktur festgelegt wurde.

Hierbei handelt es sich um eine vollständige Seite inkl. Header und Footer der Website. Das URL-Schema dieser Seiten sieht im Regelfall so aus:

`https://[WEBSITE.TLD]/immobilienmakler-agenturen/[AGENTUR-SLUG]/`<sup>1</sup> bzw. `https://[WEBSITE.TLD]/real-estate-agencies/[AGENTUR-SLUG]/`<sup>1</sup>

Hier ein Beispiel – wie immer mit *TeamQuiwi-Skin* und dem WP-Standard-Theme *Twenty Twenty*:

![Screenshot: Agentur-Details (Standard-Template)](../assets/scst-fe-agency-details-1.jpg)

> Die im Screenshot enthaltene Seitennavigation bezieht sich auf die Liste der Immobilien-Angebote.

## Widget

`immonex Kickstart: Agentur`

Das Agentur-Widget soll die Kontaktaufnahme erleichtern und beinhaltet daher nur die wichtigsten Informationen in kompakter Form sowie ein **einfaches** [Kontaktformular](kontaktformular). Der beste Platz für das Widget ist der Sidebar-Bereich der Immobilien-Detailseiten. Da die grundlegende Widget-Optik viel vom eingesetzten Theme abhängt, muss dieses allerdings auch dafür geeignet sein.

> Das Kontaktformular umfasst in der Standardvariante Eingabefelder für Name, Telefonnummer, Mailadresse sowie für die Nachricht, kann per Plugin-Option, Widget-Einstellung, Shortcode-Attribut oder Filterfunktion aber auch um zusätzliche Felder/Optionen für Anrede und Adresse der Interessenten erweitert werden.

Die Agentur, deren Daten angezeigt werden sollen, wird automatisch anhand der [primären Kontaktperson](../beitragsarten#kontaktpersonagentur-→-immobilie) ermittelt, die der betr. Immobilie zugewiesen ist.

Ein Frontend-Beispiel – hier wurde das gute alte WP-Standard-Theme *Twenty Sixteen* reaktiviert:

![Screenshot: Agentur-Widget im Einsatz](../assets/scst-fe-agency-widget-1.gif)

Das Widget kann via ***Design → Widgets*** oder (bei geöffneter Immobilien-Detailseite) ***Customizer → Widgets*** eingebunden und konfiguriert werden. Ab der WordPress-Version 5.8 wird ein Block des Typs *Legacy-Widget* eingebunden.

![Screenshot: Legacy-Widget-Auswahl im WordPress-Backend](../assets/scst-be-wordpress-legacy-widget.gif)

![Screenshot: Agentur-Widget-Konfiguration im WP-Backend](../assets/scst-be-agency-widget-1.gif)

Auch eine **mehrfache Einbindung** des Widgets ist möglich, bspw. wenn der Umfang der Ausgabe sich bei Referenzobjekten von dem regulärer Objekte unterscheiden soll. Die entsprechende Auswahl des Objektstatus erfolgt in diesem Fall über die Widget-Option *Anzeigen für* (beim folgenden Shortcode analog mit dem Attribut `display_for`).

> Das [Kontaktpersonen-Widget](kontaktpersonen-details#Widget) ist die gängigere Variante der Kontaktdaten-Einbindung.

## Shortcode

`[inx-team-agency]`

Mit diesem Shortcode kann sowohl die Vollansicht als auch das Widget in beliebige Seiten oder sonstige Inhaltselemente (z. B. Page-Builder-Content-Blöcke) eingefügt werden.

#### Attribute

Alle folgenden Attribute sind **optional**.

| Name | Beschreibung / Attributwerte |
| ---- | ---------------------------- |
| `id` | ID des Agentur-Beitrags (nur notwendig, sofern die Einbindung **nicht** als Teil eines Immobilien-Beitrags erfolgt und somit keine automatische Bestimmung möglich ist) |
| `type` | Darstellungsart - wird nur bei Einbindung als Widget benötigt (Wert ergo *widget*), Standard ist die Vollansicht |
| `title` | nur Widget: Titel/Überschrift |
| `before_title` | nur Widget: HTML-Code vor dem Titel (WP/Theme-Vorgabe überschreiben) |
| `after_title` | nur Widget: HTML-Code nach dem Titel (WP/Theme-Vorgabe überschreiben) |
| `convert_links` | *1* oder *yes*, um Mailadressen und Telefonnummern bei der Ausgabe in Links zu konvertieren |
| `elements` | kommagetrennte Liste der anzuzeigenden Elemente: |
| | *logo* : Logo (in der Vollansicht immer enthalten) |
| | *company* : Firma |
| | *about* : Kurzbeschreibung ("Über uns") |
| | *email* : Mailadresse |
| | *phone* : Telefonnummer |
| | *city* : Ort (Bürostandort) |
| | *address* : Adresse (Bürostandort) |
| | *network_icons* : Icons/Links von Business- und sozialen Netzwerken, für die eine URL in den Agenturdaten hinterlegt ist) |
| | *contact_form* : einheitliches [Kontaktformular](kontaktformular) (siehe auch `contact_form_scope` unten) |
| `link_type` | nur Widget – Art der Verlinkung von Logo und Firma/Name des Unternehmens: |
| | *internal* : Link zur [internen Agentur-Profilseite](#Vollansicht) |
| | *external* : Link zu einer externen Website, sofern eine entsprechende URL  vorhanden ist |
| | *none* : keine Verlinkung |
| `display_for` | nur Widget – Anzeige nur bei Immobilien mit einem bestimmten Status: |
| | *all* : alle Objekte |
| | *all_except_references* : alle Immobilien **außer** Referenzobjekte |
| | *available_only* : nur verfügbare Objekte |
| | *unavailable_only* : nur **nicht** verfügbare Objekte |
| | *references_only* : nur Referenzobjekte |
| `contact_form_scope` | Feldumfang des [Kontaktformulars](kontaktformular): |
| | *basic* : Name, Telefonnummer, E-Mail-Adresse und Nachricht (Standardvorgabe) |
| | *extended* : **zusätzliche** Pflichtfelder/-auswahloptionen für Anrede und Adresse, separate Felder für Vor- und Nachname |

#### Beispiele

Vollansicht mit automatisch konvertierten E-Mail/Telefon-Links
`[inx-team-agency convert_links=1]`

Widget mit Logo, Firma, Mailadresse (als Link) sowie externer Verlinkung (Logo/Firma)
`[inx-team-agency type="widget" elements="logo, company, email" convert_links=1 link_type="external"]`

Widget mit Standardumfang, aber erweitertem Kontaktformular (u. a. inkl. Adressfeldern):
`[inx-team-agency type="widget" contact_form_scope="extended"]`

---

<sup>1</sup> abhängig von der aktuellen Website-Sprache (→ [Übersetzungen & Mehrsprachigkeit](../anpassung-erweiterung/uebersetzung-mehrsprachigkeit))