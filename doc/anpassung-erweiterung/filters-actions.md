# Filter & Actions

## Filter

### WordPress Bootstrap

- [inx_team_custom_post_type_args](filter-inx-team-custom-post-type-args) (Eigenschaften der benutzerdefinierten Beitragsarten)

### Agenturen

- [inx_team_agency_archive_title](filter-inx-team-agency-archive-title) (Titel der Agentur-Archivseiten)
- [inx_team_agency_networks](filter-inx-team-agency-networks) (Business/Soziale Netzwerke für Agenturen)
- [inx_team_agency_network_icons_output](filter-inx-team-agency-network-icons-output) (HTML-Code der Business/Social-Network-Icons von Agenturen)
- [inx_team_get_agency_template_data](filter-inx-team-get-agency-template-data) (komplette "Rohdaten" für das Template-Rendering einer Agentur abrufen)

### Kontaktpersonen

- [inx_team_agent_archive_title](filter-inx-team-agent-archive-title) (Titel der Kontaktpersonen-Archivseiten)
- [inx_team_agent_networks](filter-inx-team-agent-networks) (Business/Soziale Netzwerke für Kontaktpersonen)
- [inx_team_agent_network_icons_output](filter-inx-team-agent-network-icons-output) (HTML-Code der Business/Social-Network-Icons von Kontaktpersonen)
- [inx_team_force_agency_id_on_agent_update](filter-inx-team-force-agency-id-on-agent-update) (Fixe Agentur-ID für alle Kontaktpersonen)
- [inx_team_get_agent_template_data](filter-inx-team-get-agent-template-data) (komplette "Rohdaten" für das Template-Rendering einer Kontaktperson abrufen)

### Kontaktformular

- [inx_team_contact_form_fields](filter-inx-team-contact-form-fields) (Felder des Kontaktformulars)
- [inx_team_contact_form_notification_recipients](filter-inx-team-contact-form-notification-recipients) (Empfänger der Kontaktformular-Anfragemails und Absenderangaben für Eingangsbestätigungen)
- [inx_team_contact_form_notification_subject](filter-inx-team-contact-form-notification-subject) (Betreff der Kontaktformular-Mails)
- [inx_team_contact_form_notification_subject_variables](filter-inx-team-contact-form-notification-subject-variables) (Variablen für den Betreff der Kontaktformular-Mails)
- [inx_team_contact_form_mail_headers](filter-inx-team-contact-form-mail-headers) (Header der Kontaktformular-Mails)
- [inx_team_contact_form_user_data](filter-inx-team-contact-form-user-data) (via Frontend übermittelte Benutzer-Formulardaten)
- [inx_team_contact_form_rcpt_conf_attachments](filter-inx-team-contact-form-rcpt-conf-attachments) (Dateianhänge von Eingangsbestätigungsmails)
- [inx_team_contact_form_timestamp_check_threshold](filter-inx-team-contact-form-timestamp-check-threshold) (Schwellwert in Sekunden für die Formular-Spam-Prüfung)
- [inx_team_fallback_recipient_admin_email](filter-inx-team-fallback-recipient-admin-email) (Fallback-Admin-Mailadresse(n))
- [inx_team_openimmo_feedback_params](filter-inx-team-openimmo-feedback-params) (Inhalte des OpenImmo-Feedback-Anhangs)
- [inx_team_openimmo_feedback_xml_source](filter-inx-team-openimmo-feedback-xml-source) (XML-Quelltext des OpenImmo-Feedback-Anhangs)
- [inx_team_openimmo_feedback_attachment_filename](filter-inx-team-openimmo-feedback-attachment-filename) (Dateiname des OpenImmo-Feedback-Anhangs)
- [immonex-kickstart-team_html_mail_twig_template_file](immonex-kickstart-team-html-mail-twig-template-file) (Alternatives Rahmentemplate für Kontaktformular-HTML-Mails)

### Rendering / Templates (allgemein)

- [inx_team_template_search_folders](filter-inx-team-template-search-folders) (Basisordner für Skins/Templates ergänzen)
- [inx_team_template_folder_url_mappings](filter-inx-team-template-folder-url-mappings) (URL-Zuordnungen für nicht öffentlich zugängliche Template/Skin-Dateisystem-Basisordner definieren)

## Actions

### Rendering

- [inx_team_render_agency_list](action-inx-team-render-agency-list) (Agentur-Listenansicht)
- [inx_team_render_single_agency](action-inx-team-render-single-agency) (Agentur-Details)
- [inx_team_render_agent_list](action-inx-team-render-agent-list) (Kontaktpersonen-Listenansicht)
- [inx_team_render_single_agent](action-inx-team-render-single-agent) (Kontaktpersonen-Details)