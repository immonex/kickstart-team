---
title: Fallback-Admin-Mailadresse(n) (Filter)
search: 1
---

# inx_team_fallback_recipient_admin_email (Filter)

Ist beim Versand einer Mail über das [einheitliche Kontaktformular](../komponenten/kontaktformular.html) – in Ausnahmefällen – keine direkte Empfängeradresse ermittelbar, wird hierfür die Standard-Administrations-Mailadresse der WordPress-Konfiguration übernommen.

Soll das nicht so sein, können über diesen Filter-Hook eine oder mehrere alternative Fallback-Adressen definiert werden.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$mail_address` (string) | einzelne Mailadresse oder kommagetrennte Liste mehrerer Adressen |

## Rückgabewert

alternative Fallback-Mailadresse(n)

## Rahmenfunktion

Eine Funktion zur Nutzung des Filters wird typischerweise in der folgenden Form in die Datei **functions.php** des **Child-Themes** eingebunden.

```php
add_filter( 'inx_team_fallback_recipient_admin_email', 'mysite_set_fallback_admin_mail_address' );

function mysite_set_fallback_admin_mail_address( $mail_address ) {
	// Alternative Fallback-Mailadresse festlegen.
	return 'admin2@pffff.local';
} // mysite_set_fallback_admin_mail_address
```