<?php

function includeModel()
{

    $directorio = opendir('model');
    while ($archivo = readdir($directorio)) {
        if (!is_dir($archivo)) {
            require_once 'model/' . $archivo;
        }
    }
}

/**
 * Funcion que nos permite retornar json a partir de un array
 * @param Array $data
 */
function json_response($data)
{
    if (is_array($data)) {
        $array = array();
        foreach ($data as $d) {
            $array[] = $d->getColumnas();
        }
        return json_encode($array, JSON_UNESCAPED_UNICODE);
    } else {
        return json_encode($data->getColumnas(), JSON_UNESCAPED_UNICODE);
    }
}
