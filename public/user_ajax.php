<?php   
//电信-zxq 2012-08-01
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Log_Item="用户角色";			//需处理
$Log_Funtion="数据更新";
switch($ActionId){
	case "RoleId"://新增入库数据
        $updateSql ="UPDATE $DataIn.UserTable SET roleId=$RoleId,modifier='$Operator',modified='$DateTime'  WHERE Id=$UserId";
        $updateResult =@mysql_query($updateSql);
       if($updateResult && mysql_affected_rows()>0){
		         $DelSql  ="DELETE  FROM $DataIn.ac_usermenus WHERE UserId=$UserId ";
		         $DelResult =@mysql_query($DelSql);
		         $insertSql ="INSERT INTO $DataIn.ac_usermenus  SELECT  NULL,'$UserId',menu_id,action,'1','0','0','$Operator', '$DateTime','$Operator', '$DateTime','$Date','$Operator'
		          FROM $DataIn.ac_rolemenus  WHERE role_id=$RoleId";
		         $insertResult =@mysql_query($insertSql);
		        if($insertResult){
		                echo "Y";
		                $Log.="登录名为 $UserId 的 默认角色 权限 添加成功! <br>";
		              }
		        else{
		                echo "$insertSql";
		               $Log.="<div class=redB>登录名为 $UserId 的 默认角色 权限 添加失败$insertSql! </div><br>";
		            }
             }else{
        		                echo "$updateSql";       
             }
    break;
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
?>