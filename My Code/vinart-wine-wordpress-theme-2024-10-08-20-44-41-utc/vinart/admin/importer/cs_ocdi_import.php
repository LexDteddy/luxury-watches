<?php
/**
 *
 * Import Class
 * A helper class for support CS to OCDI
 *
 */

if ( !class_exists( 'OCDI_Plugin' ) )
	return;

class CS_Import_OCDI {

    public $title;

    public function __construct($title = 'Codestart Framework data uploaded') {
	$this->title = $title;
        add_action('pt-ocdi/after_content_import_execution', array($this, 'cs_import_init'), 3, 99 );
    }
    public function cs_decode_string( $string ) {
    	return unserialize( $string );
    }
    public function cs_import_init( $selected_import_files, $import_files, $selected_index ) {

    	if ( !class_exists( 'CSFramework' ) ) {
    		return;
    	}

    	$downloader = new OCDI\Downloader();

	    if( ! empty( $import_files[$selected_index]['local_import_cs'] ) ) {

	      foreach( $import_files[$selected_index]['local_import_cs'] as $index => $import ) {
	        $file_path = $import['file_path'];
	        $file_raw  = OCDI\Helpers::data_from_file( $file_path );
	        update_option( $import['option_name'], cs_decode_string( $file_raw, true ) );
	      }

	    }
	    $ocdi       = OCDI\OneClickDemoImport::get_instance();
	    $log_path   = $ocdi->get_log_file_path();

	    OCDI\Helpers::append_to_file( $this->title, $log_path );
    }
}


$cs_init = new CS_Import_OCDI();