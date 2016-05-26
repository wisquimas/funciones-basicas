<?php
/**
 * Clase GafaTemplate
 * Se encarga de preparar el sistema de clases de forma correcta
 */
namespace Gafa;
class GafaTemplate
{
    // CONSTANTES DE FILTROS

    /**
     * Filtro para definir el lugar donde se guardan los templates.
     */
    const FilterGafaTemplatePath = "GafaTemplatePath";

    // IMPLEMENTACION DE SINGLETON

    /**
     * Unica instancia de la clase GafaTemplate
     * @var GafaTemplate null
     */
    private static $intance = null;

    /**
     * @return GafaTemplate regresa el singleton de GafaTemplate.
     */
    public static function Instance(){

        if(GafaTemplate::$intance === null) {
            GafaTemplate::$intance = new GafaTemplate();
        }

        return GafaTemplate::$intance;
    }

    private function __construct()
    {
        // Filtros de inicializacion.
        $this->templatepath = apply_filters(GafaTemplate::FilterGafaTemplatePath,TEMPLATEPATH);
    }

    /**
     * Lugar donde se guardan los templates.
     * @var string
     */
    public $templatepath = "templates";

    /**
     * Imprime el template
     * @param string $path       Path del archivo dentro de la carpeta template
     * @param array  $argumentos Variables que se quieren enviar al template
     * @return string el codigo html del template.
     */
    private function ImprimirInternal( $path = '', $argumentos = array() ){
        extract($argumentos, EXTR_PREFIX_SAME, "wddx");
        unset($argumentos);

        ob_start();
        require($this->templatepath.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$path);
        return ob_get_clean();
    }

    /**
     * Imprime el template
     * @param string $path
     * @param array $argumentos
     * @return string
     */
    public static function Imprimir( $path = '', $argumentos = array() ){
        return GafaTemplate::Instance()->ImprimirInternal($path, $argumentos);
    }
}
