# Das Standard-Skin im Detail

Die Dateien des Standard-Skins "*TeamQuiwi*" des Team-Add-on-Plugins befinden sich im Unterordner `skins/default` des Kickstart-Team-Plugin-Verzeichnisses:

`.../wp-content/plugins/immonex-kickstart-team/skins/default`

Ebenso verhält es sich übrigens beim Standard-Skin des **Kickstart-Basis-Plugins**, wobei hier der Plugin-Ordner `.../wp-content/plugins/immonex-kickstart` der Ausgangspunkt ist:

`.../wp-content/plugins/immonex-kickstart/skins/default`

Die Verwendung dieser Ordner als Vorlage für die Entwicklung eigener, sogenannter *Custom Skins* ist grundsätzlich möglich, besser hierfür eignen sich allerdings die aktuellen Quelldateien im jeweiligen Dev-Repository bei GitHub:

- [Kickstart Team Add-on](https://github.com/immonex/kickstart-team/tree/master/src/skins/default)
- [Kickstart-Basis-Plugin](https://github.com/immonex/kickstart/tree/master/src/skins/default)

Hier sind u. a. die für das Skin relevanten JavaScript- und SCSS-Quellcodes zusätzlich enthalten.

<pre class="tree">
<strong>default</strong>
…
╷
├── index.php
│
├── /assets &larr; <em class="token important">ab Plugin-Version 1.3.5: kompilierte CSS- und JS-Dateien</em> (nach Build-Kommando)
│   ╷
│   ├── index.css
│   └── index.js
│
├── /js &larr; <em class="token important">ab Plugin-Version 1.3.5: nur JS-Quelldatei</em>
│   ╷
│   └── index.js
│
├── /scss &larr; <em class="token important">(S)CSS-Quelldateien</em>
│   ╷
│   ├── /blocks
│   │   ╷
│   │   ├── _inx-team-agency-list-item.scss
│   │   ├── …
│   │   └── _inx-team-single-agent.scss
│   │
│   ├── _base.scss
│   ├── _config.scss
│   ├── _mixins.scss
│   └── index.scss
…
</pre>

## Skin-Name

Die Datei `index.php` enthält den Namen des Skins, der im WP-Backend angezeigt wird, in der folgenden Form:

```php
/**
 * Skin Name: TeamQuiwi
 *
 * @package immonex\KickstartTeam
 */
```

## CSS & Sass

Die Datei `assets/index.css` (bis Plugin-Version 1.3.5: `css/index.css`) enthält alle für das Skin relevanten CSS-Stile und wird im Website-Frontend **automatisch** eingebunden. Beim Standard-Skin wird diese auf Basis der Daten im Ordner `scss` mit dem CSS-Präprozessor [Sass](https://sass-lang.com/) kompiliert.

!> Wird das [Git-Repository des Plugins](https://github.com/immonex/kickstart-team/tree/master/src/skins/default) als Grundlage für die Entwicklung eines eigenen Skins verwendet, wird der Ordner `assets` beim erstmaligen Aufruf eines Build-Befehls angelegt (z. B. `npm run build` oder `npm run watch`).

Hier wurde ein komponentenbasierter Ansatz verfolgt, der weitgehend der [BEM-Methodik](https://en.bem.info/methodology/key-concepts/) (Block, Element, Modifier) mit der Namenskonvention [Two Dashes style](https://en.bem.info/methodology/naming-convention/#two-dashes-style) entspricht.

Alle skinspezifischen Module (`blocks`) sowie Konfigurationsvariablen, Mixins und Grundelemente werden über die Einstiegsdatei `index.scss` eingebunden.

?> Bei der Entwicklung eines *Custom Skins* ist der Einsatz eines CSS-Präprozessors optional. Die **Produktivversion** des Skins, die im Child-Theme-Ordner hinterlegt ist, muss nur die Datei `assets/index.css` bzw. `css/index.css` enthalten.

## JavaScript

<pre class="tree">
…
╷
├── /assets
│   ╷
│   ├── …
│   └── index.js &larr; <em class="token important">ab Plugin-Version 1.3.5: kompilierte JS-Datei im Ordner assets</em>
│
├── /js
│   ╷
│   └── index.js &larr; <em class="token important">ab Plugin-Version 1.3.5: Quelldatei im Ordner js</em>
…
</pre>

Auch der JavaScript-Code, der für das Skin eingebunden werden soll, ist in einer einzelnen Datei gebündelt: `assets/index.js` (ab Plugin-Version 1.3.5, vorher `js/index.js`).

Beim Standard-Skin sowie allen weiteren Skins, die (zukünftig) mit Kickstart oder hierauf basierenden Add-ons ausgeliefert werden, wird diese Bündelung im Rahmen der Entwicklung automatisiert mit dem "JavaScript-Modul-Packer" [webpack](https://webpack.js.org/) umgesetzt.

Die **Quelldateien** befinden sich im Unterordner `js` (ab Plugin-Version 1.3.5, vorher `js/src`).

?> Auch hier gilt: Ein *Custom Skin* kann auch **ohne** den Einsatz eines solchen Bundlers entwickelt werden. Sofern überhaupt spezieller JavaScript-Code hierfür benötigt wird, ist eine Datei `index.js` ausreichend. (Im Regelfall wird sich der Umfang des Skin-JS-Codes ohnehin in einem überschaubaren Rahmen bewegen.)

## Frontend-Komponenten

### Archiv & Listenansicht

<pre class="tree">
…
╷
├── archive-agency.php
├── /agency-list
│   ╷
│   ├── index.php
│   └── item.php
├── archive-agent.php
├── /agent-list
│   ╷
│   ├── index.php
│   └── item.php
…
</pre>

Die Templates für die Standard-Archivseiten der [Agentur- und Kontaktpersonen-Beitragsart](/beitragsarten) sind in den Dateien `archive-agency.php` und `archive-agent.php` enthalten.

Die Ordner `agency-list` und `agent-list` enthalten die Vorlagen für [Agentur-](/komponenten/agentur-listen) und [Kontaktpersonenlisten](/komponenten/kontaktpersonen-listen)

?> Bei allen Templates werden die zu rendernden Daten im Array `$template_data` bereitgestellt.

### Detailansichten

<pre class="tree">
…
╷
├── single-agency.php
├── /single-agency
│   ╷
│   ├── index.php
│   └── widget.php
├── single-agent.php
├── /single-agent
│   ╷
│   ├── default-contact-element-replacement.php
│   ├── index.php
│   └── widget.php
…
</pre>

Die Dateien `single-agency.php` bzw. `single-agent.php` enthalten die Templates für die **Einzelansicht** von [Immobilien-Agenturen](/komponenten/agentur-details) und [Maklern](/komponenten/kontaktpersonen-details).
