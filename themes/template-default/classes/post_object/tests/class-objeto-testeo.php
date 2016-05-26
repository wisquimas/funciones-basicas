<?php

namespace Gafa\Tests;

use Gafa\PostObject;
use Gafa\MFType;
use Gafa\PropertyBuilders\CustomField;
use Gafa\PropertyBuilders\MFArray;
use Gafa\PropertyBuilders\MFField;
use Gafa\PropertyBuilders\MFGroup;

class ObjetoTesteo extends PostObject {
    const POST_TYPE = "testeo";

    /*
     * Tenemos varios casos en los que un magic field es util.
     *
     * Un grupo no repetible con fields no repetibles (no repetible simple).
     *
     * Un grupo no repetible con fields no repetibles (no repetible complejo).
     *
     * Un grupo repetible con fields no repetibles (repetible simple).
     *
     * Un grupo repetible con fields repetibles (repetible complejo).
     *
     * Ahora, 
     */

    protected function getPropertiesInfo()
    {
        return array(
            new CustomField("poderEspecial", function($wpPost, $wpPostMeta){ return "Onda vital $wpPost->ID"; }),

            /*
             * *************************************************************************************************
             * GRUPO SIMPLE, FIELD SIMPLE
             *
             * Guarda un 'field' directamente en una propiedad del objeto.
             */

            // Aqui, el field simple 'grupo_simple_textbox' sera guardado en la propiedad 'grupoSimpleTexto' del objeto.
            new MFField("grupoSimpleTexto", "grupo_simple_textbox", MFType::Textbox),
            new MFField("grupoSimpleImageMedia", "grupo_simple_image_media", MFType::ImageMedia),

            /*
             * *************************************************************************************************
             * GRUPO SIMPLE, FIELD REPETIBLE
             *
             * Guarda un 'field' repetible en una propiedad del objeto, esa propiedad es un array, pues el field es repetible.
             */

            // Aqui, el field repetible 'grupo_complejo_textbox' (que esta guardado en el grupo 'grupo_complejo'), sera guardado en el array 'grupoComplejoTextbox'.
            new MFArray("grupoComplejoTextbox", "grupo_complejo", "grupo_complejo_textbox", MFType::Textbox),
            new MFArray("grupoComplejoImageMedia", "grupo_complejo", "grupo_complejo_image_media", MFType::ImageMedia),

            /*
             * *************************************************************************************************
             * GRUPO SIMPLE, FIELD REPETIBLE (agrupado).
             *
             * Es lo mismo que el 'grupo simple, field repetible', solo que MFGroup agrupa todos los fields repetibles
             * dentro de una sola propiedad del objeto, cada field repetible sera una sub-propiedad de esa propiedad
             * contenedora.
             *
             * La ventaja de agrupar los arrays, es que solo se hace una llamada a la DB para obtener todos los valores.
             */

            // Aqui, se creara una propiedad en el objeto llamada 'grupoComplejo', la cual, tendra sub-propiedades,
            // una llamada 'textbox', donde se almacenara el field repetible 'grupo_complejo_textbox', y otra llamada
            // 'imageMedia', donde se almacenara el field repetible 'grupo_complejo_image_media'.
            // Importante: Todos los MFArray aqui, deben pertenecer al mismo 'group' que el usaro en MFGroup.
            new MFGroup("grupoComplejo", "grupo_complejo", array(
                new MFArray("textbox", "grupo_complejo", "grupo_complejo_textbox", MFType::Textbox),
                new MFArray("imageMedia", "grupo_complejo", "grupo_complejo_image_media", MFType::ImageMedia),
            ), MFGroup::NON_DUPLICABLE_GROUP), // Como es un grupo no duplicable, le pasamos este valor para que entregue no un array sino solo un unico coso.

            /*
             * *************************************************************************************************
             * GRUPO REPETIBLE, FIELD NO REPETIBLE. (siempre son agrupados los grupos repetibles).
             *
             * Guarda un 'group' de magic field en una propiedad del objeto, y cada field de ese 'grop' se almacena en
             * una subpropiedad del objeto.
             */

            // Aqui, se creara la propiedad llamada 'grupoDuplicableSimple' en el objeto. Esa propiedad sera un objeto
            // de tipo "std_class" y contendra los fields del grupo 'grupo_duplicable_simple'.
            // Esa propiedad tendra la subpropiedades:
            //      - 'textbox' donde se guardara el field no repetible "grupo_duplicable_simple_textbox".
            //      - 'mageMedia' donde se guardara el field no repetible "grupo_duplicable_simple_image_media".
            // Nota: Es importante que todas las subpropiedades esten almacenadas dentro del mismo grupo de magic fields.
            new MFGroup("grupoDuplicableSimple", "grupo_duplicable_simple",
                array(
                    new MFField("textbox", "grupo_duplicable_simple_textbox", MFType::Textbox),
                    new MFField("imageMedia", "grupo_duplicable_simple_image_media", MFType::ImageMedia),
                ), MFGroup::DUPLICABLE_GROUP // Como es un grupo duplicable, le pasamos este valor para que lo entregue como un array.
            ),

            /*
             * *************************************************************************************************
             * GRUPO REPETIBLE, FIELD REPETIBLE. (siempre son agrupados los grupos repetibles).
             *
             * Este es el tipo de estrutura mas compleja que ofrece magic fields, pero es sencillo trabajar con ella.
             * Guarda un 'group' de magic field en una propiedad del objeto, y cada uno de sus 'fields' sean estos repetibles
             * o no, se almacnaran en subpropiedades de ese objeto.
             */

            // Aqui, se creara la propiedad 'grupoDuplicableComplejo'. El cual contendra las siguientes subpropiedades.
            //      - 'textboxSimple': Donde se guardara el field 'grupo_duplicable_complejo_textbox_simple'.
            //      - 'textboxDuplicable': Donde se guardara el field repetible 'grupo_duplicable_complejo'.
            //      - 'imageMediaSimple': Donde se guardara el field 'grupo_duplicable_complejo_image_media_simple'.
            //      - 'imageMediaDuplicable': Donde se guardara el field repetible 'grupo_duplicable_complejo'.
            new MFGroup("grupoDuplicableComplejo", "grupo_duplicable_complejo",
                array(
                    new MFField("textboxSimple", "grupo_duplicable_complejo_textbox_simple", MFType::Textbox),
                    new MFArray("textboxDuplicable", "grupo_duplicable_complejo", "grupo_duplicable_complejo_textbox_duplicable", MFType::Textbox),
                    new MFField("imageMediaSimple", "grupo_duplicable_complejo_image_media_simple", MFType::ImageMedia),
                    new MFArray("imageMediaDuplicable", "grupo_duplicable_complejo", "grupo_duplicable_complejo_image_media_duplicable", MFType::ImageMedia),
                ), MFGroup::DUPLICABLE_GROUP
            ),
        );
    }

    public static function RunAllTests(){
        gafa(new ObjetoTesteo(23577), "usando new");
        gafa(get_post_meta(23577), "el meta");
    }

    /**
     * Un recordatorio para forzarte a que implementes la funcion estatica 'getWpProperties'.
     *
     * Regresa un array de strings con los nombres de las propiedades del WP_Post que se necesitan tambien en la clase.
     *
     * @return array
     */
    protected function getWpProperties()
    {
        return array("post_title", "post_author");
    }
}