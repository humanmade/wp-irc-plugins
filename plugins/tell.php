<?php
/*
Plugin Name: Tell
*/

class Tell {

	private $to_tell = array();

	// Register the action
	function __construct() {
		add_action( 'on_channel', array(&$this, 'check_message'), 10, 2 );
		add_action( 'on_join', array(&$this, 'logged_in'), 10, 2 );
	}

	public function logged_in( $irc, $msgdata ) {

		if ( ! isset( $this->to_tell[$msgdata->nick] ) ) {
			return;
		}

		foreach ( $this->to_tell[$msgdata->nick] as $msg )
			$irc->message( SMARTIRC_TYPE_QUERY, $msgdata->channel, $msgdata->nick . ': You got a message from ' . $msg['from'] . ': ' . $msg['message'] );

		unset( $this->to_tell[$msgdata->nick] );
	}

	// Checks a message to see if it's meant for this plugin and if so, respond to it
	public function check_message( $irc, $msgdata ) {

		// Parse what was said
		$c = parse_command( $msgdata->message );
		if ( !$c )
			return;
		extract( $c );

		// Abort if the command is unknown to this plugin
		if ( !in_array( $command, array( 'tell' ) ) )
			return;

		if ( isset( $irc->channel[$msgdata->channel]->users[$nick] ) ) {
			return $irc->message( SMARTIRC_TYPE_QUERY, $msgdata->channel, $msgdata->nick . ': ' . $nick . ' is online, tell them yourself!' );
		}

		
		if ( ! isset( $this->to_tell[$nick] ) )
			$this->to_tell[$nick] = array();

		$this->to_tell[$nick][] = array( 'message' => $query, 'from' => $msgdata->nick, 'date' => time() );

		return $irc->message( SMARTIRC_TYPE_QUERY, $msgdata->channel, $msgdata->nick . ': ' . 'Ok, I\'ll tell ' . $nick . ' next time I see them.' );
	}
}

$aoe = new Tell();