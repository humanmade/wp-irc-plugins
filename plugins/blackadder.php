<?php
/*
Plugin Name: Hello Dolly
*/

/*
 * You seriously didn't think we could avoid writing a Hello Dolly plugin, did you?
 */

class BlackAdder {

	public $quotes;
	public $linecount;

	// Plugin construct
	function __construct() {

		// Watch channel messages
		add_action( 'on_channel', array(&$this, 'command_searcher'), 10, 2 );

		// load quotes
		$blackadder = file_get_contents('quotes.json');

		$this->quotes = json_decode($blackadder);
	}

	// Parses channel messages looking for !blackadder
	public function command_searcher( $irc, $data ) {

		// Parse the message and extract the result into variables
		$c = parse_command( $data->message );
		if ( !$c )
			return;
		extract( $c );

		// We're only interested in the "blackadder" command
		if ( 'blackadder' != $c )
			return;

		// get a random quote ID
		$quote_number = rand(0, count($this->quotes->quotes));

		// Get a random line of the song
		$quote = $this->quotes[$quote_number]->quote_text;

		// Send the line to the channel that the command was said in
		$irc->message( SMARTIRC_TYPE_QUERY, $data->channel, $quote );
	}
}

$blackadder = new BlackAdder();