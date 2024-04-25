<?php
namespace ppt\core;
class PPT
{
    public static function checkUri($uri,$run_mode)
    {
        $res = true;
        switch ($run_mode) {
            case 'file':
                $run_handle = app . '/code' . parse_url($uri)['path'];
                if (preg_match('/\/$/', $run_handle))
                    $run_handle .= 'index.php';
                if (!is_file($run_handle))
                    return false;
                break;
            case 'namespace':
                $run_handle = parse_url($uri)['path'];
                if (preg_match('/\/$/', $run_handle))
                    $run_handle .= 'index';
                preg_match_all('/\//',$run_handle,$x);
                if(count($x[0])===1){
                    $run_handle = '/Index'.$run_handle;
                }
                $function = substr($run_handle,strrpos($run_handle,'/')+1,strlen($run_handle));
                $namespace = substr($run_handle,0,strrpos($run_handle,'/'));
                $namespace = 'app/code'.$namespace;
                $namespace = str_replace('/','\\',$namespace);
                if(self::namespaceExists($namespace)===false)
                    return false;

                if (!(new \ReflectionClass($namespace))->hasMethod($function))
                    return false;

                if (!(new \ReflectionMethod($namespace,$function))->isPublic())
                    return false;

                break;

        }
        return $res;
    }
    public static function run($uri, $run_mode)
    {
        $res = false;
        switch ($run_mode) {
            case 'file':
                $res = self::runFile($uri);
                break;
            case 'namespace':
                $res = self::runNamespace($uri);
                break;

        }
        return $res;
    }


    public static function out($content,$type)
    {
        if ($content === false) {
            die('404');
        }
        if($type!=='Content-Type: text/html; charset=utf-8')
            header($type);
        ob_start();
        echo $content;
        ob_flush();
    }

    private static function runFile($uri)
    {
        $run_handle = app . '/code' . parse_url($uri)['path'];
        if (preg_match('/\/$/', $run_handle))
            $run_handle .= 'index.php';
        $param = getBaseParam($uri);
        $res = self::realRunFile($param, $run_handle);
        return $res;
    }
    private static function runNamespace($uri)
    {
        $run_handle = parse_url($uri)['path'];
        if (preg_match('/\/$/', $run_handle))
            $run_handle .= 'index';
        preg_match_all('/\//',$run_handle,$x);
        if(count($x[0])===1){
            $run_handle = '/Index'.$run_handle;
        }
        $function = substr($run_handle,strrpos($run_handle,'/')+1,strlen($run_handle));
        $namespace = substr($run_handle,0,strrpos($run_handle,'/'));
        $namespace = 'app/code'.$namespace;
        $namespace = str_replace('/','\\',$namespace);
        $param = getBaseParam($uri);
        $res = self::realRunNamespace($param, $namespace,$function);
        return $res;
    }

    private static function realRunFile($_p, $_f)
    {
        $_GET = $_p;
        ob_start();
        require $_f;
        $res = ob_get_clean();
        return $res;
    }
    private static function realRunNamespace($_p,$_n, $_f)
    {
        $_GET = $_p;
        ob_start();
        (new $_n)->$_f();
        $res = ob_get_clean();
        return $res;
    }

    private static function namespaceExists($namespace) {
        $res = false;
        $class_file=$namespace.'.php';
        $class_file = str_replace('\\','/',$class_file);
        $class_file = preg_replace('/^ppt/',ppt,$class_file);
        $class_file = preg_replace('/^app/',app,$class_file);
        if(is_file($class_file))
            $res = true;
        return $res;
    }

}