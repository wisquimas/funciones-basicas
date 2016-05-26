<?php

// Cargar clases base
foreach (glob(__DIR__ . DIRECTORY_SEPARATOR . "class-*.php") as $filename)
{
    require_once $filename;
}

// Cargar property builders
foreach (glob(__DIR__ . DIRECTORY_SEPARATOR . "property_builders" . DIRECTORY_SEPARATOR . "class-*.php") as $filename)
{
    require_once $filename;
}

// Cargar property builders para magic field.
foreach (glob(__DIR__ . DIRECTORY_SEPARATOR . "property_builders" . DIRECTORY_SEPARATOR. "mf_property_builders" . DIRECTORY_SEPARATOR . "class-*.php") as $filename)
{
    require_once $filename;
}

// Cargar clases de testing.
foreach (glob(__DIR__ . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "class-*.php") as $filename)
{
    require_once $filename;
}