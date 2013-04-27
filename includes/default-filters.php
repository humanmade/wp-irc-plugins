<?php
$filters = array( 'on_query', 'on_channel', 'on_join', 'on_nickchange', 'on_quit', 'on_kick', 'on_part', 'on_name' );
foreach ( $filters as $filter )
	add_filter( $filter, 'globalize_request_nick', 0, 2 );

$filters = array( 'irc_query', 'irc_action', 'irc_notice' );
foreach ( $filters as $filter )
	add_filter( $filter, 'do_shortcode', 11 );
