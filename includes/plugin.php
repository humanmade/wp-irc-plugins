<?php
/**
 * Parse the file contents to retrieve its metadata.
 *
 * Searches for metadata for a file, such as a plugin or theme.  Each piece of
 * metadata must be on its own line. For a field spanning multple lines, it
 * must not have any newlines or only parts of it will be displayed.
 *
 * Some users have issues with opening large files and manipulating the contents
 * for want is usually the first 1kiB or 2kiB. This function stops pulling in
 * the file contents when it has all of the required data.
 *
 * The first 8kiB of the file will be pulled in and if the file data is not
 * within that first 8kiB, then the author should correct their plugin file
 * and move the data headers to the top.
 *
 * The file is assumed to have permissions to allow for scripts to read
 * the file. This is not checked however and the file is only opened for
 * reading.
 *
 * @since 1.0.0
 *
 * @param string $file Path to the file
 */
function get_file_data( $file ) {
	$default_headers = array(
		'Name'		=> 'Plugin Name',
		'PluginURI'	=> 'Plugin URI',
		'Version'	=> 'Version',
		'Description'	=> 'Description',
		'Author'	=> 'Author',
		'AuthorURI'	=> 'Author URI',
		'TextDomain'	=> 'Text Domain',
		'DomainPath'	=> 'Domain Path'
	);

	// We don't need to write to the file, so just open for reading.
	$fp = fopen( $file, 'r' );

	// Pull only the first 8kiB of the file in.
	$file_data = fread( $fp, 8192 );

	// PHP will close file handle, but we are good citizens.
	fclose( $fp );

	foreach ( $default_headers as $field => $regex ) {
		preg_match( '/' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, ${$field} );
		if ( !empty( ${$field} ) )
			${$field} = _cleanup_header_comment( ${$field}[1] );
		else
			${$field} = '';
	}

	$file_data = compact( array_keys( $default_headers ) );

	return $file_data;
}

function load_plugins() {
	if ( is_dir( PLUGIN_DIR ) ) {
		if ( $dh = opendir( PLUGIN_DIR ) ) {
			while ( ( $plugin = readdir( $dh ) ) !== false ) {
				if ( substr( $plugin, -4 ) == '.php' ) {
					$plugin_data = get_file_data( PLUGIN_DIR . '/' . $plugin );
					if ( isset( $plugin_data['Name'] ) && ! empty( $plugin_data['Name'] ) )
						include_once( PLUGIN_DIR . '/' . $plugin );
				}
			}
			closedir( $dh );
		}
	}
	do_action( 'plugins_loaded' );
}

// vim: set ts=8:sw=8
