<?php
/**
 * Server-side render of the astrotathya/panchang block.
 *
 * @package PanchangByAstroTathya
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

$astrotathya_panchang_html = Astrotathya_Panchang_Render::html( $attributes );

// No site key and a visitor: nothing at all, not even an empty wrapper.
if ( '' !== $astrotathya_panchang_html ) {
	echo wp_kses(
		'<div ' . get_block_wrapper_attributes() . '>' . $astrotathya_panchang_html . '</div>',
		Astrotathya_Panchang_Render::allowed_html()
	);
}
