<?php

class Header{
    private static $title;
    
    public static function setTitle($newTitle){
        self::$title = $newTitle;
    }
    
    public static function getTitle(){
        return self::$title;
    }
    
    
}