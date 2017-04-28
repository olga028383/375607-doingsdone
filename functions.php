<?php
function includeTemplate($template, $templateData)
{
    if(!isset($template)){
        return "";
    }
    ob_start();
    
    /*htmlspecialcharacters() используется в шаблоне при выводе данных*/
    require_once __DIR__."/templates/$template";
    
    $html = ob_get_clean();
    
    return $html;
}