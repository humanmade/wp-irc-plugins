<?php
/*
Plugin Name: Google
*/

class Google {

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
		if ( !in_array( $command, array( 'g', 'google' ) ) )
			return;

		// Make sure there was a search query
		if ( empty($query) )
			return $irc->message( SMARTIRC_TYPE_NOTICE, $msgdata->nick, 'You must enter a search term.' );

		// Allow the result to be directed at someone
		$nick = ( !empty($nick) ) ? $nick : $msgdata->nick;

		# First try and get a calculator result (the AJAX API doesn't support the calculator)

		// Fetch the HTML and ensure there was a valid response
		$response = wp_remote_get( 'http://www.google.com/search?q=' . urlencode( $query ) );
		if ( 200 != wp_remote_retrieve_response_code( $response ) )
			return $irc->message( SMARTIRC_TYPE_NOTICE, $msgdata->nick, 'Unable to connect to Google. Please try again later. (Reponse code was ' . wp_remote_retrieve_response_code( $response ) . ')' );

		// Look for the calculator image
		$html = wp_remote_retrieve_body( $response );
		if ( strpos( $html, 'images/calc_img.gif' ) ) {
			preg_match ( '#<h2 class=r style="font-size:138%"><b>([^<]+)</b></h2><div style="padding-top:5px">#i', $html, $result );

			if ( !empty($result[1]) )
				return $irc->message( SMARTIRC_TYPE_QUERY, $msgdata->channel, $nick . ': ' . trim( $result[1] ) );
		}

		// Google is doing A/B testing (this is for the form/graph version)
		elseif ( strpos( $html, '<input id=exchange_rate type=hidden' ) ) {
			preg_match ( '#<h3 class=r><b>([^<]+)</b></h2></div><input id=exchange_rate#i', $html, $result );

			if ( !empty($result[1]) )
				return $irc->message( SMARTIRC_TYPE_QUERY, $msgdata->channel, $nick . ': ' . trim( $result[1] ) );
		}

		# Not a calculator result, so do a standard search

		// Fetch the JSON and ensure there was a valid response
		$response = wp_remote_get( 'http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=' . urlencode( $query ) );
		if ( 200 != wp_remote_retrieve_response_code( $response ) )
			return $irc->message( SMARTIRC_TYPE_NOTICE, $msgdata->nick, 'Unable to connect to Google. Please try again later. (Reponse code was ' . wp_remote_retrieve_response_code( $response ) . ')' );

		// Decode the JSON
		$results = json_decode( wp_remote_retrieve_body( $response ) );
		if ( empty($results->responseData->results[0]->url) )
			return $irc->message( SMARTIRC_TYPE_NOTICE, $msgdata->nick, 'There were no results for your Google search.' );
		else
			return $irc->message( SMARTIRC_TYPE_QUERY, $msgdata->channel, $nick . ': ' . $results->responseData->results[0]->url );
	}
}

$google = new Google();

?>