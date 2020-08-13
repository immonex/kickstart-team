=== immonex Kickstart Team ===
Contributors: immonex
Tags: realestate, agent, agency, openimmo, immomakler
Requires at least: 4.7
Tested up to: 5.5
Stable Tag: 1.0.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

immonex Kickstart add-on for handling, linking and integrating real estate (OpenImmo-XML) related agent/agency information and contact forms

== Description ==

This plugin is an **add-on** solution for automated creating, updating, linking and integrating information and contact data on real estate agents and agencies. All relevant data are collected on **importing OpenImmo-XML-based real estate objects** [2] and assigned to the related property, realtor and real estate agent posts automatically. Of course, these records can also be created and edited manually in the WordPress backend.

Widgets for displaying agent and agency information and a unified contact form in property detail pages are also included, as well as ready-to-use, customizable templates for CPT archive pages and list views etc. Property inquiries that are sent via the provided contact form contain an auto-generated OpenImmo-Feedback XML attachment suitable for further processing steps within the used real estate management software.

tl;dr
- See it in action at [base.immonex.one](https://base.immonex.one/unser-team/)!
- Install [immonex Kickstart](https://wordpress.org/plugins/immonex-kickstart/) first.
- Download and install a compatible OpenImmo import plugin [2] and example data at [immonex.dev](https://immonex.dev/) (free of charge for testing/development).
- Read the [docs](https://docs.immonex.de/kickstart-team/) for detailed usage/customization instructions.

= immonex® =

**immonex** is an umbrella brand for various real estate related software solutions and services with a focus on german-speaking markets/users.

**immonex Kickstart** is a plugin providing essential basic components and an add-on framework for integrating imported OpenImmo-based property offers in real estate websites built upon multi-purpose themes.

= OpenImmo® =

[OpenImmo-XML](http://openimmo.de/) is the de-facto standard for exchanging real estate data in the german-speaking countries. Here, it is supported by almost every common software solution and portal for realtors (import/export interfaces).

Plugins like immonex OpenImmo2WP [2] are used to import OpenImmo-XML data into the specific WordPress/theme/plugin data structures of the destination site.

= Main Features =

* Custom post types for real estate agents and agencies (automatic creation/update on property imports)
* Widgets for embedding agent and agency contact data (including agent photo and company logo)
* Customizable template set for CPT archive, list, single and widget views ("Skin")
* Clean and responsive default skin
* Possibility to use update-safe custom skins in the child theme folder
* Shortcodes for embedding list and single (agent or agency details) views
* Unified (mail) contact form, usable in single views and widgets
* OpenImmo-Feedback XML attachments for property inquiries via contact form
* Separate, editable form consent texts on withdrawal and privacy policies
* Translation via translate.wordpress.org (GlotPress)
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
4. Check/Modify the default plugin options under *immonex > Settings > Kickstart Team [Add-on]*.
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

[2] Current and fully functional versions of premium immonex plugins as well as OpenImmo demo data are available **free of charge** at the [immonex Developer Portal](https://immonex.dev/) for testing/development purposes.

= Documentation & Development =

A detailed plugin installation/integration documentation in German is available here:

[docs.immonex.de/kickstart-team](https://docs.immonex.de/kickstart-team/)

immonex Kickstart Team is free software. Sources, development docs/support and issue tracking are available at GitHub:

[github.com/immonex/kickstart-team](https://github.com/immonex/kickstart-team)

== Screenshots ==

1. Screenshot descriptions follow...

== Changelog ==

= 1.0.0 =
* Release date: 2020-08-31
* Initial release.
