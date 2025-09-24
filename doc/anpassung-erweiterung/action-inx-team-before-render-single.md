# inx_team_before_render_single_(agency|agent) (Action)

Sind das [Standard-Skin](standard-skin) (oder ein hierauf aufbauendes [Custom Skin](skins?id=komplett)) sowie das hierin enthaltene **Standard-Template** der [Agentur-](/komponenten/agentur-details) oder [Kontaktpersonen-Detailseite](/komponenten/kontaktpersonen-details) (`single-agency.php`/`single-agent.php`) im Einsatz, können über die Action-Hooks `inx_team_before_render_single_agency` und `inx_team_before_render_single_agent` beliebige Inhalte **vor** der Ausgabe der jeweiligen Detaildaten eingefügt werden.

## Rahmenfunktion/Beispiel

[](_info-snippet-einbindung.md ':include')

```php
/**
 * [immonex Kickstart Team] Textabschnitt vor Agenturdaten (Single Template) ergänzen.
 */

add_action( 'inx_team_before_render_single_agency', 'mysite_add_contents_before_single_agency' );

function mysite_add_contents_before_single_agency() {
	echo '<p>Hello, World!</p>';
} // mysite_add_contents_before_single_agency
```

## Siehe auch

- [inx_team_after_render_single_(agency|agent)](action-inx-team-after-render-single) (Inhalte **nach** der Ausgabe der Detailansicht einer Agentur oder Kontaktperson einfügen)
- [inx_team_before_render_(agency|agent)_list_item](action-inx-team-before-render-list-item) (Inhalte **vor** der Ausgabe eines Agentur-/Kontaktpersonen-Listenelements einfügen)
- [inx_team_after_render_(agency|agent)_list_item](action-inx-team-after-render-list-item) (Inhalte **nach** der Ausgabe eines Agentur-/Kontaktpersonen-Listenelements einfügen)

[](_backlink.md ':include')