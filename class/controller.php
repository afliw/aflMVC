<?php

class Controller {

	static $calledControllers;

    public static function Call($cName,$aName = "",$alternateParams = []){
        if(!self::includeController($cName,$aName)){
            Debug::Out("Controller <b>$cName</b> or action <b>$aName</b> could not be invoked.");
            return false;
        }
        self::includeModel($cName);
		if($aName)
			return self::performAction($aName,$alternateParams);
		else
			return true;
    }

    private static function performAction($aName,$alternateParams = []){
        if(!function_exists($aName)){
          Debug::Out("Function <b>$aName</b> doesn't exist.");
          return false;
        }
        $actionParameters = self::setActionParameters(self::get_function_arguments($aName));
		
        call_user_func_array($aName,count($actionParameters) > 0 ? $actionParameters : array($alternateParams));
        return true;
    }

    private static function includeController($cName,$aName){
        $cFullURI = self::buildControllerFullURI($cName);
        if(!self::verifyController($cFullURI)){
          Debug::Out("Controller <b>$cFullURI</b> doesn't exist.");
          return false;
        }
        include_once($cFullURI);
        return true;
    }

    private static function includeModel($mName){
        $modelPath = "model/{$mName}.php";
        if(file_exists($modelPath)){
			include_once("class/db.php");
			include_once("class/model.php");
            include_once($modelPath);
		}
    }

    private static function verifyController($cFullURI){
        return file_exists($cFullURI);
    }

    private static function buildControllerFullURI($cName){
        return "controller/{$cName}Controller.php";
    }

    private static function setActionParameters($actionArgs){
        $parameters = array();
        for($i = 0; $i < count($actionArgs);$i++){
            $parameters[$i] = isset($_REQUEST[$actionArgs[$i]]) ? $_REQUEST[$actionArgs[$i]] : null;
        }
        return count($parameters) > 0 && $parameters[0] != null ? $parameters : null;
    }

    private static function get_function_arguments($fName){
        $f = new ReflectionFunction($fName);
        $args = array();
        foreach ($f->getParameters() as $param) {
            $args[] = $param->name;
        }
        return $args;
    }
}
