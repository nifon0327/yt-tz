<?php
  include_once "../model/MyDbHelper.php";
  $id=isset($_GET['id'])?$_GET['id']:0;
  if($id==0){
  	  StatusCode(-100,null,'非法操作');
  }
  $sql="update nonbom7_inmain SET Bill=0,BillNumber='' where id=$id";
  $flag=execute($sql,$link);
  StatusCode(100,null,'删除成功');

?>