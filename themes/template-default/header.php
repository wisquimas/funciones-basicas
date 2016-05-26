<!doctype html>
<html lang="es">
<!--
 ______   ______   ______  ______       __    __   __  __
/\  ___\ /\  __ \ /\  ___\/\  __ \     /\ "-./  \ /\_\_\_\
\ \ \__ \\ \  __ \\ \  __\\ \  __ \    \ \ \-./\ \\/_/\_\/_
 \ \_____\\ \_\ \_\\ \_\   \ \_\ \_\    \ \_\ \ \_\ /\_\/\_\
  \/_____/ \/_/\/_/ \/_/    \/_/\/_/     \/_/  \/_/ \/_/\/_/

 Desarrollado por: gafa.mx
-->
<head>
    <meta charset="utf-8"/>
    <title><?php if( is_home() || is_front_page() ){ bloginfo('name'); }else{ wp_title(); };?></title>
    <link rel="icon" href=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!-- Meta SEO -->
    <!-- Meta Facebook -->
    <?php
    $variableParaVersionDeFiles = '0.00001';
    etiquetas_og();
    ?>
    <link type="text/css" rel="stylesheet" href="<?php assets();?>/css/compilados/style.css?v=<?= $variableParaVersionDeFiles;?>">
    <link type="text/css" rel="stylesheet" href="<?php assets();?>/css/compilados/responsive.css?v=<?= $variableParaVersionDeFiles;?>">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

    <?php require_once("assets/js/gafaCore.php");?>
    <script src="<?php assets();?>/js/script.js?v=<?= $variableParaVersionDeFiles;?>"></script>
    <!-- Comienza WP Head -->
    <?php wp_head();?>
    <!-- Google Analytics -->
</head>
<body <?php body_class("normal color_negro blanco");?>>
