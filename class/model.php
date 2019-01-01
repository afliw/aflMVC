<?php


abstract class Model{

    public $id, $table;

    public function __construct(){
        $argsCount = func_num_args();
        if($argsCount == 0) return;
        if($argsCount == 1 && is_array($arrArg = func_get_arg(0))){
            $this->arraySetter($arrArg);
        }
    }
    
	public function arraySetter($arr){
        foreach($arr as $k => $v){
            $this->$k = $v;
        }
    }
	
	public function testFunc(){
		var_dump(get_object_vars($this));
	}
	
    // public function Update(){
        // if(!is_numeric($this->id)) return false;
        // $parsedAttributes = $this->parseAttributesForUpdate();
        // if(!$parsedAttributes) return false;
        // $query = "UPDATE client SET " . $parsedAttributes;
        // return DB::Write($query);
    // }
    
    // private function getRecordFromId($id){
        
    // }
    
    // private function parseAttributesForUpdate(){
        // if(!is_numeric($this->id)) return false;
        // $attr = get_object_vars($this);
        // $updateString = "";
        // foreach($attr as $k => $v){
            // if($k == "id" || $v == null || $v == "") continue;
            // $updateString .= "{$k} = {$v}, ";
        // }
        // $updateString = substr($updateString,0,strrpos($updateString,", "));
        // $updateString .= " WHERE id = {$this->id}";
        // return $updateString;
    // }  
}