---
title: Kontaktpersonen-Details rendern (Action)
search: 1
---

# inx_team_render_single_agent (Action)

Über diesen Action-Hook kann eine [Kontaktpersonen-Detailansicht](../komponenten/kontaktpersonen-details.html) in eine Template-Datei eingebunden werden.

> Das Rendern von (Teil)Komponenten erfolgt **anstelle von direkten Funktionsaufrufen** per Action-Hook, da so bspw. auch in anderen Add-ons/Plugins oder Themes **nicht** explizit die Verfügbarkeit dieses Plugins geprüft werden muss. <i>Render Actions</i> können auch als <i>Low-Level-Varianten</i> der hierauf aufbauenden Shortcodes betrachtet werden.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$post_id` (int\|bool) | ID der Kontaktperson (CPT *inx_agent*), deren Daten eingebunden werden sollen oder *false* für eine automatische Ermittlung |
| `$template` (string) | zu rendernde Template-Datei im [Skin-Ordner](../anpassung-erweiterung/skins.html#Ordner), Standard: *single-agent/index* |
| `$atts` (array) | Liste beliebiger (optionaler) Attribute, die zum PHP-Template "durchgeschleift" werden (hier verfügbar im Array `$template_data`). Im Standard-Skin werden aktuell die folgenden Angaben berücksichtigt: |
| | `is_regular_single_page` (bool): *true* bei Einbindung in regulären CPT-Single-Seiten, ansonsten *false* |
| | Zusätzlich können alle [Kontaktpersonen-Shortcode-Attribute](../komponenten/kontaktpersonen-details.html#Attribute) als Parameter verwendet werden. |

## Code-Beispiel

Die folgenden Aufrufe der <i>Render-Action</i> erfolgen typischerweise in einer **Template-Datei** ([Skin](../anpassung-erweiterung/skins.html), Theme/Child-Theme oder Plugin).

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