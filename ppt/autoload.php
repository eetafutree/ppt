<?php
require ppt.'/common.php';
require app.'/common.php';
if(is_file(root.'/vendor/autoload.php'))
    require root.'/vendor/autoload.php';
spl_autoload_register(function ($class_name) {
    $class_file=$class_name.'.php';
    $class_file = str_replace('\\','/',$class_file);
    $class_file = preg_replace('/^ppt/',ppt,$class_file);
    $class_file = preg_replace('/^app/',app,$class_file);
    require $class_file;
});
if(is_file(app.'/database.php'))
    require app.'/database.php';