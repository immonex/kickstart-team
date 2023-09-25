# Skins

Ein *Skin* ist - technisch gesehen - ein einfacher Ordner, der alle für die Darstellung im **Website-Frontend** maßgeblichen Ressourcen in (möglichst) einheitlicher, strukturierter Form enthält:

- Templates (PHP)
- JavaScript-Dateien
- CSS-Dateien
- Bilddateien
- Fonts

## Ordner

### Add-on Skins

**Mitgelieferte** Skins sind im **Plugin-Verzeichnis des Kickstart-Add-ons** unter `skins` zu finden:

`.../wp-content/plugins/immonex-kickstart-team/skins/`

Aktuell ist hier nur der Ordner des Standard-Skins *TeamQuiwi* enthalten (`default`):

`.../wp-content/plugins/immonex-kickstart-team/skins/default`

> Die Ordnernamen sind unabhängig vom eigentlichen (angezeigten) Skin-Namen, der in der Datei index.php festgelegt wird.

### Custom Skins

Der passende und *update-sichere* Ort für **eigene oder angepasste** Skins ist der Unterordner `immonex-kickstart-team` im **(Child-)Theme-Verzeichnis**:

`.../wp-content/themes/(CHILD-)THEME-NAME/immonex-kickstart-team/`

## Auswahl

![Skin-Auswahl](../assets/scst-skin-selection-1.gif)

[Skin-Auswahl](../schnellstart/einrichtung#Skin) unter ***immonex → Einstellungen → Team [Add-on]***

Die zugehörige Ordner-Struktur könnte so aussehen:

<pre class="tree">
<strong>.../wp-content/plugins/immonex-kickstart-team/skins</strong>
╷
└── /default

<strong>.../wp-content/themes/(CHILD-)THEME-NAME/immonex-kickstart-team</strong>
╷
├── /denise
├── /paula
├── /agnus
└── /one
</pre>

## Aufbau

Der **grundlegende** Aufbau eines Kickstart(-Add-on)-Skins ist denkbar einfach:

<pre class="tree">
skin-name
╷
├── /css
│   ╷
│   └── index.css
│
├── /js
│   ╷
│   └── index.js
│
└── index.php
</pre>

Die Dateien `index.css` und `index.js` des aktiven Skins werden im Frontend automatisch eingebunden. Das gilt auch für weitere Dateien mit den folgenden Namen, sofern vorhanden:

- `extend.css` / `extend.js`
- `custom.css` / `custom.js`
- `frontend.css` / `frontend.js`
- `skin.css` / `skin.js`

Je nach Umfang bietet es sich an, bei der **Entwicklung** mit mehreren Quelldateien zu arbeiten, die anschließend per Bundler bzw. Präprozessor à la [webpack](https://webpack.js.org/), [Sass](https://sass-lang.com/) & Co. kompiliert werden. Die *kompilierten und/oder minimierten* Varianten der Dateien sollten in diesem Fall im Ordner `assets` gespeichert werden:

<pre class="tree">
skin-name
╷
├── /assets
│   ╷
│   ├── index.css
│   └── index.js
…
</pre>

Auch die Aufteilung der CSS- und JS-Dateien in separate Unterordner ist hier möglich:

<pre class="tree">
…
╷
├── /assets
│   ╷
│   ├── /css
│   │   ╷
│   │   ├── index.css
│   │   ├── custom.css
│   │   …
│   └── /js
│       ╷
│       ├── index.js
│       ├── extend.js
…       …
</pre>    

Jede der o. g. CSS/JSS-Dateien wird nur **einmalig** eingebunden. Sind mehrere Dateien gleichen Namens im Skin-Ordner enthalten, erfolgt die entsprechende Priorisierung anhand der **Unterordner** in dieser Reihenfolge:

- `assets/css/` / `assets/js/`
- `assets/`
- `css/` / `js/`

Ist also bspw. eine Datei `custom.css` in den Unterordnern `assets` **und** `css` enthalten, wird nur die Variante im Ordner `assets` im Website-Frontend geladen.

Die Datei `index.php` enthält nur den Namen des (Add-on-)Skins für die Ausgabe:

```php
<?php
/**
 * Skin Name: TeamQuiwi
 */

die( "Don't event think about it!" );
```

## Individuelle Anpassung

### Partiell

Ist ein mitgeliefertes Add-on-Skin grundsätzlich passend und sollen nur geringfügige optische Anpassungen vorgenommen werden, ist in den meisten Fällen das [Überschreiben bzw. Ergänzen der betreffenden CSS-Stile](css) via Customizer ausreichend. Auch bei weitergehenden Änderungen muss aber **nicht** zwingend mit einem individuellen Skin mit dem vollen Datei/Ordner-Umfang des [Standard-Skins](standard-skin)) gearbeitet werden.

Stattdessen besteht die Möglichkeit, nur die zu anzupassenden Dateien eines  vorhandenes Plugin-Skins im (Child-)Theme-Ordner zu überschreiben, dessen Name dem des Basis-Skin-Ordners (im Plugin-Verzeichnis) entspricht.

Sollen bspw. nur eigene Varianten der Dateien `index.css` und `single-agency.php` zum Einsatz kommen, alles andere aber weiterhin vom Standard-Skin (Ordnername `default`) übernommen werden, ergibt sich bspw. die folgende Struktur:

#### Standard-Skin-Ordner (Plugin-Verzeichnis)

<pre class="tree">
<strong>.../wp-content/plugins/immonex-kickstart-team/skins</strong>
╷
└── /default
    ╷
    ├── /css
    │   ╷
    │   └── index.css
    │
    ├── /agency-list
    ├── /agent-list
    ├── /js
    ├── /mail
    ├── /single-agency
    ├── /single-agent
    ├── archive-agency.php
    ├── archive-agent.php
    ├── contact-form.php
    ├── index.php
    ├── single-agency.php
    └── single-agent.php
</pre>

#### Skin-Ordner mit angepassten Dateien (Theme/Child-Theme)

<pre class="tree">
<strong>.../wp-content/themes/(CHILD-)THEME-NAME/immonex-kickstart-team</strong>
╷
└── /default
    ╷
    ├── /css
    │   ╷
    │   └── index.css
    │
    └── single-agency.php
</pre>

### Komplett

Sollen umfangreiche Anpassungen oder einer ganz individuelle Darstellung umgesetzt werden, ist ein vollständig **benutzerdefiniertes Skin** (*Custom Add-on Skin*) die passende Wahl. Hier kann wiederum das Standard-Skin als Basis verwendet und schrittweise angepasst werden.

Die Vorgehensweise entspricht hierbei der Entwicklung von [Custom Skins für das Kickstart-Basis-Plugin](https://docs.immonex.de/kickstart/#/anpassung-erweiterung/skins?id=komplett).
