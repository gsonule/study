<?php
/**
 * @author Anushree
 * 
 * @uses file to create service object centrally 
 */
if(!isset($serviceObj)){
    /*file to get vebbler class objects*/
    include_once ECP_PATH_DIR.'Class/ServiceCreator.php';   

    include_once ECP_PATH_DIR."ConfigurationEcpServiceCreator.php";

    $classObj = new ClassObjects();
    /*get Vebbler Class Objects*/
    $classObj->getObjects();    
    $serviceObj = $classObj->getServiceObject();

    /*exception class*/
    include_once ECP_PATH_DIR."Services/ServiceValidator.php";      

    /*exception class object*/
    $expObj = new ServiceException();
}

?>
