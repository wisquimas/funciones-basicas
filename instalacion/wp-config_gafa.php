<?php
/**
 * Configuración básica de WordPress.
 *
 * Este archivo contiene las siguientes configuraciones: ajustes de MySQL, prefijo de tablas,
 * claves secretas, idioma de WordPress y ABSPATH. Para obtener más información,
 * visita la página del Codex{@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} . Los ajustes de MySQL te los proporcionará tu proveedor de alojamiento web.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

 /*
 ** SETEO CORRECTO DE CONSTANTES EN WP-CONFIG
 */
 set_constantes_hack();
 function set_constantes_hack(){
 	$dev = false;
 	$desarrollos = array(
 		'localhost',
 		'187.188.119.69',
 		'192.168',
 		'.ngrok.io',
 		'gafa.codes',
 	);
 	$protocolo = 'http';
 	foreach( $desarrollos as $aguja ){
 		/*
 		** BUSCAMOS SI ESTAMOS O NO EN DEV
 		*/
 		if( strpos( $_SERVER['HTTP_HOST'] ,$aguja) !== false ){
 			$dev = true;
 			define('DESARROLLO', true);

			define('WP_HOME', 'http://' . $_SERVER['SERVER_NAME'].'/'.basename( dirname( __DIR__ ) ) );
			define('WP_SITEURL',    WP_HOME.'/wp');

 			if( $_SERVER['SERVER_ADMIN'] === 'mario@gafa.mx'){
 				define('DB_HOST', 'localhost');
 			}else{
 				define('DB_HOST', '192.168.100.101');
 			};
 			//define('DB_HOST', '187.188.119.69');

 			define('DB_USER', '__USER__');
 			define('DB_NAME', '__NAME__');

 			define('DB_PASSWORD', '__PASS__');
 			return;
 		};
 	};
 	if( !$dev ){
 		/*
 		** VALORES DEL SITIO REMOTO
 		*/
 		define('DESARROLLO', false);

		define('WP_HOME',    $protocolo.'://' . $_SERVER['SERVER_NAME']);
		define('WP_SITEURL',    WP_HOME.'/wp');

 		define('DB_HOST', '');
 		define('DB_USER', '');
 		define('DB_NAME', '');
 	};
 };

/** Codificación de caracteres para la base de datos. */
define('DB_CHARSET', 'utf8mb4');

/** Cotejamiento de la base de datos. No lo modifiques si tienes dudas. */
define('DB_COLLATE', '');

/**#@+
 * Claves únicas de autentificación.
 *
 * Define cada clave secreta con una frase aleatoria distinta.
 * Puedes generarlas usando el {@link https://api.wordpress.org/secret-key/1.1/salt/ servicio de claves secretas de WordPress}
 * Puedes cambiar las claves en cualquier momento para invalidar todas las cookies existentes. Esto forzará a todos los usuarios a volver a hacer login.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '1U[;N|[VU{89O-jD ]K{5F$=c7hBwY{%#&~vB9oJi`B[NB J+.1AY*j^++!|5&~;');
define('SECURE_AUTH_KEY', 'Tzd5(kI%4f-,5{{%|SLbOj-w(h>L+BK&eoH#Oxs%wzz>_2Pv&NC|!`!=;Os_1-pN');
define('LOGGED_IN_KEY', 'jXtH7)P_SU;+47sn<GO|4<?9N#mw^w:}B a,cf9h+a?g(6vA_zHl[EL+)vSif17N');
define('NONCE_KEY', 'BWNgOkcH<5UOMvYxl7BQUAIdFgeeleuJ3Ot*74t*nqKFVhD1([vGg`?I,j|n:0M_');
define('AUTH_SALT', '%>,RAJOqS+)oSst[ H,*JP7)2%128$*p,3?0%X^vx|>)}{on/z*S]*?P$v&u,`ep');
define('SECURE_AUTH_SALT', 'CyoOLJ`O3Nu%RVL#52gRN@1srv2$04X +f7uvp6!3j[L^J*4bb8$>_P-_V:#8eY/');
define('LOGGED_IN_SALT', 'y`d@d1`xx3EPu)J>+S{9/P`34l/$*+MOAi;hFF5-j$wKj{s^<2&:rnc~~5**b-s=');
define('NONCE_SALT', 'tg>x-PQ1dIs2yl(H4vYGeFwSZc@L_8Z=$F.H l-~]UqzBf;6.<WaGeYX]Tm]VW%S');

/**#@-*/

/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar multiples blogs en una sola base de datos.
 * Emplea solo números, letras y guión bajo.
 */
$table_prefix  = 'wp_';


/**
 * Para desarrolladores: modo debug de WordPress.
 *
 * Cambia esto a true para activar la muestra de avisos durante el desarrollo.
 * Se recomienda encarecidamente a los desarrolladores de temas y plugins que usen WP_DEBUG
 * en sus entornos de desarrollo.
 */
define('WP_DEBUG', false);

/* ¡Eso es todo, deja de editar! Feliz blogging */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
