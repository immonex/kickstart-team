# inx_team_contact_form_user_data (Filter)

Mit diesem Filter können die im Frontend per [Kontaktformular](../komponenten/kontaktformular) erfassten Benutzer-Daten verarbeitet oder modifiziert werden.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$form_data` (array) | vom Benutzer erfasste Formulardaten |

### Form-Data-Array im Detail

Das übergebene Formulardaten-Array sieht typischerweise so aus:

```php
$form_data = [
	'name'    => 'Heinz Tester',
	'phone'   => '0999 1234567',
	'email'   => 'heinz@inveris.de',
	'message' => 'Das ist eine Testanfrage - ehrlich!',
	'consent' => 'on',
];
```

## Rückgabewert

(eventuell) modifiziertes Formulardaten-Array

## Rahmenfunktion

[](_info-snippet-einbindung.md ':include')

```php
add_filter( 'inx_team_contact_form_user_data', 'mysite_process_contact_form_user_data' );

function mysite_process_contact_form_user_data( $form_data ) {
	// Benutzer-Formulardaten verarbeiten oder modifizieren...

	return $form_data;
} // mysite_process_contact_form_user_data
```

[](_backlink.md ':include')