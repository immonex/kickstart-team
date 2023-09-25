# inx_team_render_single_agent (Action)

Über diesen Action-Hook kann eine [Kontaktpersonen-Detailansicht](../komponenten/kontaktpersonen-details) in eine Template-Datei eingebunden werden.

?> Das Rendern von (Teil)Komponenten erfolgt **anstelle von direkten Funktionsaufrufen** per Action-Hook, da so bspw. auch in anderen Add-ons/Plugins oder Themes **nicht** explizit die Verfügbarkeit dieses Plugins geprüft werden muss. *Render Actions* können auch als *Low-Level-Varianten* der hierauf aufbauenden Shortcodes betrachtet werden.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$post_id` (int\|bool) | ID der Kontaktperson (CPT *inx_agent*), deren Daten eingebunden werden sollen oder *false* für eine automatische Ermittlung |
| `$template` (string) | zu rendernde Template-Datei im [Skin-Ordner](../anpassung-erweiterung/skins#Ordner), Standard: *single-agent/index* |
| `$atts` (array) | Liste beliebiger (optionaler) Attribute, die zum PHP-Template "durchgeschleift" werden (hier verfügbar im Array `$template_data`). Im Standard-Skin werden aktuell die folgenden Angaben berücksichtigt: |
| | `is_regular_single_page` (bool): *true* bei Einbindung in regulären CPT-Single-Seiten, ansonsten *false* |
| | Zusätzlich können alle [Kontaktpersonen-Shortcode-Attribute](../komponenten/kontaktpersonen-details#Attribute) als Parameter verwendet werden. |

## Code-Beispiel

Die folgenden Aufrufe der *Render-Action* erfolgen typischerweise in einer **Template-Datei** ([Skin](../anpassung-erweiterung/skins), Theme/Child-Theme oder Plugin).

```php
// Standard-Detail-Template innerhalb des regulären Single-Templates des Kontaktpersonen-CPT rendern (automatische Ermittlung der Beitrags-ID)
do_action(
	'inx_team_render_single_agent',
	false,
	'',
	[
		'is_regular_single_page' => true,
		'convert_links'          => true,
	]
);
```

[](_backlink.md ':include')