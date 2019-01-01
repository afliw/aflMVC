<?php

class ExampleModel {
    public function __construct(Type $var = null) {
        $this->var = $var;
    }

    public function getDate(){
        return date("Y-m-d H:i:s");
    }
}