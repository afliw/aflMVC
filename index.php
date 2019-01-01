<?php
// CFG Includes START-----------------------------------------------------------
include_once("cfg/cfg_db.php");
include_once("cfg/cfg_path.php");
include_once("cfg/cfg_router.php");
include_once("cfg/cfg_general.php");
// CFG Includes END-------------------------------------------------------------

// Classes Includes START-------------------------------------------------------
include_once("class/router.php");
include_once("class/trace.php");
include_once("class/view.php");
include_once("class/controller.php");
include_once("class/header.php");
include_once("class/debug.php");
// Classes Includes END---------------------------------------------------------

Router::Route($_SERVER["REQUEST_URI"]);
