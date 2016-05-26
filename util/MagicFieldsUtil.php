<?php

/**
 * Class MagicFieldsUtil
 * Contiene funciones útiles para trabajar con el plugin de MagicFields v.2.
 */
class MagicFieldsUtil
{
    /**
     * @param string $postId Id del post donde se encuentrael grupo.
     * @param string $groupName nombre del grupo declarado en magic fields.
     * @param array $memberMappings un array que mapea cómo construir el objeto a partir de los campos declarados en el grupo de magic fields.
     *          Tal mapeo debe seguir ésta estructura:
     *			"key" : string Nombre que será usado en la llave del objeto creado de cada campo.
     *			"value" : string|closure Si es un string, el valor para ese campo será el primer elemento del array de campos cuyo nombre coincida con éste valor. Si ningún campo magic-fields tiene dicho nombre, un string vacío será asignado como value.
     *                  El value también puede ser una función anónima, usada para campos con estructuras más complejas, como "image_media".
     *                  Tal función anónima deberá aceptar un parametro llamado (preferentemente) $group al cuál le será pasado el grupo tal
     *                  cuál es devuelto por majic fields, y como tal contendrá todos los campos del grupo, de modo que puedas aplicar lógica más completa para
     *                  seleccionar el valor que se desea asignar a ese miembro del objeto. Ésta función anónima debe regresar el elemento a ser usado como value.
     *
     * Ejemplo
     *
     * Un código como éste:
     * <code>
     *      $data = get_group( 'info_instagram', $this->ID );
     *      $instagramTitulo = isset( $data['info_instagram_titulo'] ) ? $data['info_instagram_titulo'] : false;
     *      $instagramDescripcion = isset( $data['info_instagram_descripcion'] )	? $data['info_instagram_descripcion'] : false;
     *      $instagramFoto = isset( $data['info_instagram_foto'] )	? $data['info_instagram_foto'] : false;
     * </code>
     *
     * Puede ser convertido en éste más sencillo:
     * <code>
     *      $instagramData = Producto::magic_fields_get_group_as_single(
     *          $this->ID,
     *          'info_instagram',
     *          array(
     *              'titulo' => 'info_instagram_titulo',
     *              'descripcion' => 'info_instagram_descripcion',
     *              'foto' => function($elements){return isset($elements['info_instagram_foto']) ? reset($elements['info_instagram_foto'])['original'] : "";},
     *          )
     *      );
     * </code>
     *
     * @return array El array de objetos construidos en base a las reglas especificadas en $memberMappings. Si no se encontró ningún grupo, será devuelto un array vacío.
     */
    public static function get_group_as_array($postId, $groupName, $memberMappings)
    {
        /**
         * The array of groups as returned by magic fields.
         */
        $groups = get_group($groupName, $postId);

        if(!$groups){ return array(); }

        /**
         * The formatted magic fields' group formatted by the rules of $memberMappings.
         */
        $formatedGroup = array();

        foreach($groups as $group)
        {
            $members = array();

            foreach ($memberMappings as $memberName => $memberValue) {
                if(is_string($memberValue))
                {
                    $members[$memberName] = isset($group[$memberValue]) ? reset($group[$memberValue]) : "";
                }
                else
                {
                    $members[$memberName] = $memberValue($group);
                }
            }
            $formatedGroup[] = $members;
        }
        return $formatedGroup;
    }

    /**
     * Lo mismo que get_group_as_array, sólo que devuelve el primer elemento del array que tal función devuelve. Devuelve null si get_group_as_array devuelve un array vacío.
     *
     * @param string $postId Id del post donde se encuentrael grupo.
     * @param string $groupName nombre del grupo declarado en magic fields.
     * @param $memberMappings
     * @return array|null
     */
    public static function get_group_as_single($postId, $groupName, $memberMappings)
    {
        $theArray = MagicFieldsUtil::get_group_as_array($postId, $groupName, $memberMappings);
        return $theArray ? reset($theArray) : null;
    }

    /**
     * Regresa un array de objectos de la clase especificada por $className que contiene todos los elementos de ese tipo en la db.
     *
     * IMPORTANTE: Ésa la clase $className debe tener un constructor público que reciba como parámetro un entero que será la id del post que corresponde al objeto dado de alta en wordpress.
     *
     * @param $postType string nombre del "post type" definido en magic fields para éste tipo de objetos.
     * @param $className string nombre de la clase de datos que será instanciada para generar el objeto.
     * @param array $args argumentos que se pasan al a función get_posts de Wordpress. Los únicos que serán ignorados, serán "fields", y "post_type", pues post type será el pasado por el parametro $postType.
     * @return array <$className>
     */
    public static function GetAllOfType($postType, $className, $args = array())
    {
        $args["post_type"]  = $postType;
        $args["fields"]     = "ids";

        $ids = get_posts($args);

        $collection = array();

        foreach($ids as $id)
        {
            $collection[] = new $className($id);
        }
        return $collection;
    }

    /**
     * Si al crear o actualizar un post, este tiene magic fields, se tendra que llamar este metodo para que los magic
     * fields sean guardados.
     * @param string $postType
     * @param object $magicfields estructura con los valores de los magicfields a gaurdar.
     * @param null $pageTemplate no se que sea esto, pero creo que no se usa, el valor por defecto es null.
     */
    public static function PrepareMagicFieldsToSave($postType, $magicfields, $pageTemplate = null) {
        $_POST['post_type'] = $_REQUEST['post_type'] = $postType;
        $_POST['magicfields'] = $_REQUEST['magicfields'] = $magicfields;
        $_POST['page_template'] = $_REQUEST['page_template'] = $pageTemplate;

        // Ahora, si, el siguiente guardado de post, requerira que los MF sean guardados.
        MagicFieldsUtil::$didRequestPrepareSaveMF = true;
    }

    /**
     * Sera true cuando se solicite interceptar la accion "save_post" para guardar manualmente los magic fields.
     * Sera true al llamar a la funcion PrepareMagicFieldsToSave.
     * @var bool
     */
    private static $didRequestPrepareSaveMF = false;

    function __construct(){
        add_action( 'save_post', array( &$this, 'gafa_mf_save_post_data' ) );
    }

    /**
     * Guarda la data de los magic fields. No deberias llamar esta funcion manualmente, deberas llamar
     * PrepareMagicFieldsToSave cuando requieras guardar los MF.
     * @param $post_id
     */
    function gafa_mf_save_post_data( $post_id ){

        // Si no se solicito interceptar el "save_post" para guardar los MF, no hacer nada.
        if(!MagicFieldsUtil::$didRequestPrepareSaveMF) { return; }

        $this->internal_mf_save_post_data($post_id);

        // Inmediatamente despues de actualizar los MF, poner como false.
        MagicFieldsUtil::$didRequestPrepareSaveMF = false;
    }

    /**
     * Este metodo es una copia adaptada de "mf_save_post_data", pero sin bugs, y mejoras.
     */
    private function internal_mf_save_post_data( $post_id ) {
        global $wpdb;

        //@todo hay que ponerle nonce a una de las metaboxes
        /*if ( !wp_verify_nonce( $_POST['myplugin_noncename'], plugin_basename(__FILE__) ) ) {*/
        //return $post_id;
        /*}*/

        // No necesitamos que el usuario tenga el privilegio 'edit_post'. Ahora dependemos de otro tipo de seguridad.
        // y el usuario podria no poder editar el post y aun asi modifica ciertos MF.
//        if ( !current_user_can( 'edit_post', $post_id ) )
//            return $post_id;

        //just in case if the post_id is a post revision and not the post inself
        if ( $the_post = wp_is_post_revision( $post_id ) ) {
            $post_id = $the_post;
        }

        // Check if the post_type has page attributes
        // if is the case is necessary need save the page_template
        if ( isset($_REQUEST['post_type']) && $_REQUEST['post_type'] != 'page' && isset($_REQUEST['page_template'])) {
            add_post_meta($post_id, '_wp_mf_page_template', $_POST['page_template'], true) or update_post_meta($post_id, '_wp_mf_page_template', $_POST['page_template']);
        }

        if (!empty($_POST['magicfields'])) {

            $customfields = $_POST['magicfields'];

            /** Deleting the old values **/
            $sql_delete = $wpdb->prepare( "DELETE FROM ".MF_TABLE_POST_META." WHERE post_id = %s",$post_id );
            $wpdb->query($sql_delete);

            foreach ( $customfields as $field_name => $field ) {
                delete_post_meta($post_id, $field_name);
            }
            /** / Deleting the old values **/

            //creating the new values
            foreach( $customfields as $field_name => $groups ) {

                $group_count = 1;
                foreach( $groups as $fields ) {
                    $field_count = 1;
                    foreach( $fields as $value ) {
                        //here if the value of the field needs a process before to be saved
                        //should be trigger that method here
                        //$value =  mf_process_value_by_type($field_name,$value);

                        // Adding field value meta data
                        add_post_meta($post_id, "{$field_name}", $value);

                        $meta_id = $wpdb->insert_id;

                        $sql_insert = $wpdb->prepare(
                            "INSERT INTO " . MF_TABLE_POST_META .
                            " ( meta_id, field_name, field_count, group_count, post_id ) " .
                            " VALUES " .
                            " (%s,'%s',%s,%s,%s) ",
                            $meta_id,
                            $field_name,
                            $field_count,
                            $group_count,
                            $post_id
                        );

                        $wpdb->query($sql_insert);

                        $field_count++;
                    }
                    $group_count++;
                }
            }
        }
    }
}
$myMagicFieldsUtil = new MagicFieldsUtil();