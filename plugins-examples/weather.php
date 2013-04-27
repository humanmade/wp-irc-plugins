<?php
/*
Plugin Name: Weather
*/

class Weather {

	public $curmsgfmt;
	public $formsgfmt;
	public $forfmt;

	// Register the action
	function __construct() {
		add_action( 'on_channel', array(&$this, 'check_message'), 10, 2 );

		// Format for the current weather response
		$this->curmsgfmt = apply_filters( 'weather_curmsgfmt', 'Current conditions for [location]: Temperature ([temp_f]°F/[temp_c]°C) Conditions ([conditions]) Wind ([wind]) Humidity ([humidity]) Updated ([timeago] ago)' );

		// Format for the weather forecast response
		$this->formsgfmt = apply_filters( 'weather_formsgfmt', 'Forecast for [location]: [forecast]' );

		// Format for the actual forcast (you probably want a trailing space)
		$this->forfmt    = apply_filters( 'weather_forfmt', '[day] ([forecast]) ' );
	}

	// Checks a message to see if it's meant for this plugin and if so, respond to it
	public function check_message( $irc, $msgdata ) {

		// Parse what was said
		$c = parse_command( $msgdata->message );
		if ( !$c )
			return;
		extract( $c );

		// Abort if the command is unknown to this plugin
		if ( !in_array( $command, array( 'w', 'weather', 'fc', 'forecast' ) ) )
			return;

		// Make sure there was a search query
		if ( empty($query) )
			return $irc->message( SMARTIRC_TYPE_NOTICE, $msgdata->nick, 'You must enter a weather location.' );

		// Fetch the XML and ensure there was a valid response
		$response = wp_remote_get( 'http://api.wunderground.com/auto/wui/geo/WXCurrentObXML/index.xml?query=' . urlencode( $query ) );
		if ( 200 != wp_remote_retrieve_response_code( $response ) )
			return $irc->message( SMARTIRC_TYPE_NOTICE, $msgdata->nick, 'Unable to connect to the weather service. Please try again later. (Reponse code was ' . wp_remote_retrieve_response_code( $response ) . ')' );

		// Parse the XML
		$w = simplexml_load_string( wp_remote_retrieve_body( $response ) );
		if ( !is_object( $w ) )
			return $irc->message( SMARTIRC_TYPE_NOTICE, $msgdata->nick, 'Something is wrong with the weather service. Please try again later.' );

		// Make sure we have a result
		$city = trim( $w->display_location->city );
		if ( empty( $city ) )
			return $irc->message( SMARTIRC_TYPE_NOTICE, $msgdata->nick, "No weather results found for \"$query\". Please try a different location." );

		// Clean up the location a bit
		$location = trim( $w->display_location->full );
		if ( ',' == substr( $location, -1 ) )
			$location = substr( $location, 0, -1 );

		//$location = $w->observation_location->city . ', ' . $w->display_location->state_name;

		// Array containing placeholders and their current values
		$data = array(
			'bold'          => '', // Since this character causes wierdness in my code editor
			'query'         => $query,
			'location'      => $location,
			'timeago'       => human_time_diff( intval( $w->observation_epoch ), intval( $w->local_epoch ), 2 ),
			'city'          => $w->display_location->city,
			'state'         => $w->display_location->state,
			'country'       => $w->display_location->country,
			'conditions'    => $w->weather,
			'temp'          => $w->temperature_string,
			'temp_f'        => $w->temp_f,
			'temp_c'        => $w->temp_c,
			'humidity'      => $w->relative_humidity,
			'wind'          => $w->wind_string,
			'pressure'      => $w->pressure_string,
			'pressure_mb'   => $w->pressure_mb,
			'pressure_in'   => $w->pressure_in,
			'dewpoint'      => $w->dewpoint_string,
			'dewpoint_f'    => $w->dewpoint_f,
			'dewpoint_c'    => $w->dewpoint_c,
			'heatindex'     => $w->heat_index_string,
			'heatindex_f'   => $w->heat_index_f,
			'heatindex_c'   => $w->heat_index_c,
			'windchill'     => $w->windchill_string,
			'windchill_f'   => $w->windchill_f,
			'windchill_c'   => $w->windchill_c,
			'visibility_mi' => $w->visibility_mi,
			'visibility_km' => $w->visibility_km,
		);

		// Now that the common data is out of the way, figure out how to handle the response
		switch ( $command ) {

			// Current weather
			case 'w':
			case 'weather':

				// We have everything we need already, so just finish it up
				$placeholders = array_map( array(&$this, 'bracket_wrap'), array_keys( $data ) );
				$values = array_map( 'trim', array_values( $data ) );

				// Replace the placeholders with their values and then send the response
				$irc->message( SMARTIRC_TYPE_NOTICE, $msgdata->channel, str_replace( $placeholders, $values, $this->curmsgfmt ) );

				break;

			// Forecast
			case 'fc':
			case 'forecast':

				// Fetch the XML and ensure there was a valid response
				$response = wp_remote_get( 'http://api.wunderground.com/auto/wui/geo/ForecastXML/index.xml?query=' . urlencode( $query ) );
				if ( 200 != wp_remote_retrieve_response_code( $response ) )
					return $irc->message( SMARTIRC_TYPE_NOTICE, $msgdata->nick, 'Unable to connect to the weather forecast service. Please try again later. (Reponse code was ' . wp_remote_retrieve_response_code( $response ) . ')' );

				// Parse the XML
				$f = simplexml_load_string( wp_remote_retrieve_body( $response ) );
				if ( !is_object( $f ) )
					return $irc->message( SMARTIRC_TYPE_NOTICE, $msgdata->nick, 'Something is wrong with the weather forecast service. Please try again later.' );

				// Make sure we have a result
				if ( empty( $f->txt_forecast->forecastday ) && empty( $f->simpleforecast->forecastday ) )
					return $irc->message( SMARTIRC_TYPE_NOTICE, $msgdata->nick, "No weather forecast results found for \"$query\". Please try a different location." );

				// Loop through the forecasts and create the forecast for each day
				$forecast = '';
				// This is the good, human readable format
				if ( !empty( $f->txt_forecast->forecastday ) ) {
					foreach ( $f->txt_forecast->forecastday as $forecastday ) {
						$foredata = array(
							'day'      => $forecastday->title,
							'forecast' => $forecastday->fcttext,
						);
						$placeholders = array_map( array(&$this, 'bracket_wrap'), array_keys( $foredata ) );
						$values = array_map( 'trim', array_values( $foredata ) );

						$forecast .= str_replace( $placeholders, $values, $this->forfmt );
					}
				}
				// This is just the conditions/high/low fallback :(
				elseif ( !empty( $f->simpleforecast->forecastday ) ) {
					$count = 0;
					foreach ( $f->simpleforecast->forecastday as $forecastday ) {
						if ( $count >= 3 )
							break;

						$foredata = array(
							'day'      => $forecastday->date->weekday,
							'forecast' => "{$forecastday->conditions}, High of {$forecastday->high->fahrenheit}°F/{$forecastday->high->celsius}°C, Low of {$forecastday->low->fahrenheit}°F/{$forecastday->low->celsius}°C",
						);
						$placeholders = array_map( array(&$this, 'bracket_wrap'), array_keys( $foredata ) );
						$values = array_map( 'trim', array_values( $foredata ) );

						$forecast .= str_replace( $placeholders, $values, $this->forfmt );

						$count++;
					}
				}

				// Add the forecast to the list of placeholders
				$data['forecast'] = str_replace( '&deg;', '°', trim( $forecast ) );

				$placeholders = array_map( array(&$this, 'bracket_wrap'), array_keys( $data ) );
				$values = array_map( 'trim', array_values( $data ) );

				// Replace the placeholders with their values and then send the response
				$irc->message( SMARTIRC_TYPE_NOTICE, $msgdata->channel, str_replace( $placeholders, $values, $this->formsgfmt ) );

				break;

			// Unknown command, do nothing
			default;
		}
	}

	// Wrap a string in brackets
	public function bracket_wrap( $string ) {
		return "[$string]";
	}
}

$weather = new Weather();

?>