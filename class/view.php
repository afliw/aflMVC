<?php

class View {
	/*
		Variable parameters:
		If the first argument is a string, then that parameter define a custom view name to be called.
		If the first parameter is a bool, then is taken as a toggle for using master template.
		If it's neither, then is taken to be the view data passed on to the view.
	*/
    public static function Show(){
        $viewName = "";

        $useMasterTemplate = true;
        for($i = 0; $i < func_num_args(); $i++){
            $testArg = func_get_arg($i);
            if(is_string($testArg) && $i == 0){
                $viewName = $testArg;
            }else if(is_bool($testArg)){
                $useMasterTemplate = $testArg;
            }else{
                $_ViewData = $testArg;
            }
        }

        $callTrace = Trace::GetLastMethodCall();
        $viewPath = self::parseViewPath($viewName,$callTrace);
        if(self::verifyView($viewPath)){
            if($useMasterTemplate){
				include("view/_public/master.php");
			} else {
				include($viewPath);
			}
				
        }else {
          Debug::Out("View <b>$viewPath</b> could not be found.");
          Router::UnknownResource();
        }
    }

    private static function parseViewPath($viewName,$callTrace){
        if(strpos($viewName,"/")){
            return $viewName;
        }else if(strlen($viewName) > 0){
            return self::buildFullViewPath($viewName, self::getControllerName($callTrace["file"]));
        }else{
            return self::buildFullViewPath($callTrace["function"], self::getControllerName($callTrace["file"]));
        }
    }

    private static function buildFullViewPath($viewName,$controllerName){
        return "view/".$controllerName."/".$viewName.".php";
    }

    private static function getControllerName($scriptPath){
        $scriptPath = str_replace("Controller.php","",$scriptPath);
        $sIdx = strrpos($scriptPath,DIRECTORY_SEPARATOR)+1;
        return substr($scriptPath,$sIdx);
    }

    private static function verifyView($viewPath){
        return file_exists($viewPath);
    }

}
