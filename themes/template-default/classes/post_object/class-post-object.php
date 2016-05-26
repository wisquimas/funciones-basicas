<?php
/**
 * Contiene la clase PostObject.
 */

namespace Gafa;
use Gafa\PropertyBuilders\AbstractPropertyBuilder;

/**
 * Class PostObject. Clase base de la que heredan los demas objetos basados en post_types de WordPress.
 *
 * Existe un concepto raro, pero muy util que se utilizara para automatizar el codigo: funciones estaticas abstractas,
 * constantes abstractas. PHP, no las soporta nativamente, pero se utilizaran via 'reflection. En cada uno de las clases
 * heredatas de 'PostObject' se deveran implementar valores o funcionalidad para:
 *
 *  - La constante POST_TYPE.
 *
 * @package Gafa
 */
abstract class PostObject
{
    /**
     * Cada clase que herede de 'PostObject' debera proveer una constante con el nombre POST_TYPE que indique
     * el nombre del post_type que usa wordpress para almacenar ese tipo de objetos.
     *
     * @var string POST_TYPE Nombre del post_type usado en wordpress para almacenar objetos de este tipo.
     */
    const POST_TYPE = "";

    /**
     * Array con cada una de las instancias que se hayan hecho de la clase, en este pool, no podra haber dos instancias
     * con el mismo ID.
     *
     * @var array<PostObject> Pool de instancias.
     */
    protected static $_pool = array();

    /**
     * @var int Id del post.
     */
    public $ID = 0;

    /**
     * @var string Titulo del post.
     */
    public $title = "";

    /**
     * @param int $id Id of the post.
     * @throws \Exception Si 'getWPProperties' tiene una propiedad no definita en WP_Post, lanza una excepcion.
     */
    public function __construct($id = 0)
    {
        if($id === 0) { return; }

        /** @var PostObject $calledClass */
        $calledClass = get_called_class();

        if($calledClass::POST_TYPE === "") {
            throw new \Exception("The class '$calledClass' must provide a constant called POST_TYPE indicating the wordpress' post type for the object.");
        }

        /** @var \WP_Post $wpPost */
        $wpPost = get_post($id);

        /** @var array $wpPostMeta */
        $wpPostMeta = get_post_meta($id);

        // Set Wordpress native object properties.

        $this->ID = $wpPost->ID;
        $this->permalink = get_permalink($wpPost->ID);

        /** @var string $wpNativeProperty */
        foreach ($this->getWpProperties() as $wpNativeProperty) {
            if(property_exists($wpPost, $wpNativeProperty)) {
                $this->$wpNativeProperty = $wpPost->$wpNativeProperty;
            } else {
                throw new \Exception("The property '$wpNativeProperty' does not exist fot the object WP_Post.");
            }
        }

        /** @var AbstractPropertyBuilder $propertyInfo */
        foreach ($this->getPropertiesInfo() as $propertyInfo) {
            $this->{$propertyInfo->getPropertyName()} = $propertyInfo->getPropertyValue($wpPost, $wpPostMeta);
        }
    }

    /**
     * Regresa un array de 'AbstractPropertyBuilder' con las que se construiran las propiedades de la clase.
     * @return array<AbstractPropertyBuilder>
     */
    abstract protected function getPropertiesInfo();

    /**
     * Regresa un array de strings con los nombres de las propiedades del WP_Post que se necesitan tambien en la clase.
     * @return array
     */
    abstract protected function getWpProperties();

    /**
     * Regresa una instancia de la clase para tal ID de forma optimizada.
     *
     * Se reducen las llamadas a la base de datos al guardar en cache los elementos que ya se han obtenido de la base
     * de datos previamente.
     *
     * @param int $id Id del post.
     * @return mixed|PostObject
     */
    public static final function getFromPool($id)
    {
        /** @var PostObject $calledClass */
        $calledClass = get_called_class();

        if (!isset($calledClass::$_pool[$id])) {
            $calledClass::$_pool[$id] = new $calledClass($id);
        }

        return $calledClass::$_pool[$id];
    }

    /**
     * Regresa un array con todos los posts del tipo.
     *
     * @param array $args Argumentos tipo WP_Query usados para obtener los resultados.
     *      Los unicos argumentos que seran ignorados y sobre-escritos, seran "post_type" y "fields".
     *      Por defecto es un array("posts_per_page" => -1).
     * @param bool $optimizeUsingPool si es true, usara el pool para optimizar las llamadas a la db.
     * @return array
     */
    public static final function getAll($args = array("posts_per_page" => -1), $optimizeUsingPool = true)
    {
        /** @var PostObject $calledClass */
        $calledClass = get_called_class();

        $args['post_type'] = $calledClass::POST_TYPE;
        $args['fields'] = "ids";

        $ids = get_posts($args);
        $instances = array();

        if ($optimizeUsingPool) {
            foreach ($ids as $id) {
                $instances[] = $calledClass::getFromPool($id);
            }
        }
        else{
            foreach ($ids as $id) {
                $instances[] = new $calledClass($id);
            }
        }

        return $instances;
    }
}