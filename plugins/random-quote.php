<?php
/**
 * Plugin name: Random Quotes
 * Displays a random quote
 * Class Random_Quote
 */

class Random_Quote {

	public $quotes;

	// Plugin construct
	function __construct() {

		// Watch channel messages
		add_action( 'on_channel', array( &$this, 'command_searcher' ), 10, 2 );

		// load quotes
		$quotes = file_get_contents( dirname( __FILE__ ) . '/quotes.json' );

		$this->quotes = json_decode( $quotes );

	}

	// Parses channel messages looking for !sayquote
	public function command_searcher( $irc, $data ) {

		// Parse the message and extract the result into variables
		$c = parse_command( $data->message );
		if ( ! $c )
			return;

		extract( $c );

		// Abort if the command is unknown to this plugin
		if ( ! in_array( $command, array( 'sq', 'sayquote' ) ) )
			return;

		// Make sure there was a search query
		if ( empty( $query ) )
			return $irc->message( SMARTIRC_TYPE_NOTICE, $msgdata->nick, 'You must enter a source for the quote (e.g.
			blackadder.');

		// set subset of quotes to value of argument
		$source = $this->quotes->$query;

		//get a random quote ID
		$quote_id = array_rand( $source, 1 );

		// fetch the quote
		$quote = $source[$quote_id]->quote_text;

		// Send the line to the channel that the command was said in
		$irc->message( SMARTIRC_TYPE_QUERY, $data->channel, $quote );

	}

}

$random_quote = new Random_Quote();
