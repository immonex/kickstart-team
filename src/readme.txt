=== immonex Kickstart Team ===
Contributors: immonex
Tags: immobilien, immobilienmakler, realestate, agent, openimmo
Requires at least: 5.5
Tested up to: 7.0
Stable Tag: 1.8.3
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

**immonex** is the *PropTech umbrella brand* of a versatile portfolio of software solutions for the German-speaking real estate industry.

As a part of this, the **immonex WP Plugin Suite** includes a wide range of WordPress plugins for the implementation of sophisticated real estate agency websites and portals, which can be flexibly combined depending on the specific project requirements.

[immonex Kickstart](https://wordpress.org/plugins/immonex-kickstart/) is a license-fee free [Open Source Plugin](https://github.com/immonex/kickstart/) that extends WordPress sites – regardless of the theme used – by essential components for publishing real estate offers, which are synchronized via an import interface: list and detail views, property search, location maps etc. The range of functions and content elements can be expanded modularly by various add-ons as needed.

= OpenImmo® =

[OpenImmo-XML](http://openimmo.de/) is a proven standard for the exchange of real estate data, which is supported primarily in German-speaking markets by almost all common software solutions and portals for real estate professionals in the form of corresponding import/export interfaces.

immonex OpenImmo2WP [2], initially released in 2015, is a tried and tested solution for importing OpenImmo-XML data into WordPress sites that supports the specific data structures of various popular real estate themes and frontend plugins by means of customizable *mapping tables*.

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
* SEO/GEO: Extensive structured data (Schema.org markup) for agents and agencies
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

= 1.8.6-beta =
* Release date: ?
* Added spam protection controls to plugin options.
* Removed unneccessary geocoding requests.
* Updated dependencies.

= 1.8.3 =
* Release date: 2026-02-11
* Added filtering of autofilled honeypot form fields.
* Reworked automated agency assignment.
* Updated dependencies.

= 1.8.0 =
* Release date: 2026-01-10
* Added optional archive links to shortcode-based agency and agent lists.
* Added pagination to agency and agent archive pages.
* Fixed an OpenImmo-Feedback XML special character encoding issue.
* Fixed partially duplicate structured data embedding in archive pages.
* Updated dependencies.

See changelog.txt for the complete version history.