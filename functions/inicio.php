<?php
define('DEBUG_GAFA',true);
require_once(dirname( __FILE__ ) ."/fragmentos/access_gafa.php");
require_once(dirname( __FILE__ ) ."/fragmentos/etiquetas_og.php");

if( DEBUG_GAFA ){
	/*PHP CONSOLE*/
	require_once('php-console/src/PhpConsole/__autoload.php');
	$handler = PhpConsole\Handler::getInstance();
	$handler->start();
};
function gafa($var, $tags = null) {
    PhpConsole\Connector::getInstance()->getDebugDispatcher()->dispatchDebug($var, $tags, 1);
}


if( !function_exists('traer_PHPMailer') ){
	function traer_PHPMailer(){
		if ( !class_exists('PHPMailer') ) {
			require_once(dirname( __FILE__ ).'/PHPMailer/class.phpmailer.php');
		}
	};
};

if( is_admin() ){
	
	function custom_colors() {
		require_once("admin/css/style.php");
	}
	add_action('admin_head', 'custom_colors');
};

?>