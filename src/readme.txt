=== immonex Kickstart Team ===
Contributors: immonex
Tags: immobilien, immobilienmakler, realestate, agent, agency
Requires at least: 4.7
Tested up to: 6.7
Stable Tag: 1.5.2
Requires PHP: 5.6
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

Plugins like immonex OpenImmo2WP [2] are used to import OpenImmo-XML data into the specific WordPress/theme/plugin data structures of the destination site.

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
- [immonex OpenImmo2WP](https://immonex.dev/)

= Steps =

1. WordPress backend: *Plugins > Add New > Upload Plugin* [1]
2. Select the plugin ZIP file and click the install button.
3. Activate the plugin after successful installation.
4. Check/Modify the default plugin options under *immonex > Settings > Team [Add-on]*.
5. OPTIONAL: Add Kickstart Team widgets to sidebars or shortcodes to arbitrary pages or page builder elements as needed, e.g. for embedding agent/agency lists or single views.
6. OPTIONAL: Reimport/Update [2] all properties (preferably full import) to auto-create agents and agency posts.
7. OPTIONAL: Complete/Extend the data of the agents and agencies that have been created automatically.

= Kickstart Team Shortcodes =

Agent List View: `[inx-team-agent-list]`
Agency List View: `[inx-team-agency-list]`
Agent Single View (Contact data/form, related properties): `[inx-team-agent]`
Agency Single View (Contact data/form, related agents & properties): `[inx-team-agency]`

(See documentation mentioned below for attributes and further details.)

[1] Alternative: Unzip the plugin ZIP archive, copy it to the folder `wp-content/plugins` and activate the plugin in the WordPress backend under *Plugins > Installed Plugins* afterwards.

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

= 1.5.2 =
* Release date: 2024-09-09
* Fixed possible PHP warnings during contact form processing.
* Reverified WordPress 6.7 compatibility.
* Updated dependencies.

= 1.5.0 =
* Release date: 2024-09-05
* Added plugin options for form mail sender name and address.
* Added automatic agency logo import/update.
* Reworked form recipient determination.
* Updated inquiry/contact form withdrawal note.
* Updated dependencies.

= 1.4.4 =
* Release date: 2024-01-22
* Resolved Twig issues related to third-party plugins.
* Fixed translation file selection (beta versions only).
* Updated dependencies (improved compatibility with current WP/PHP versions).

See changelog.txt for the complete version history.