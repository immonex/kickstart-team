# inx_team_before_render_(agency|agent)_list (Action)

Ist das [Standard-Skin](standard-skin) (oder ein hierauf aufbauendes [Custom Skin](skins?id=komplett)) im Einsatz, können über diese Filter-Hooks beliebige Inhalte **vor** der Ausgabe einer [Agentur-](/komponenten/agentur-listen) (`inx_team_before_render_agency_list`) **oder** [Kontaktpersonen-Listenansicht](/komponenten/kontaktpersonen-listen) (`inx_team_before_render_agent_list`) eingefügt werden.

## Parameter

| Name | Inhalt/Beschreibung |
| ---- | ------------ |
| `$has_properties` (bool) | Agenturen/Kontaktpersonen vorhanden? |

## Rahmenfunktion/Beispiel

[](_info-snippet-einbindung.md ':include')

```php
/**
 * [immonex Kickstart Team] Infotext vor der Ausgabe einer Agenturliste einfügen.
 */

add_action( 'inx_team_before_render_agency_list', 'mysite_add_contents_before_agency_list' );

function mysite_add_contents_before_agency_list( $has_properties ) {
	if ( $has_properties ) {
		echo '<p>Hello, World!</p>';
	}
} // mysite_add_contents_before_agency_list
```

## Siehe auch

- [inx_team_after_render_(agency|agent)_list](action-inx-team-after-render-list) (Inhalte **nach** nach der Ausgabe einer Agentur-/Kontaktpersonenliste einfügen)
- [inx_team_before_render_(agency|agent)_list_item](action-inx-team-before-render-list-item) (Inhalte **vor** der Ausgabe eines Agentur-/Kontaktpersonen-Listenelements einfügen)
- [inx_team_after_render_(agency|agent)_list_item](action-inx-team-after-render-list-item) (Inhalte **nach** der Ausgabe eines Agentur-/Kontaktpersonen-Listenelements einfügen)

[](_backlink.md ':include')