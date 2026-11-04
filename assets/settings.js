/**
 * Settings > Panchang: the preview beside the form follows the choices as they are made, and the
 * shortcode under it is the one for those choices. Values are checked against the same lists as
 * the PHP side before they reach the iframe address.
 */
( function () {
	var data = window.astrotathyaPanchangSettings;
	var frame = document.getElementById( 'astrotathya-panchang-preview' );
	var code = document.getElementById( 'astrotathya-panchang-shortcode' );
	if ( ! data || ! frame ) {
		return;
	}
	var SLUG = /^[a-z0-9-]{1,64}$/;
	var ALLOWED = {
		type: [ 'panchang', 'festivals' ],
		lang: [ 'auto', 'en', 'hi' ],
		theme: [ 'light', 'dark', 'maroon' ],
		size: [ 'card', 'banner' ],
	};

	function value( key ) {
		var el = document.getElementById( 'astrotathya-panchang-' + key );
		var v = el ? el.value : '';
		if ( 'city' === key ) {
			return SLUG.test( v ) ? v : 'new-delhi';
		}
		return ALLOWED[ key ].indexOf( v ) !== -1 ? v : ALLOWED[ key ][ 0 ];
	}

	function update() {
		var w = { city: value( 'city' ), type: value( 'type' ), lang: value( 'lang' ), theme: value( 'theme' ), size: value( 'size' ) };
		var lang = 'auto' === w.lang ? data.siteLang : w.lang;
		// The same address the PHP side builds: defaults (en, light, card) left out, then the key.
		var q = [];
		if ( 'en' !== lang ) {
			q.push( 'lang=' + lang );
		}
		if ( 'light' !== w.theme ) {
			q.push( 'theme=' + w.theme );
		}
		if ( 'card' !== w.size ) {
			q.push( 'size=' + w.size );
		}
		// The key as typed (same characters the PHP side keeps), so the preview shows what visitors will see.
		var keyEl = document.getElementById( 'astrotathya-panchang-key' );
		var key = ( keyEl ? keyEl.value : data.key || '' ).replace( /[^A-Za-z0-9._-]/g, '' ).slice( 0, 2048 );
		if ( key ) {
			q.push( 'k=' + encodeURIComponent( key ) );
		}
		var src = data.origin + '/embed/' + w.type + '/' + w.city + ( q.length ? '?' + q.join( '&' ) : '' );
		if ( frame.getAttribute( 'src' ) !== src ) {
			frame.setAttribute( 'src', src );
		}
		frame.style.height = data.heights[ w.type ][ w.size ] + 'px';
		if ( code ) {
			code.textContent = '[astrotathya_panchang city="' + w.city + '" type="' + w.type + '" lang="' + w.lang + '" theme="' + w.theme + '" size="' + w.size + '"]';
		}
	}

	[ 'city', 'type', 'lang', 'theme', 'size', 'key' ].forEach( function ( key ) {
		var el = document.getElementById( 'astrotathya-panchang-' + key );
		if ( el ) {
			el.addEventListener( 'change', update );
		}
	} );
	update();
} )();
