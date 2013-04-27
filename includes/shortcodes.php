<?php
add_shortcode( 'command', 'command_shortcode' );

function command_shortcode( $atts ) {
	global $command;
	return $command;
}

add_shortcode( 'nick', 'nick_shortcode' );

function nick_shortcode( $atts ) {
	global $nick;
	return $nick;
}

add_shortcode( 'self', 'self_shortcode' );

function self_shortcode( $atts ) {
	return IRC_NICK;
}
