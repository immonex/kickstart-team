<img src="assets/immonex-kickstart-team-logo.png" width="286" height="286" align="right" alt="immonex Kickstart Team Logo">

# immonex Kickstart Team

Kickstart Team is an **add-on plugin** that extends real-estate websites based on **WordPress**, [immonex Kickstart](https://de.wordpress.org/plugins/immonex-kickstart/) and [immonex OpenImmo2WP](https://immonex.dev/).

It adds functionality for automated creating, updating, linking and integrating information and **contact data on real estate agents and agencies**. All relevant data are collected on **importing OpenImmo-XML-based real estate offers** and assigned to the related property, real estate agency and agent posts automatically.

**immonex**® is an umbrella brand for various real estate related software solutions and services with a focus on german-speaking countries/users.

[OpenImmo-XML](http://openimmo.de/) is the de-facto standard for exchanging real estate data in the german-speaking countries. Here, it is supported by almost every common software solution and portal for realtors (import/export interfaces).

## Basics & Scope

Kickstart Team itself will be available in the official [WordPress Plugin Repository](https://wordpress.org/plugins/) soon. A **user documentation** including detailed instructions how to install, setup and customize the plugin (in German) is available here:

https://docs.immonex.de/kickstart-team/

With *skins* (template sets), an easy and update-safe way for implementing extensive individual customization (if required) is being provided. The following sections relate to the development of the **core add-on plugin** in this repository only.

## Development

### Requirements

- [Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
- [npm (Node.js)](https://www.npmjs.com/get-npm)
- [Composer](https://getcomposer.org/)
- [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer/)
- [WordPress Coding Standards for PHP_CodeSniffer](https://github.com/WordPress/WordPress-Coding-Standards)
- [WP-CLI](https://wp-cli.org/)
- ready-to-go [WordPress](https://wordpress.org/download/) installation on a local webserver

### Guidelines

**We keep it simple and rely on battle-proven standards and best practices whenever possible!**

- PHP compatibility: 7.4+ (switch to 8 envisaged for future releases)
- Coding Standard (PHP): [WordPress PHP Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/)
- Git branching strategy: [GitHub flow](https://guides.github.com/introduction/flow/)
- Git commit messages: [Conventional Commits](https://www.conventionalcommits.org/)
- CSS of *Core Skins* (template sets) shipped with this plugin are most widely built upon the [BEM methodology](https://en.bem.info/methodology/) with [Two Dashes style](https://en.bem.info/methodology/naming-convention/#two-dashes-style) as naming convention.
- Plugin and skin CSS files are being compiled from SCSS files during the build process using [node-sass](https://github.com/sass/node-sass).

### Setup

Setting up a simple development environment starts by cloning this repository and installing dependencies:

```bash
$ cd ~/projects
$ git clone git@github.com:immonex/kickstart-team.git immonex-kickstart-team
$ cd immonex-kickstart-team
$ npm install
$ composer install
```
> :warning: PHP_CodeSniffer and the related WP sniffs are **not** part of the default dependencies and should be [installed globally](https://github.com/WordPress/WordPress-Coding-Standards#composer).

Then, a symlink to the `src` directory has to be created in the `plugins` folder of the local WP installation:

```bash
$ ln -s ~/projects/immonex-kickstart-team/src/ ~/htdocs/wp-dev-installation/wp-content/plugins/immonex-kickstart-team
```

Now, the plugin can be activated in the WP backend.

### Building

The JS/CSS build process is based on the module/asset bundler [webpack](https://webpack.js.org/). A set of simple **npm scripts** defined in `package.json` is used to perform the required steps (no Gulp or Grunt needed).

Create a **production version** of the sources in the `build` folder and a corresponding ZIP archive in `dist`:

```bash
$ npm run build
```

Create and serve a **development version** with automatic rebuild and browser reload whenever PHP, (S)CSS or JS files are updated:

```bash
$ npm run watch
```

For the latter option, a [Browsersync](https://browsersync.io/) proxy URL to the **local WP dev installation** has to be defined in the file `.env` ([use .env.example](.env.example) as template).

### Coding Standard

The PHP source code formatting corresponds to the [WordPress PHP Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/).

The source files can be checked with PHP_CodeSniffer (if, as recommended, installed globally as described [here](https://github.com/WordPress/WordPress-Coding-Standards#composer)):

```bash
$ phpcs
```

To fix violations automatically as far as possible:

```bash
$ phpcbf
```

> :warning: The JavaScript code may differ from the (slightly dusty...) WP recommendations in favor of a more contemporary style.

### Testing

Locally running unit tests ([PHPUnit](https://phpunit.de/)) requires an additional **temporary** WordPress installation (see [infos on make.wordpress.org](https://make.wordpress.org/cli/handbook/plugin-unit-tests/#running-tests-locally)). To use the test install script included in this repository, the file `.env` containing credentials of a local test database has to be created or extended first (see [.env.example](.env.example)).

After that, the temporary testing environment can be installed:

```bash
$ npm run test:install
```

Running tests in the `tests` folder:

```bash
$ npm run test
```

### Translations

The **regular translations** for this plugin are provided via [translate.wordpress.org (GlotPress)](https://translate.wordpress.org/projects/wp-plugins/immonex-kickstart-team/).

Additionally, formal and informal German translations (PO/MO files) are available in the [src/languages](src/languages) folder, too. This directory also contains a current POT file (as base for custom/local translations) that can be updated with the following command:

```bash
$ npm run pot
```

### API Documentation

The API documentation based on the sources can be generated with the following command and is available in the `apidoc` folder afterwards:

```bash
$ npm run apidoc
```

To view it using a local webserver (port 8000):

```bash
$ npm run apidoc:view
```

If these docs are not needed anymore, the respective folders can be deleted with this command:

```bash
$ npm run apidoc:delete
```

(The folder `apidoc` is meant to be used locally, it should **not** be a part of any repository.)

### User/Integrator Documentation

The source files (markdown) of the [docs for users and integrators](https://docs.immonex.de/kickstart-team/) mentioned above are located in the `doc` folder. A globally installed *doc-gen* package (currently **not** available publicly) and a suitable user account are required for publishing...

```bash
$ npm run doc
```

...or publishing it under https://docs.immonex.de/kickstart-team/:

```bash
$ npm run doc:publish
```

## License

[GPLv2 or later](LICENSE)

Copyright (C) 2014, 2020 [inveris OHG](https://inveris.de/) / [immonex](https://immonex.dev/)

This plugin is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

## Trivia

The immonex Kickstart and add-on logos are a tribute to the developers of the original **Amiga** (later known as [Commodore Amiga 1000](https://en.wikipedia.org/wiki/Amiga_1000)). :smirk: