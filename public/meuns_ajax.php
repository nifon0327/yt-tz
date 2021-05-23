<?php   
//电信-zxq 2012-08-01
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
switch($ActionId){
    case "1":
			 $mysql = "SELECT   M.id,M.name,T.name AS TypeName FROM ac_menus  M 
			LEFT JOIN ac_menutypes  T ON T.id = M.typeid
			 WHERE parent_id=0";
			$myResult = mysql_query($mysql);
			$return ="   <td scope='col' align='right'>一级菜单</td><td scope='col'><select name='parent_id' id='parent_id'  style='width:380px' dataType='Require'  msg='未选择一级菜单'>";
			    $return.="<option value='' >请选择</option>";
			while($myRow = mysql_fetch_array($myResult)){
			      $id = $myRow["id"];
			      $name = $myRow["name"];
			      $TypeName = $myRow["TypeName"];
			    $return.="<option value='$id' >$TypeName-$name</option>";
			}
			    $return.="</select></td>";
			echo $return;
      break;
    case "2":
			 $mysql = "SELECT   id,name FROM ac_menutypes order by id ASC ";
			$myResult = mysql_query($mysql);
			$return ="   <td scope='col' align='right'>一级菜单类型</td><td scope='col'><select name='typeid' id='typeid'  style='width:380px' dataType='Require'  msg='未选择一级菜单类型'>";
			    $return.="<option value='' >请选择</option>";
			while($myRow = mysql_fetch_array($myResult)){
			      $id = $myRow["id"];
			      $name = $myRow["name"];
			    $return.="<option value='$id' >$name</option>";
			}
			    $return.="</select></td>";
			echo $return;
      break;
}
?>