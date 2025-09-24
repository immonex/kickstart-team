# inx_team_after_render_(agency|agent)_list_item (Action)

Ist das [Standard-Skin](standard-skin) (oder ein hierauf aufbauendes [Custom Skin](skins?id=komplett)) im Einsatz, können über diesen Filter-Hook beliebige Inhalte **nach** der Ausgabe jedes [Agentur-](/komponenten/agentur-listen) (`inx_team_after_render_agency_list_item`) **oder** [Kontaktpersonen-Elements](/komponenten/kontaktpersonen-listen) (`inx_team_after_render_agent_list_item`) innerhalb einer Listenansicht eingefügt werden.

## Rahmenfunktion/Beispiel

[](_info-snippet-einbindung.md ':include')

```php
/**
 * [immonex Kickstart Team] Inhalt nach jedem Agentur-Listenelement einfügen.
 */

add_action( 'inx_team_after_render_agency_list_item', 'mysite_add_contents_after_agency_list_item' );

function mysite_add_contents_after_agency_list_item() {
	echo '<p>Hello, World!</p>';
} // mysite_add_contents_after_agency_list_item
```

## Siehe auch

- [inx_team_before_render_(agency|agent)_list_item](action-inx-team-before-render-list-item) (Inhalte **vor** der Ausgabe eines Agentur-/Kontaktpersonen-Listenelements einfügen)
- [inx_team_before_render_(agency|agent)_list](action-inx-team-before-render-list) (Inhalte **vor** nach der Ausgabe einer Agentur-/Kontaktpersonenliste einfügen)
- [inx_team_after_render_(agency|agent)_list](action-inx-team-after-render-list) (Inhalte **nach** nach der Ausgabe einer Agentur-/Kontaktpersonenliste einfügen)

[](_backlink.md ':include')