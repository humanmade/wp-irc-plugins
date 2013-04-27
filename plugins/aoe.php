<?php
/*
Plugin Name: Google
*/

class AOE {

	// Register the action
	function __construct() {
		add_action( 'on_channel', array(&$this, 'check_message'), 10, 2 );
	}

	// Checks a message to see if it's meant for this plugin and if so, respond to it
	public function check_message( $irc, $msgdata ) {

		// Parse what was said
		$c = parse_command( $msgdata->message );
		if ( !$c )
			return;
		extract( $c );

		// Abort if the command is unknown to this plugin
		if ( !in_array( $command, array( 'aoe' ) ) )
			return;

		// Allow the result to be directed at someone
		$nick = ( !empty($nick) ) ? $nick : $msgdata->nick;

		# First try and get a calculator result (the AJAX API doesn't support the calculator)

		return $irc->message( SMARTIRC_TYPE_QUERY, $msgdata->channel, $nick . ': ' . 'A game of Age of Empires will be starting shorty!' );
	}
}

$aoe = new AOE();