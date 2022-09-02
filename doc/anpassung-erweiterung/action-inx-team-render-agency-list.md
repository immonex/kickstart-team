# inx_team_render_agency_list (Action)

Über diesen Action-Hook kann eine [Agentur-Listenansicht](../komponenten/agentur-listen) in eine Template-Datei eingebunden werden.

> Das Rendern von (Teil)Komponenten erfolgt **anstelle von direkten Funktionsaufrufen** per Action-Hook, da so bspw. auch in anderen Add-ons/Plugins oder Themes **nicht** explizit die Verfügbarkeit dieses Plugins geprüft werden muss. *Render Actions* können auch als *Low-Level-Varianten* der hierauf aufbauenden Shortcodes betrachtet werden.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$atts` (array) | Liste beliebiger (optionaler) Attribute, die zum PHP-Template "durchgeschleift" werden (hier verfügbar im Array `$template_data`). Im Standard-Skin werden aktuell die folgenden Angaben berücksichtigt: |
| | `template` (string): zu rendernde Template-Datei im [Skin-Ordner](../anpassung-erweiterung/skins#Ordner), Standard: *agency-list/index* |
| | `is_regular_archive_page` (bool): *true* bei Einbindung in regulären Archivseiten, ansonsten *false* |
| | `inx-author` (string), `inx-limit` (int), `inx-limit-page` (int), `inx-order` (string), `inx-ignore-pagination` (string), `inx-demo` (string): Pendants der gleichnamigen [Listen-GET-Parameter](../schnellstart/listen-attribute#GET-Parameter) |

## Code-Beispiel

Die folgenden Aufrufe der *Render-Action* erfolgen typischerweise in einer **Template-Datei** ([Skin](../anpassung-erweiterung/skins), Theme/Child-Theme oder Plugin).

```php
// Standard-Listen-Template innerhalb des regulären Archiv-Templates der Agentur-Beitragsart rendern
do_action(
	'inx_team_render_agency_list',
	[
		'is_regular_archive_page' => true,
		'inx-order'               => 'title ASC',
	]
);
```