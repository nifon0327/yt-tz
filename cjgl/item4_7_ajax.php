<?php   
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$OperationResult = "Y";
$Log_Funtion="生产入库确认";
$Operator=$Login_P_Number;

switch ($level){
	case "1":
		$UpdateSql="UPDATE $DataIn.sc1_cjtj SET Estate='0'  
		WHERE POrderId=$POrderId AND StockId = $StockId AND Estate =1 AND DATE_FORMAT(Date,'%Y-%m-%d') = '$chooseDate'";
		$UpdateResult=@mysql_query($UpdateSql);
		if($UpdateResult && mysql_affected_rows()>0){
		    $UpdateSeatId=mysql_query("update $DataIn.yw1_ordersheet set SeatId='$SeatId' WHERE POrderId='$POrderId' AND Estate >0");
		    $OperationResult="Y";

            include_once "../weixin/weixin_api.php";

            $weixin = new weixin_api();

            $touser = 'op_Tyw_8h2hzceNzjvmkDMICU60s'; //微信出货 open_id

            $next_user = '出货';//发送给的用户名字，与$touser相对应

            $login_user = $_SESSION['Login_Name'];  //当前登录用户

            $Log_Item = "出货";  //当前操作

            $login_time = date('Y-m-d H:i:s');//操作时间

            $time = explode(' ', $login_time);

            $time = $time[1];

            $login_detail = $login_user.'于今日'.$time.'完成'.$Log_Item.'流程。现需要您完成下一步"出货"工作，请及时登录研砼治筑运营平台进行操作。';//登录详情

            $remark = "\n 流程测试，如有疑问，请及时联系".$login_user."或ＩＴ部。";//备注

            $res = $weixin->send_login_temp_msg($touser, $login_user, $next_user, $Log_Item, $login_time, $login_detail, $remark);


        }
		else{
		    $OperationResult="N";
		}
	break;
	 
	case "2": //半成品入库确认，不需插入入库数据
		$UpdateSql="UPDATE $DataIn.sc1_cjtj SET Estate='0'  
		WHERE POrderId=$POrderId AND StockId = $StockId AND Estate =1 AND DATE_FORMAT(Date,'%Y-%m-%d') = '$chooseDate'";
		$UpdateResult=@mysql_query($UpdateSql);
		if($UpdateResult && mysql_affected_rows()>0){
		     $OperationResult="Y";

            include_once "../weixin/weixin_api.php";

            $weixin = new weixin_api();

            $touser = 'op_Tyw_8h2hzceNzjvmkDMICU60s'; //微信出货 open_id

            $next_user = '出货';//发送给的用户名字，与$touser相对应

            $login_user = $_SESSION['Login_Name'];  //当前登录用户

            $Log_Item = "出货";  //当前操作

            $login_time = date('Y-m-d H:i:s');//操作时间

            $time = explode(' ', $login_time);

            $time = $time[1];

            $login_detail = $login_user.'于今日'.$time.'完成'.$Log_Item.'流程半成品。现需要您完成下一步"出货"工作，请及时登录研砼治筑运营平台进行操作。';//登录详情

            $remark = "\n 流程测试，如有疑问，请及时联系".$login_user."或ＩＴ部。";//备注

            $res = $weixin->send_login_temp_msg($touser, $login_user, $next_user, $Log_Item, $login_time, $login_detail, $remark);



        }
		 else{
		     $OperationResult="N";
		}
	break;
}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
echo $OperationResult;
 ?>