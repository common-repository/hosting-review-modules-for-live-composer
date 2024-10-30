<?php
/**
 * @link              https://hosting.review
 * @since             0.0.1
 * @package           HRMFLC
 *
 * @wordpress-plugin
 * Plugin Name:       Hosting Review Modules for Live Composer
 * Plugin URI:        https://hosting.review
 * Description:       Adds a list of modules for live composer, first of which is ACF Placeholder, which allows to output Advanced Custom Fields data to frontend.
 * Version:           0.0.1
 * Author:            Hosting Review Team
 * Author URI:        https://hosting.review/about-us/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hrmflc
 * Domain Path:       /languages
 *
 * Hosting Review Modules for Live Composer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Hosting Review Modules for Live Composer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Live Composer. If not, see <http://www.gnu.org/licenses/>.
 *
 */


// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}


define( 'HRMFLC_ROOT_URL', plugin_dir_url( __FILE__ ) );
define( 'HRMFLC_ROOT_DIR', plugin_dir_path( __FILE__ ) );

if ( ! class_exists( 'HRMFLC' ) ) {
	class HRMFLC {

		public $plugin = 'Hosting Review Modules for Live Composer';
		public $shortname = 'hrmflc';
		public $version = '0.0.1';
		public $optionsPage;

		public function __construct() {
			add_action( 'init', [ $this, 'add_options' ] );
			add_action( 'plugins_loaded', [ $this, 'load_acf_placeholder' ] );
			add_action( 'wp_footer', [ $this, 'powered_by_optional' ]);
		}

		public function acf_placeholder_admin_notice__error() {
			$class   = 'notice notice-error';
			$message = __( 'Plugins: Live Composer and Advanced Custom Fields or Advanced Custom Fields Pro, are required in order for this plugin to work.', $this->shortname );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		}

		public function load_acf_placeholder() {
			if ( defined( 'DS_LIVE_COMPOSER_VER' ) && class_exists( 'acf' ) ) {
				require_once HRMFLC_ROOT_DIR . 'modules/acf-placeholder/module.php';
			} else {
				add_action( 'admin_notices', [ $this, 'acf_placeholder_admin_notice__error'] );
			}
		}

		public function add_options() {
			require_once HRMFLC_ROOT_DIR . 'includes/optionsPage.php';
			$this->optionsPage = new HRMFLCOptionsPage($this->plugin, $this->shortname, $this->version);
		}

		public function powered_by_optional() {
			$options = get_option($this->shortname . '_main', 'no');
			$is_on = $options == 'yes' ? 'yes' : $options['powered_by_switch'];

			if($is_on == 'yes') {
				echo '<div><div class="wrap"><p>Our website is supported by <a href="https://hosting.review" title="Click to go to Hosting Review website">hosting.review</a> plugin</p></div></div>';
			}
		}

	}

	$HRMFLC = new HRMFLC();
}


