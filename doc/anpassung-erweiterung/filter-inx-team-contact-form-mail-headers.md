# inx_team_contact_form_mail_headers (Filter)

Über diesen Filter-Hook können die Header-Zeilen der via [Kontaktformular](../komponenten/kontaktformular) generierten Mails modifiziert werden. Der Versand erfolgt regulär per WordPress-Funktion [wp_mail](https://developer.wordpress.org/reference/functions/wp_mail/).

!> Die Header-Angaben sollten nur in Ausnahmefällen angepasst werden, vor allem wenn in den [mailbezogenen Plugin-Optionen](../schnellstart/einrichtung#Kontaktformular-Mails) der Versand als HTML-Mails aktiviert ist.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$headers` (array) | Mail-Header (eine Zeile pro Array-Element) |
| `$context` (string) | Kontext/Art (z. B. Empfängergruppe) der Mail (aktuell immer *admin*) |

## Rückgabewert

modifiziertes/erweitertes Header-Array

## Rahmenfunktion

[](_info-snippet-einbindung.md ':include')

```php
add_filter( 'inx_team_contact_form_mail_headers', 'mysite_modify_contact_form_mail_headers', 10, 2 );

function mysite_modify_contact_form_mail_headers( $headers, $context ) {
	// Headerzeilen anpassen/ergänzen...
	// if ( 'admin' === $context ) {
	//     $headers[] = 'Xxx: Yyy/Zzz';
	// }

	return $headers;
} // mysite_modify_contact_form_mail_headers
```

[](_backlink.md ':include')