<?php
/**
 * Include a file and die with a message if the file cannot be included.
 *
 * @since 1.0
 *
 * @param string $file File to include
 * @param string $message Custom die message if $file cannot be included
 * @return none
 */
function include_or_die( $file, $message = null ) {
	if ( ! @include_once( $file ) ) {
		if ( isset( $message ) ) 
			die( "$message\n" );
		else
			die( "Could not include $file, file is not readable or is missing. Please reinstall and restart the bot.\n" );
	}
}

/**
 * Retrieve SMARTIRC_TYPE_ string based of it's numeric constant equivalent
 *
 * @since 1.0
 *
 * @param integer $type SMARTIRC_TYPE_ integer value
 * @return string
 */
function get_smartirc_type( $type ) {
	$type = absint( $type );

	$smartirc_inttype_to_texttype = array(
		1          => 'SMARTIRC_TYPE_UNKNOWN',
		2          => 'SMARTIRC_TYPE_CHANNEL',
		4          => 'SMARTIRC_TYPE_QUERY',
		8          => 'SMARTIRC_TYPE_CTCP',
		16         => 'SMARTIRC_TYPE_NOTICE',
		32         => 'SMARTIRC_TYPE_WHO',
		64         => 'SMARTIRC_TYPE_JOIN',
		128        => 'SMARTIRC_TYPE_INVITE',
		256        => 'SMARTIRC_TYPE_ACTION',
		512        => 'SMARTIRC_TYPE_TOPICCHANGE',
		1024       => 'SMARTIRC_TYPE_NICKCHANGE',
		2048       => 'SMARTIRC_TYPE_KICK',
		4096       => 'SMARTIRC_TYPE_QUIT',
		8192       => 'SMARTIRC_TYPE_LOGIN',
		16384      => 'SMARTIRC_TYPE_INFO',
		32768      => 'SMARTIRC_TYPE_LIST',
		65536      => 'SMARTIRC_TYPE_NAME',
		131072     => 'SMARTIRC_TYPE_MOTD',
		262144     => 'SMARTIRC_TYPE_MODECHANGE',
		524288     => 'SMARTIRC_TYPE_PART',
		1048576    => 'SMARTIRC_TYPE_ERROR',
		2097152    => 'SMARTIRC_TYPE_BANLIST',
		4194304    => 'SMARTIRC_TYPE_TOPIC',
		8388608    => 'SMARTIRC_TYPE_NONRELEVANT',
		16777216   => 'SMARTIRC_TYPE_WHOIS',
		33554432   => 'SMARTIRC_TYPE_WHOWAS',
		67108864   => 'SMARTIRC_TYPE_USERMODE',
		134217728  => 'SMARTIRC_TYPE_CHANNELMODE',
		268435456  => 'SMARTIRC_TYPE_CTCP_REQUEST',
		536870912  => 'SMARTIRC_TYPE_CTCP_REPLY',
		1073741823 => 'SMARTIRC_TYPE_ALL'
	);

	if ( isset( $smartirc_inttype_to_texttype[$type] ) )
		return $smartirc_inttype_to_texttype[$type];
	else
		return false;
}

/**
 * Create a MySQL database connection as $mysql
 *
 * @since 1.0
 */
function make_mysql_database_connection() {
	global $mysql;

	if ( !isset($mysql) ) {
		require_once( 'backpress/class.bpdb.php' );

		$mysql = new BPDB( array(
			'user'     => DB_USER,
			'password' => DB_PASSWORD,
			'name'     => DB_NAME,
			'host'     => DB_HOST,
			'charset'  => DB_CHARSET,
			'collate'  => DB_COLLATE,
		) );
	}
}

/**
 * Parses a command captured by the bot and returns an array with the information separated
 *
 * @since 1.0
 * @param string $msg The message as received by the bot
 * @param bool $cmdcharreq If the command character(s) are required or not, defaults to required
 * @param bool $nickdetect If true, the query is looked at to see if the command was directed at a user
 * @return array The command, the query (if there was one), and the directed at nick (if there was one)
 */
function parse_command( $msg, $cmdcharreq = true, $nickdetect = true ) {
	global $command;
	$cmd_chars = ( defined( 'CMD_CHARS' ) && CMD_CHARS ) ? CMD_CHARS : '.!';
	$cmd_chars = str_split( $cmd_chars );

	// Check to make sure $msg started with a command character
	if ( $cmdcharreq && !in_array( substr( $msg, 0, 1 ), $cmd_chars ) )
		return false;

	// Break the message up
	$msgparts = explode( ' ', $msg );

	// The command is the first word. If command char(s) is required, then strip the char off.
	$command = ( $cmdcharreq ) ? substr( array_shift($msgparts), 1 ) : array_shift($msgparts);

	// The query is everything else
	$query = implode( ' ', $msgparts );

	// Look to see if the command was directed at a user
	$position = strrpos( $query, '>' );
	if ( $nickdetect && ( $position || 0 === $position ) ) {
		$nick  = substr( $query, $position + 1 );
		$query = substr( $query, 0, $position );
	} else {
		$nick = false;
	}

	$data = compact( 'command', 'query', 'nick' );
	$data = array_map( 'trim', $data );

	return $data;
}

/**
 * Checks if a nick is a bot admin
 *
 * @since 1.0
 * @param object $data Net_SmartIRC $data object
 * @return bool
 */
function is_admin( $data ) {
	global $bot_admins;
	if ( isset( $bot_admins[$data->nick] ) && $bot_admins[$data->nick] == $data->host )
		return apply_filters( 'is_admin_true', true, $data->nick, $data->host, $data );
	else
		return apply_filters( 'is_admin_false', false, $data->nick, $data->host, $data );
}

function globalize_request_nick( $irc, $data ) {
	global $nick;
	$nick = $data->nick;
}

/**
 * Helper function to send an IRC QUERY
 *
 * @since 1.0
 * @param string $target Channel or Nick to send query to
 * @param string $message Message to send
 */
function irc_query( $target, $message ) {
	global $irc;
	$irc->message( SMARTIRC_TYPE_QUERY, $target, apply_filters( 'irc_query', $message, $target ) );
}

/**
 * Helper function to send an IRC ACTION
 *
 * @since 1.0
 * @param string $target Channel or Nick to send action to
 * @param string $message Message to send
 */
function irc_action( $target, $message ) {
	global $irc;
	$irc->message( SMARTIRC_TYPE_ACTION, $target, apply_filters( 'irc_action', $message, $target ) );
}

/**
 * Helper function to send an IRC NOTICE
 *
 * @since 1.0
 * @param string $target Channel or Nick to send notice to
 * @param string $message Message to send
 */
function irc_notice( $target, $message ) {
	global $irc;
	$irc->message ( SMARTIRC_TYPE_NOTICE, $target, apply_filters( 'irc_notice', $message, $target ) );
}

// vim: set ts=8:sw=8
