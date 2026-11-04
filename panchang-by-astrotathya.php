<?php
/**
 * Plugin Name:       Panchang by AstroTathya
 * Description:       Daily Hindu panchang for any city: tithi, nakshatra, Rahu Kaal, festivals and vrats, in Hindi or English. Needs a free site key.
 * Version:           1.0.0
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            AstroTathya
 * Author URI:        https://astrotathya.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       panchang-by-astrotathya
 * Domain Path:       /languages
 *
 * @package PanchangByAstroTathya
 */

defined( 'ABSPATH' ) || exit;

define( 'ASTROTATHYA_PANCHANG_VERSION', '1.0.0' );
define( 'ASTROTATHYA_PANCHANG_FILE', __FILE__ );
define( 'ASTROTATHYA_PANCHANG_DIR', plugin_dir_path( __FILE__ ) );

require_once ASTROTATHYA_PANCHANG_DIR . 'includes/class-astrotathya-panchang-cities.php';
require_once ASTROTATHYA_PANCHANG_DIR . 'includes/class-astrotathya-panchang-render.php';
require_once ASTROTATHYA_PANCHANG_DIR . 'includes/class-astrotathya-panchang-settings.php';
require_once ASTROTATHYA_PANCHANG_DIR . 'includes/class-astrotathya-panchang-widget.php';

/**
 * Loads the bundled translations (Hindi ships with the plugin).
 */
function astrotathya_panchang_load_textdomain() {
	load_plugin_textdomain( 'panchang-by-astrotathya', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'astrotathya_panchang_load_textdomain', 0 );

/**
 * Registers the embed script, the block and the shortcode.
 */
function astrotathya_panchang_init() {
	Astrotathya_Panchang_Render::register_script();
	add_shortcode( 'astrotathya_panchang', array( 'Astrotathya_Panchang_Render', 'shortcode' ) );

	register_block_type( ASTROTATHYA_PANCHANG_DIR . 'blocks/panchang' );

	$handle = generate_block_asset_handle( 'astrotathya/panchang', 'editorScript' );
	wp_set_script_translations( $handle, 'panchang-by-astrotathya', ASTROTATHYA_PANCHANG_DIR . 'languages' );
}
add_action( 'init', 'astrotathya_panchang_init' );

/**
 * Hands the city list and saved defaults to the block editor script.
 */
function astrotathya_panchang_editor_data() {
	$handle = generate_block_asset_handle( 'astrotathya/panchang', 'editorScript' );
	$cities = array();
	foreach ( Astrotathya_Panchang_Cities::all() as $slug => $row ) {
		$cities[] = array(
			'value' => $slug,
			'label' => $row[0],
		);
	}
	$data = array(
		'cities'      => $cities,
		'defaults'    => Astrotathya_Panchang_Settings::get(),
		'siteLang'    => Astrotathya_Panchang_Render::site_lang(),
		'embedUrl'    => Astrotathya_Panchang_Render::ORIGIN . '/embed/',
		'settingsUrl' => current_user_can( 'manage_options' ) ? admin_url( 'options-general.php?page=' . Astrotathya_Panchang_Settings::PAGE ) : '',
	);
	wp_add_inline_script( $handle, 'window.astrotathyaPanchang = ' . wp_json_encode( $data ) . ';', 'before' );
}
add_action( 'enqueue_block_editor_assets', 'astrotathya_panchang_editor_data' );

add_action(
	'widgets_init',
	static function () {
		register_widget( 'Astrotathya_Panchang_Widget' );
	}
);

if ( is_admin() ) {
	Astrotathya_Panchang_Settings::hooks();
}
