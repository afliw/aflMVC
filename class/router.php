<?php

class Router {

    public static function Route($requestUrl){
        $requestUrl = self::removePath($requestUrl);
        $controllerAction = self::parseUrl($requestUrl);
        if(count($controllerAction) > 0 && strlen($controllerAction[0]) > 1){
            $controller = $controllerAction[0];
            $action = count($controllerAction) > 1 && strlen($controllerAction[1]) > 1 ? $controllerAction[1] : "index";
            $alternateParams = array();
			for($i = 2; $i < count($controllerAction); $i++){
				array_push($alternateParams,$controllerAction[$i]);
			}
			if(!Controller::Call($controller,$action,$alternateParams)){
                Debug::Out("Controller call failed.");
                self::UnknownResource();
            }
        }else{
            Debug::Out("No controller specified. Using default if defined.");
            self::Route(CFG_ROUTER_DEFAULT_CONTROLLER);
        }
    }

    private static function parseUrl($requestUrl){
        if(strpos($requestUrl,"?")){
            $requestUrl = substr($requestUrl,0,strpos($requestUrl,"?"));
        }
        $requestUrl = trim($requestUrl,"/");
        $decomposedURI = explode("/",$requestUrl);
        return $decomposedURI;
    }

    public static function UnknownResource(){
        //var_dump(Trace::GetLastMethodCall());
        include(CFG_ROUTER_404_FILE);
        exit;
    }

    private static function removePath($requestUrl){
	    if(BASE_URL == "/") return $requestUrl;
		if(substr(BASE_URL,-1) == '/' && substr($requestUrl,-1) !== '/')
			$requestUrl = $requestUrl . "/";
        return str_replace(BASE_URL,"",$requestUrl);
    }
}
