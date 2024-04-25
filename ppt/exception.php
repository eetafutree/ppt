<?php
set_error_handler('_error_handler');
set_exception_handler('_exception_handler');

//restore_error_handler();


function _error_handler($errno, $errstr, $errfile, $errline) {
    global $_error;
    global $_error_msg;
    $_error = true;
    $arr = [
        'errno'=>$errno,
        'errstr'=>$errstr,
        'errfile'=>$errfile,
        'errline'=>$errline,
    ];
    $_error_msg[] = $arr;
}
function _exception_handler($exception) {
    global $_error;
    global $_error_msg;
    $_error = true;
    $_error_msg[] = $exception;
}


function dump($s=null,$return = false)
{
    ob_start();
    var_dump($s);
    $res = ob_get_clean();
    $res = preg_replace('/'.preg_quote(']=>','/').'\n[\s]+/m', '] => ', $res);
    switch (php_sapi_name()){
        case 'cgi-fcgi':
            $res = preg_replace('/  /U', "\t", $res);
            $res = '<pre><code>'.$res.'</code></pre>';
            if($return)
                return $res;
            echo $res;
            break;
        case 'cli':
            if($return)
                return $res;
            echo $res;
            break;
    }
}