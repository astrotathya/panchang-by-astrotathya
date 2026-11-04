<?php
/**
 * Builds the widget markup shared by the block, the classic widget and the shortcode.
 *
 * @package PanchangByAstroTathya
 */

defined( 'ABSPATH' ) || exit;

/**
 * Renders the AstroTathya panchang / festivals widget.
 */
final class Astrotathya_Panchang_Render {

	const ORIGIN = 'https://astrotathya.com';

	/** Iframe heights in px by widget type and size (the front end, the block and the settings preview). */
	const HEIGHTS = array(
		'panchang'  => array(
			'card'   => 356,
			'banner' => 124,
		),
		'festivals' => array(
			'card'   => 372,
			'banner' => 124,
		),
	);
	const HANDLE = 'astrotathya-panchang-embed';

	const TYPES  = array( 'panchang', 'festivals' );
	const LANGS  = array( 'auto', 'en', 'hi' );
	const THEMES = array( 'light', 'dark', 'maroon' );
	const SIZES  = array( 'card', 'banner' );
	const MODES  = array( 'script', 'iframe' );

	/** The widget's own defaults on astrotathya.com (src/lib/widget-variant.ts DEFAULT_VARIANT); left out of its URL. */
	const URL_DEFAULTS = array(
		'lang'  => 'en',
		'theme' => 'light',
		'size'  => 'card',
	);

	/**
	 * Registers (does not enqueue) the hosted loader; it is enqueued only on pages showing a widget.
	 */
	public static function register_script() {
		wp_register_script(
			self::HANDLE,
			self::ORIGIN . '/embed.js',
			array(),
			ASTROTATHYA_PANCHANG_VERSION,
			array(
				'in_footer' => true,
				'strategy'  => 'async',
			)
		);
	}

	/**
	 * Shortcode callback: [astrotathya_panchang city="mumbai" type="panchang" lang="hi" theme="maroon" size="card"].
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @return string
	 */
	public static function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'city'  => '',
				'type'  => '',
				'lang'  => '',
				'theme' => '',
				'size'  => '',
			),
			is_array( $atts ) ? $atts : array(),
			'astrotathya_panchang'
		);
		return self::html( $atts );
	}

	/**
	 * Language of the site, mapped to a widget language.
	 *
	 * @return string "hi" or "en".
	 */
	public static function site_lang() {
		return 0 === strpos( determine_locale(), 'hi' ) ? 'hi' : 'en';
	}

	/**
	 * Merges attributes over the saved defaults and validates every value against its allow-list.
	 *
	 * @param array $atts Raw attributes; empty values fall back to the settings.
	 * @return array Clean values: city, type, lang (en|hi), theme, size, mode, credit, key.
	 */
	public static function normalize( array $atts ) {
		$defaults = Astrotathya_Panchang_Settings::get();
		$pick     = static function ( $key, array $allowed ) use ( $atts, $defaults ) {
			$value = isset( $atts[ $key ] ) ? strtolower( trim( (string) $atts[ $key ] ) ) : '';
			return in_array( $value, $allowed, true ) ? $value : $defaults[ $key ];
		};

		$city = isset( $atts['city'] ) ? strtolower( trim( (string) $atts['city'] ) ) : '';
		$lang = $pick( 'lang', self::LANGS );

		return array(
			'city'   => Astrotathya_Panchang_Cities::is_slug( $city ) ? $city : $defaults['city'],
			'type'   => $pick( 'type', self::TYPES ),
			'lang'   => 'auto' === $lang ? self::site_lang() : $lang,
			'theme'  => $pick( 'theme', self::THEMES ),
			'size'   => $pick( 'size', self::SIZES ),
			'mode'   => $defaults['mode'],
			'credit' => $defaults['credit'],
			'key'    => Astrotathya_Panchang_Settings::clean_key( $defaults['key'] ),
		);
	}

	/**
	 * Iframe URL for a widget, in the form astrotathya.com's own loader (embed.js) builds:
	 * /embed/{panchang|festivals}/{city}, then lang, theme and size only when they differ from the
	 * widget's defaults (en, light, card), then the site key as k. The site keeps one cached page
	 * per distinct widget, so default values stay out of the address.
	 *
	 * @param array $w Normalized widget values.
	 * @return string
	 */
	public static function iframe_url( array $w ) {
		$args = array();
		foreach ( array( 'lang', 'theme', 'size' ) as $part ) {
			if ( $w[ $part ] !== self::URL_DEFAULTS[ $part ] ) {
				$args[ $part ] = $w[ $part ];
			}
		}
		// The site key: the widget answers only for the site it was issued to, and only for the cities and
		// site addresses the key was issued for.
		$key = isset( $w['key'] ) ? Astrotathya_Panchang_Settings::clean_key( $w['key'] ) : '';
		if ( '' !== $key ) {
			$args['k'] = $key;
		}
		return add_query_arg( array_map( 'rawurlencode', $args ), self::ORIGIN . '/embed/' . $w['type'] . '/' . $w['city'] );
	}

	/**
	 * What a block, widget or shortcode shows while no site key is set: nothing for visitors (a
	 * widget without a key would only show AstroTathya's invitation card), and for anyone who can
	 * change the settings a short note pointing to Settings > Panchang.
	 *
	 * @return string
	 */
	public static function no_key_html() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '';
		}
		return sprintf(
			'<div class="astrotathya-panchang astrotathya-panchang--no-key" style="padding:12px 14px;border:1px dashed #c3c4c7;border-radius:8px;font-size:14px;line-height:1.5;"><p><strong>%1$s</strong> %2$s <a href="%3$s">%4$s</a></p></div>',
			esc_html__( 'Add your site key.', 'panchang-by-astrotathya' ),
			esc_html__( 'The panchang widget needs a free site key for this site. Get one at astrotathya.com/panchang-widget and enter it in Settings > Panchang. Visitors see nothing here until then; only site administrators see this note.', 'panchang-by-astrotathya' ),
			esc_url( admin_url( 'options-general.php?page=' . Astrotathya_Panchang_Settings::PAGE ) ),
			esc_html__( 'Settings > Panchang', 'panchang-by-astrotathya' )
		);
	}

	/**
	 * Widget markup. Every value is validated in normalize() and escaped here.
	 *
	 * @param array $atts Raw attributes.
	 * @return string
	 */
	public static function html( array $atts ) {
		$w = self::normalize( $atts );
		if ( '' === $w['key'] ) {
			return self::no_key_html();
		}

		$titles  = array(
			'panchang'  => array(
				'en' => "Today's panchang",
				'hi' => 'आज का पंचांग',
			),
			'festivals' => array(
				'en' => 'Upcoming festivals',
				'hi' => 'आने वाले त्योहार',
			),
		);

		$iframe = sprintf(
			'<iframe src="%1$s" title="%2$s" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" sandbox="allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox" style="width:100%%;height:%3$dpx;border:0;"></iframe>',
			esc_url( self::iframe_url( $w ) ),
			esc_attr( $titles[ $w['type'] ][ $w['lang'] ] ),
			(int) self::HEIGHTS[ $w['type'] ][ $w['size'] ]
		);

		$credit = '';
		if ( $w['credit'] ) {
			$city   = Astrotathya_Panchang_Cities::name( $w['city'], $w['lang'] );
			$label  = 'hi' === $w['lang']
				? ( 'festivals' === $w['type'] ? '%s के त्योहार:' : '%s पंचांग:' )
				: ( 'festivals' === $w['type'] ? '%s festivals by' : '%s panchang by' );
			$credit = sprintf(
				'<p class="astrotathya-panchang__credit">%1$s <a href="%2$s" rel="nofollow noopener">AstroTathya</a></p>',
				esc_html( sprintf( $label, $city ) ),
				esc_url( self::ORIGIN . '/panchang/' . $w['city'] )
			);
		}

		if ( 'iframe' === $w['mode'] ) {
			return '<div class="astrotathya-panchang">' . $iframe . $credit . '</div>';
		}

		wp_enqueue_script( self::HANDLE );

		return sprintf(
			'<div class="astrotathya-panchang" data-astrotathya-panchang="" data-city="%1$s" data-widget="%2$s" data-lang="%3$s" data-theme="%4$s" data-size="%5$s"%6$s>%7$s<noscript>%8$s</noscript></div>',
			esc_attr( $w['city'] ),
			esc_attr( $w['type'] ),
			esc_attr( $w['lang'] ),
			esc_attr( $w['theme'] ),
			esc_attr( $w['size'] ),
			'' !== $w['key'] ? ' data-key="' . esc_attr( $w['key'] ) . '"' : '',
			$credit,
			$iframe
		);
	}

	/**
	 * Tags and attributes the widget markup may contain, for wp_kses() at echo time.
	 *
	 * @return array
	 */
	public static function allowed_html() {
		return array(
			'div'      => array(
				'class'  => true,
				'id'     => true,
				'style'  => true,
				'data-*' => true,
			),
			'p'        => array( 'class' => true ),
			'strong'   => array(),
			'a'        => array(
				'href' => true,
				'rel'  => true,
			),
			'noscript' => array(),
			'iframe'   => array(
				'src'            => true,
				'title'          => true,
				'loading'        => true,
				'referrerpolicy' => true,
				'sandbox'        => true,
				'style'          => true,
			),
		);
	}
}
