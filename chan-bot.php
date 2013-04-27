#!/usr/bin/php -q
<?php
/* Version of this bot */
define( 'VERSION', '1.0' );

/* Try to load the config file and error and die if we can't */
if ( ! @include_once( 'chan-bot-config.php' ) )
        die( "Could not include the chan-bot-config.php config file. Please create this file from chan-bot-config-sample.php and restart the bot.\n" );

/* Absolute Path to the install directory */
define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/* Absolute Path to the plugins directory */
if ( ! defined( 'PLUGIN_DIR' ) )
	define( 'PLUGIN_DIR', ABSPATH . 'plugins' );

/* Absolute Path to includes directory */
if ( ! defined( 'INCLUDE_DIR' ) )
	define( 'INCLUDE_DIR', ABSPATH . 'includes' );

if ( ! defined( 'LANG_DIR' ) )
        define( 'LANG_DIR', ABSPATH . 'languages' );

/* Try to load Net_SmartIRC and error and die if we can't */
if ( ! @include_once( INCLUDE_DIR . '/Net_SmartIRC/Net/SmartIRC.php' ) )
	die( "This bot requires the Net_SmartIRC pear package. Please install this package and restart the bot.\n" );

/* Try to load the functions.php include file which contains function dependencies */
if ( ! @include_once( INCLUDE_DIR . '/functions.php' ) )
	die( "Could not include " . INCLUDE_DIR . "/functions.php, file is not readable or is missing. Please reinstall and restart the bot.\n" );

/* Include additional files */
include_or_die( INCLUDE_DIR . '/backpress/functions.core.php' );
include_or_die( INCLUDE_DIR . '/backpress/class.wp-error.php' );
include_or_die( INCLUDE_DIR . '/backpress/functions.plugin-api.php' );
include_or_die( INCLUDE_DIR . '/backpress/functions.bp-options.php' );
include_or_die( INCLUDE_DIR . '/backpress/functions.shortcodes.php' );
include_or_die( INCLUDE_DIR . '/backpress/class.wp-http.php' );
include_or_die( INCLUDE_DIR . '/backpress/pomo/translations.php' );
include_or_die( INCLUDE_DIR . '/backpress/pomo/mo.php' );
include_or_die( INCLUDE_DIR . '/admin.php' );
include_or_die( INCLUDE_DIR . '/plugin.php' );
include_or_die( INCLUDE_DIR . '/formatting.php' );
include_or_die( INCLUDE_DIR . '/shortcodes.php' );
include_or_die( INCLUDE_DIR . '/l10n.php' );
include_or_die( INCLUDE_DIR . '/default-filters.php' );

/* Load plugins from PLUGIN_DIR */
load_plugins();

class bot {

	function do_on_action( &$irc, &$data ) {
		do_action( 'on_action', $irc, $data );
	}

	function do_on_all( &$irc, &$data ) {
		do_action( 'on_all', $irc, $data );
	}

	function do_on_banlist( &$irc, &$data ) {
		do_action( 'on_banlist', $irc, $data );
	}

	function do_on_channel( &$irc, &$data ) {
		do_action( 'on_channel', $irc, $data );
	}

	function do_on_channelmode( &$irc, &$data ) {
		do_action( 'on_channelmode', $irc, $data );
	}

	function do_on_ctcp( &$irc, &$data ) {
		do_action( 'on_ctcp', $irc, $data );
	}

	function do_on_ctcp_reply( &$irc, &$data ) {
		do_action( 'on_ctcp_reply', $irc, $data );
	}

	function do_on_ctcp_request( &$irc, &$data ) {
		do_action( 'on_ctcp_request', $irc, $data );
	}

	function do_on_error( &$irc, &$data ) {
		do_action( 'on_error', $irc, $data );
	}

	function do_on_info( &$irc, &$data ) {
		do_action( 'on_info', $irc, $data );
	}

	function do_on_invite( &$irc, &$data ) {
		do_action( 'on_invite', $irc, $data );
	}

	function do_on_join( &$irc, &$data ) {
		do_action( 'on_join', $irc, $data );
	}

	function do_on_kick( &$irc, &$data ) {
		do_action( 'on_kick', $irc, $data );
	}

	function do_on_list( &$irc, &$data ) {
		do_action( 'on_list', $irc, $data );
	}

	function do_on_login( &$irc, &$data ) {
		do_action( 'on_login', $irc, $data );
	}

	function do_on_modechange( &$irc, &$data ) {
		do_action( 'on_modechange', $irc, $data );
	}

	function do_on_motd( &$irc, &$data ) {
		do_action( 'on_motd', $irc, $data );
	}

	function do_on_name( &$irc, &$data ) {
		do_action( 'on_name', $irc, $data );
	}

	function do_on_nickchange( &$irc, &$data ) {
		do_action( 'on_nickchange', $irc, $data );
	}

	function do_on_nonrelevant( &$irc, &$data ) {
		do_action( 'on_nonrelevant', $irc, $data );
	}

	function do_on_notice( &$irc, &$data ) {
		do_action( 'on_notice', $irc, $data );
	}

	function do_on_part( &$irc, &$data ) {
		do_action( 'on_part', $irc, $data );
	}

	function do_on_query( &$irc, &$data ) {
		do_action( 'on_query', $irc, $data );
	}

	function do_on_quit( &$irc, &$data ) {
		do_action( 'on_quit', $irc, $data );
	}

	function do_on_topic( &$irc, &$data ) {
		do_action( 'on_topic', $irc, $data );
	}

	function do_on_topicchange( &$irc, &$data ) {
		do_action( 'on_topicchange', $irc, $data );
	}

	function do_on_unknown( &$irc, &$data ) {
		do_action( 'on_unknown', $irc, $data );
	}

	function do_on_usermode( &$irc, &$data ) {
		do_action( 'on_usermode', $irc, $data );
	}

	function do_on_who( &$irc, &$data ) {
		do_action( 'on_who', $irc, $data );
	}

	function do_on_whois( &$irc, &$data ) {
		do_action( 'on_whois', $irc, $data );
	}

	function do_on_whowas( &$irc, &$data ) {
		do_action( 'on_whowas', $irc, $data );
	}

}

/* Last action before we initialize the bot */
do_action( 'init' );

$bot = new bot();
$irc = new Net_SmartIRC();

$action_handlers = array(
	array( 'type' => SMARTIRC_TYPE_ACTION,       'method' => 'do_on_action' ),
	array( 'type' => SMARTIRC_TYPE_ALL,          'method' => 'do_on_all' ),
	array( 'type' => SMARTIRC_TYPE_BANLIST,      'method' => 'do_on_banlist' ),
	array( 'type' => SMARTIRC_TYPE_CHANNEL,      'method' => 'do_on_channel' ),
	array( 'type' => SMARTIRC_TYPE_CHANNELMODE,  'method' => 'do_on_channelmode' ),
	array( 'type' => SMARTIRC_TYPE_CTCP,         'method' => 'do_on_ctcp' ),
	array( 'type' => SMARTIRC_TYPE_CTCP_REPLY,   'method' => 'do_on_ctcp_reply' ),
	array( 'type' => SMARTIRC_TYPE_CTCP_REQUEST, 'method' => 'do_on_ctcp_request' ),
	array( 'type' => SMARTIRC_TYPE_ERROR,        'method' => 'do_on_error' ),
	array( 'type' => SMARTIRC_TYPE_INFO,         'method' => 'do_on_info' ),
	array( 'type' => SMARTIRC_TYPE_INVITE,       'method' => 'do_on_invite' ),
	array( 'type' => SMARTIRC_TYPE_JOIN,         'method' => 'do_on_join' ),
	array( 'type' => SMARTIRC_TYPE_KICK,         'method' => 'do_on_kick' ),
	array( 'type' => SMARTIRC_TYPE_LIST,         'method' => 'do_on_list' ),
	array( 'type' => SMARTIRC_TYPE_LOGIN,        'method' => 'do_on_login' ),
	array( 'type' => SMARTIRC_TYPE_MODECHANGE,   'method' => 'do_on_modechange' ),
	array( 'type' => SMARTIRC_TYPE_MOTD,         'method' => 'do_on_motd' ),
	array( 'type' => SMARTIRC_TYPE_NAME,         'method' => 'do_on_name' ),
	array( 'type' => SMARTIRC_TYPE_NICKCHANGE,   'method' => 'do_on_nickchange' ),
	array( 'type' => SMARTIRC_TYPE_NONRELEVANT,  'method' => 'do_on_nonrelevant' ),
	array( 'type' => SMARTIRC_TYPE_NOTICE,       'method' => 'do_on_notice' ),
	array( 'type' => SMARTIRC_TYPE_PART,         'method' => 'do_on_part' ),
	array( 'type' => SMARTIRC_TYPE_QUERY,        'method' => 'do_on_query' ),
	array( 'type' => SMARTIRC_TYPE_QUIT,         'method' => 'do_on_quit' ),
	array( 'type' => SMARTIRC_TYPE_TOPIC,        'method' => 'do_on_topic' ),
	array( 'type' => SMARTIRC_TYPE_TOPICCHANGE,  'method' => 'do_on_topicchange' ),
	array( 'type' => SMARTIRC_TYPE_UNKNOWN,      'method' => 'do_on_unknown' ),
	array( 'type' => SMARTIRC_TYPE_USERMODE,     'method' => 'do_on_usermode' ),
	array( 'type' => SMARTIRC_TYPE_WHO,          'method' => 'do_on_who' ),
	array( 'type' => SMARTIRC_TYPE_WHOIS,        'method' => 'do_on_whois' ),
	array( 'type' => SMARTIRC_TYPE_WHOWAS,       'method' => 'do_on_whowas' )
);

$irc->setAutoReconnect( apply_filters( 'irc_auto_reconnect', true ) );
$irc->setAutoRetry( apply_filters( 'irc_auto_retry', true ) );
$irc->setUseSockets( apply_filters( 'irc_use_sockets', true ) );
$irc->setCtcpVersion( apply_filters( 'irc_ctcp_version', BOT_DESCRIPTION . ' v' . VERSION ) );
$irc->setChannelSyncing( apply_filters( 'irc_channel_syncing', true ) );

apply_filters( 'register_action_handlers', $action_handlers );
foreach ( $action_handlers as $action_handler ) {
	do_action( 'register_action_handler', $action_handler );
	$irc->registerActionhandler( $action_handler['type'], '.*', $bot, $action_handler['method'] );
}

$irc->connect( IRC_HOST, IRC_PORT );
do_action( 'irc_connected' );
$irc->login( IRC_NICK, IRC_REALNAME, IRC_USERMODE, IRC_USERNAME, IRC_PASSWORD );
do_action( 'irc_logged_in' );
$irc->join( (array) $irc_channels );
do_action( 'irc_joined' );
$irc->listen();
do_action( 'irc_listening' );

$irc->disconnect();
do_action( 'irc_disconnected' );

// vim: set ts=8:sw=8
