<?php
class Generador_style_css{

    /*
    ** Esta funcion recoge el style_php y lo compara con una copia del style
    */
    static public function init(){
        if( !DESARROLLO ){ return; };
        $file_names = apply_filters('gafa_files_to_transform_in_static', array(
            array(
                'folder'=> 'css',
                'file'  => 'style'
            ),
            array(
                'folder'=> 'css',
                'file'  => 'responsive'
            ),
//            array(
//                'folder'=> 'js',
//                'file'  => 'js'
//            ),
        ) );
        foreach ($file_names as $file_data) {
            /*
            ** GENERAR STYLE
            */
            $style_php      = Generador_style_css::get_master_file( $file_data );
            if( empty( $style_php ) ){
                continue;
            }
            $style_backup   = Generador_style_css::get_file_backup( $file_data );

            $crc32_php        = crc32( $style_php );
            $crc32_php_backup = crc32( $style_backup );

            if( $crc32_php !== $crc32_php_backup ){
                /*
                ** COMPILAMOS EL CSS EN UNO NUEVO
                */
                Generador_style_css::generar_files( $file_data );
            };
        }
    }

    static public function generar_files( $file_data = false ){
        /*
        ** URLS
        */
        $style_php_path      = Generador_style_css::get_master_file( $file_data, true );
        $style_php_server_url   = assets(false).'/'.$file_data['folder'].'/'.$file_data['file'].'.php';

        $style_backup_path   = Generador_style_css::get_file_backup( $file_data, true );
        $style_css_path      = Generador_style_css::get_final_file( $file_data, true );
        /*
        ** ARCHIVOS
        */
        $style_php_file      = file_get_contents( $style_php_path );
        /*
        ** COMPILAR CSS
        ** NECESITAMOS PEDIRLE AL SERVIDOR LA PÁGINA PROCESADA
        */
        $style_php_server       = file_get_contents( $style_php_server_url );

        /*
        ** ACTUALIZAMOS EL BACKUP
        */
        file_put_contents( $style_backup_path, $style_php_file );
        file_put_contents( $style_css_path, $style_php_server );
    }
    /*
    ** (string) Recoge la carpeta donde se encuentran los styles
    */
    static public function file_folder( $path = 'css' ){
        return TEMPLATEPATH.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR;
    }
    /*
    ** (string) Recoge la carpeta donde se encuentran los styles
    ** $file_data nombre del archivo a trabajar
    ** $path es para solo recoger el path del elemento
    */
    static public function get_master_file( $file_data = false, $path = false ){
        $direccion_archivo = Generador_style_css::file_folder( $file_data['folder'] ).$file_data['file'].'.php';
        if( $path ){
            return $direccion_archivo;
        };
        if( file_exists($direccion_archivo) ){
            return file_get_contents( $direccion_archivo );
        }else{
            return '';
        }
    }
    /*
    ** (string) Recoge la carpeta donde se encuentran los styles
    ** $file_data nombre del archivo a trabajar
    ** $path es para solo recoger el path del elemento
    */
    static public function get_file_backup( $file_data = false, $path = false ){
        $direccion_archivo = Generador_style_css::file_folder( $file_data['folder'] ).DIRECTORY_SEPARATOR."backups".DIRECTORY_SEPARATOR.$file_data['file'].".php.backup";
        if( !file_exists( $direccion_archivo ) ){
            /*
            ** CREAMOS EL FILE
            */
            file_put_contents( $direccion_archivo,'' );
        };
        if( $path ){
            return $direccion_archivo;
        }
        return file_get_contents( $direccion_archivo );
    }
    /*
    ** RECOGE EL STYLE CSS ACTUAL Y SI NO HAY LO CREA
    ** $file_data nombre del archivo a trabajar
    ** $path es para solo recoger el path del elemento
    */
    static public function get_final_file( $file_data = false, $path = false ){
        $direccion_archivo = Generador_style_css::file_folder( $file_data['folder'] ).DIRECTORY_SEPARATOR.'compilados'.DIRECTORY_SEPARATOR.$file_data['file'].".".$file_data['folder'];
        if( !file_exists( $direccion_archivo ) ){
            /*
            ** CREAMOS EL FILE
            */
            file_put_contents( $direccion_archivo,'' );
        };
        if( $path ){
            return $direccion_archivo;
        }

        return file_get_contents( $direccion_archivo );
    }
}
