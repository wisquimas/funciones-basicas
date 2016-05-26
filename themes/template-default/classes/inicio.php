<?php
// Incluir todos los archivos php de la carpeta actual que comienzen con "class-".

require_once  __DIR__ . "/post_object/autoload.php";

foreach (glob(__DIR__ . DIRECTORY_SEPARATOR. 'abstract' . DIRECTORY_SEPARATOR . "class-*.php") as $filename)
{
    require_once $filename;
}
foreach (glob(__DIR__ . DIRECTORY_SEPARATOR . "class-*.php") as $filename)
{
    require_once $filename;
}
