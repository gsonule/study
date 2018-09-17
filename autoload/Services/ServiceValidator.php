<?php
/**
 * 
 * @main_class "ServiceException"
 * 
 * @uses Exception handling class for Services.php
 */
class ServiceException extends Exception{
    
    /*variables to hold custom error message*/
    private $user_message;
    
    function __construct($message = "") {
        $this->user_message = $message;
    }
    
    public function checkArguments($argArray,$checkArray){
        for($i=0;$i<count($argArray);$i++)
        {
            if(!isset($checkArray[ $argArray[$i] ])){
                throw new ServiceException("missing parameter '".$argArray[$i]."'");
                break;
            }
        }        
    }    
    
    public function typeOfArg($argArray,$checkArray){
        $data_type_check = array(
            "int"=>function($value){
                return array("status"=>is_numeric(trim($value)),"data_type"=>"numeric");
            },            
            "array"=>function($value){
                return array("status"=>is_array($value),"data_type"=>"array");
            },
            "bool"=>function($value){
                return array("status"=>($value != 0 || $value != 1)?true:false,"data_type"=>"boolean");
            }
        );
        foreach($argArray as $key=>$value){
            /*check the argument is correct*/
            $get_status = $data_type_check[$value]($checkArray[$key]);
               
            if($get_status["status"] === FALSE){
                /*on error send user friendly error message*/
                throw new ServiceException("parameter '".$key."' should be ".$get_status["data_type"]);
                break;
            }                
        }            
    }
    
    public function checkSizeExist($size){
        $valid_size = array("thumb","status","about");
        if(!in_array($size,$valid_size))
            throw new ServiceException("parameter '".$size."' doesn't exist. Valid parameter for size ".implode(",",$valid_size));
    }
    
    public function getUserMessage(){
        return $this->user_message;
    }
}

?>
