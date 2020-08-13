---
title: Fixe Agentur-ID für alle Kontaktpersonen definieren (Filter)
search: 1
---

# inx_team_force_agency_id_on_agent_update (Filter)

Dieser Hook ist in erster Linie für den [OpenImmo-Import](../systemvoraussetzungen.html#Datenimport) von Immobilien-Angeboten und den zugehörigen Ansprechpartner-Daten relevant: Beim Anlegen oder Aktualisieren von [Kontaktpersonen-Beiträgen](../beitragsarten.html) (`inx_agent`) wird diesen automatisiert ein passender **Agentur-Beitrag** zugewiesen, der entweder bereits vorhanden ist oder auf Basis der übermittelten Kontaktdaten neu angelegt wird.

Soll stattdessen immer eine bestimmte Agentur zugewiesen werden, kann deren ID über diesen Filter-Hook fix definiert werden. Eine automatische Ermittlung/Erstellung findet dann nicht mehr statt.

> **ACHTUNG!** Eine entsprechende Filterfunktion greift nur dann, wenn diese eine valide ID eines veröffentlichten Agentur-Beitrags zurückliefert.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$id` (int) | ID des zuzuweisenden Agentur-Beitrags |

## Rückgabewert

fixe ID eines aktiven/veröffentlichten [Agentur-Beitrags](../beitragsarten.html) (<i>Custom Post Type</i> `inx_agency`)

## Rahmenfunktion

Eine Funktion zur Nutzung des Filters wird typischerweise in der folgenden Form in die Datei **functions.php** des **Child-Themes** eingebunden.

```php
add_filter( 'inx_team_force_agency_id_on_agent_update', 'mysite_set_static_agency_id' );

function mysite_set_static_agency_id( $id ) {
	// Fixe Agentur-ID festlegen.
	return 4711;
} // mysite_set_static_agency_id
```