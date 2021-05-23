<?php 
/*
代码、数据库合并后共享-ZXQ 2012-08-08
 读取公司db数据库名称
 */
if ($cSign>0){
    $cSignResult = mysql_query("SELECT Db FROM $DataPublic.companys_group WHERE cSign=$cSign ORDER BY Id",$link_id);
    if($cSignRow = mysql_fetch_array($cSignResult)){
         $DataIn=$cSignRow["Db"];
    }
}
?>