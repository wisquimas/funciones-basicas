<?php
/*Plugin Name: Funciones de arranque GAFA v2.0*/
global $movil;

/*
** CONSTANTES
*/
define('ROUT_GAFA',__DIR__);

define('THEMES_GAFA',dirname( ABSPATH ).DIRECTORY_SEPARATOR.'themes');

define('PLUGINS_GAFA',ROUT_GAFA.DIRECTORY_SEPARATOR.'plugins');

/*
**ARRANQUE DE FUNCIONES
*/
require_once(dirname( __FILE__ ) ."/functions/inicio.php");

// Clases de utilidades. Importará todos los archivos .php dentro de la carpeta "util".
foreach (glob(__DIR__ . DIRECTORY_SEPARATOR . "util" . DIRECTORY_SEPARATOR . "*.php") as $filename)
{
	require_once $filename;
}
/*
** CORRER UTILES INICIALES
*/
add_action('get_header','iniciador_de_utiles');
function iniciador_de_utiles(){
	Generador_style_css::init();
};
/*FUNCIONES LOGIN*/
if( !function_exists('nuevo_logo') ){
	function nuevo_logo() { ?>
		<style type="text/css">
			body.login div#login h1 a {
				background-image: url("/wp-content/logo.png");
				padding-bottom: 30px;
				background-position: center;
				width: 100%;
				background-size: 50%;
			}
			#nav {
				display: none;
			}
			body{
				background-color: #FFF;
			}
		</style>
	<?php }
};
add_action( 'login_enqueue_scripts', 'nuevo_logo' );
if( !function_exists('M_my_login_logo_url') ){
	function M_my_login_logo_url() {
		return home_url();
	};
};
add_filter( 'login_headerurl', 'M_my_login_logo_url' );
if( !function_exists('M_my_login_logo_url_title') ){
	function M_my_login_logo_url_title() {
		return 'Retornar al home';
	};
};
add_filter( 'login_headertitle', 'M_my_login_logo_url_title' );

/*CAMBIAR MAILS*/
add_filter( 'wp_mail_content_type', 'set_html_content_type' );
if( !function_exists('set_html_content_type') ){
	function set_html_content_type() {
		return 'text/html';
	}
};
/*QUITAR CSS DEL HEADER*/
add_action('get_header', 'quitar_margin');
if( !function_exists('quitar_margin') ){
	function quitar_margin() {
		remove_action('wp_head', '_admin_bar_bump_cb');
	};
};
/*FUNCIONES GAFA*/
if( !function_exists('mario') ){
	function mario($texto = '' , $print = true){
		global $current_user;
		if( isset($current_user->data) && 'administrator' == $current_user->roles[0] ){
			$text = '';

			$text .= '<pre class="mario_dev">';
			$text .=print_r($texto, true);
			$text .='</pre>';
			if( $print ){
				echo $text;
			}else{
				return $text;
			};
		};
	};
};
if( !function_exists('plantilla') ){
	function plantilla($echo=true){
		if( $echo ){
			echo get_template_directory_uri();
		}else{
			return get_template_directory_uri();
		};
	};
};
if( !function_exists('assets') ){
	function assets($echo=true){
		if( $echo ){
			echo get_template_directory_uri().'/assets/';
		}else{
			return get_template_directory_uri().'/assets/';
		};
	};
};
if( !function_exists('get_is_mobile') ){
	function get_is_mobile(){
		global $movil;
		//mobile browsers
		$iphone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
		$ipod = strpos($_SERVER['HTTP_USER_AGENT'],"iPad");
		$android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
		$palmpre = strpos($_SERVER['HTTP_USER_AGENT'],"webOS");
		$berry = strpos($_SERVER['HTTP_USER_AGENT'],"BlackBerry");
		$iemobile = ( strpos($_SERVER['HTTP_USER_AGENT'],"iemobile") || strpos($_SERVER['HTTP_USER_AGENT'],"IEMobile") );

		if ( (($iphone || $android || $palmpre || $ipod || $berry !== FALSE || $iemobile) === true) )  {
			$movil = true;
		}else{
			$movil = false;
		};
	};
};
get_is_mobile();


function quitar_barra_administracion(){
	return false;
}
add_filter( 'show_admin_bar' , 'quitar_barra_administracion');

/* Revisar Admin */
/**
 * No esta haciendo una revision correcta del archivo
 * @deprecated
 */
function sera_admin(){
	if( is_admin() && isset( $current_user->data->ID ) ){
		$role = $current_user->roles;
		if( isset( $role[0] ) && $role[0] != 'administrator'){
			wp_redirect(get_home_url());
			exit;
		};
	};
};
//add_action('admin_init','sera_admin');

add_filter( 'wp_get_attachment_image_src', 'media_files_url',4,50 );
function media_files_url( $image ) {
	$url = reset( $image );
	$split= explode('/wp-content/',$url);

	$image[0] = WP_SITEURL.'/wp-content/'.end($split);
	return $image;
}
add_filter( 'send_password_change_email', 'no_enviar_password_change_email',3,50 );
function no_enviar_password_change_email( $enviar, $user, $userdata ){
	return false;
};
/**
 * Annadimos carpeta customizada de templates
 */
add_action('setup_theme','preload_template_gafa');
function preload_template_gafa(){
	global $wp_theme_directories;
	$wp_theme_directories[] = THEMES_GAFA;
}
add_filter( 'template_directory_uri', 'preload_template_gafa_plantilla',10,3 );
function preload_template_gafa_plantilla($template_dir_uri, $template, $theme_root_uri){

	//    gafa(get_defined_vars());
	return get_home_url()."/themes/{$template}/";
}
/*
** AÑADIR NUEVOS PLUGINS
** ESTA FUNCION AÚN NO SE PUEDE UTILIZAR DADO QUE WORDPRESS NO PERMITE UN USO CORRECTO DE LA CONSTANTE PLUGIN DIR
*/
//add_filter( 'all_plugins', 'annadir_plugins_gafa' );
function annadir_plugins_gafa( $info = false ){
	/*
    **MAGIC FIELDS
    */
	$mf = get_plugin_data(PLUGINS_GAFA.DIRECTORY_SEPARATOR.'magic-fields-2'.DIRECTORY_SEPARATOR.'main.php');
	$info['magic-fields-2'] = $mf;
	gafa($info,'todos los plugins');
	return $info;
};
/*
** activacion_funciones_basicas
*/
register_activation_hook( __FILE__, 'activacion_funciones_basicas' );
/*
** MAIN DE ACTIVACION
*/
function activacion_funciones_basicas(){
	/** @noinspection PhpIncludeInspection */
	require_once( __DIR__ . DIRECTORY_SEPARATOR . 'instalacion' . DIRECTORY_SEPARATOR . 'instalacion.php' );
}
add_action('admin_init','funcionesArranque');
function funcionesArranque(){
	add_filter('wp_editor_settings', 'GafaEditorSetting', 10, 2);
}
/**
 * Filtro de arranque en editor
 * @param array $settings
 * @param int   $editor_id
 *
 * @return array
 */
function GafaEditorSetting(array $settings = array(), $editor_id = 0)
{
	$postId = isset($_GET["post"]) ? $_GET["post"] : null;

	$naturalNativeTypes = array("post", "page");
	if(!$postId || in_array(get_post_type($postId), $naturalNativeTypes)) {
		$settings['default_editor'] = 'tinymce';
	}else{
		$settings['default_editor'] = 'html';
	}
	return $settings;
}
