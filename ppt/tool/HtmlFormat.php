<?php
namespace ppt\tool;
class HtmlFormat
{
    public static function miniHtml($s)
    {
        //注：只支持压缩含一个<script></script>的html,且变量内多个空格也会被压缩为一个
        //提取css跟javascript单独处理
        $s = preg_replace('/\<style[\s\S]*\>/U','<style>',$s);
//    preg_replace('/\<script[\s\S]*\>/','<script>',$s);

        preg_match('/\<style\>[\s\S]*\<\/style\>/',$s,$style);
        preg_match('/\<script\>[\s\S]*\<\/script\>/',$s,$script);
        empty($style)?$style='':$style = $style[0];
        empty($script)?$script='':$script = $script[0];

        //处理html
        $html = preg_replace('/\<style\>[\s\S]*\<\/style\>/','<style></style>',$s);
        $html = preg_replace('/\<script\>[\s\S]*\<\/script\>/','<script></script>',$html);
        $html = preg_replace('/\<\!\-\-[\s\S]*\-\-\>/U','',$html);

        $html = preg_replace('/[\s]+/',' ',$html);
        $html = preg_replace('/>[\s]+</','><',$html);

        //处理css
        $style = preg_replace('/\/\*[\s\S]*\*\//U','',$style);
        $style = preg_replace('/[\s]+/',' ',$style);
        $style = preg_replace('/[\s]?\'[\s]?/','\'',$style);
        $style = preg_replace('/[\s]?"[\s]?/','"',$style);
        $style = preg_replace('/[\s]?\{[\s]?/','{',$style);
        $style = preg_replace('/[\s]?\}[\s]?/','}',$style);
        $style = preg_replace('/[\s]?\:[\s]?/',':',$style);
        $style = preg_replace('/[\s]?;[\s]?/',';',$style);
        $style = preg_replace('/[\s]?,[\s]?/',',',$style);
        $style = preg_replace('/[\s]?\<style\>[\s]?/','<style>',$style);
        $style = preg_replace('/[\s]?\<\/style\>[\s]?/','</style>',$style);

        //处理js

        $script = preg_replace('/\/\*[\s\S]*\*\//U','',$script);

        $script = preg_replace('/^[\s]*\/\/.*$\n/m','',$script);
        $script = preg_replace('/[\s]+/',' ',$script);
        $script = preg_replace('/[\s]?\([\s]?/','(',$script);
        $script = preg_replace('/[\s]?\)[\s]?/',')',$script);
        $script = preg_replace('/[\s]?\{[\s]?/','{',$script);
        $script = preg_replace('/[\s]?\}[\s]?/','}',$script);
        $script = preg_replace('/[\s]?\=[\s]?/','=',$script);
        $script = preg_replace('/[\s]?\|\|[\s]?/','||',$script);
        $script = preg_replace('/[\s]?\&\&[\s]?/','&&',$script);
        $script = preg_replace('/[\s]?\+[\s]?/','+',$script);

        $script = preg_replace('/[\s]?\:[\s]?/',':',$script);
        $script = preg_replace('/[\s]?;[\s]?/',';',$script);
        $script = preg_replace('/[\s]?,[\s]?/',',',$script);
        $script = preg_replace('/[\s]?\<script\>[\s]?/','<script>',$script);
        $script = preg_replace('/[\s]?\<\/script\>[\s]?/','</script>',$script);

        //合并css跟js
        $html = preg_replace('/\<style\>[\s\S]*\<\/style\>/',$style,$html);
        $html = preg_replace('/\<script\>[\s\S]*\<\/script\>/',$script,$html);
        return $html;
    }

//格式化html
    public static function fomatHtml($content)
    {
        $content = self::miniHtml($content);
        preg_match('/\<style\>[\s\S]*\<\/style\>/',$content,$style);
        preg_match('/\<script\>[\s\S]*\<\/script\>/',$content,$script);
        empty($style)?$style='':$style = $style[0];
        empty($script)?$script='':$script = $script[0];
        $style = str_replace('<style>',"<style>\n",$style);
        $style = preg_replace('/;\}\}/',"@#",$style);
        $style = preg_replace('/;\}/',"@@",$style);
        $style = preg_replace('/\}/',"$$$",$style);
        $style = preg_replace('/;/',";\n\t",$style);
        $style = preg_replace('/@#/',";\n\t}\n}\n\n",$style);
        $style = preg_replace('/@@/',";\n}\n",$style);
        $style = preg_replace('/\$\$\$/',"\n}\n\n",$style);
        $style = preg_replace('/\{/',"{\n\t",$style);
        $style = preg_replace('/\n/',"\n\t\t",$style);
        $style = str_replace("\t</style>","</style>",$style);

        $script = self::format_javascript($script);

        $content = preg_replace('/\<style\>[\s\S]*\<\/style\>/','<style></style>',$content);
        $content = preg_replace('/\<script\>[\s\S]*\<\/script\>/','<script></script>',$content);

        $tmp = explode('><', $content);
        $new = [];
        foreach ($tmp as $key => $item) {
            if ($key === 0) {
                $item .= '>';
                $new[] = $item;
            } else if ($key === count($tmp) - 1) {
                $item = '<' . $item;
                $new[] = $item;
            } else {
                if (strpos($item, '<') !== false || strpos($item, '>') !== false) {
                    $tmp1 = str_replace(['<', '>'], '@', $item);
                    $tmp2 = explode('@', $tmp1);
                    foreach ($tmp2 as $keyxxx=>$itemxxx){
                        if(($keyxxx+1)%3===0){
                            $new[] = '<' . $tmp2[$keyxxx-2] . '>';
                            $new[] = str_replace(" ",'',$tmp2[$keyxxx-1]);
                            $new[] = '<' . $tmp2[$keyxxx] . '>';
                        }
                    }
                } else {
                    $item = '<' . $item . '>';
                    $new[] = $item;

                }
            }
        }
        $i = 0;
        $str = '';
        foreach ($new as $key => $item) {
            if(substr($item,0,strlen('<html'))==='<html'||substr($item,0,strlen('<body'))==='<body'){
                $i=0;
            }
            if($key>0){
                if(substr($new[$key-1],0,strlen('<body'))==='<body')
                    $i=0;
            }

            if(strpos($item,'<')===false&&strpos($item,'>')===false){
                //内容
                $str.= $item;
            }
            else if(substr($item,strlen($item)-2,2)=='/>'){
                //单标签
                $p = '';
                $k = $i;
                while ($k>0){
                    $p.="\t";
                    $k--;
                }
                $str.= $p;
                $str.= $item;
                $str.= "\r\n";
            }else if(strpos($item,'<meta')!==false
                ||strpos($item,'<link')!==false
                ||strpos($item,'<input')!==false
                ||strpos($item,'<img')!==false
                ||strpos($item,'<hr')!==false
                ||strpos($item,'<br')!==false
                ||strpos($item,'<html')!==false
            ){
                //单标签
                $p = '';
                $k = $i;
                while ($k>0){
                    $p.="\t";
                    $k--;
                }
                $str.= $p;
                $str.= $item;
                $str.= "\r\n";

            }else{
                if(substr($item,0,2)!=='</'){
                    //双标签开始
                    $p = '';
                    $k = $i;
                    while ($k>0){
                        $p.="\t";
                        $k--;
                    }
                    if($key!==(count($new)-2)){
                        if(strpos($new[$key+1],'<')===false&&strpos($new[$key+1],'>')===false){
                            $str.= $p;
                            $str.= $item;
                        }else{
                            $str.= $p;
                            $str.= $item;
                            $str.= "\r\n";
                        }
                    }else{
                        $str.= $p;
                        $str.= $item;
                        $str.= "\r\n";
                    }
                    $i++;
                }else{
                    //双标签结束
                    $i--;
                    $p = '';
                    $k = $i;
                    while ($k>0){
                        $p.="\t";
                        $k--;
                    }
                    if($key>0){
                        if(strpos($new[$key-1],'<')===false&&strpos($new[$key-1],'>')===false){
                        }else{
                            $str.= $p;
                        }
                    }else{
                        $str.= $p;
                    }
                    $str.= $item;
                    $str.= "\r\n";
                }
            }
        }
        $str = preg_replace('/\<style\>[\s\S]*\<\/style\>/',$style,$str);
        $str = preg_replace('/\<script\>[\s\S]*\<\/script\>/',$script,$str);
        $str = preg_replace('/\n[\s]*\n/m',"\n",$str);
        return $str;
    }

//格式化js
    private static function format_javascript($js_code) {
        $js_code = str_replace(['<script>','</script>'],'',$js_code);
        $js_code = str_replace(';',";\n",$js_code);
        $js_code = str_replace('{',"\n{\n",$js_code);
        $js_code = str_replace('}',"\n}\n",$js_code);
        $tmp = explode("\n",$js_code);
        $tmp = array_filter($tmp);
        $lines = '';
        $index = 0;
        $space = '';
        foreach ($tmp as $item){

            if(strpos($item,'}')!==false){
                $index--;
            }

            $space = '';
            for ($i=0;$i<$index;$i++){
                $space.="\t";
            }
            $lines.="\n".$space.$item;
            if(strpos($item,'{')!==false){
                $index++;
            }
        }
        $lines = str_replace("\nfunction","\n\nfunction",$lines);
        $lines = str_replace("\n;",";",$lines);
        $lines = preg_replace('/\}[\s]+\)/',"})",$lines);
        $lines = preg_replace('/\)[\s]+\{/',"){",$lines);
        $lines="<script>\n".$lines."\n</script>";
        return $lines;
    }
}