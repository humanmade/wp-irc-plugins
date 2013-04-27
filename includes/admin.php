<?php
/*
 * Some basic admin commands for the bot
*/

class IRCActions {
	function __construct() {
		add_action( 'on_query', array( &$this, 'dispatch' ), 10, 2 );
	}

	function dispatch( $irc, $data ) {
		if ( ! is_admin( $data ) )
			return;

		global $irc_channels;

		$c = parse_command( $data->message, false );
		if ( ! $c )
			return;
		extract( $c );

		$args = strstr( $query, ':' ) ? (array) split( ':', $query ) : false;

		switch ( $command ) {
			case 'join':
				if ( empty( $query ) )
					return;

				if ( ! $irc->isJoined( $query ) ) {
					$irc->message( SMARTIRC_TYPE_QUERY, $data->nick, "Joining $query" );
					$irc_channels[] = $query;
					$irc_channels = array_unique($irc_channels);
					$irc->join( array( $query ) );
				} else {
					$irc->message( SMARTIRC_TYPE_QUERY, $data->nick, "Already joined to $query" );
				}

				break;
			case 'part':
				if ( empty( $query ) )
					return;

				if ( $irc->isJoined( $query ) ) {
					$irc->message( SMARTIRC_TYPE_QUERY, $data->nick, "Parting $query" );
					unset($irc_channels[array_search($query, $irc_channels)]);
					$irc_channels = array_unique($irc_channels);
					$irc->part( array( $query ) );
				} else {
					$irc->message( SMARTIRC_TYPE_QUERY, $data->nick, "Not joined to $query" );
				}

				break;
			case 'quit':
				$irc->message( SMARTIRC_TYPE_QUERY, $data->nick, "Quitting" );
				$irc->quit( 'Quit Requested', SMARTIRC_CRITICAL );

				break;
			case 'nick':
				if ( empty( $query ) )
					return;

				if ( ! $irc->isMe( $query ) ) {
					$irc->message( SMARTIRC_TYPE_QUERY, $data->nick, "Changing nick to $query" );
					$irc->changeNick( $query );
				} else {
					$irc->message( SMARTIRC_TYPE_QUERY, $data->nick, "Nick is already $query" );
				}

				break;
			case 'query':
				if ( ! $args )
					return;

				$irc->message( SMARTIRC_TYPE_QUERY, $data->nick, "Sending query to {$args[0]}" );
				$irc->message( SMARTIRC_TYPE_QUERY, $args[0], $args[1] );

				break;
			case 'notice':
				if ( ! $args )
					return;

				$irc->message( SMARTIRC_TYPE_QUERY, $data->nick, "Sending notice to {$args[0]}" );
				$irc->message( SMARTIRC_TYPE_NOTICE, $args[0], $args[1] );

				break;
		}
	}
}

$IRCActions = new IRCActions();

?>
