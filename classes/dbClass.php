<?php
/*
* Database PDO Connection Class, using prepared statements
*/

if (file_exists('../classes/variables.php')
    || file_exists('classes/variables.php') ) {
    require_once "variables.php";
    define('DB_HOST',$DB["host"]);
    define('DB_USER',$DB["user"]);
    define('DB_PASS',$DB["pass"]);
    define('DB_NAME',$DB["dbName"]);
} else {
    define('DB_HOST','');
    define('DB_USER','');
    define('DB_PASS','');
    define('DB_NAME','');
}


class DB
{  
    private $db;
    private $sql='';
    private $replace=array();
   
    // initialize connection
    function __construct($host=DB_HOST, $database=DB_NAME, $dbUser=DB_USER, $dbPassword=DB_PASS) {
        $this->db = new PDO("mysql:host=".$host.";dbname=".$database, $dbUser, $dbPassword
                , array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
    }
    
    private function build_where($where){
        if(!empty($where)){
            $this->sql.=" WHERE ";
            $c=count($this->replace);
            foreach($where as $key=>$w){
                // look for any comparitive symbols within the where array value.
                if(substr($w,0,1)=='%'){
                    // prep the query for PDO->prepare
                    $this->sql.=$key.'=%:'.$c.'% && ';
                    $this->replace[':'.$c]=$w;
                }
                else{
                    if(substr($w,0,2)=='<=')
                        $eq='<=';
                    elseif(substr($w,0,2)=='>=')
                        $eq='>=';
                    elseif(substr($w,0,1)=='>')
                        $eq='>';
                    elseif(substr($w,0,1)=='<')
                        $eq='<';
                    elseif(substr($w,0,1)=='!')
                        $eq='!=';
                    else
                        $eq='=';
                    
                    // prep the query for PDO->prepare
                    $this->sql.=$key.$eq.':'.$c.' && ';
                    $this->replace[':'.$c]=$w;
                }
                $c++;
            }
            $this->sql=substr($this->sql,0,-4);
        }
    }
    
    // general query function
    function query($query,$vals=''){
        $sth=$this->db->prepare($query);
        if($vals)
            $sth->execute($vals);
        else
            $sth->execute();
        $result=$sth->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }
    
    // select function
    function select($table,$vals='*',$where=array()){
        // initialize the sql query
        $this->replace=array();
        $this->sql="SELECT ";
        
        // add all the values to be selected 
        if(is_array($vals)){
            foreach($vals as $v)
                $this->sql.=$v.',';
            $this->sql=substr($this->sql,0,-1);
        }
        else
            $this->sql.=$vals;
        
        $this->sql.=' FROM '.$table;
        
        // build the WHERE portion of the query
        $this->build_where($where);
        $ret=$this->query($this->sql,$this->replace);
        return $ret;
    }
    
    // insert
    function insert($table,$vals){
        // empty the replace array
        $this->replace=array();
        
        $this->sql="INSERT INTO ".$table." SET ";
        
        // build the replace array and the query
        $c=count($this->replace);
        foreach($vals as $key=>$v){
            $this->sql.=$key.'=:'.$c.', ';
            $this->replace[':'.$c]=$v;
            $c++;
        }
        $this->sql=substr($this->sql,0,-2);
        
        // run and return the query
        $ret=$this->query($this->sql,$this->replace);
        $id=$this->db->lastInsertId();
        
        if($id)
            return $id;
        else
            return $ret;
    }
    
    // update
    function update($table,$vals,$where=array()){
        // empty the replace array
        $this->replace=array();
        
        $this->sql="UPDATE ".$table." SET ";
        
        // build the replace array and the query
        $c=count($this->replace);
        foreach($vals as $key=>$v){
            $this->sql.=$key.'=:'.$c.', ';
            $this->replace[':'.$c]=$v;
            $c++;
        }
        $this->sql=substr($this->sql,0,-2);
        
        // build the WHERE portion of the query
        $this->build_where($where);
        
        // run and return the query
        return $this->query($this->sql,$this->replace);
    }
    function delete($table,$where){
        // empty the replace array
        $this->replace=array();
        
        $this->sql="DELETE FROM ".$table;
        
        // build the WHERE portion of the query
        $this->build_where($where);
        
        // run and return the query
        return $this->query($this->sql,$this->replace);
    }
    
    // get the number of records matching the requirements
    function count($table,$where=false){
        
        // start query
        $this->sql="SELECT COUNT(*) FROM ".$table;
        
        // build the WHERE portion of the query
        if($where)
            $this->build_where($where);
        
        // run and return the query
        $sth=$this->db->prepare($query);
        if($vals)
            $sth->execute($vals);
        
        // return the row count
        return $sth->rowCount();
    }
    
    // gets value of requested column
    function get_value($table,$val,$where=array()){
        // run query
        $o=$this->select($table,$val,$where);
        
        // convert first object in associative array to array
        $v=get_object_vars($o[0]);
        
        // return requested value
        return $v[$val];
    }

    // query function for complicated sql
    public function extQuery($sql)
    {
        $this->sql = $sql;
        $q = $this->db->prepare($this->sql);
        $q->execute();
        $result=$q->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }
        // Returns single row Array
    public function extQueryRow($sql)
    {
        $this->sql = $sql;
        $q = $this->db->prepare($this->sql);
        $q->execute();
        $result=$q->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    public function extQueryRowObj($sql) {
        $this->sql = $sql;
        $q = $this->db->prepare($this->sql);
        $q->execute();
        $result=$q->fetch(PDO::FETCH_OBJ);
        return $result;
    }
    // Returns multiple rows Array
    public function extQueryRows($sql)
    {
        $this->sql = $sql;
        $q = $this->db->prepare($this->sql);
        $q->execute();
        $result=$q->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    // quote string inputs
    public function quoteString($string)
    {
        $this->string = $string;
        return $this->db->quote($this->string);
    }
}

?>
