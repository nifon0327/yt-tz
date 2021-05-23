<?php   
session_start();
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//生成送货单
$Log_Item="半成品送货单";			//需处理
$Log_Funtion="保存";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$DateTemp = date("Ymd");
$Tempyear= date("Y");
$ArrId=explode("|",$Ids);
$ArrQty=explode("|",$Qty);
$sLen=count($ArrId); 
$saveSign = 0 ;
if (count($ArrQty)==$sLen && $sLen>0){
	for ($i=0;$i<$sLen;$i++){
        $tempArray  =  explode("@", $ArrId[$i]);
		$sPOrderId  =  $tempArray[0];
		$StockId    =  $tempArray[1];
		$shQty      =  $ArrQty[$i];
		if($shQty>0 && $StockId>0 && $sPOrderId>0){
	         $checkResult = mysql_fetch_array(mysql_query("
	                     SELECT D.SendFloor,D.StuffId,S.CompanyId 
	                     FROM $DataIn.cg1_stocksheet S
           	             LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
           	             WHERE S.StockId = $StockId",$link_id));
		    $myCompanyId = $checkResult["CompanyId"];
		    $floor = $checkResult["SendFloor"];
		    $StuffId = $checkResult["StuffId"];
		    
			$maxBillResult = mysql_fetch_array(mysql_query("SELECT MAX(BillNumber) AS BillNumber FROM $DataIn.gys_shmain WHERE BillNumber  LIKE '$DateTemp%'",$link_id));
			$TempBillNumber=$maxBillResult["BillNumber"];
			if($TempBillNumber){
			     
				$TempBillNumber=$TempBillNumber+1;
				}
			else{
				$TempBillNumber=$DateTemp."0001";//默认
			}
			
			$maxGysResult = mysql_fetch_array(mysql_query("SELECT MAX(GysNumber) AS GysNumber FROM $DataIn.gys_shmain WHERE GysNumber  LIKE '$Tempyear%' AND CompanyId = '$myCompanyId'",$link_id));
			$tempGysNumber=$maxGysResult["GysNumber"];
			if($tempGysNumber){
			     
				$tempGysNumber=$tempGysNumber+1;
				}
			else{
				$tempGysNumber=$Tempyear."00001";//默认
			  }
			
		    if($Mid==0){//如果没生成主送货单就先生成主送货单
		      $inRecode="INSERT INTO $DataIn.gys_shmain 
		      (Id,BillNumber,GysNumber,CompanyId,Locks,Date,Remark,Floor,Operator,creator,created) 
		      VALUES (NULL,'$TempBillNumber','$tempGysNumber','$myCompanyId','1','$DateTime','半成品入库','$floor','$Operator','$Operator',NOW())";
		      $inAction=@mysql_query($inRecode);
		      $Mid=mysql_insert_id();
	       } 

		   if($Mid>0){
			    $addRecodes="INSERT INTO $DataIn.gys_shsheet (Id,Mid,sPOrderId,StockId,StuffId,Qty,SendSign,Estate,Locks,Operator,creator,created) 
			    VALUES (NULL,'$Mid','$sPOrderId','$StockId','$StuffId','$shQty','0','2','1','$Operator','$Operator',NOW())"; 
			    $addAction=@mysql_query($addRecodes);
			    if($addAction){
				    $saveSign = 1;
				    $updatesql="update $DataIn.yw1_scsheet set Estate = '0' where sPOrderId='$sPOrderId' ";
				    @mysql_query($updatesql);
			    }
			}		
		}
	}
}
if($saveSign == 1){

   echo "Y";

    include_once "../weixin/weixin_api.php";

    $weixin = new weixin_api();

    $touser = 'op_Tyw5JIez2pejo5NMdLv1m76gc'; //微信张丽香 open_id

    $next_user = '张丽香';//发送给的用户名字，与$touser相对应

    $login_user = $_SESSION['Login_Name'];  //当前登录用户

    $Log_Item = '半成品质检申请';  //当前操作

    $login_time = date('Y-m-d H:i:s');//操作时间

    $time = explode(' ', $login_time);

    $time = $time[1];

    $login_detail = $login_user.'于今日'.$time.'完成'.$Log_Item.'流程。现需要您完成下一步"质检审核"工作，请及时登录研砼治筑运营平台进行操作。';//登录详情

    $remark = "\n 流程测试，如有疑问，请及时联系".$login_user."或ＩＴ部。";//备注

    $res = $weixin->send_login_temp_msg($touser, $login_user, $next_user, $Log_Item, $login_time, $login_detail, $remark);

    if ($res){
        $Log.="已通知 <span style='color: red'>$next_user</span> 进行下一步操作. <br>";
    }

}else{
	$delSql = "DELETE FROM $DataIn.gys_shmain WHERE Id='$Mid' LIMIT 1"; 
	$delRresult = mysql_query($delSql);
    echo "N";
}

?>