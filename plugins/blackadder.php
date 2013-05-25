<?php
/*
Plugin Name: Random Blackadder quotes
*/

/*
 * Posts a random blackadder quote
 */

class BlackAdder {

	public $quotes;

	// Plugin construct
	function __construct() {

		// Watch channel messages
		add_action( 'on_channel', array(&$this, 'command_searcher'), 10, 2 );

		// load quotes
		$quotes = file_get_contents('quotes.json');

		$this->quotes = json_decode($quotes);
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
		$quote_id = array_rand($this->quotes->blackadder,1);
		
		$quote = $this->quotes->blackadder[$quote_id]->quote_text;


		// Send the line to the channel that the command was said in
		$irc->message( SMARTIRC_TYPE_QUERY, $data->channel, $quote );
	}
}

$blackadder = new BlackAdder();