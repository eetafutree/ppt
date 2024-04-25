<?php
header('Content-Type: text/html; charset=utf-8');
define('root',strtolower(str_replace('\\','/',realpath(__DIR__.'/..'))));

require root.'/ppt/start.php';