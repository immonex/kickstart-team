# Über dieses Plugin

<span style="float: right">![immonex Kickstart Team Logo](assets/kickstart-team-logo.png)</span>

**immonex Kickstart Team** ist ein *Add-on-Plugin*, das **WordPress-Immobilien-Websites** auf Basis von [immonex Kickstart](https://de.wordpress.org/plugins/immonex-kickstart/)<sup>1</sup> um folgende Funktionen und Inhalte erweitert:

- [benutzerdefinierte Beitragsarten](beitragsarten) (*Custom Post Types*) inkl. passenden Archiv- und Detailseiten
  - **Immobilien-Agenturen** (Maklerbüros)
  - **Kontaktpersonen** (Immobilienmakler/innen)
- **automatisierte** Erstellung und Aktualisierung der Agentur- und Makler-Datensätze beim [OpenImmo®-basierten Import](systemvoraussetzungen#datenimport-openimmo-xml)<sup>2, 3</sup> von Immobilien-Angeboten
- **Widgets** für die Einbindung von Agentur/Makler-Informationen und Kontaktformularen
- einheitliches **Mail-Kontaktformular** mit Spamschutz und OpenImmo-Feedback-Anhängen bei objektbezogenen Anfragen
- Versand von HTML-Mails auf Basis von [Twig-Templates](https://twig.symfony.com/)
- **Shortcodes** für die flexible Einbindung von Listen- und Detailansichten
- direkt einsatzbereites, responsives Template-Set ("[Skin](anpassung-erweiterung/skins)")
- Möglichkeit der update-sicheren Anpassung vorhandener oder Ergänzung eigener Skins im (Child-)Theme-Ordner

## Zielgruppen dieser Dokumentation

- Web-/Werbeagenturen mit Fokus auf bzw. Kunden aus der Immobilienbranche
- WordPress-Entwickler und -Integratoren
- technikaffine Immobilienmakler/innen

## Lizenz & Entwicklung

immonex Kickstart Team ist **freie, quelloffene Software** (Open Source), die unter der GNU GPL 2.0 (oder später) lizenziert ist.

Entwicklung und Support: [GitHub-Repository](https://github.com/immonex/kickstart-team)

## Trivia

Kickstart- und Add-on-Logos sind eine Hommage an die Entwickler des ersten **Amiga** (später bekannt als [Commodore Amiga 1000](https://en.wikipedia.org/wiki/Amiga_1000)) 😉

---

<sup>1</sup> Das Plugin immonex Kickstart stellt - neben einem Framework für Erweiterungen - Basiskomponenten für die Einbindung von Immobilien-Angeboten bereit, die per *OpenImmo-Schnittstelle* importiert werden.

<sup>2</sup> [OpenImmo-XML](http://openimmo.de/) ist der De-facto-Standard für den Austausch von Immobiliendaten in den deutschsprachigen Ländern und wird hier von allen gängigen Softwarelösungen und Portalen für Immobilienmakler/innen durch entsprechende Import/Export-Schnittstellen unterstützt.

<sup>3</sup> Die OpenImmo-Lösung für WordPress, die Kickstart und das Team-Add-on "out of the box" unterstützt, ist [immonex OpenImmo2WP](https://plugins.inveris.de/shop/immonex-openimmo2wp/) ([Dokumentation](https://plugins.inveris.de/de/shop/immonex-openimmo2wp/?target=dokumentation)). Eine uneingeschränkt funktionsfähige Version dieses und weiterer immonex-Plugins sowie passende Beispieldaten können nach einer kurzen Registrierung unter [immonex.dev](https://immonex.dev/) für Test- und Entwicklungszwecke kostenlos und unverbindlich heruntergeladen werden.