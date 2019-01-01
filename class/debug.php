<?php

class Debug
{
  public static function Out($msg) {
    $callingFunction = Trace::GetLastMethodCall();
    if(CFG_DEBUG_ENABLED) {
      if($callingFunction["class"] == "bolas"){
        echo "<p>Function <b>{$callingFunction['function']}</b> says: $msg</p>";
      }else {
        echo "<p>Method <b>{$callingFunction['function']}</b> in class <b>{$callingFunction['class']}</b> says: $msg</p>";
      }

    }
  }
}
