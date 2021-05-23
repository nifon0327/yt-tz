<?php
  /**
   * 
   */

  class Dbhelper
  {
    public $conn;
  	function __construct(){
  		$config = parse_ini_file("config.ini");
        $this->conn = new mysqli(
            $config["resources.database.dev.hostname"],
            $config["resources.database.dev.username"],
            $config["resources.database.dev.password"],
            $config["resources.database.dev.database"],
            $config["resources.database.dev.port"]);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
  	}

  	  /**
    * 查询返回数据集合
    */
    public function query_result($sql){
        $result_query = $this->conn->query($sql);
        $result = array();
        if ($result_query->num_rows > 0) {
            while ($row = $result_query->fetch_assoc()) {
                $result[] = $row;
            }
            return $result;
        } else {
            return null;
        }

    }
    /**
     * 返回查询对象单条
    */
    public function query_rows($sql){
       $result=$this->conn->query($sql);
       return $result->fetch_object();
    }


    /*
    *  返回查询主键ID
    */
    public function query_insert_id($sql){
       $query=$this->conn->query($sql);
       return $this->conn->insert_id;
    }
    

   
    /*
    * 返回查询影响行数
    */
    public function query_affected_rows($sql){
       $this->conn->query($sql);
       return $this->conn->affected_rows;
    }


    public function Q($query=''){
      return $this->conn->query($query);
    }
     


    
  }