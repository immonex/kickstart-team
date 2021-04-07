---
title: Felder des Kontaktformulars (Filter)
search: 1
---

# inx_team_contact_form_fields (Filter)

Mit diesem Filter können bestimmte Details (Typ, Bezeichnung, Platzhalter etc.) der Felder des [einheitlichen Kontaktformulars](../komponenten/kontaktformular.html) angepasst werden.

> **ACHTUNG!** Der Umfang des Formulars wurde bewusst auf die wesentlichen Angaben reduziert. Eine Erweiterung des Umfangs über diesen Filter-Hook ist zwar grundsätzlich möglich, wird aber nicht empfohlen. (In diesem Fall muss auch die zugehörige [Skin-Datei](skins.html) `contact-form.php` entsprechend erweitert werden.)

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$fields` (array) | Array mit vollständigen Felddaten oder nur den Namen (Keys) |
| `$names_only` (bool) | *true*, wenn – kontextabhängig – nur die Feldnamen (Keys) zurückgeliefert werden sollen, ansonsten *false* (Standardvorgabe) |

### Fields-Array im Detail

Die folgenden Optionen können pro Feld definiert werden:

| Name (Typ) | Beschreibung / mögliche Werte |
| ---------- | ------------ |
| `type` (string) | Typ – relevant für die Art der Validierung (*text*, *textarea*, *email* oder *checkbox*) |
| `required` (bool) | *true* bei Pflichtfeldern, ansonsten *false* |
| `required_or` (string) | **alternative** Pflichtangabe (entweder das aktuelle **oder** das Feld mit dem angegebenen Namen/Key muss ausgefüllt sein – siehe Beispiel email/phone) |
| `caption` (string) | Feldbezeichnung im Frontend |
| `caption_mail` (string) | alternative Feldbezeichnung in Mails, sofern abweichend |
| `placeholder` (string) | Platzhaltertext |

#### Standardkonfiguration

##### vollständige Daten ($names_only === false)
```php
$fields = [
	'name'    => [
		'type'         => 'text',
		'required'     => true,
		'caption'      => __( 'Name', 'immonex-kickstart-team' ),
		'caption_mail' => __( 'Prospect', 'immonex-kickstart-team' ),
		'placeholder'  => __( 'Name', 'immonex-kickstart-team' ),
	],
	'phone'   => [
		'type'        => 'text',
		'required'    => false,
		'required_or' => 'email',
		'caption'     => __( 'Phone', 'immonex-kickstart-team' ),
		'placeholder' => __( 'Phone', 'immonex-kickstart-team' ),
	],
	'email'   => [
		'type'        => 'email',
		'required'    => false,
		'required_or' => 'phone',
		'caption'     => __( 'Email Address', 'immonex-kickstart-team' ),
		'placeholder' => __( 'Email Address', 'immonex-kickstart-team' ),
	],
	'message' => [
		'type'        => 'textarea',
		'required'    => true,
		'caption'     => __( 'Message', 'immonex-kickstart-team' ),
		'placeholder' => __( 'Message', 'immonex-kickstart-team' ),
	],
	'consent' => [
		'type'     => 'checkbox',
		'required' => true,
	),
);
```

##### nur Feldnamen ($names_only === true)

```php
$fields = [
	'name',
	'phone',
	'email',
	'message',
	'consent',
);
```

## Rückgabewert

angepasstes Felddaten-Array, wobei das Format abhängig vom Parameter `$names_only` ist

## Rahmenfunktion

Eine Funktion zur Nutzung des Filters wird typischerweise in der folgenden Form in der Datei **functions.php** des **Child-Themes** oder per Code-Snippets-Plugin eingebunden.

```php
add_filter( 'inx_team_contact_form_fields', 'mysite_modify_contact_form_fields', 10, 2 );

function mysite_modify_contact_form_fields( $fields, $names_only ) {
	// Daten der Formularfelder im Array $fields anpassen...

	return $names_only ? array_keys( $fields ) : $fields;
} // mysite_modify_contact_form_fields
```