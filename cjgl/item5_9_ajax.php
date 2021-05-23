<?php   
//电信-zxq 2012-08-01
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="车间领料数据";			//需处理
$Log_Funtion="数据更新";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";

switch($ActionId){
	case 41://删除车间领料数据 
	    $delSql="DELETE FROM $DataIn.ck5_llsheet WHERE sPOrderId='$sPOrderId' AND StockId='$StockId' AND Estate='1'";
	    $delResult=mysql_query($delSql);	
		if($delResult){
		    echo "Y";
	        $Log.="<div class=greenB>".$Id."领料记录册除成功!</div><br>";
	        $delSql1="DELETE FROM $DataIn.ck5_llsheet  L 
	        LEFT JOIN $DataIn.cg1_stuffcombox  G ON G.StockId = L.StockId
	        WHERE L.sPOrderId='$sPOrderId' AND G.mStockId='$StockId' AND L.Estate='1'";
	        $delResult1=mysql_query($delSql1);	
	     } 
         else{
		     echo "N"; 
  	         $Log.="<div class=redB>".$Id."领料记录册除失败!</div><br>";
	         $OperationResult="N";
	    }
	break;
   	case 42://更新领料数据状态
        $upSql="UPDATE $DataIn.ck5_llsheet SET Estate=0,Receiver='$Operator',Received='$DateTime' 
                WHERE sPOrderId='$sPOrderId' AND StockId='$StockId' ";   
        $upResult=mysql_query($upSql);	
        
	    if($upResult){
	        echo "Y";
		    $UpdateComboxSql = "UPDATE $DataIn.ck5_llsheet  L  
		    LEFT JOIN $DataIn.cg1_stuffcombox C ON C.StockId = L.StockId 
		    SET L.Estate = 0,L.Receiver='$Operator',L.Received='$DateTime' 
		    WHERE C.mStockId = '$StockId' AND L.sPOrderId = '$sPOrderId'";
		    $UpdateComboxResult = mysql_query($UpdateComboxSql);
            $Log="<div class=greenB>" . $Id . "领料单确认成功!</div><br>";

           /* include_once "../weixin/weixin_api.php";

            $weixin = new weixin_api();

            $touser = 'op_Tywxk7DOt-5ky6delIIU4KcFo'; //微信丁秀琳 open_id

            $next_user = '丁秀琳';//发送给的用户名字，与$touser相对应

            $login_user = $_SESSION['Login_Name'];  //当前登录用户

            $Log_Item = '工单领料确认';  //当前操作

            $login_time = date('Y-m-d H:i:s');//操作时间

            $time = explode(' ', $login_time);

            $time = $time[1];

            $login_detail = $login_user.'于今日'.$time.'完成'.$Log_Item.'流程。现需要您完成下一步"生产"工作，请及时登录研砼治筑运营平台进行操作。';//单个操作详情

            $remark = "\n 流程测试，如有疑问，请及时联系".$login_user."或ＩＴ部。";//备注

            $res = $weixin->send_login_temp_msg($touser, $login_user, $next_user, $Log_Item, $login_time, $login_detail, $remark);

            if ($res){
                $Log.="已通知 <span style='color: red'>$next_user</span> 进行下一步操作. <br>";
            }*/

        } 
        else{
	        echo "N"; 
	        $Log="<div class=redB>" . $Id . "领料单确认失败!</div><br>";
            $OperationResult="N";
        }
	break;
}

//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
?>