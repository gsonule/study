<?php 
require_once(__DIR__."/../config.php");
class EyeBallProClass{ 
    private $path;
    function __construct($classname="",$path="Class"){
        /*set the path for class*/
        $this->path=$path;
        if(is_object($classname)){                        
            $this->handler=$classname;							
        }                    
    }             
    public function setClass($value){
        if(is_object($value))
            $this->handler=$value;
    }
    public function getPath(){
        return $this->path;
    }
    public function setPath($path){
        $this->path=$path;
    }
}
spl_autoload_register(function ($classname){
    $proClass   = new EyeBallProClass();
    $class      = $proClass->getPath()."/{$classname}.php";
    if (stream_resolve_include_path($class))
        include $class;
});
?>
