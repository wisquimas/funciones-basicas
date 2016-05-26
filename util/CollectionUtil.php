<?php

/**
 * Funciónes útiles para trabajar con colecciones de datos.
 */
class CollectionUtil
{
    /**
     * Agrupa un array de arrays por una de sus keys.
     * @param $arr array
     * @param $key string
     * @return array<array>
     */
    public static function GroupArrayByKey($arr, $key)
    {
        $result = array();

        foreach ($arr as $data) {
            $id = $data[$key];
            if (isset($result[$id])) {
                $result[$id][] = $data;
            } else {
                $result[$id] = array($data);
            }
        }
        return $result;
    }

    /**
     * Agrupa un array de objetos por una de sus keys.
     * @param $arr array
     * @param $key string
     * @return array<object>
     */
    public static function GroupObjectByKey($arr, $key)
    {
        $result = array();

        foreach ($arr as $data) {

            $id = $data->$key;
            if (isset($result[$id])) {
                $result[$id][] = $data;
            } else {
                $result[$id] = array($data);
            }
        }
        return $result;
    }

    /**
     * Agrupa un array de objetos o un array de arrays por una de sus keys.
     * @param $arr array
     * @param $key string
     * @return array<object|array>
     */
    public static function GroupByKey($arr, $key)
    {
        $result = array();

        foreach ($arr as $data) {

            $id = is_array($data) ? $data[$key] : $data->$key;

            if (isset($result[$id])) {
                $result[$id][] = $data;
            } else {
                $result[$id] = array($data);
            }
        }
        return $result;
    }
}