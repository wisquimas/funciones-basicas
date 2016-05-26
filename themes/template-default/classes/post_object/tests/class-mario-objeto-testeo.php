<?php

namespace Gafa\Tests;

use Gafa\PostObject;
use Gafa\PropertyBuilders\Property;

class MarioObjetoTesteo extends PostObject {
    const POST_TYPE = "page";

    public $templatePagina = '';
    public $Link           = '';
    public $templatePaginaNuevo = '';

    protected function getPropertiesInfo()
    {
        return array(
            new Property("Link", function ($wpPost, $wpPostMeta) { return get_permalink($wpPost->ID); }),
            new Property("templatePagina", "_wp_page_template", true),
            new Property("templatePaginaNuevo", "info_my_page_template", true),
        );
    }

    /**
     * Seteamos las opciones
     * @return array
     * @deprecated Usar la funcion 'getPropertiesInfo'.
     */
    static public function GetOptions()
    {
        return array(
            array(
                'propiedad'      => 'Link',
                //'meta_slug' => '_wp_page_template',
                //'reset'     => false,
                "customFunction" => function ( $clase ) { return get_permalink( $clase->ID ); },
            ),
            array(
                'propiedad' => 'templatePagina',
                'meta_slug' => '_wp_page_template',
                //'reset'     => false,
                //"customFunction" => function($clase){ return get_permalink( $clase->ID ); },
            ),
        );
    }

    public static function RunAllTests(){
        gafa(new MarioObjetoTesteo(23583), "usando el objeto MarioObjetoTesteo");
    }

    /**
     * Regresa un array de strings con los nombres de las propiedades del WP_Post que se necesitan tambien en la clase.
     * @return array
     */
    protected function getWpProperties()
    {
        return array();
    }
}