<?php
/**
 * Contiene la clase Property.
 */

namespace Gafa\PropertyBuilders;

/**
 * Class Property. Construye una propiedad de una forma muy sencilla.
 * @package Gafa
 */
class Property extends AbstractPropertyBuilder {

    /**
     * @var bool If the MF meta value should be reseted when assigning its value to the property.
     */
    public $shouldReset = false;

    /**
     * @var \Closure|string
     */
    public $metaKeyOrClosure;

    /**
     * @param string $propertyName Nombre del field en magic fields.
     * @param string|\Closure $metaKeyOrClosure Meta slug (meta key) or funcion personalizada para obtener el valor.
     * @param bool $shouldReset si se deberia hacer un reset al obtener el valor.
     *      A esta funcion se le pasaran los parametros de (WP_Post) $wpPost, y (array) $wpPostMeta.
     */
    public function __construct($propertyName, $metaKeyOrClosure, $shouldReset = true)
    {
        parent::__construct($propertyName);
        $this->shouldReset = $shouldReset;
        $this->metaKeyOrClosure = $metaKeyOrClosure;
    }

    /**  @inheritdoc */
    public function getPropertyValue(&$wpPost, &$wpPostMeta)
    {
        /*
         * Este era el codigo original de mario :v solo se cambiaron algunos nombres
         *      - en lugar de 'reset' se usa $this->shouldReset
         *      - en lugar de 'propiedad' que cointenia el valor de la propiedad... bueno, eso se define en $this->propertyName.
         *      - en lugar de 'metas_slug' y 'customFunction', se llama $this->metaKeyOrClosure. Si es un string, es tu meta slug, si es un Closure, es una funcion personalizada.
         *
         * Una diferencia era que en el codigo de mario a la custom function se le pasaba un $this, pero a esta se le pasara un $wpPost, y un $wpPostMeta.
         *
         * Otra diferencia, es que el codigo de mario asignaba directamente el valor a la propiedad, mas esta forma lo que hace no es asignar el valor
         * sino regresar ese valor, el que asigna los valores el el mismisimo PostObject, el cual usa una instancia
         * de esta clase para obtener la propiedad y su valor a asignarle.
         *
         *
         * //////////////////////////////////
         * // Esto era el codigo original :V
         * //////////////////////////////////
         *
         * // Si se da un meta slug
         * $this->$opcion[ 'propiedad' ] = $opcion[ 'reset' ] === true && is_array( $meta[ $opcion[ 'meta_slug' ] ] ) ? reset( $meta[ $opcion[ 'meta_slug' ] ] ) : $meta[ $opcion[ 'meta_slug' ] ];
         *
         * // ...
         *
         *
         * $funcion = $opcion[ 'customFunction' ];
         * $this->$opcion[ 'propiedad' ] = $funcion( $this );
         */

        if($this->metaKeyOrClosure instanceof \Closure){
            // Custom function handle
            $customFunction = $this->metaKeyOrClosure;
            return $customFunction($wpPost, $wpPostMeta);
        } else if(is_string($this->metaKeyOrClosure)) {
            // Meta key handle
            if (!isset($wpPostMeta[$this->metaKeyOrClosure])) {
                return null;
            }
            return $this->shouldReset && is_array($wpPostMeta[$this->metaKeyOrClosure]) ?
                    reset($wpPostMeta[$this->metaKeyOrClosure]) :
                    $wpPostMeta[$this->metaKeyOrClosure];
        } else {
            throw new \Exception("Non allowed type for 'metaKeyOrClosure', only strings and Closures are allowed.");
        }
    }
}