<?php
/*
Plugin Name: Relay

This plugin will relay messages that come in via PM if they are prefixed with the correct secret key.
This is a useful way to get the bot to announce things (such as new blog posts).
You just need to have a script connect and then PM this bot.
*/

class Relay {

	private $secret = 'ChangeThisToSomethingElse';
	public $channel = '#yourchannel';

	// Register the action
	function __construct() {
		add_action( 'on_query', array(&$this, 'maybe_relay'), 10, 2 );
	}

	public function maybe_relay( $irc, $msgdata ) {

		// Parse what was said
		$c = parse_command( $msgdata->message, false );
		if ( !$c )
			return;
		extract( $c );

		// The first word needs to be the secret
		if ( $command !== $this->$secret )
			return;

		return $irc->message( SMARTIRC_TYPE_QUERY, $this->channel, $query );
	}
}

$relay = new Relay();

?>