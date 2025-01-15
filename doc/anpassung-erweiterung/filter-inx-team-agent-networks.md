# inx_team_agent_networks (Filter)

Über diesen Filter-Hook kann die Liste der Business- (XING, LinkedIn etc.) und sozialen Netzwerke (X, Facebook & Co.) verändert oder erweitert werden, für die entsprechende URLs in den Datensätzen von **Kontaktpersonen** hinterlegt werden können.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$networks` (array) | Key-Value-Array: Key → Netzwerkname (Ausgabe) |

### Networks-Array im Detail

```php
$networks = [
	'xing'      => 'XING',
	'linkedin'  => 'LinkedIn',
	'x'         => 'X',
	'facebook'  => 'Facebook',
	'instagram' => 'Instagram',
];
```

## Rückgabewert

modifizierte/erweiterte Netzwerk-Liste

## Rahmenfunktion

[](_info-snippet-einbindung.md ':include')

```php
add_filter( 'inx_team_agent_networks', 'mysite_modify_agent_networks' );

function mysite_modify_agent_networks( $networks ) {
	// Branchenspezifisches Business-Netzwerk ergänzen.
	$networks['idisk'] = 'ImmobilienDiskussion';

	return $networks;
} // mysite_modify_agent_networks
```

[](_backlink.md ':include')