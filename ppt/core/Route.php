<?php
namespace ppt\core;
class Route{
    public static function get($route,$uri)
    {
        foreach ($route as $item){
            $status = self::getStatus($item,$uri);
            if($status===false)
                continue;
            $uri = self::bindRoute($item,$uri);
            break;
        }
        return $uri;
    }
    private static function getStatus($route,$uri)
    {
        $status = true;
        $urix = parse_url($uri)['path'];
        $tmp1 = explode('.',$route[0]);
        $tmp2 = explode('.',$urix);
        if($tmp1[count($tmp1)-1]!==$tmp2[count($tmp2)-1]){
            return false;
        }
        $tmp1 = explode('/',$tmp1[0]);
        $tmp2 = explode('/',$tmp2[0]);

        foreach ($tmp1 as $key=>$item){
            if(!preg_match('/\{@\w+\}/',$item)){
                if($item!==$tmp2[$key]){
                    $status = false;
                    break;
                }
            }else{
                preg_match('/【[\s\S]*】/',$item,$p_preg);
                if(empty($p_preg)){
                    $p_preg = '\w+';
                }else{
                    $p_preg = $p_preg[0];
                    $p_preg = str_replace(['【','】'],'',$p_preg);
                }

                if(!preg_match('/^'.$p_preg.'$/',$tmp2[$key])){
                    $status = false;
                    break;
                }
            }

        }
        return $status;
    }

    private static function bindRoute($route,$uri)
    {
        $base_param = getBaseParam($uri);
        $urix = parse_url($uri)['path'];
        $tmp1 = explode('.',$route[0]);
        $tmp2 = explode('.',$urix);
        $tmp1 = explode('/',$tmp1[0]);
        $tmp2 = explode('/',$tmp2[0]);
        foreach ($tmp1 as $key=>$item){
            if(!preg_match('/\{@\w+\}/',$item)){
                continue;
            }
            preg_match('/\{@\w+\}/',$item,$p_key);
            $p_key = $p_key[0];
            $p_key = str_replace(['{@','}'],'',$p_key);
            $base_param[$p_key] = $tmp2[$key];
        }
        $uri = '/'.$route[1];
        if(!empty($base_param))
            $uri .= '?'.http_build_query($base_param);
        return $uri;
    }


}