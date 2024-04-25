<?php
namespace ppt\core;
class Huancun
{
    private static $path = root.'/run';
    private $file;
    private $id;
    public function __construct($id)
    {
        $this->id = $id;
        $this->file = self::$path.'/'.$id.'.html';
    }

    public function check()
    {
        $res = false;
        if(is_file($this->file))
            $res = true;
        return $res;
    }
    public function write($content,$type,$uri)
    {
        $type_str = '<?php /* type->'.$type.'<-type */ ?>';
        $uri_str = '<?php /* uri->'.$uri.'<-uri */ ?>';
        $content = $type_str.$uri_str.$content;
        file_put_contents($this->file,$content);
    }
    public function read()
    {
        $content = file_get_contents($this->file);
        preg_match('/^\<\?php \/\* type\-\>[\s\S]+\<\-type \*\/ \?\>/',$content,$type);
        $type = str_replace(['<?php /* type->','<-type */ ?>'],'',$type[0]);
        $content = preg_replace('/^\<\?php \/\*[\s\S]*\*\/ \?\>/','',$content);
        return [
            'content'=>$content,
            'type'=>$type
        ];
    }
    public static function clean()
    {
        $list = glob(self::$path.'/*');
        foreach ($list as $item){
            unlink($item);
        }
    }
}