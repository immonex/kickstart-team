# inx_team_agency_archive_title (Filter)

Der **Seiten- und Dokumenttitel** für Agentur-Archivseiten kann in den Add-on-Optionen festgelegt werden. Alternativ kann hierfür aber auch dieser Filter-Hook verwendet werden, der den Wert der Option überschreibt.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$title` (string) | Titel für Agentur-Archive |

## Rückgabewert

alternativer Titel für die Archivseiten des [*Custom Post Types* für Agenturen](../beitragsarten) (`inx_agency`)

## Rahmenfunktion

[](_info-snippet-einbindung.md ':include')

```php
add_filter( 'inx_team_agency_archive_title', 'mysite_modify_agency_archive_title' );

function mysite_modify_agency_archive_title( $title ) {
	// Alternativen Seiten-/Dokumenttitel verwenden.
	return 'Die besten Immobilienmakler der Welt - mindestens!';
} // mysite_modify_agency_archive_title
```

[](_backlink.md ':include')