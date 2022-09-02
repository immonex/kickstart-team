# inx_team_openimmo_feedback_params (Filter)

Mit diesem Filter können die Inhalte modifiziert werden, die bei der Generierung des OpenImmo-Feedback-Anhangs in die entsprechenden XML-Elemente eingefügt werden. Hierzu gehören u. a. die im Frontend per [Kontaktformular](../komponenten/kontaktformular) erfassten Interessenten-Kontaktdaten sowie Angaben zur angefragten Immobilie.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$params` (array) | Daten für den OpenImmo-Feedback-XML-Anhang |
| `$property_post_id` (int) | Beitrags-ID der Immobilie, auf den sich die Anfrage bezieht |

### Parameter-Array im Detail

Die Array-Keys entsprechen den Namen der jeweiligen XML-Elemente. Die Werte von `portal_obj_id` und `oobj_id` sind im Regelfall identisch.

```php
$params = [
	'name'             => 'Name der Website',
	'openimmo_anid'    => '123',
	'datum'            => '23.02.2021',
	'makler_id'        => 4711, // ID des zugehörigen Agentur-Beitrags, sofern vorhanden
	'portal_unique_id' => 98323, // ID des Immobilien-Beitrags
	'portal_obj_id'    => 'Objekt-26-297', // vom Makler vergebene Objekt-ID 
	'oobj_id'          => 'Objekt-26-297', // dito
	'expose_url'       => 'https://immobilienmakler-website.de/immobilien/einfamilienhaus-im-gruenen/',
	'vermarktungsart'  => 'Verkauf',
	'bezeichnung'      => 'Einfamilienhaus im Grünen',
	'ort'              => 'Trier-West',
	'land'             => 'DEU',
	'preis'            => 255200,
	'anrede'           => 'Herr',
	'vorname'          => 'Heinz',
	'nachname'         => 'Tester',
	'strasse'          => 'Simeonstraße 182',
	'plz'              => '54290',
	'ort'              => 'Trier',
	'tel'              => '0651 123456789',
	'email'            => 'heinz@inveris.de',
	'anfrage'          => 'Das ist nur eine Testanfrage - ehrlich!',
];
```

## Rückgabewert

(eventuell) modifiziertes OpenImmo-Feedback-Daten-Array

## Rahmenfunktion

Eine Funktion zur Nutzung des Filters wird typischerweise in der folgenden Form in der Datei **functions.php** des **Child-Themes** oder per Code-Snippets-Plugin eingebunden.

```php
add_filter( 'inx_team_openimmo_feedback_params', 'mysite_modify_openimmo_feedback_xml_params' );

function mysite_process_contact_form_user_data( $params, $property_post_id ) {
	/**
	 * OpenImmo-Feedback-XML-Parameter modifizieren.
	 * Beispiel: Wert von oobj_id durch OBID ersetzen
	 */
	$obid = get_post_meta( $property_post_id, '_openimmo_obid', true );
	if ( $obid ) {
		$params['oobj_id'] = $obid;
	}

	return $params;
} // mysite_modify_openimmo_feedback_xml_params
```