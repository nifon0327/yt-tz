<?php
//取得配件属性; 传入值:$StuffId;返回值:$StuffPropertys数组;

$StuffPropertys=array();
$PropertyResult=mysql_fetch_array(mysql_query("SELECT GROUP_CONCAT(Property,'') AS Property  FROM stuffproperty WHERE StuffId='$StuffId'",$link_id));
$PropertyStr=$PropertyResult['Property'];
if ($PropertyStr!=""){
	$StuffPropertys=explode(',', $PropertyStr);
}
?>