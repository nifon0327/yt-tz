<?php
  session_start();

  include_once "../basic/parameter.inc";
  $hostip='';
  function myconnect($hostname,$database,$username,$password){
  	 $link=new mysqli($hostname,$username,$password,$database,'3306');
  	 return $link;
  }

  function query($sql, $link){
	 $query = mysqli_query($link, $sql);
	 return $query;
  }

    /*查询返回结果集*/
   function result($sql, $link){
	    $result = array();
	    $query=query($sql,$link);
		while ($row=mysqli_fetch_assoc($query)) {
			$result[]=$row;
		}
		return $result;
   }

   function row($sql, $link){
		$result=query($sql, $link);
		return mysqli_fetch_assoc($result);
	 }

   function execute($sql, $link){
    $query=query($sql, $link);
    return mysqli_affected_rows($link);
  }

  function exec_insertid($sql, $link){
    $query=query($sql,$link);
    return mysqli_insert_id($link);
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

  $link = myconnect($host,$DataPublic,$user,$pass);
  mysqli_set_charset($link,'utf8');
  
