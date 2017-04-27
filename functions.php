<?php
function includeTemplate($template, $array)
{
    if(!isset($template)){
        return "";
    }
    
    require_once __DIR__."/templates/$template";
}