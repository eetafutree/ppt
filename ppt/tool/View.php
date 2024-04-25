<?php

namespace ppt\tool;
class View
{
    private static $tpl_path = app . '/html/';
    private static $ext = '.html';

    public static function display($_tpl = '', $_param = [])
    {
        if (!empty($_param)) {
            foreach ($_param as $_key => $_v) {
                $$_key = $_v;
            }
        }
        if (empty($_tpl)) {
            switch (run_mode){
                case 'file':
                    $_tpl =  debug_backtrace()[0]['file'];
                    break;
                case 'namespace':
                    $_tpl =  str_replace('.php','\\',debug_backtrace()[0]['file']).debug_backtrace()[1]['function'].'.php';
                    break;
            }
            $_tpl = strtolower($_tpl);
            $_tpl = str_replace('\\','/',$_tpl);
            $_tpl = preg_replace('/^'.preg_quote(app,'/').'\/code\//',app . '/html/',$_tpl);
            $_file1 = str_replace('.php',self::$ext,$_tpl);
        }
        $_file2 = self::$tpl_path . 'tmp' . self::$ext;
        if(is_file($_file2))
            unlink($_file2);
        /*if (!is_file($_file1)) {
            $_msg = 'error: 文件' . __FILE__ . ' 第' . __LINE__ . '行 函数' . __FUNCTION__ . ' file【' . $_file1 . '】不存在';
            echo $_msg;
            exit();
        }*/
        $_html = file_get_contents($_file1);
        $_content = self::biuBiu($_html);
        file_put_contents($_file2, $_content);
        ob_start();
        require_once $_file2;
        $_res = ob_get_clean();
        unlink($_file2);
        return $_res;
    }

    public static function biuBiu($tpl)
    {
        $content = self::fanyiIf($tpl);
        $content = self::fanyiElse($content);
        $content = self::fanyiXunhuan($content);
        $content = self::fanyiBianliang($content);
        $content = self::biaoqianQuchon($content);
        return $content;
    }
    private static function biaoqianQuchon($tpl)
    {
        $pattern = "/[\s]*\?\>[\s]*\<\?php /U";
        preg_match_all($pattern, $tpl, $res);
        if (!empty($res)) {
            foreach ($res[0] as $item) {
                $tpl = str_replace($item, ' ', $tpl);
            }
        }
        return $tpl;
    }

    private static function fanyi($tpl,$preg,$replace_preg,$replace_arr)
    {
        preg_match_all($preg, $tpl, $res);
        if (!empty($res)) {
            $res_p = $res[0];
            foreach ($replace_preg as $pk=>$pv){
                $res_p = preg_replace($pk,$pv,$res_p);
            }
            foreach ($replace_arr as $ak=>$av){
                $res_p = str_replace($ak,$av,$res_p);
            }
            foreach ($res[0] as $key => $item) {
                $tpl = str_replace($item, $res_p[$key], $tpl);
            }
        }
        return $tpl;
    }

    private static function fanyiElse($tpl)
    {
        $tpl = self::fanyiElse_head($tpl);
        $tpl = self::fanyiElse_end($tpl);
        return $tpl;
    }

    private static function fanyiElse_head($tpl)
    {
        return self::fanyi($tpl,'/\{[\s]*else[\s\S]*[\s]*\}/U',['/[\s]+/'=>'', '/\{else/U'=>'<?php else ', '/\}/U'=>'{ ?>'],[]);
    }

    private static function fanyiElse_end($tpl)
    {
        return self::fanyi($tpl,'/\{[\s]*\/[\s]*else[\s\S]*[\s]*\}/U',['/[\s]+/'=>'', '/{\/else[\s\S]*\}/U'=>'<?php } ?>',],[]);
    }

    private static function fanyiIf($tpl)
    {
        $tpl = self::fanyiIf_head($tpl);
        $tpl = self::fanyiIf_end($tpl);
        return $tpl;
    }

    private static function fanyiIf_head($tpl)
    {
        return self::fanyi($tpl,'/\{[\s]*if[\s\S]*[\s]*\}/U',['/[\s]+/'=>'',],['{if'=>'<?php if(', '}'=>'){ ?>',]);
    }

    private static function fanyiIf_end($tpl)
    {
        return self::fanyi($tpl,'/\{[\s]*\/[\s]*if[\s]*\}/',['/[\s]+/'=>'',],['{/if}'=>'<?php } ?>',]);
    }

    private static function fanyiBianliang($tpl)
    {
        return self::fanyi($tpl,'/\{[\s]*\$[\S]+[\s]*\}/U',['/[\s]+/'=>'',],['{'=>'<?php echo ', '}'=>'; ?>',]);
    }

    private static function fanyiXunhuan($tpl)
    {
        $tpl = self::fanyiXunhuan_head($tpl);
        $tpl = self::fanyiXunhuan_end($tpl);
        return $tpl;
    }

    private static function fanyiXunhuan_head($tpl)
    {
        return self::fanyi($tpl,'/\{[\s]*for[\s\S]*[\s]*\}/U',['/[\s]+/'=>' ', '/\{[\s]+/U'=>'{', '/[\s]+\}/U'=>'}',],['{for'=>'<?php for', '}'=>'{ ?>',]);
    }

    private static function fanyiXunhuan_end($tpl)
    {
        return self::fanyi($tpl,'/\{[\s]*\/[\s]*for[\s\S]*[\s]*\}/U',['/[\s]+/'=>'', '/\{\/for[\s\S]*\}/'=>'<?php } ?>',],[]);
    }
}