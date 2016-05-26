<?php

namespace gafa;

/**
 * Gestiona datos del usuario dentro de un "user meta" como colecciones de datos.
 * @package gafa
 */
class UserPrefs
{
    /**
     * Inserta una nueva entrada en el diccionario de entradas.
     * @param string $userId Id del usuario.
     * @param string $dictName Nombre del diccionario donde será insertada la nueva entrada.
     * @param array $entryData Array relacional (data-value) con los datos de la entrada a guardar.
     * @return mixed id de la entrada insertada.
     */
    public static function Insert($userId, $dictName, $entryData) {
        $dictionary = UserPrefs::GetDictionary($userId, $dictName);
        $dictionary[] = $entryData;

        // Get the assigned key of the entry.
        end($dictionary);
        $key = key($dictionary);

        update_user_meta($userId, $dictName, $dictionary);
        return $key;
    }

    /**
     * Actualiza una entrada en el diccionario de entradas.
     * @param string $userId Id del usuario.
     * @param string $dictName Nombre del diccionario donde será insertada la nueva entrada.
     * @param string $entryId Id de la entrada del diccionario a reemplazar. Si no existe entrada con tal id, no se hará nada.
     * @param array $entryData Array relacional (data-value) con los datos de la entrada a actualizar.
     * @param bool $found (out) (optional) Será true si la entrada fue encontrada y actualizada.
     */
    public static function Update($userId, $dictName, $entryId, $entryData, &$found = false) {
        $dictionary = UserPrefs::GetDictionary($userId, $dictName);
        $found = isset($dictionary[$entryId]);

        if($found) {
            $dictionary[$entryId] = $entryData;
        }

        update_user_meta($userId, $dictName, $dictionary);
    }

    /**
     * Elimina una entrada del diccionario de entradas.
     * @param string $userId Id del usuario.
     * @param string $dictName Nombre del diccionario.
     * @param string $entryId Id de la entrada a eliminar del diccionario.
     * @param bool $found (out) (optional) True si la variable fue encontrada y eliminada.
     */
    public static function Delete($userId, $dictName, $entryId, &$found = false) {
        $dictionary = UserPrefs::GetDictionary($userId, $dictName);
        $found = isset($dictionary[$entryId]);
        if($found) {
            unset($dictionary[$entryId]);
        }
        update_user_meta($userId, $dictName, $dictionary);
    }

    /**
     * Elimina todas las entradas de un diccionario.
     * @param string $userId Id del usuario.
     * @param string $dictName Nombre del diccionario a ser vaciado.
     */
    public static function DeleteAll($userId, $dictName) {
        update_user_meta($userId, $dictName, array());
    }

    /**
     * Obtiene una entrada del diccionario.
     * @param string $userId id del usuario.
     * @param string $dictName nombre del diccionario de dónde obtener la entrada.
     * @param string $entryId Id de la entrada a eliminar del diccionario.
     * @param bool $found (out) (optional) Si es colocada, el valor de ésta será true si la entrada es encontrada.
     * @return array
     */
    public static function GetEntry($userId, $dictName, $entryId, &$found = false) {
        $dictionary = UserPrefs::GetDictionary($userId, $dictName);
        $found = isset($dictionary[$entryId]);
        return $found ? $dictionary[$entryId] : null;
    }

    /**
     * Obtiene un diccionario de entradas.
     * @param string $userId id del usuario.
     * @param string $dictName nombre del diccionario de dónde obtener la entrada.
     * @return array
     */
    public static function GetDictionary($userId, $dictName) {
        $dictionary = get_user_meta($userId, $dictName, true);
        return is_array($dictionary) ? $dictionary : array();
    }
}