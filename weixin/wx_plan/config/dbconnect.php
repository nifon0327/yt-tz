<?php
/**
 * Created by PhpStorm.
 * User: IceFire
 * Date: 2018/11/24
 * Time: 22:17
 */
class DbConnect
{
	private $_link;
	private $_charset;
	function __construct()
	{
	   $config = parse_ini_file("config.ini");
	   $this->_link = new mysqli(
            $config["resources.database.dev.hostname"],
            $config["resources.database.dev.username"],
            $config["resources.database.dev.password"],
            $config["resources.database.dev.database"],
            $config["resources.database.dev.port"]);
        if ($this->_link->connect_error) {
            die("Connection failed: " . $this->_link->connect_error);
        }
        $this->_charset=$config["resources.database.dev.charset"];
        mysqli_set_charset($this->_link,$this->_charset);
	}
    
    /*
     * query
     */
	public function query($sql){
	  $query = mysqli_query($this->_link, $sql);
	  return $query;
	}

    /*查询返回结果集*/
	public function result($sql){
	    $result = array();
	    $query=$this->query($sql);
		while ($row=mysqli_fetch_assoc($query)) {
			$result[]=$row;
		}

		return $result;
	}
    
    /*查询返回一行*/
	public function row($sql){
		$result=$this->query($sql);
		return mysqli_fetch_assoc($result);
	}
    

    /*执行SQL返回行数*/
	public  function execute($sql){
		$query=$this->query($sql);
		return mysqli_affected_rows($this->_link);
	}

	public function exec_insertid($sql){
		$query=$this->query($sql);
		return mysqli_insert_id($this->_link);
	}
     
	function StatusCode($status, $result, $msg = '成功')
	{
	    echo json_encode(array(
	        'status' => $status,
	        'result' => $result,
	        'msg' => $msg
	    ));
	    exit();
	}
}