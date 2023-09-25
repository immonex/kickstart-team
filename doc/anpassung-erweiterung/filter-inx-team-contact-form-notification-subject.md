# inx_team_contact_form_notification_subject (Filter)

Über diesen Filter-Hook kann der Betreff der [Kontaktformular-Mails](../komponenten/kontaktformular) unmittelbar vor dem Versand modifiziert werden. Hierbei können auch [Platzhalter](#Platzhalter-Variablen) verwendet werden (z. B. `{property_title}` oder `{external_id}`).

Anhand des Kontext-Parameters kann unterschieden werden, ob es sich um eine Mail an den Immobilien-Anbieter (im Regelfall der Website-Betreiber bzw. Administrator) oder eine Eingangsbestätigung an den Absender (Interessent/in) handelt.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$subject` (string) | Inhalt der Mail-Betreffzeile/Platzhalter |
| `$context` (string) | Kontext/Empfängergruppe (*admin* oder *prospect*) |
| `$mail_data` (array) | Objekt-/Formulardaten in folgenden Elementen: |
| | `site_title` (string): Haupt-Titel der Website |
| | `form_data` (array): übermittelte Formulardaten, jeweils ein Unterarray pro Feld |
| | `property` (array): Objektdaten inkl. Anbieter-Mailadresse |

## Platzhalter (Variablen)

Die folgenden Platzhalter sind standardmäßig verfügbar. Eine Erweiterung dieser Liste ist mit dem Filter [inx_team_contact_form_notification_subject_variables](filter-inx-team-contact-form-notification-subject-variables) möglich.

| Platzhalter        | Beschreibung                                       |
| ------------------ | -------------------------------------------------- |
| `{site_title}`     | Haupt-Titel der Website                            |
| `{post_id}`        | ID des **Immobilien**-Beitrags                     |
| `{obid}`           | OpenImmo-ID (OBID) der angefragten Immobilie       |
| `{external_id}`    | vom Anbieter selbst vergebene ID bzw. Objektnummer |
| `{property_title}` | Bezeichnung der Immobilie                          |

## Rückgabewert

angepasster Betreff (optional inkl. Platzhaltern)

## Rahmenfunktion

[](_info-snippet-einbindung.md ':include')

Siehe auch: [Beispielfunktion zur Erweiterung der Variablen](filter-inx-team-contact-form-notification-subject-variables#Rahmenfunktion)

```php
add_filter( 'inx_team_contact_form_notification_subject', 'mysite_modify_contact_form_subject', 10, 3 );

function mysite_modify_contact_form_subject( $subject, $context, $mail_data ) {
	if ( 'admin' === $context ) {
		// Betreff von Admin-Benachrichtigungen anpassen.
		return '{foo} [{site_title}] Anfrage für {property_title} (Objektnr.: {external_id}, OBID: {obid})';
	}

	return $subject;
} // mysite_modify_contact_form_subject
```

[](_backlink.md ':include')