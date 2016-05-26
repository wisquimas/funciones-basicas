<?php
/*FUNCIONES EXCLUSIVAS DE AJAX*/

if( !function_exists('add_newsletter') )
{
    function add_newsletter( $mail = false ){
        return NewsletterHelper::Add($mail);
    }
}