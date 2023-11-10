# Kontaktpersonen-Listen

Auch die Listendarstellung von Beiträgen des Typs **Kontaktperson** (CPT *inx_agent*) kommt sowohl in den zugehörigen Archivseiten als auch in beliebigen anderen Seiten oder Inhaltselementen (per Shortcode) zum Einsatz.

## Archivseite

Die Standard-Archivseiten der Kontaktpersonen-Beiträge sind unter `https://[DOMAIN.TLD]/immobilienmakler/`<sup>1</sup> bzw. `.../real-estate-agent/`<sup>1</sup> abrufbar, sofern diese nicht in den [Plugin-Optionen](../schnellstart/einrichtung?id=kontaktpersonen-archiveinzelansicht) deaktiviert wurden und im WP-Backend unter ***Einstellungen → Permalinks*** die Option *Beitragsname* o. vgl. für die Permalink-Struktur festgelegt wurde.

Die Optik im Website-Frontend entspricht dabei weitestgehend der der [Agentur-Listen](index):

![Kontaktpersonen-Archivseite im Frontend](../assets/scst-fe-agent-archive.png)

Der **Seiten- und Dokumenttitel** der Archivseite kann in den Plugin-Optionen (***immonex → Einstellungen → Team <sup>ADD-ON</sup>***: *Kontaktpersonen-Archiv-Titel*) festgelegt werden.

## Shortcode

`[inx-team-agent-list]`

Der Shortcode für die Einbindung der Kontaktpersonen-Listen unterstützt alle [allgemeinen Attribute](../schnellstart/listen-attribute#Shortcodes) zur Filterung, Sortierung und Begrenzung der anzuzeigenden Elemente.

---

<sup>1</sup> abhängig von der aktuellen Website-Sprache (→ [Übersetzungen & Mehrsprachigkeit](../anpassung-erweiterung/uebersetzung-mehrsprachigkeit))