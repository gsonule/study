<?php
/**
* @main class ServiceCreator
* @author Salimkhan Sodha
* @uses 
* when we create an instance of a class in other classes function's or constructor's or we keep classes tightly coupled 
* is termed as 'concrete dependancy'. Concrete Dependancy has many. This files allows developers to add Dependancy 
* Injection in a very easy and clean wayr
*/
class ServiceCreator implements ArrayAccess{
    protected $values;
    function __construct(array $values = array()){
        $this->values = $values;
    }

    /**
    * sets a parameter or object
    * Objects must be defined as Closures
    * @param string $id    The unique identifier for the parameter or object
    * @param mixed  $value The value of the parameter or a closure to defined an object
    */
    public function offsetSet($id, $value) {
        $this->values[$id] = $value;
    }

    /**
    * Gets a parameter or an object.
    * @param string $id The unique identifier for the parameter or object
    * @return mixed The value of the parameter or an object
    * @throws InvalidArgumentException if the identifier is not defined
    */
    public function offsetGet($id) {
        if(!array_key_exists($id,$this->values))
            throw new InvalidArgumentException(sprintf("Identifier '%s' is not defined",$id));
        $isFactory = (is_object($this->values[$id])) && is_callable($this->values[$id]);
        return $isFactory?$this->values[$id]($this):$this->values[$id];
    }

    /**
    * Checks if a parameter or an object is set.
    * @param string $id The unique identifier for the parameter or object
    * @return Boolean
    */
    public function offsetExists($id) {
        return array_key_exists($id,$this->values);
    }
    /**
    * Unsets a parameter or an object.
    * @param string $id The unique identifier for the parameter or object
    */
    public function offsetUnset($id) {
        unset($this->values[$id]);
    }

    /**
    * Returns a closure that stores the result of the given service definition
    * for uniqueness in the scope of this instance of Pimple.
    * @param callable $callable A service definition to wrap for uniqueness
    * @return Closure The wrapped closure
    */
    public static function share($callable){
        if(!is_object($callable) && !is_callable($callable))
            throw new InvalidArgumentException("Callable is not a closure or a invokable object");
        return function($obj)use($callable){
            static $object;
            if(null == $object)
                $object = $callable($obj);
            return $object;   
        };
    }
}


class ClassObjects{
    private static $serviceObj;
    function __construct(){
        ClassObjects::$serviceObj = new ServiceCreator();
    }
    /**
    * sets Vebbler class objects, so that objects remains
    * central to application and get instantiated only once
    */
    public function getObjects(){
        ClassObjects::$serviceObj["configurationProObj"] = ClassObjects::$serviceObj->share(function(){
            return new EyeBallProClass();
        });

        ClassObjects::$serviceObj["dashboardObj"] = ClassObjects::$serviceObj->share(function($obj){
            $obj["configurationProObj"]->setClass(new Dashboard());
            return $obj["configurationProObj"]->handler;
        });
        ClassObjects::$serviceObj["profileObj"] = ClassObjects::$serviceObj->share(function($obj){
            $obj["configurationProObj"]->setClass(new Profile());
            return $obj["configurationProObj"]->handler;
        });

        ClassObjects::$serviceObj["shootEmailObj"] = ClassObjects::$serviceObj->share(function($obj){
            $obj["configurationProObj"]->setClass(new EcpShootEmail());
            return $obj["configurationProObj"]->handler;
        });

        ClassObjects::$serviceObj["reportObj"] = ClassObjects::$serviceObj->share(function($obj){
            $obj["configurationProObj"]->setClass(new Report());
            return $obj["configurationProObj"]->handler;
        });

        ClassObjects::$serviceObj["patientsObj"] = ClassObjects::$serviceObj->share(function($obj){
            $obj["configurationProObj"]->setClass(new Patients());
            return $obj["configurationProObj"]->handler;
        });

        ClassObjects::$serviceObj["ecpblogObj"] = ClassObjects::$serviceObj->share(function($obj){
            $obj["configurationProObj"]->setClass(new Blog());
            return $obj["configurationProObj"]->handler;
        });
    }
    public static function getServiceObject(){
        return ClassObjects::$serviceObj;
    }
}
?>