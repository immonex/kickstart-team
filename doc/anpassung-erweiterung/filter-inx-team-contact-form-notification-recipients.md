# inx_team_contact_form_notification_recipients (Filter)

Über diesen Filter-Hook können die Empfänger der [Kontaktformular-Anfragemails](../komponenten/kontaktformular) sowie die Absenderangaben für [Eingangsbestätigungen](../schnellstart/einrichtung?id=eingangsbestätigungsmails) unmittelbar vor dem Versand modifiziert werden.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$recipients_and_conf_sender` (array) | Empfängerlisten (regulär/CC) und Absenderdaten für Eingangsbestätigungsmails |
| `$form_data` (array) | Formulardaten |
| `$property_data` (array\|bool) | Daten zur angefragen Immobilie oder `false` bei nicht-objektbezogenen Anfragen |

### Beispielwerte

```php
$recipients_and_conf_sender = [
	'recipients'               => [ 'broeckl@immobilienmakler-website.de' ]
	'cc'                       => [ 'anfragen@immobilienmakler-website.de' ],
	'receipt_conf_sender_info' => [
		'type'    => 'agent', // alternativ: agency
		'name'    => 'B. Bröckl',
		'company' => 'Bröckl Immobilien',
		'email'   => 'broeckl@immobilienmakler-website.de'
	]
];

$form_data = [
	'scope'            => 'extended',
	'post_type'        => 'inx_property',
	'origin_post_id'   => '1502',
	'post_id'          => '12354',
	'property_post_id' => '12354',
	'recipients_enc'   => '',
	'cc_enc'           => '',
	'salutation'       => 'Herr',
	'first_name'       => 'Heinz',
	'last_name'        => 'Tester',
	'street'           => 'Teststraße 1'
	'postal_code'      => '99999',
	'city'             => 'Demostadt',
	'phone'            => '0999 1234567',
	'email'            => 'heinz@foo.bar',
	'message'          => 'Ich interessiere mich für die Immobilie "Traumhaftes Einfamlilienhaus in Top-Lage! (123-456).',
	'consent'          => 'on',
	'name'             => 'Heinz Tester'
];

$property_data = [
	'post_id'               => 12345,
	'external_id'           => '123-456',
	'obid'                  => 'OINV123-123456',
	'title'                 => 'Traumhaftes Einfamlilienhaus in Top-Lage!',
	'url'                   => 'https://immobilienmakler-website.de/immobilien/traumhaftes-einfamilienhaus-in-top-lage/',
	'primary_agent_name'    => 'B. Bröckl',
	'primary_agent_company' => 'Bröckl Immobilien',
	'primary_agent_email'   => 'broeckl@immobilienmakler-website.de',
	'mail_recipients'       => [ 'broeckl@immobilienmakler-website.de' ]
];
```

## Rückgabewert

angepasste Empfängeradressen (Anfragemails) und Absenderangaben (Eingangsbestätigungen)

## Rahmenfunktion

Eine Funktion zur Nutzung des Filters wird typischerweise in der folgenden Form in der Datei **functions.php** des **Child-Themes** oder per Code-Snippets-Plugin eingebunden.

```php
/**
 * [immonex Kickstart Team] CC-Empfängeradresse bei Finanzierungsanfrage ergänzen.
 */

add_filter( 'inx_team_contact_form_notification_recipients', 'mysite_maybe_add_cc', 10, 3 );

function mysite_maybe_add_cc( $recipients_and_conf_sender, $form_data, $property_data ) {
	if ( ! empty( $form_data['mysite-financing'] ) ) {
		$recipients_and_conf_sender['cc'][] = 'finanzierung@immobilienmakler-website.de';
	}

	return $recipients;
} // mysite_maybe_add_cc
```