/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );

	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.site-title, .site-description' ).css( {
					'clip': 'rect(1px, 1px, 1px, 1px)',
					'position': 'absolute'
				} );
			} else {
				$( '.site-title, .site-description' ).css( {
					'clip': 'auto',
					'position': 'relative'
				} );
				$( '.site-title a, .site-description' ).css( {
					'color': to
				} );
			}
		} );
	} );

	// Primary color.
	wp.customize( 'willsx_primary_color', function( value ) {
		value.bind( function( to ) {
			// Update custom CSS
			const darkerColor = adjustBrightness(to, -20);
			const lighterColor = adjustBrightness(to, 20);
			
			document.documentElement.style.setProperty('--color-primary', to);
			document.documentElement.style.setProperty('--color-primary-dark', darkerColor);
			document.documentElement.style.setProperty('--color-primary-light', lighterColor);
		} );
	} );

	// Secondary color.
	wp.customize( 'willsx_secondary_color', function( value ) {
		value.bind( function( to ) {
			// Update custom CSS
			const darkerColor = adjustBrightness(to, -20);
			const lighterColor = adjustBrightness(to, 20);
			
			document.documentElement.style.setProperty('--color-secondary', to);
			document.documentElement.style.setProperty('--color-secondary-dark', darkerColor);
			document.documentElement.style.setProperty('--color-secondary-light', lighterColor);
		} );
	} );

	// Footer copyright text.
	wp.customize( 'willsx_footer_copyright', function( value ) {
		value.bind( function( to ) {
			// Replace %s with current year
			const currentYear = new Date().getFullYear();
			const copyrightText = to.replace(/%s/g, currentYear);
			
			$( '.site-info p' ).html( copyrightText );
		} );
	} );

	// Cookie notice text.
	wp.customize( 'willsx_cookie_notice_text', function( value ) {
		value.bind( function( to ) {
			$( '.cookie-notice-content p' ).html( to );
		} );
	} );

	/**
	 * Helper function to adjust brightness of a hex color
	 * 
	 * @param {string} hex Hex color code
	 * @param {number} steps Steps to adjust brightness (negative for darker, positive for lighter)
	 * @returns {string} Adjusted hex color
	 */
	function adjustBrightness(hex, steps) {
		// Remove # if present
		hex = hex.replace(/^#/, '');
		
		// Convert to RGB
		let r = parseInt(hex.substring(0, 2), 16);
		let g = parseInt(hex.substring(2, 4), 16);
		let b = parseInt(hex.substring(4, 6), 16);
		
		// Adjust brightness
		r = Math.max(0, Math.min(255, r + steps));
		g = Math.max(0, Math.min(255, g + steps));
		b = Math.max(0, Math.min(255, b + steps));
		
		// Convert back to hex
		return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
	}
	
} )( jQuery ); 