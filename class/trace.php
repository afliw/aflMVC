<?php

class Trace {

    public static function GetLastMethodCall(){
        $e = new Exception();
        $trace = $e->getTrace();
        $last_call = $trace[1];
        $last_call["function"] = debug_backtrace()[2]["function"];
        $last_call["class"] = array_key_exists("class", $trace[2]) ? $trace[2]['class'] : "bolas";
        return $last_call;
    }

}
