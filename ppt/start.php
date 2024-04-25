<?php
define('ppt',root.'/ppt');
require ppt.'/conf.php';
require app.'/conf.php';
require ppt.'/exception.php';
require ppt.'/autoload.php';
require app.'/route.php';
use ppt\core\Route;
use ppt\core\PPT;
use ppt\core\Huancun;
$uri = '/';
switch (mode){
    case 'cgi-fcgi':
        $uri = $_SERVER['REQUEST_URI'];
        break;
    case 'cli':
        empty($_SERVER['argv'][1])? $uri = '/':$uri=$_SERVER['argv'][1];
        break;
}
$uri = Route::get(route,$uri);
$uri_ext = getUriExt($uri);
if(!in_array($uri_ext,uri_ext)){
    http_404();
}
$uri_status = PPT::checkUri($uri,run_mode);
if($uri_status===false){
    http_404();
}
if(huancun===true){
    $hc_id = sha1($uri);
    $huancun_handle = new Huancun($hc_id);
//    Huancun::clean();die;
    $hc_status = $huancun_handle->check();
    if(!$hc_status){
        $res = PPT::run($uri,run_mode);
        if($_error)
            http_500($_error_msg);
        $type = findHeaderType(headers_list());
        $huancun_handle->write($res,$type,$uri);
    }
    $content = $huancun_handle->read();
    $res = $content['content'];
    $type = $content['type'];
}else{
    $res = PPT::run($uri,run_mode);
    if($_error)
        http_500($_error_msg);
    $type = findHeaderType(headers_list());
}
PPT::out($res,$type);