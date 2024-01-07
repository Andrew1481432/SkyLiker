<?php

function req($item){
    include (__DIR__.'/../'.$item.'.php');
}

spl_autoload_register(function($class){
    req(str_replace("\\", "/", $class));
});