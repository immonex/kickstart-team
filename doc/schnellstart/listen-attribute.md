# Allgemeine Listen-Attribute

## Shortcodes

Die folgenden Parameter zur Filterung, Sortierung und Begrenzung der anzuzeigenden Elemente können bei **allen** Listen-Shortcodes verwendet werden:

| Name | Beschreibung / Attributwerte |
| ---- | ---------------------------- |
| `author` | Listenelemente nach **Autor(en)** filtern (kommagetrennte Liste von **Benutzer-IDs** oder **Login-Namen**; **Minus zum Ausschließen** bestimmter Benutzer, z. B. *128,264*, *maklerx,agentur-y,dieter.demo* oder *-1,-2,-10*) |
| `limit` | **Gesamtanzahl** der anzuzeigenden Listenelemente begrenzen |
| `limit-page` | Anzahl der Elemente **pro Seite** begrenzen, sofern eine Seitennavigation vorhanden ist (Standardvorgabe: unter ***Einstellungen → Lesen*** hinterlegte max. Beitragsanzahl für Blogseiten) |
| `order` | **Sortierung/Reihenfolge**: eines der folgenden Kriterien plus (optional) *ASC* für aufsteigend oder *DESC* für absteigend, Beispiel: *date DESC* |
| | *ID*: Beitrags-ID |
| | *title*: Beitragstitel (Firma oder Name der Kontaktperson) |
| | *name*: *Slug* |
| | *date*: Erstellungsdatum |
| | *modified*: Datum der letzten Änderung |
| | *rand*: zufällige Reihenfolge |
| `ignore-pagination` | *yes* oder *1* zum Ignorieren eventuell vorhandener **URL-Seiten-Parameter** (`page` oder `paged`), die sich auf eine andere Liste beziehen |
| `demo` | **Demo-Elemente** bei der Ausgabe berücksichtigen (*yes*, *no* oder *only*) |

### Beispiele

Agenturliste mit maximal vier Einträgen (zufällige Auswahl ohne Demo-Elemente)
`[inx-team-agency-list limit=4 demo="no" order="rand"]`

Übersicht aller Kontaktpersonen, denen der WordPress-Benutzer mit dem Benutzernamen *maklerx* als Autor<sup>1</sup> zugeordnet ist, aktuellste Einträge zuerst
`[inx-team-agent-list author="maklerx" order="date DESC"]`

## GET-Parameter

Analog zu den o. g. Shortcode-Attributen können die gleichen Angaben auch in Form von **GET-Parametern** definiert werden, wobei diese dann Priorität gegenüber den ggfls. vorhandenen gleichnamigen Shortcode-Werten haben.

Die GET-Parameter werden – jeweils mit dem Präfix `inx-` – an die URL angehangen und gelten für **alle** Kickstart-Elemente, die in der betreffenden Seite eingebunden sind:

- `inx-author`
- `inx-limit`
- `inx-limit-page`
- `inx-order`
- `inx-demo`

### Beispiele

Agenturliste (Standard-Archivseite) mit maximal vier Einträgen (zufällige Auswahl ohne Demo-Elemente)
`https://[DOMAIN.TLD]/immobilienmakler-agenturen/?inx-limit=4&inx-order=rand&inx-demo=no`

Übersicht aller Kontaktpersonen, denen der WordPress-Benutzer mit dem Benutzernamen *maklerx* als Autor<sup>1</sup> zugeordnet ist, aktuellste Einträge zuerst
`https://[DOMAIN.TLD]/immobilienmakler/?inx-author=maklerx&inx-order=date%20DESC`

---

<sup>1</sup> In der Praxis ist bei Makler/Ansprechpartner-Listen eher die [Agentur-Zugehörigkeit](einbindung#Beispiel-1) (`agency`) als Filterkriterium relevant.
