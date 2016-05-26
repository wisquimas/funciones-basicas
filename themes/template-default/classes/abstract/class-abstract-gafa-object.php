<?php
/**
 * Class GafaObject
 */
namespace Gafa;

abstract
class GafaObject
{
    /**
     * @var int
     */
    public $ID;
    /**
     * Postype del objeto
     */
    const PostType = '';
    /**
     * Array con instancias singletonas
     * @var array
     */
    protected static $Instances = array();

    protected
    function __construct( $id = 0 )
    {
        $this->ID    = $id;
        $this->title = get_the_title( $this->ID );

        $clase    = get_called_class();
        $opciones = $clase::GetOptions();
        if ( $opciones ) {
            $meta = get_post_meta( $this->ID );
            foreach ( $opciones as $opcion ) {
                $this->CleanOption( $opcion );
                if ( empty( $opcion[ 'propiedad' ] ) ) {
                    return;
                };

                if ( isset( $meta[ $opcion[ 'meta_slug' ] ] ) ) {
                    /*
                     * Si existe la meta etiqueta
                     */
                    $this->$opcion[ 'propiedad' ] = null;
                    if ( $opcion[ 'meta_slug' ] ) {
                        /*
                         * Si se da un meta slug
                         */
                        $this->$opcion[ 'propiedad' ] = $opcion[ 'reset' ] === true && is_array( $meta[ $opcion[ 'meta_slug' ] ] ) ? reset( $meta[ $opcion[ 'meta_slug' ] ] ) : $meta[ $opcion[ 'meta_slug' ] ];
                    }
                } else {
                    $this->$opcion[ 'propiedad' ] = null;
                }
                if ( $opcion[ 'customFunction' ] ) {
                    /*
                     * Si se da una funcion custom
                     */
                    $funcion                      = $opcion[ 'customFunction' ];
                    $this->$opcion[ 'propiedad' ] = $funcion( $this, $opcion[ 'propiedad' ] );
                }
            }
        }
    }

    /**
     * Recibe funciones para filtrar con contenido
     * @return \Closure
     */
    final static protected function FuncionHtml(){
        return function( $clase,$propiedad ){
            return apply_filters('the_content',$clase->$propiedad);
        };
    }

    /**
     * Recibe el link del objeto
     * @return \Closure
     */
    final static protected
    function FuncionLink()
    {
        return function( $clase,$propiedad ){
            return get_permalink($clase->ID);
        };
    }
    /**
     * Recibe html del contenido del post
     * @return \Closure
     */
    final static protected function FuncionContenido(){
        return function( $clase,$propiedad ){
            return apply_filters('the_content',get_post_field('post_content',$clase->ID));
        };
    }


    /**
     * Recibe funciones de imagenes
     * @return \Closure
     */
    final static protected function FuncionImagen(){
        return function( $clase,$propiedad ){
            return _processed_value($clase->$propiedad,'image_media');
        };
    }

    /**
     * Recibe un loop de imagenes
     * @return \Closure
     */
    final static protected function FuncionImagenes(){
        return function( $clase,$propiedad ){
            $data = array();
            foreach ( (array)$clase->$propiedad as $elemento ){
                $data[] = _processed_value($elemento,'image_media');
            }
            return $data;
        };
    }

    /**
     * Devuelve un grupo Limpio de MF
     * @param string $grupo
     *
     * @return \Closure
     */
    final public static function FuncionDeGrupo( $grupo = '' ){
        return function( $clase,$propiedad ) use( $grupo ){
            $groupMf = get_group($grupo,$clase->ID);
            static::LimpiarGrupo($groupMf);
            return $groupMf;
        };
    }

    /**
     * Limpia los grupos MF
     * @param array $grupo
     */
    final public static function LimpiarGrupo( array &$grupo = array() ){
        if( $grupo ){
            foreach ($grupo as &$propiedad){
                foreach ($propiedad as &$valor){
                    $valor = reset($valor);
                }
            }
        }
    }

    /**
     * Setea las opciones por defecto del constructor
     *
     * @param array $options
     */
    final private
    function CleanOption( &$options = array() )
    {
        $args    = array(
            'propiedad'      => null,
            'meta_slug'      => null,
            'reset'          => true,
            'customFunction' => null,
        );
        $options = array_merge( $args, $options );
    }

    /**
     * Opciones de la clase
     * @return array
     */
    static public
    function GetOptions()
    {
        return array();
    }

    /**
     * Nos permite almacenar singletones
     *
     * @param int $id
     *
     * @return static
     */
    final static public
    function InstanceCached( $id = 0 )
    {
        $clase = get_called_class();
        if ( !isset( $clase::$Instances[ $id ] ) ) {
            $clase::$Instances[ $id ] = new $clase( $id );
        }

        return $clase::$Instances[ $id ];
    }

    /**
     * Instancia la clase
     *
     * @param int $id
     *
     * @return static
     */
    final static public
    function Instance( $id = 0 )
    {
        $clase = get_called_class();

        return new $clase( $id );
    }

    /**
     * Trae todos los posts del tipo
     *
     * @param array $options Sobreescribe las opciones por defecto
     *
     * @return static[]
     */
    final static public
    function Get( $options = array() )
    {
        $clase = get_called_class();
        $args  = array(
            'post_type'      => $clase::PostType,
            'fields'         => 'ids',
            'posts_per_page' => -1,
        );
        $args  = array_merge( $args, $options );

        return get_posts( $args );
    }
}