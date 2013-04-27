<?php
/*
Plugin Name: Hello Dolly
*/

/*
 * You seriously didn't think we could avoid writing a Hello Dolly plugin, did you?
 */

class HelloDolly {

	public $lyrics;
	public $linecount;

	// Plugin construct
	function __construct() {

		// Watch channel messages
		add_action( 'on_channel', array(&$this, 'command_searcher'), 10, 2 );

		// Create the lyrics
		$lyrics = "Hello, Dolly
Well, hello, Dolly
It's so nice to have you back where you belong
You're lookin' swell, Dolly
I can tell, Dolly
You're still glowin', you're still crowin'
You're still goin' strong
We feel the room swayin'
While the band's playin'
One of your old favourite songs from way back when
So, take her wrap, fellas
Find her an empty lap, fellas
Dolly'll never go away again
Hello, Dolly
Well, hello, Dolly
It's so nice to have you back where you belong
You're lookin' swell, Dolly
I can tell, Dolly
You're still glowin', you're still crowin'
You're still goin' strong
We feel the room swayin'
While the band's playin'
One of your old favourite songs from way back when
Golly, gee, fellas
Find her a vacant knee, fellas
Dolly'll never go away
Dolly'll never go away
Dolly'll never go away again";

		// Convert the lyrics into an array (easier to write them as a single text block)
		$this->lyrics = explode( "\n", $lyrics );
	}

	// Parses channel messages looking for !hellodolly
	public function command_searcher( $irc, $data ) {

		// Parse the message and extract the result into variables
		$c = parse_command( $data->message );
		if ( !$c )
			return;
		extract( $c );

		// We're only interested in the "hellodolly" command
		if ( 'hellodolly' != $command )
			return;

		// Figure out how many lines there are and store it for later (to save CPU cycles)
		if ( empty($this->linecount) )
			$this->linecount = count( $this->lyrics );

		// Get a random line of the song
		$line = $this->lyrics[mt_rand( 0, $this->linecount - 1 )];

		// Send the line to the channel that the command was said in
		$irc->message( SMARTIRC_TYPE_QUERY, $data->channel, $line );
	}
}

$hellodolly = new HelloDolly();

?>