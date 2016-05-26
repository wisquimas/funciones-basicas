<?php

namespace Mario;

use Gafa\GafaObject;

class Page extends GafaObject
{
    const PostType = 'page';

    public $templatePagina = '';
    public $Link           = '';

    /**
     * Seteamos las opciones
     * @return array
     */
    static public
    function GetOptions()
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

}
