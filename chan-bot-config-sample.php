<?php
/**
 * The base configurations for the chan bot.
 *
 * This configuration file contains settings for IRC.
 *
 * This file is the sample configuration file, copy this file to 
 * chan-bot-config.php
 */

// ** Chan Bot Version Information ** //
/* Very brief description of bot for use in CTCP VERSION replies */
define( 'BOT_DESCRIPTION', 'Channel Bot' );


// ** IRC Connection Information ** //
/* IRC Host to connect to */
define( 'IRC_HOST', 'irc.freenode.net' );

/* IRC Port to connect to IRC_HOST with */
define( 'IRC_PORT', 6667);

/* Public IRC nick for this bot */
define( 'IRC_NICK', 'chan-bot' );

/* Real name for this bot */
define( 'IRC_REALNAME', 'chan-bot' );

/* Ident/Username, this is does not have to be the same as the nick */
define( 'IRC_USERNAME', 'chan-bot' );

/* IRC Usermode, if you don't know what this is leave it set to 0 */
define( 'IRC_USERMODE', 0);

/* IRC Services identification password */
define( 'IRC_PASSWORD', 'pa55w0rd' );

/* IRC Channels to join after connection to the IRC_HOST */
$irc_channels = array(
	'#channel_1',
	'#channel_2',
	'#channel_3'
);

/* The characters to prefix commands with, do not leave this blank */
/* If you want no command char specify true as the second arg to parse_command */
define( 'CMD_CHARS', '.!' );

// ** Admin configuration ** //
/* Admin users allowed to bypass limits and run restricted commands */
$bot_admins = array(
	'user_1' => 'user@host.com',
	'user_2' => 'user@host.com',
	'user_3' => 'user@host.com',
);

// ** Optional: MySQL database configuration ** //
/** The name of the database */
define( 'DB_NAME', 'database_name_here' );

/** MySQL database username */
define( 'DB_USER', 'username_here' );

/** MySQL database password */
define( 'DB_PASSWORD', 'password_here' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );


// vim: set ts=8:sw=8
