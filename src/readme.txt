=== immonex Kickstart Team ===
Contributors: immonex
Tags: immobilien, immobilienmakler, realestate, agent, agency
Requires at least: 5.5
Tested up to: 6.9
Stable Tag: 1.6.9
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

immonex Kickstart add-on for handling, linking and embedding OpenImmo-XML-based real estate agent/agency information and contact forms

== Description ==

This **add-on** plugin extends sites using the [immonex Kickstart base plugin](https://wordpress.org/plugins/immonex-kickstart/) by functions for automatically creating, updating, linking and integrating real estate agency and agent posts. All relevant data is collected on **importing OpenImmo-XML real estate offers** [2] and assigned to the associated property, agency and agent posts. Alternatively, these data records can also be created and updated manually in the WordPress backend.

Widgets for displaying agent/agency contact data and forms in property detail pages are also included, as well as ready-to-use, customizable templates for CPT archive pages and list views etc. Property related inquiry mails that are sent via the included, **unified contact form** contain an auto-generated **OpenImmo-Feedback XML attachment** usable for further processing steps, e.g. within the real estate management software that is used by the agency.

tl;dr
- See it in action at [base.immonex.one](https://base.immonex.one/unser-team/)!
- Install [immonex Kickstart](https://wordpress.org/plugins/immonex-kickstart/) first.
- Install a compatible OpenImmo import plugin [2] and import example data (both available free of charge at [immonex.dev](https://immonex.dev/) for testing/development).
- Read the [docs](https://docs.immonex.de/kickstart-team/) for detailed usage/customization instructions.

= immonex® =

**immonex** is an umbrella brand for various real estate related software solutions and services with a focus on german-speaking markets/users.

**immonex Kickstart** is a plugin providing essential basic components and an add-on framework for integrating imported OpenImmo-based property offers and related data in real estate websites built upon multi-purpose themes.

= OpenImmo® =

[OpenImmo-XML](http://openimmo.de/) is the de-facto standard for exchanging real estate data in the german-speaking countries. Here, it is supported by almost every common software solution and portal for real estate professionals (as import/export interfaces).

immonex OpenImmo2WP [2], initially released in 2015, is a tried and tested solution for importing OpenImmo-XML data into WordPress sites with support for the specific data structures of various popular real estate themes and frontend plugins.

= Main Features =

* Custom post types for real estate agents and agencies (automatic creation/update on property import processing)
* Lists, single views and widgets for embedding agent and agency contact data (including agent photo and company logo)
* Clean, responsive and fully customizable template set ("Skin")
* Possibility to add individual custom skins update-safe in the (child) theme folder
* Shortcodes for embedding list and single views (agent or agency details)
* Unified and simple (mail) contact form, usable in agent/agency single views and widgets and protected by multiple anti-spam checks
* OpenImmo-Feedback XML attachments for property inquiry mails via contact form
* Optional receipt confirmation mails on successful form submissions
* Separate, editable form consent texts on withdrawal and privacy policies (EU GDPR compliance)
* Translations provided via translate.wordpress.org (GlotPress)
* Current POT file and German translations included as PO/MO files additionally
* Plugin option strings translatable with Polylang or WPML

== Installation ==

= Prerequisities =

The following plugins should be installed first:

- [immonex Kickstart](https://wordpress.org/plugins/immonex-kickstart/)
- [immonex OpenImmo2WP](https://immonex.dev/) [2]

= Steps =

immonex Kickstart Team is available in the official [WordPress Plugin Directory](https://wordpress.org/plugins/) and can be installed via the WordPress backend.

1. *Plugins > Add New > Search for "immonex" ...* [1]
2. Check/Modify the default plugin options under *immonex > Settings > Team [Add-on]*.
3. OPTIONAL: Add Kickstart Team widgets to sidebars or shortcodes to arbitrary pages or page builder elements as needed, e.g. for embedding agent/agency lists or single views.
4. OPTIONAL: Reimport/Update [2] all properties (preferably full import) to auto-create agents and agency posts.
5. OPTIONAL: Complete/Extend the data of the agents and agencies that have been created automatically.

= Kickstart Team Shortcodes =

Agent List View: `[inx-team-agent-list]`
Agency List View: `[inx-team-agency-list]`
Agent Single View (Contact data/form, related properties): `[inx-team-agent]`
Agency Single View (Contact data/form, related agents & properties): `[inx-team-agency]`

(See documentation mentioned below for attributes and further details.)

[1] Alternatives: Download an installation ZIP file from the WP Plugin Directory or immonex.dev and select *Upload Plugin* **or** manually unzip and transfer it to the folder `wp-content/plugins`. In the latter case, activating the plugin under *Plugins > Installed Plugins* is required afterwards.

[2] Current and fully functional versions of premium immonex plugins as well as OpenImmo demo data are available **free of charge** at the [immonex Developer Portal](https://immonex.dev/) for testing/development and demonstration purposes.

= Documentation & Development =

A detailed plugin installation/integration documentation in German is available here:

[docs.immonex.de/kickstart-team](https://docs.immonex.de/kickstart-team/)

immonex Kickstart Team is free software. Sources, development docs/support and issue tracking are available at GitHub:

[github.com/immonex/kickstart-team](https://github.com/immonex/kickstart-team)

== Screenshots ==

1. Real estate agent list view via shortcode (team page)
2. Real estate agency detail view including related agent and property lists
3. Real estate agent detail view including list of related properties
4. Extended contact section and form on property detail pages
5. Agent/Contact person widget in property details sidebar
6. Agency list view in default archive page (Twenty Sixteen theme)
7. Agent list view in default archive page (Twenty Sixteen theme)
8. Agency and agent CPT provided by this add-on plugin
9. Agent widget configuration in Customizer
10. Add-on options

== Changelog ==

= 1.6.9 =
* Release date: 2025-08-28
* Fixed a post sorting issue.

= 1.6.8 =
* Release date: 2025-08-25
* Added optional legal notice section to agency detail views.
* Added filter hooks inx_team_agent_element_value and inx_team_agency_element_value.
* Improved inquiry mail recipient handling.
* Improved contact form and list view WCAG conformity.
* Reworked default skin SCSS code.
* Fixed some minor CSS issues (link colors).

= 1.6.0 =
* Release date: 2025-03-26
* Fixed a platform compatibility issue (new minimum PHP version: 7.4).
* Updated dependencies.

See changelog.txt for the complete version history.