---
title: Kontaktpersonen-Listenansicht rendern (Action)
search: 1
---

# inx_team_render_agent_list_list (Action)

Über diesen Action-Hook kann eine [Kontaktpersonen-Listenansicht](../komponenten/kontaktpersonen-listen.html) in eine Template-Datei eingebunden werden.

> Das Rendern von (Teil)Komponenten erfolgt **anstelle von direkten Funktionsaufrufen** per Action-Hook, da so bspw. auch in anderen Add-ons/Plugins oder Themes **nicht** explizit die Verfügbarkeit dieses Plugins geprüft werden muss. <i>Render Actions</i> können auch als <i>Low-Level-Varianten</i> der hierauf aufbauenden Shortcodes betrachtet werden.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$atts` (array) | Liste beliebiger (optionaler) Attribute, die zum PHP-Template "durchgeschleift" werden (hier verfügbar im Array `$template_data`). Im Standard-Skin werden aktuell die folgenden Angaben berücksichtigt: |
| | `template` (string): zu rendernde Template-Datei im [Skin-Ordner](../anpassung-erweiterung/skins.html#Ordner), Standard: *agent-list/index* |
| | `is_regular_archive_page` (bool): *true* bei Einbindung in regulären Archivseiten, ansonsten *false* |
| | `inx-author` (string), `inx-limit` (int), `inx-limit-page` (int), `inx-order` (string), `inx-ignore-pagination` (string), `inx-demo` (string): Pendants der gleichnamigen [Listen-GET-Parameter](../schnellstart/listen-attribute.html#GET-Parameter) |

## Code-Beispiel

Die folgenden Aufrufe der <i>Render-Action</i> erfolgen typischerweise in einer **Template-Datei** ([Skin](../anpassung-erweiterung/skins.html), Theme/Child-Theme oder Plugin).

```php
// Standard-Listen-Template innerhalb des regulären Archiv-Templates der Kontaktpersonen-Beitragsart rendern
do_action(
	'inx_team_render_agent_list',
	[
		'is_regular_archive_page' => true,
		'inx-order'               => 'title ASC',
	]
);
```