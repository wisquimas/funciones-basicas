<?php
$path_to_wp_config  = ABSPATH . 'wp-config.php';
$path_wp_config_gafa= __DIR__.DIRECTORY_SEPARATOR.'wp-config_gafa.php';

reescribir_wp_config( $path_wp_config_gafa, $path_to_wp_config );

function reescribir_wp_config( $gafa_config = '', $wp_default = '' ){
    if( defined('DESARROLLO') && !DESARROLLO ){ return; };
    if ( !file_exists( $gafa_config ) || !file_exists( $wp_default ) ) {
        wp_die('No existen los archivos básicos de configuracion');
    }
    $nuevo_config = file_get_contents($gafa_config);

    /*
    ** SOBREESCRIBIMOS VALORS DE BASE DE DATOS SOLO EN DESARROLLO
    */
    $aKeys = array(
		'__USER__'    => DB_USER,
		'__NAME__'    => DB_NAME,
		'__PASS__'    => DB_PASSWORD,
	);
	$nuevo_config = strtr($nuevo_config, $aKeys);

    file_put_contents($wp_default, $nuevo_config);
}
