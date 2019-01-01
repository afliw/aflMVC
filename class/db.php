<?php	
/*
	Ejemplo de uso de clase [DB]:
	DB::Read("SELECT id, name FROM client WHERE name = 'Pedro'");
	DB::Write("INSERT INTO client (name, lastname) VALUES ('Pablo','Perez')");
*/
class DB{
	protected static $con,$initialized = false;
	
	private static function initialize(){
	    if(self::$initialized) return;
		try{
			self::$con = new PDO(CFG_DB_DRIVER.":host=".CFG_DB_HOST.";dbname=".CFG_DB_DBNAME.";charset=".CFG_DB_CHARSET, CFG_DB_USER, CFG_DB_PASSWORD);
			self::$con->prepare("SET TEXTSIZE 9145728")->execute();
			self::$initialized = true;
		}catch(PDOException $e) {  
			echo "<h3 style='text-align:center;background-color:black;color:red;font-weight:bolder;border:1px solid black;'>{$e->getMessage()}</h3>";  
		}
	}



	public static function Read($query,$arrayType = PDO::FETCH_ASSOC){
	    self::initialize();
		$STH = self::$con->prepare($query);
		$result = $STH->execute();
		if($result){
			return $STH->fetchAll($arrayType);
		}else{
			return false;
		}
	}

	public static function Write($query){
	    self::initialize();
		$STH = self::$con->prepare($query);
		$result = $STH->execute();
		return $result;
	}
	
	public static function CloseConnection(){
		self::$con = null;
		self::$initialized = false;
	}
}


/*
	Clase [DBO] en desarrollo, no usar.
*/

class DBO {
    private $table,$fields,$values,$joins,$where,$orderby,$limit,$offset;
    private $fieldHolders, $valueHolders;
    private $operation;
    public $Error;
    private $lastShackle;
    
    protected $con;
	
	public function __construct(){
		try{
			$this->con = new PDO(CFG_DB_DRIVER.":host=".CFG_DB_HOST.";dbname=".CFG_DB_DBNAME.";charset=".CFG_DB_CHARSET, CFG_DB_USER, CFG_DB_PASSWORD);
			$this->con->prepare("SET TEXTSIZE 9145728")->execute();
		}catch(PDOException $e) {  
			echo "<h3 style='text-align:center;background-color:black;color:red;font-weight:bolder;border:1px solid black;'>{$e->getMessage()}</h3>";  
		}
	}
    
    private $errorMsg = [
        "Operation mode not set or not valid.",
        "Invalid paramenter for operation. Expecting associative array.",
        "Unknown operation.",
        "Method `And`|`Or` only can be used after `Where`.",
        "Invalid parameter. Expecting 3 element array or array with 3 elements arrays.",
        "Can't call Where method two times in a chain. Use _And|_Or instead."
        ];
    
    public function Select($table){
        $this->operation = "SELECT";
        $this->table = $table;
        return $this;
    }
    
    public function Insert($table){
        $this->operation = "INSERT";
        $this->table = $table;
        return $this;
    }
    
    public function Update($table){
        $this->operation = "UPDATE";
        $this->table = $table;
        return $this;
    }
    
    public function Delete($table){
        $this->operation = "DELETE";
        $this->table = $table;
        return $this;
    }
    
    public function SetData($data){
        if(!$this->operation || $this->operation == "DELETE") return $this->setError(0);
        if(($this->operation == "UPDATE" || "INSERT" ) && !is_array($data) && !$this->isAssoc($data)) return $this->setError(1);
        
        switch ($this->operation){
            case "SELECT":
                $this->fields = implode(", ",$data);
                break;
            case "INSERT":
                $this->fields = implode(", ",array_keys($data));
                $this->valueHolders = "";
                foreach($data as $k => $v){
                    $this->valueHolders .= ":v{$k}, ";
                    $data[":v".$k] = $v;
                    unset($data[$k]);
                }
                $this->valueHolders = substr($this->valueHolders,0,strrpos($this->valueHolders,", "));
                $this->values = $data;
                break;
            case "UPDATE":
                $this->fields = "";
                foreach($data as $k => $v){
                    $this->fields .= "{$k} = :v{$k}, ";
                    $data[":v".$k] = $v;
                    unset($data[$k]);
                }
                $this->fields = substr($this->fields,0,strrpos($this->fields,", "));
                $this->values = $data;
                break;
            default:
                return $this->setError(2);
        }
        return $this;
    }
    
    public function Exec($fetchType = PDO::FETCH_ASSOC){
        $query = $this->buildQuery();
        if(is_array($this->joins))
            foreach($this->joins as $join){
                $query .= " " . $join;
            }
        $query .= $this->where ? " WHERE ".$this->where : "";
        $query .= $this->orderby ? " ORDER BY " . $this->orderby : "";
        $query .= $this->limit ? " LIMIT " . $this->limit : "";
        $query .= $this->offset ? " OFFSET " . $this->offset : "";
        $sth = $this->con->prepare($query);
        $result = $sth->execute($this->values);
        switch($this->operation){
        	case "UPDATE":
        	case "DELETE":
        		return $result;
        		break;
        	case "INSERT":
        		return $result ? $this->con->lastInsertId() : false;
        		break;
        	case "SELECT":
        		return $result ? $sth->fetchAll($fetchType) : false;
        		break;
        }
            
        
    }
    
    private function buildQuery(){
        $ret = "";
        switch($this->operation){
            CASE "SELECT":
                $ret = "SELECT {$this->fields} FROM {$this->table}";
                break;
            CASE "INSERT":
                $ret = "INSERT INTO {$this->table} ({$this->fields}) VALUES ($this->valueHolders)";
                break;
            CASE "UPDATE":
                $ret = "UPDATE {$this->table} SET {$this->fields}";
                break;
            CASE "DELETE":
                $ret = "DELETE FROM {$this->table}";
                break;
            default:
                return $this->setError(2);
        }
        return $ret;
    }
    
    public function Where($conditionData,$logicalOperator = null){
        if($this->operation == "INSERT") return $this->setError(0);
        if(!is_array($conditionData)) return $this->setError(4);
        if($this->where && !$logicalOperator) return setError(5);
        $this->where = $logicalOperator ? $this->where . $logicalOperator : $this->where;
        if(is_array($conditionData[0])){
            $multiClause = " (";
            foreach($conditionData as $w){
                $multiClause .= $this->buildWhereClause($w) . " AND ";
            }
            $multiClause = substr($multiClause,0,strrpos($multiClause," AND "));
            $this->where .= $multiClause . ")";
        }else{
            $this->where .= $this->buildWhereClause($conditionData);
        }
        
        
        $this->lastShackle = "Where";
        return $this;
    }
    
    private function buildWhereClause($arrClause){
        $identifier = ":we".substr_count($this->where,":we");
        $whereClause = "{$arrClause[0]} {$arrClause[1]} {$identifier}{$arrClause[0]}";
        $this->values[$identifier.$arrClause[0]] = $arrClause[2];
        return $whereClause;
    }
    
    public function _And($conditionData){
        if( !($this->lastShackle == "Where") ) return setError(3);
        $this->Where($conditionData," AND ");
        return $this;
    }
    
    public function _Or($conditionData){
        if( !($this->lastShackle == "Where") ) return setError(3);
        $this->Where($conditionData," OR ");
        return $this;
    }
    
    private function setError($msg){
        $this->Error = $this->errorMsg[$msg];
        return false;
    }
    
    private function isAssoc($arr){
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}