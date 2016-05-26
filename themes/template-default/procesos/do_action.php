<?php require_once('../../../../wp-load.php');

$mensaje = new Mensajes();

if( !isset( $_POST['funcion'] ) || !isset( $_POST['attr'] ) ){
	$mensaje->add_error('Falta información');
	$mensaje->imprimir( 'JSON' );
	die();
};
$funcion = strip_tags( (string)$_POST['funcion'] );
$attr = $_POST['attr'];

if( function_exists( $funcion ) ){
	if( is_array( $attr ) ){
		$mensaje->add_data( call_user_func_array($funcion,$attr) );
	}else{
		$mensaje->add_data( call_user_func($funcion,$attr) );
	};
}else{
	$mensaje->add_error('No existe la funcion: '.$funcion);
};

$mensaje->imprimir( 'JSON', true );
?>