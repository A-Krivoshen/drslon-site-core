<?php
/**
 * Canonical MAX messenger logo SVG (same as services landing contacts).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Official MAX bubble path (viewBox 7 7 22 22).
 */
function krv_max_messenger_icon_path(): string {
	return 'M18.1,28.3c-2,0-2.9-0.3-4.4-1.5c-1,1.3-4.2,2.3-4.3,0.6c0-1.3-0.3-2.4-0.6-3.6C8.4,22.4,8,20.8,8,18.4c0-5.7,4.7-10,10.2-10S28,13,28,18.4C27.9,23.9,23.6,28.3,18.1,28.3z M18.2,13.3c-2.7-0.1-4.8,1.7-5.2,4.7c-0.4,2.4,0.3,5.4,0.9,5.5c0.3,0.1,0.9-0.5,1.4-0.9c0.7,0.5,1.5,0.8,2.4,0.9c2.8,0.1,5.2-2,5.4-4.8C23.1,15.9,20.9,13.5,18.2,13.3z';
}

/**
 * Inline MAX icon SVG for UI chips and buttons.
 *
 * @param string $class CSS class on the root svg element.
 */
function krv_max_messenger_icon_svg( string $class = 'krv-max-icon' ): string {
	return sprintf(
		'<svg class="%s" viewBox="7 7 22 22" aria-hidden="true" focusable="false"><path d="%s"/></svg>',
		esc_attr( $class ),
		esc_attr( krv_max_messenger_icon_path() )
	);
}