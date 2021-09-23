---
title: Felder des Kontaktformulars (Filter)
search: 1
---

# inx_team_contact_form_fields (Filter)

Mit diesem Filter können die Felder des [einheitlichen Kontaktformulars](../komponenten/kontaktformular.html) angepasst oder um eigene Elemente erweitert werden.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$fields` (array) | Array mit vollständigen Felddaten oder nur den Namen (Keys) |
| `$names_only` (bool) | *true*, wenn – kontextabhängig – nur die Feldnamen (Keys) zurückgeliefert werden sollen, ansonsten *false* (Standardvorgabe) |
| `$scope`\* (string) | für die Ausgabe vorgesehener Feldumfang: *basic* (Standardvorgabe: einfaches Formular) oder *extended* (erweitertes Formular) |

\* Der Umfang des Formulars kann – je nach Art der Einbindung – per [Plugin-Option](../schnellstart/einrichtung.html#Erweitertes-Formular), Widget-Einstellung (<i>Kontaktformular-Umfang</i>) oder Shortcode-Attribut (`contact_form_scope`) festgelegt werden.

### Fields-Array im Detail

Die folgenden Optionen können pro Feld definiert werden:

| Name (Typ) | Beschreibung / mögliche Werte |
| ---------- | ------------ |
| `type` (string) | **Typ** des Formularelements |
| | *text*: normales Textfeld |
| | *email*: Textfeld für die Eingabe einer E-Mail-Adresse (mit entsprechender Validierung) |
| | *textarea*: mehrzeiliges Textfeld – siehe `default_value` |
| | *checkbox*: einzelne Checkbox – siehe `value` |
| | *radio*: Radio-Auswahlelemente (Gruppe) – siehe `options` |
| | *select*: Dropdown-Auswahlbox – siehe `options` |
| `required` (bool) | *true* bei Pflichtfeldern, ansonsten *false* |
| `required_or` (string) | **alternative** Pflichtangabe (entweder das aktuelle **oder** das Feld mit dem angegebenen Namen/Key muss ausgefüllt sein – siehe Beispiel email/phone) |
| `caption` (string) | Feldbezeichnung im Frontend |
| `caption_mail` (string) | **alternative** Feldbezeichnung in Mails, sofern abweichend |
| `placeholder` (string) | Platzhaltertext |
| `value` (string) | zu übermittelnder **Wert** eines aktivierten *checkbox*-Elements (optional, Standard: *X*) |
| `default_value` (string) | Standardinhalt eines *textarea*-Elements (optional) |
| `options` (array) | Key-Value-Array der Auswahloptionen für die Feldtypen *radio* und *select* |
| `layout_type` (string) | **optionale** Mindestbeite des Elements |
| | *half*: mindestens 50 % des Rahmenelements |
| | *full*: komplette Breite des Rahmenelements |
| `scope` (array) | Liste von <i>Scopes</i>, in denen das Element enthalten ist (enthält im Regelfall *basic*, *extended* oder beide Angaben) |
| `order` (int) | Sortierindex für die Reihenfolge der Ausgabe |

#### Standardkonfiguration

##### vollständige Daten (`$names_only === false` – siehe Parameter)
```php
$fields = [
	'salutation'  => [
		'type'        => 'radio',
		'required'    => true,
		'caption'     => __( 'Salutation', 'immonex-kickstart-team' ),
		'options'     => [
			'f' => __( 'Ms.', 'immonex-kickstart-team' ),
			'm' => __( 'Mr.', 'immonex-kickstart-team' ),
		],
		'layout_type' => 'full',
		'scope'       => [ 'extended' ],
		'order'       => 10,
	],
	'first_name'  => [
		'type'        => 'text',
		'required'    => true,
		'caption'     => __( 'First Name', 'immonex-kickstart-team' ),
		'placeholder' => __( 'First Name', 'immonex-kickstart-team' ),
		'scope'       => [ 'extended' ],
		'order'       => 20,
	],
	'last_name'   => [
		'type'        => 'text',
		'required'    => true,
		'caption'     => __( 'Last Name', 'immonex-kickstart-team' ),
		'placeholder' => __( 'Last Name', 'immonex-kickstart-team' ),
		'scope'       => [ 'extended' ],
		'order'       => 30,
	],
	'street'      => [
		'type'        => 'text',
		'required'    => true,
		'caption'     => __( 'Street', 'immonex-kickstart-team' ),
		'placeholder' => __( 'Street', 'immonex-kickstart-team' ),
		'scope'       => [ 'extended' ],
		'layout_type' => 'full',
		'order'       => 40,
	],
	'postal_code' => [
		'type'        => 'text',
		'required'    => true,
		'caption'     => __( 'Postal Code', 'immonex-kickstart-team' ),
		'placeholder' => __( 'Postal Code', 'immonex-kickstart-team' ),
		'layout_type' => 'half',
		'scope'       => [ 'extended' ],
		'order'       => 50,
	],
	'city'        => [
		'type'        => 'text',
		'required'    => true,
		'caption'     => __( 'City', 'immonex-kickstart-team' ),
		'placeholder' => __( 'City', 'immonex-kickstart-team' ),
		'layout_type' => 'half',
		'scope'       => [ 'extended' ],
		'order'       => 60,
	],
	'name'        => [
		'type'         => 'text',
		'required'     => true,
		'caption'      => __( 'Name', 'immonex-kickstart-team' ),
		'caption_mail' => __( 'Prospect', 'immonex-kickstart-team' ),
		'placeholder'  => __( 'Name', 'immonex-kickstart-team' ),
		'scope'        => [ 'basic' ],
		'order'        => 70,
	],
	'phone'       => [
		'type'        => 'text',
		'required'    => false,
		'required_or' => 'email',
		'caption'     => __( 'Phone', 'immonex-kickstart-team' ),
		'placeholder' => __( 'Phone', 'immonex-kickstart-team' ),
		'order'       => 80,
	],
	'email'       => [
		'type'        => 'email',
		'required'    => false,
		'required_or' => 'phone',
		'caption'     => __( 'Email Address', 'immonex-kickstart-team' ),
		'placeholder' => __( 'Email Address', 'immonex-kickstart-team' ),
		'scope'       => [ 'basic', 'extended' ],
		'order'       => 90,
	],
	'message'     => [
		'type'        => 'textarea',
		'required'    => true,
		'caption'     => __( 'Message', 'immonex-kickstart-team' ),
		'placeholder' => __( 'Message', 'immonex-kickstart-team' ),
		'layout_type' => 'full',
		'scope'       => [ 'basic', 'extended' ],
		'order'       => 100,
	],
);
```

##### nur Feldnamen (`$names_only === true` – siehe Parameter)

```php
$fields = [
	'name',
	'phone',
	'email',
	'message',
);
```

## Rückgabewert

angepasstes bzw. erweitertes Felddaten-Array, wobei das Format abhängig vom Parameter `$names_only` ist

## Rahmenfunktion

Eine Funktion zur Nutzung des Filters wird typischerweise in der folgenden Form in der Datei **functions.php** des **Child-Themes** oder per Code-Snippets-Plugin eingebunden.

```php
add_filter( 'inx_team_contact_form_fields', 'mysite_extend_contact_form_fields', 10, 3 );

function mysite_extend_contact_form_fields( $fields, $names_only, $scope ) {
	// Zwei benutzerdefinierte Elemente hinzufügen (Dropdown-Auswahl und Checkbox).
	$my_fields = [
		'immo_verkauf'   => [
			'type'        => 'select',
			'required'    => false,
			'caption'     => 'Immobilie zu verkaufen',
			'options'     => [
				''    => 'Planen Sie den Verkauf einer Immobilie?',
				'n'   => 'nein',
				'j6'  => 'ja, in den kommenden 6 Monaten',
				'j12' => 'ja, in den kommenden 12 Monaten'
			],
			'scope'       => [ 'extended' ],
			'layout_type' => 'full',
			'order'       => 95,
		],
		'immo_bewertung' => [
			'type'         => 'checkbox',
			'required'     => false,
			'caption'      => 'Ich interessiere mich für eine Wertermittlung.',
			'caption_mail' => 'Interesse an Wertermittlung',
			'value'        => 'Ja',
			'scope'        => [ 'extended' ],
			'layout_type'  => 'full',
			'order'        => 96,
		],
	];

	$fields = array_merge( $fields, $my_fields );

	return $names_only ? array_keys( $fields ) : $fields;
} // mysite_extend_contact_form_fields
```