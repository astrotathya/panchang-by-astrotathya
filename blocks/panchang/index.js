/**
 * Editor side of the "Panchang by AstroTathya" block. Plain script, no build step.
 * The front end is rendered by render.php; the editor shows the same widget in an iframe.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var TextControl = wp.components.TextControl;
	var Placeholder = wp.components.Placeholder;

	var data = window.astrotathyaPanchang || { cities: [], defaults: {}, siteLang: 'en', embedUrl: 'https://astrotathya.com/embed/' };
	var SLUG = /^[a-z0-9-]{1,64}$/;
	// The same allowlists as the PHP side (includes/class-astrotathya-panchang-render.php).
	var TYPES = [ 'panchang', 'festivals' ];
	var LANGS = [ 'en', 'hi' ];
	var THEMES = [ 'light', 'dark', 'maroon' ];
	var SIZES = [ 'card', 'banner' ];
	function pick( value, allowed, fallback ) {
		return allowed.indexOf( value ) !== -1 ? value : fallback;
	}
	var HEIGHTS = { panchang: { card: 356, banner: 124 }, festivals: { card: 372, banner: 124 } };

	function withDefault( options ) {
		return [ { value: '', label: __( 'Site default', 'panchang-by-astrotathya' ) } ].concat( options );
	}

	function resolved( attributes ) {
		var d = data.defaults;
		var lang = attributes.lang || d.lang || 'auto';
		return {
			city: SLUG.test( attributes.city ) ? attributes.city : SLUG.test( d.city ) ? d.city : 'new-delhi',
			type: pick( attributes.type || d.type, TYPES, 'panchang' ),
			lang: pick( 'auto' === lang ? data.siteLang : lang, LANGS, 'en' ),
			theme: pick( attributes.theme || d.theme, THEMES, 'light' ),
			size: pick( attributes.size || d.size, SIZES, 'card' ),
		};
	}

	function Edit( props ) {
		var a = props.attributes;
		var set = function ( key ) {
			return function ( value ) {
				var next = {};
				next[ key ] = value;
				props.setAttributes( next );
			};
		};
		var w = resolved( a );
		// The same address the PHP side builds: defaults (en, light, card) left out, then the key.
		var q = [];
		if ( 'en' !== w.lang ) {
			q.push( 'lang=' + w.lang );
		}
		if ( 'light' !== w.theme ) {
			q.push( 'theme=' + w.theme );
		}
		if ( 'card' !== w.size ) {
			q.push( 'size=' + w.size );
		}
		// The site key from Settings > Panchang (cleaned there; cleaned again here before it joins the address).
		var key = String( ( data.defaults && data.defaults.key ) || '' ).replace( /[^A-Za-z0-9._-]/g, '' );
		if ( key ) {
			q.push( 'k=' + encodeURIComponent( key ) );
		}
		var src = data.embedUrl + w.type + '/' + w.city + ( q.length ? '?' + q.join( '&' ) : '' );
		// Without a key the front end shows visitors nothing; the editor says so instead of a preview.
		var preview = key
			? el( 'iframe', {
					src: src,
					title: __( 'Panchang preview', 'panchang-by-astrotathya' ),
					sandbox: 'allow-scripts allow-same-origin',
					style: { width: '100%', height: ( ( HEIGHTS[ w.type ] || HEIGHTS.panchang )[ w.size ] || 356 ) + 'px', border: 0, pointerEvents: 'none' },
			  } )
			: el(
					Placeholder,
					{
						icon: 'calendar-alt',
						label: __( 'Add your site key', 'panchang-by-astrotathya' ),
						instructions: __( 'The panchang widget needs a free site key for this site, from astrotathya.com/panchang-widget. Until one is entered in Settings > Panchang, visitors see nothing here.', 'panchang-by-astrotathya' ),
					},
					data.settingsUrl
						? el( 'a', { href: data.settingsUrl, className: 'components-button is-secondary' }, __( 'Open Settings > Panchang', 'panchang-by-astrotathya' ) )
						: null
			  );

		return el(
			'div',
			useBlockProps(),
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Widget', 'panchang-by-astrotathya' ) },
					el( SelectControl, {
						label: __( 'City', 'panchang-by-astrotathya' ),
						value: a.city,
						options: withDefault( data.cities ),
						onChange: set( 'city' ),
						__nextHasNoMarginBottom: true,
					} ),
					el( TextControl, {
						label: __( 'Or type a city slug', 'panchang-by-astrotathya' ),
						help: __( 'As in astrotathya.com/panchang/your-city', 'panchang-by-astrotathya' ),
						value: a.city,
						onChange: function ( value ) {
							props.setAttributes( { city: value.toLowerCase().trim() } );
						},
						__nextHasNoMarginBottom: true,
					} ),
					el( SelectControl, {
						label: __( 'Show', 'panchang-by-astrotathya' ),
						value: a.type,
						options: withDefault( [
							{ value: 'panchang', label: __( "Today's panchang", 'panchang-by-astrotathya' ) },
							{ value: 'festivals', label: __( 'Upcoming festivals', 'panchang-by-astrotathya' ) },
						] ),
						onChange: set( 'type' ),
						__nextHasNoMarginBottom: true,
					} ),
					el( SelectControl, {
						label: __( 'Language', 'panchang-by-astrotathya' ),
						value: a.lang,
						options: withDefault( [
							{ value: 'en', label: __( 'English', 'panchang-by-astrotathya' ) },
							{ value: 'hi', label: __( 'Hindi', 'panchang-by-astrotathya' ) },
						] ),
						onChange: set( 'lang' ),
						__nextHasNoMarginBottom: true,
					} ),
					el( SelectControl, {
						label: __( 'Theme', 'panchang-by-astrotathya' ),
						value: a.theme,
						options: withDefault( [
							{ value: 'light', label: __( 'Light', 'panchang-by-astrotathya' ) },
							{ value: 'dark', label: __( 'Dark', 'panchang-by-astrotathya' ) },
							{ value: 'maroon', label: __( 'Maroon', 'panchang-by-astrotathya' ) },
						] ),
						onChange: set( 'theme' ),
						__nextHasNoMarginBottom: true,
					} ),
					el( SelectControl, {
						label: __( 'Size', 'panchang-by-astrotathya' ),
						value: a.size,
						options: withDefault( [
							{ value: 'card', label: __( 'Card', 'panchang-by-astrotathya' ) },
							{ value: 'banner', label: __( 'Banner', 'panchang-by-astrotathya' ) },
						] ),
						onChange: set( 'size' ),
						__nextHasNoMarginBottom: true,
					} )
				)
			),
			preview
		);
	}

	wp.blocks.registerBlockType( 'astrotathya/panchang', {
		edit: Edit,
		save: function () {
			return null;
		},
	} );
} )( window.wp );
