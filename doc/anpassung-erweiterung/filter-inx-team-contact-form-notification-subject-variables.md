# inx_team_contact_form_notification_subject_variables (Filter)

Dieser Filter dient der Erweiterung oder Anpassung von Variablen, die für den Austausch der Platzhalter im [Kontaktformular-Mail-Betreff](filter-inx-team-contact-form-notification-subject) verwendet werden.

Anhand des Kontext-Parameters kann unterschieden werden, ob es sich um eine Mail an den Immobilien-Anbieter (im Regelfall der Website-Betreiber bzw. Administrator) oder eine Eingangsbestätigung an den Absender (Interessent/in) handelt.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$vars` (array) | Key-Value-Array (*Platzhalter/Variablenname ➞ Variablenwert*) |
| `$context` (string) | Kontext/Empfängergruppe (*admin* oder *prospect*) |

## Variablen im Array $vars (Standard)

| Name             | Beschreibung                                       |
| ---------------- | -------------------------------------------------- |
| `site_title`     | Haupt-Titel der Website                            |
| `post_id`        | ID des **Immobilien**-Beitrags                     |
| `obid`           | OpenImmo-ID (OBID) der angefragten Immobilie       |
| `external_id`    | vom Anbieter selbst vergebene ID bzw. Objektnummer |
| `property_title` | Bezeichnung der Immobilie                          |

## Rückgabewert

angepasstes/erweitertes Variablen-Array

## Rahmenfunktion

[](_info-snippet-einbindung.md ':include')

Siehe auch: [Beispielfunktion zur Verwendung der Variable "foo"](filter-inx-team-contact-form-notification-subject#Rahmenfunktion)

```php
add_filter( 'inx_team_contact_form_notification_subject_variables', 'mysite_extend_inquiry_mail_subject_vars', 10, 2 );

function mysite_extend_inquiry_mail_subject_vars( $vars, $context ) {
	/**
	 * Wert "bar" für den Austausch des Platzhalters "{foo}"
	 * im Mail-Betreff ergänzen.
	 */
	$vars['foo'] = 'bar';

	return $vars;
} // mysite_extend_inquiry_mail_subject_vars
```

[](_backlink.md ':include')