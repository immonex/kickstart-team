# Kontaktformular

Das Add-on bringt ein **einheitliches** Kontaktformular mit, das in den Detailansichten und Widgets der [Agenturen](agentur-details) und [Kontaktpersonen](kontaktpersonen-details) verwendet werden kann.

Die Grundeinstellungen bezüglich Formularumfang und Mailversand/-inhalten werden in den [Plugin-Optionen](../schnellstart/einrichtung#Kontaktformular) vorgenommen.

## Widget

Wird das Formular in einem Widget aktiviert, das bspw. in einem Sidebar-Bereich der Immobilien-Detailseiten eingebunden ist, wird automatisch der entsprechende Objekttitel im Nachrichtenfeld übernommen. Dieser ist – neben der ID, Objektnummer, URL sowie einem optionalen **OpenImmo-Feedback-Anhang** für die weitere (automatisierte) Verarbeitung – nach dem Absenden auch in den Anfragemails enthalten.

![Kontaktformular in einem Widget](../assets/scst-contact-form-widget-1.png)

Per Widget-Einstellung (*Kontaktformular-Umfang*) kann eine erweiterte Variante des Kontaktformulars mit zusätzlichen Pflichtfeldern aktiviert werden, sofern diese nicht ohnehin schon in den [Plugin-Optionen](../schnellstart/einrichtung#Erweitertes-Formular) voreingestellt wurde. (Bei Widgets, die via Shortcode eingebunden werden, kann hierfür das Attribut `contact_form_scope` mit dem Wert *extended* verwendet werden.)

![erweitertes Kontaktformular](../assets/scst-contact-form-widget-2.png)

## Detailansicht

Hier ein Beispiel der Formulareinbindung in einer [Kontaktpersonen-Detailseite](kontaktpersonen-details):

![Kontaktformular in einer Kontaktpersonen-Detailansicht](../assets/scst-contact-form-agent-details-1.jpg)

Auch hier gilt: Eine **erweiterte Formular-Variante** kann in den [Plugin-Optionen](../schnellstart/einrichtung#erweitertes-formular) aktiviert werden. Zudem besteht die Möglichkeit (in Sonderfällen) zusätzliche, **benutzerdefinierte** Formularelemente über den Filter-Hook [`inx_team_contact_form_fields`](../anpassung-erweiterung/filter-inx-team-contact-form-fields) zu ergänzen oder vorhandene Elemente individuell anzupassen.

## Mailversand

### Empfänger

Die per Formular übermittelten Daten werden – abhängig von der Art der Rahmenseite (Objekt-, Makler- oder Agenturdetails) – an die Mailadressen der jeweiligen Kontaktperson(en) gesendet. Fallback- und CC-Adressen können in den [Plugin-Optionen](../schnellstart/einrichtung#kontaktformular-mails) festgelegt werden.

Zusätzlich kann (optional) auch der Versand von [Eingangsbestätigungen](../schnellstart/einrichtung#eingangsbestätigungsmails) aktiviert werden, die an die Absenderadressen gesendet werden.

### Inhalte / HTML-Mails

Sowohl bei den regulären Formularmails als auch bei den Eingangsbestätigungen ist ein Versand in Form **HTML-formatierter Nachrichten**<sup>1</sup> möglich. Die Mailinhalte können hierbei in den zugehörigen Formularfeldern der Plugin-Optionen hinterlegt werden, wobei hier auch Variablen und Abfragen auf Basis der [PHP-Template-Engine Twig](https://twig.symfony.com/) unterstützt werden.

### OpenImmo Feedback XML

Bei Anfragen, die sich auf eine bestimmte Immobilie beziehen (Formular innerhalb einer Objekt-Detailansicht) werden Mailanhänge im **OpenImmo-Feedback-XML-Format** unterstützt, mit denen bspw. eine automatisierte Verarbeitung der Daten in einer externen Immobilienmakler-Softwarelösung realisiert werden kann.

Beim [OpenImmo-Import](../systemvoraussetzungen#datenimport-openimmo-xml) wird hierfür in der Regel eine spezielle *Feedback-Mailadresse* übertragen, die automatisch als Empfängeradresse übernommen wird.

---

<sup>1</sup> Beim Versand von HTML-Mails kommt ein **Rahmentemplate** zum Einsatz, das neben einem Abschnitt für die eigentlichen, per Formular übermittelten Inhalte auch Bereiche für die Einbettung eines Logos sowie einer Signatur enthält. Eine alternative, benutzerdefinierte Rahmenvorlage kann per Filterfunktion (Hook: [`immonex-kickstart-team_html_mail_twig_template_file`](../anpassung-erweiterung/immonex-kickstart-team-html-mail-twig-template-file)) definiert werden.