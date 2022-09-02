# Übersetzungen & Mehrsprachigkeit

Die offiziellen Übersetzungen werden via [translate.wordpress.org (GlotPress)](https://translate.wordpress.org/projects/wp-plugins/immonex-kickstart-team/) bereitgestellt. Die Varianten *de_DE* (**informell/Du**) und *de_DE_formal* (**formell/Sie**) sind hier immer vollständig verfügbar. Weitere Sprachen und länderspezifische Varianten können ebenfalls hierüber ergänzt werden (Infos zu Hintergrund und Vorgehensweise im offiziellen [Handbuch für Übersetzer](https://make.wordpress.org/polyglots/handbook/)).

Die Übersetzungen von translate.wordpress.org werden automatisch in den globalen WordPress-Übersetzungs-Ordner `.../wp-content/languages/plugins` heruntergeladen, sofern diese für die unter ***Einstellungen → Allgemein*** eingestellte Website-Sprache verfügbar sind:

```
.../wp-content/languages/plugins
├── immonex-kickstart-team-de_DE_formal.po
└── immonex-kickstart-team-de_DE_formal.mo
```

> **ACHTUNG!** Die Übersetzungen im globalen WP-Sprachordner haben Priorität. Die gleichnamigen Dateien, die **zusätzlich** im Unterordner `languages` des Plugin-Verzeichnisses enthalten sind, werden im Regelfall **nicht** eingebunden.

**Individuelle lokale Übersetzungen** können mit [Loco Translate](https://de.wordpress.org/plugins/loco-translate/) erstellt und aktualisiert werden.

Detaillierte Infos und Screenshots zu diesen Themen sind in der [Dokumentation des Kickstart-Basis-Plugins](https://docs.immonex.de/kickstart/#/anpassung-erweiterung/uebersetzung-mehrsprachigkeit) abrufbar. (Bitte hier auch die [Besonderheit beim Einsatz von Beta-Versionen](https://docs.immonex.de/kickstart/#/anpassung-erweiterung/uebersetzung-mehrsprachigkeit?id=besonderheit-bei-beta-versionen) beachten!)

## Übersetzung von Plugin-Optionen

In **mehrsprachigen Websites** (mit Sprachumschalter im Frontend) sind im Regelfall auch Texte zu übersetzen, die in den [Plugin-Optionen](../schnellstart/einrichtung) im WordPress-Backend hinterlegt sind (z. B. Seitentitel und Kontaktformular-Einwilligungstexte).

Diese können mit einer Übersetzungslösung wie [Polylang](https://de.wordpress.org/plugins/polylang/) oder [WPML](https://wpml.org/) übersetzt werden (*String Translation*).
