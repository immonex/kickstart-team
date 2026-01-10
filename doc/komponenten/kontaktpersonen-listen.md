# Kontaktpersonen-Listen

Auch die Listendarstellung von Beiträgen des Typs **Kontaktperson** (CPT *inx_agent*) kommt sowohl in den zugehörigen Archivseiten als auch in beliebigen anderen Seiten oder Inhaltselementen (per Shortcode) zum Einsatz.

## Archivseite

Die Standard-Archivseiten der Kontaktpersonen-Beiträge sind unter `https://[DOMAIN.TLD]/immobilienmakler/`<sup>1</sup> bzw. `.../real-estate-agent/`<sup>1</sup> abrufbar, sofern diese nicht in den [Plugin-Optionen](../schnellstart/einrichtung?id=kontaktpersonen-archiveinzelansicht) deaktiviert wurden und im WP-Backend unter ***Einstellungen → Permalinks*** die Option *Beitragsname* o. vgl. für die Permalink-Struktur festgelegt wurde.

Die Optik im Website-Frontend entspricht dabei weitestgehend der der [Agentur-Listen](index):

![Kontaktpersonen-Archivseite im Frontend](../assets/scst-fe-agent-archive.png)

Der **Seiten- und Dokumenttitel** der Archivseite kann in den Plugin-Optionen (***immonex → Einstellungen → Team <sup>ADD-ON</sup>***: *Kontaktpersonen-Archiv-Titel*) festgelegt werden.

## Shortcode

`[inx-team-agent-list]`

Im Vergleich zur Archivseite gibt es hier eine Besonderheit: Bei Listen, die mittels Shortcode eingebunden werden, ist generell **keine Seitennavigation** verfügbar. Bei einer größeren Anzahl wird aber alternativ ein Link zur Archivseite angezeigt, sofern die Anzahl der **pro Seite** anzuzeigenden Kontaktpersonen-Einträge per [Attribut](/schnellstart/listen-attribute?id=shortcodes) `limit-page` begrenzt wird **und** das Kontaktpersonen-Archiv in den [Plugin-Optionen](/schnellstart/einrichtung?id=archiveinzelansicht3) aktiviert ist. Beispiel:

`[inx-team-agent-list limit-page=8]`

#### Attribute

| Name | Beschreibung / Attributwerte |
| ---- | ---------------------------- |
| `agency` | optionale **Agentur-ID**, wenn nur Ansprechpartner einer bestimmten [Immobilien-Agentur](../beitragsarten?id=agentur-→-kontaktperson) angezeigt werden sollen |

Der Shortcode für die Einbindung der Kontaktpersonen-Listen unterstützt zudem alle [allgemeinen Attribute](../schnellstart/listen-attribute#Shortcodes) zur Filterung, Sortierung und Begrenzung der anzuzeigenden Elemente.

## Erweiterte Anpassungen

- [Templates (Skin)](/anpassung-erweiterung/standard-skin?id=archiv-amp-listenansicht)
- [Filter-Hooks](/anpassung-erweiterung/filters-actions?id=kontaktpersonen)
- [Action-Hooks](/anpassung-erweiterung/filters-actions?id=actions)

---

<sup>1</sup> abhängig von der aktuellen Website-Sprache (→ [Übersetzungen & Mehrsprachigkeit](../anpassung-erweiterung/uebersetzung-mehrsprachigkeit))