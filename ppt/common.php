<?php
function getBaseParam($uri)
{
    $base_param = [];
    $tmp = parse_url($uri);
    if(empty($tmp['query']))
        return $base_param;
    $tmpx = explode('&',$tmp['query']);
    $tmpx = array_filter($tmpx);
    foreach ($tmpx as $item){
        if(strpos($item,'=')===false)
            continue;
        $tmpxx = explode('=',$item);
        $base_param[$tmpxx[0]] = $tmpxx[1];
    }
    return $base_param;
}

function findHeaderType($list)
{
    $res = 'Content-Type: text/html; charset=utf-8';
    foreach ($list as $item){
        if(preg_match('/Content\-Type/',$item)){
            $res = $item;
            break;
        }
    }
    return $res;
}
function getUriExt($uri)
{
    $ext = false;
    $path = parse_url($uri)['path'];
    $p1 = explode('/',$path);
    $p1 = array_unique($p1);
    $p2 = $p1[count($p1)-1];
    if(strpos($p2,'.')===false){
        $ext = '/';
    }else{
        $p3 = explode('.',$p2);
        $p4 = $p3[count($p3)-1];
        $ext = $p4;
    }
    return $ext;
}

function http_404()
{
    header('HTTP/1.1 404 Not Found');
    echo '<html>
<head><title>404 Not Found</title></head>
<body>
<center><h1>404 Not Found</h1></center>
<hr><center>nginx/1.26.0</center>
</body>
</html>';
    exit();
}

function http_500($msg)
{
    if(debug===false)
        $msg = '';
    else
        $msg = dump($msg,true);
    header('HTTP/1.1 500 Internal Server Error');
    echo '<html>
<head><title>500 Internal Server Error</title></head>
<body>
<center><h1>500 Internal Server Error</h1></center>
<hr><center>nginx/1.26.0</center>
<p>'.$msg.'</p>
</body>
</html>';
    ;
    exit();
}