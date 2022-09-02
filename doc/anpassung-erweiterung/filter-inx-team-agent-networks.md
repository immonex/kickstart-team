# inx_team_agent_networks (Filter)

Über diesen Filter-Hook kann die Liste der Business- (XING, LinkedIn etc.) und sozialen Netzwerke (Twitter, Facebook & Co.) verändert oder erweitert werden, für die entsprechende URLs in den Datensätzen von **Kontaktpersonen** hinterlegt werden können.

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
add_filter( 'inx_team_agent_networks', 'mysite_modify_agent_networks' );

function mysite_modify_agent_networks( $networks ) {
	// Branchenspezifisches Business-Netzwerk ergänzen.
	$networks['idisk'] = 'ImmobilienDiskussion';

	return $networks;
} // mysite_modify_agent_networks
```