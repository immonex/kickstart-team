# inx_team_force_agency_id_on_agent_update (Filter)

Dieser Hook ist in erster Linie für den [OpenImmo-Import](../systemvoraussetzungen#datenimport-openimmo-xml) von Immobilien-Angeboten und den zugehörigen Ansprechpartner-Daten relevant: Beim Anlegen oder Aktualisieren von [Kontaktpersonen-Beiträgen](../beitragsarten) (`inx_agent`) wird diesen automatisiert ein passender **Agentur-Beitrag** zugewiesen, der entweder bereits vorhanden ist oder auf Basis der übermittelten Kontaktdaten neu angelegt wird.

Soll stattdessen immer eine bestimmte Agentur zugewiesen werden, kann deren ID über diesen Filter-Hook fix definiert werden. Eine automatische Ermittlung/Erstellung findet dann nicht mehr statt.

!> Eine entsprechende Filterfunktion greift nur dann, wenn diese eine valide ID eines veröffentlichten Agentur-Beitrags zurückliefert.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$id` (int) | ID des zuzuweisenden Agentur-Beitrags |

## Rückgabewert

fixe ID eines aktiven/veröffentlichten [Agentur-Beitrags](../beitragsarten) (*Custom Post Type* `inx_agency`)

## Rahmenfunktion

[](_info-snippet-einbindung.md ':include')

```php
add_filter( 'inx_team_force_agency_id_on_agent_update', 'mysite_set_static_agency_id' );

function mysite_set_static_agency_id( $id ) {
	// Fixe Agentur-ID festlegen.
	return 4711;
} // mysite_set_static_agency_id
```

[](_backlink.md ':include')