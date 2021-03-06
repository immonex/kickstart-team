---
title: Business/Soziale Netzwerke für Agenturen (Filter)
search: 1
---

# inx_team_agency_networks (Filter)

Über diesen Filter-Hook kann die Liste der Business- (XING, LinkedIn etc.) und sozialen Netzwerke (Twitter, Facebook & Co.) verändert oder erweitert werden, für die entsprechende URLs in den Kontaktdaten von **Agenturen** hinterlegt werden können.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$networks` (array) | Key-Value-Array: Key → Netzwerkname (Ausgabe) |

### Networks-Array im Detail

```php
$networks = [
	'xing'      => 'XING',
	'linkedin'  => 'LinkedIn',
	'twitter'   => 'Twitter',
	'facebook'  => 'Facebook',
	'instagram' => 'Instagram',
];
```

## Rückgabewert

modifizierte/erweiterte Netzwerk-Liste

## Rahmenfunktion

Eine Funktion zur Nutzung des Filters wird typischerweise in der folgenden Form in der Datei **functions.php** des **Child-Themes** oder per Code-Snippets-Plugin eingebunden.

```php
add_filter( 'inx_team_agency_networks', 'mysite_modify_agency_networks' );

function mysite_modify_agency_networks( $networks ) {
	// Branchenspezifisches Business-Netzwerk ergänzen.
	$networks['idisk'] = 'ImmobilienDiskussion';

	return $networks;
} // mysite_modify_agency_networks
```