<?php
function includeTemplate($template, $array)
{
    if(!isset($template)){
        return "";
    }
    ob_start();
    
    require_once __DIR__."/templates/$template";
    
    $html = ob_get_clean();
    
    return $html;
}