<?php
/**
 * Removes the plugin's option, widget settings and cached city list on uninstall, on every site
 * of a multisite network.
 *
 * @package PanchangByAstroTathya
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Deletes this plugin's data on the current site.
 */
function astrotathya_panchang_uninstall_site() {
	delete_option( 'astrotathya_panchang_options' );
	delete_option( 'widget_astrotathya_panchang' );
}

if ( is_multisite() ) {
	foreach ( get_sites( array( 'fields' => 'ids', 'number' => 0 ) ) as $astrotathya_panchang_site_id ) {
		switch_to_blog( $astrotathya_panchang_site_id );
		astrotathya_panchang_uninstall_site();
		restore_current_blog();
	}
} else {
	astrotathya_panchang_uninstall_site();
}
