<?php

// Called on '/example'
function index(){
    Header::setTitle("Example Controller");
    // Model included automatically by filename match
    $exampleModel = new ExampleModel();
    $date = $exampleModel->getDate();
    $viewData = array(
        "nowDate" => $date
    );
    // Include file in view folder with the same name as controller and action: 'view/[controller]/[action].php'
    // In this case: 'view/example/index.php'
    // It is possible to specify view name (filename) in View::Show method call. In example, to call a 
    // view with filename 'about.php' ('view/example/about.php') it will be 'View::Show("about"[, $viewData])'.
	View::Show($viewData);
}

// Called on '/example/paramEcho?namedParam=value'
// In case no param is present in the URL, and empty array is passed to the function call
function paramEcho($namedParam){
    if(is_array($namedParam)) {
        echo "No parameter present in the URL";
    } else {
        echo $namedParam;
    }    
}

// Called on '/example/addressParam[/param1/param2.../paramN]'
function addressParam($addressParam){
    View::Show($addressParam);
}