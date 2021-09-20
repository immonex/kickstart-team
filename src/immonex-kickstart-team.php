<?php
/**
 * Plugin Name:       immonex Kickstart Team
 * Plugin URI:        https://wordpress.org/plugins/immonex-kickstart-team/
 * Description:       immonex Kickstart add-on for handling, linking and embedding OpenImmo-XML-based real estate agent/agency information and contact forms
 * Version:           1.1.15-beta
 * Requires at least: 4.7
 * Requires PHP:      5.6
 * Author:            inveris OHG / immonex
 * Author URI:        https://immonex.dev/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       immonex-kickstart-team
 *
 * @codingStandardsIgnoreLine
 * immonex Kickstart Team is free software: you can redistribute it and/ormodify it under
 * the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 2 of the License, or any
 * later version.
 *
 * immonex Kickstart Team is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this software. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 *
 * @package immonex-kickstart-team
 */

namespace immonex\Kickstart\Team;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Initialize autoloaders (Composer AND WP/plugin-specific).
 */
require __DIR__ . '/autoload.php';

/**
 * Load and register TGMPA.
 */
require __DIR__ . '/tgmpa.php';

$immonex_kickstart_team = new Kickstart_Team( basename( __FILE__, '.php' ) );
$immonex_kickstart_team->init( 20 );
