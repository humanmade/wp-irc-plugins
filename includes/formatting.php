<?php
/**
 * Determines the difference between two timestamps.
 *
 * The difference is returned in a human readable format such as "1 hour",
 * "5 mins", "2 days".
 *
 * @since 1.0.0
 *
 * @param int $from Unix timestamp from which the difference begins.
 * @param int $to Optional. Unix timestamp to end the time difference. Default becomes time() if not set.
 * @param int $limit Optional. The number of unit types to display (i.e. the accuracy). Defaults to 1.
 * @return string Human readable time difference.
 */
function human_time_diff( $from, $to = '', $limit = 1 ) {
	// Since all months/years aren't the same, these values are what Google's calculator says
	$units = apply_filters( 'time_units', array(
			31556926 => array( __('%s year'),  __('%s years') ),
			2629744  => array( __('%s month'), __('%s months') ),
			604800   => array( __('%s week'),  __('%s weeks') ),
			86400    => array( __('%s day'),   __('%s days') ),
			3600     => array( __('%s hour'),  __('%s hours') ),
			60       => array( __('%s min'),   __('%s mins') ),
	) );

	if ( empty($to) )
		$to = time();

	$from = (int) $from;
	$to   = (int) $to;
	$diff = (int) abs( $to - $from );

	$items = 0;
	$output = array();

	foreach ( $units as $unitsec => $unitnames ) {
			if ( $items >= $limit )
				break;

			if ( $diff < $unitsec )
				continue;

			$numthisunits = floor( $diff / $unitsec );
			$diff = $diff - ( $numthisunits * $unitsec );
			$items++;

			if ( $numthisunits > 0 )
				$output[] = sprintf( _n( $unitnames[0], $unitnames[1], $numthisunits ), $numthisunits );
	 }

	// translators: The seperator for human_time_diff() which seperates the years, months, etc.
	$seperator = _x( ', ', 'human_time_diff' );

	if ( !empty($output) ) {
		return implode( $seperator, $output );
	} else {
		$smallest = array_pop( $units );
		return sprintf( $smallest[0], 1 );
	}
}

// vim: set ts=8:sw=8
