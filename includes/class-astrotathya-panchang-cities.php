<?php
/**
 * City list: the cities bundled with the plugin. Any other city works by typing its slug.
 *
 * @package PanchangByAstroTathya
 */

defined( 'ABSPATH' ) || exit;

/**
 * Looks up cities by slug. The plugin makes no request of its own: the list ships with it.
 */
final class Astrotathya_Panchang_Cities {

	const SLUG_RE = '/^[a-z0-9-]{1,64}$/';

	/**
	 * Bundled list, loaded once.
	 *
	 * @var array|null
	 */
	private static $featured = null;

	/**
	 * The cities shipped with the plugin.
	 *
	 * @return array slug => array( name_en, name_hi, country ).
	 */
	public static function featured() {
		if ( null === self::$featured ) {
			self::$featured = require ASTROTATHYA_PANCHANG_DIR . 'includes/cities-data.php';
		}
		return self::$featured;
	}

	/**
	 * Every city the pickers offer: the bundled list.
	 *
	 * @return array slug => array( name_en, name_hi, country ).
	 */
	public static function all() {
		return self::featured();
	}

	/**
	 * Whether a string is a well-formed city slug.
	 *
	 * @param mixed $slug Candidate slug.
	 * @return bool
	 */
	public static function is_slug( $slug ) {
		return is_string( $slug ) && 1 === preg_match( self::SLUG_RE, $slug );
	}

	/**
	 * Display name for a city, without any network request.
	 *
	 * @param string $slug City slug.
	 * @param string $lang "en" or "hi".
	 * @return string
	 */
	public static function name( $slug, $lang = 'en' ) {
		$cities = self::featured();
		if ( isset( $cities[ $slug ] ) ) {
			return 'hi' === $lang ? $cities[ $slug ][1] : $cities[ $slug ][0];
		}
		return ucwords( str_replace( '-', ' ', $slug ) );
	}
}
