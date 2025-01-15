# inx_team_openimmo_feedback_xml_source (Filter)

Der **Quelltext** der Anhänge im [OpenImmo-Feedback-XML-Format](../schnellstart/einrichtung#OpenImmo-Feedback-Typ), die bei Anfragen via [Objekt-Kontaktformular](../komponenten/kontaktformular) mitgesendet werden, kann bei Bedarf über diesen Filter-Hook angepasst oder erweitert werden.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$xml_source` (string) | XML-Quelltext des OpenImmo-Feedback-Anhangs |

## Rückgabewert

angepasster/erweiterter XML-Quelltext

## Rahmenfunktion

[](_info-snippet-einbindung.md ':include')

```php
add_filter( 'inx_team_openimmo_feedback_xml_source', 'mysite_modify_openimmo_feedback_xml_source' );

function mysite_modify_openimmo_feedback_xml_source( $xml_source ) {
	// XML-Quelltext anpassen/erweitern.
	// $xml_source = str_replace( '...', '...', $xml_source );

	return $xml_source;
} // mysite_modify_openimmo_feedback_xml_source
```

[](_backlink.md ':include')