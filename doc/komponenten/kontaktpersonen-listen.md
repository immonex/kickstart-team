# Kontaktpersonen-Listen

Auch die Listendarstellung von Beiträgen des Typs **Kontaktperson** (CPT *inx_agent*) kommt sowohl in den zugehörigen Archivseiten als auch in beliebigen anderen Seiten oder Inhaltselementen (per Shortcode) zum Einsatz.

## Archivseite

Die Standard-Archivseiten der Kontaktpersonen-Beiträge sind unter `https://[DOMAIN.TLD]/immobilienmakler/`<sup>1</sup> bzw. `.../real-estate-agent/`<sup>1</sup> abrufbar, sofern diese nicht in den [Plugin-Optionen](../schnellstart/einrichtung?id=kontaktpersonen-archiveinzelansicht) deaktiviert wurden und im WP-Backend unter ***Einstellungen → Permalinks*** die Option *Beitragsname* o. vgl. für die Permalink-Struktur festgelegt wurde.

Die Optik im Website-Frontend entspricht dabei weitestgehend der der [Agentur-Listen](index):

![Kontaktpersonen-Archivseite im Frontend](../assets/scst-fe-agent-archive.png)

Der **Seiten- und Dokumenttitel** der Archivseite kann in den Plugin-Optionen (***immonex → Einstellungen → Team <sup>ADD-ON</sup>***: *Kontaktpersonen-Archiv-Titel*) festgelegt werden.

## Shortcode

`[inx-team-agent-list]`

Im Gegensatz zur *Archivseite* ist bei der Shortcode-basierten Listenvariante **keine Seitennavigation** verfügbar. Stattdessen wird ggf. ein Link zur Archivseite angezeigt, sofern die Anzahl der **pro Seite** anzuzeigenden Kontaktpersonen-Einträge per [Attribut](/schnellstart/listen-attribute?id=shortcodes) `limit-page` begrenzt wird **und** das Kontaktpersonen-Archiv in den [Plugin-Optionen](/schnellstart/einrichtung?id=archiveinzelansicht3) nicht deaktiviert wurde. Beispiel:

`[inx-team-agent-list limit-page=8]`

#### Attribute

| Name | Beschreibung / Attributwerte |
| ---- | ---------------------------- |
| `agency` | optionale **Agentur-ID**, wenn nur Ansprechpartner einer bestimmten [Immobilien-Agentur](../beitragsarten?id=agentur-→-kontaktperson) angezeigt werden sollen |

Der Shortcode für die Einbindung der Kontaktpersonen-Listen unterstützt zudem alle [allgemeinen Attribute](../schnellstart/listen-attribute#Shortcodes) zur Filterung, Sortierung und Begrenzung der anzuzeigenden Elemente.

## Elementor

<div class="two-column-layout"><div>

![Screenshot: Optionen des Kontaktpersonen-Listen-Widgets im Elementor-Editor](../assets/scst-widget-kontaktpersonen-liste-optionen.png)

</div><div>

Wurde die Website auf Basis von [Elementor](https://de.wordpress.org/plugins/elementor/) umgesetzt, kann das Listenelement auch per [Elementor-Widget](https://docs.immonex.de/kickstart-for-elementor/#/elementor-immobilien-widgets/kontaktpersonen-liste) eingebunden und konfiguriert werden.

Voraussetzung hierfür ist die Installation des kostenfreien [Kickstart-Elementor-Add-ons](https://immonex.dev/wordpress-immobilien-plugin/immonex-kickstart-for-elementor).

</div></div>

## Erweiterte Anpassungen

- [Templates (Skin)](/anpassung-erweiterung/standard-skin?id=archiv-amp-listenansicht)
- [Filter-Hooks](/anpassung-erweiterung/filters-actions?id=kontaktpersonen)
- [Action-Hooks](/anpassung-erweiterung/filters-actions?id=actions)

---

<sup>1</sup> abhängig von der aktuellen Website-Sprache (→ [Übersetzungen & Mehrsprachigkeit](../anpassung-erweiterung/uebersetzung-mehrsprachigkeit))