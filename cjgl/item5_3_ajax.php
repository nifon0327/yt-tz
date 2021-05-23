<?php
session_start();
$MyPDOEnabled = 1;
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="订单领料";			//需处理
$Log_Funtion="数据更新";
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

switch($ActionId){
    case 31://新增领料数据

    $allblSign = 1;
    $CG_mStockId = "";
    $Price_mStockId = "";
    $bl_sPOrderId = "";
    $tempCG_mStockId = array();
    $tempPrice_mStockId = array();
    $tempbl_sPOrderId = array();
    $ActionIdArray = array(102,103,104,105);
    $ArrId=explode("|",$Id);
    $ArrQty=explode("|",$Qty);
    $sLen=count($ArrId);
        //$fromPage='仓库备料';
    if (count($ArrQty)==$sLen && $sLen>0){
    	for ($i=0;$i<$sLen;$i++){
                //取得ID号
    		$tempArray  =  explode("@", $ArrId[$i]);

    		$POrderId   =  $tempArray[0];
    		$sPOrderId  =  $tempArray[1];
    		$StockId    =  $tempArray[2];
    		$mStockId   =  $tempArray[3];
    		$WorkShopId =  $tempArray[4];
    		$ActionId   =  $tempArray[5];
    		$StuffId    =  $tempArray[6];
    		$llQty      =  $ArrQty[$i];



    		$myResult=$myPDO->query("CALL proc_ck5_llsheet_save('$POrderId','$sPOrderId','$StockId','$StuffId','$llQty',$Operator,'$fromPage');");
                //echo "CALL proc_ck5_llsheet_save('$POrderId','$sPOrderId','$StockId','$StuffId','$llQty',$Operator,'$fromPage');";
    		$myRow = $myResult->fetch(PDO::FETCH_ASSOC);
    		$OperationResult = $myRow['OperationResult']!="Y"?$myRow['OperationResult']:$OperationResult;
    		$myResult=null;
    		$myRow=null;

    		$TypeIdResult=$myPDO->query("SELECT TypeId FROM stuffdata WHERE StuffId = '$StuffId'");
    		$TypeIdRow  = $TypeIdResult->fetch(PDO::FETCH_ASSOC);
    		$TypeId = $TypeIdRow["TypeId"];
    		$TypeIdResult = null;
    		$TypeIdRow = null;

    		// if ($TypeId == 9006 ) {
    		// 	$upSql = "UPDATE $DataIn.ck5_llsheet SET Estate=0,Receiver='$Operator',Received='$DateTime'
    		// 	WHERE sPOrderId='$sPOrderId' AND StockId='$StockId' ";
    		// 	$upResult = $myPDO->exec($upSql);

    		// 	if ($upResult) {
      //                   //echo "Y";
    		// 		$UpdateComboxSql = "UPDATE $DataIn.ck5_llsheet  L
    		// 		LEFT JOIN $DataIn.cg1_stuffcombox C ON C.StockId = L.StockId
    		// 		SET L.Estate = 0,L.Receiver='$Operator',L.Received='$DateTime'
    		// 		WHERE C.mStockId = '$StockId' AND L.sPOrderId = '$sPOrderId'";
    		// 		$UpdateComboxResult = $myPDO->exec($UpdateComboxSql);
    		// 	}
    		// 	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','工单领料确认','数据更新','领料单确认成功','Y','$Operator')";
    		// 	$IN_res= $myPDO->exec($IN_recode);
    		// }
                /*
                if ($Login_P_Number==10868){
	                $fp = fopen("item5_3.log", "a");
					fwrite($fp, date("Y-m-d H:i:s") .  "  info=". "CALL proc_ck5_llsheet_save('$POrderId','$sPOrderId','$StockId','$StuffId','$llQty',$Operator,'$fromPage')" . "\r\n");
					fclose($fp);
                }
                */
                // echo "CALL proc_ck5_llsheet_save('$POrderId','$sPOrderId','$StockId','$StuffId','$llQty',$Operator,'$fromPage');";
                $tempCG_mStockId[]    = $mStockId;
                $tempPrice_mStockId[] = $mStockId;
                $tempbl_sPOrderId[]   = $sPOrderId;
            }

            //去掉重复值
            $tempCG_mStockId    = array_unique($tempCG_mStockId);
            $tempPrice_mStockId = array_unique($tempPrice_mStockId);
            $tempbl_sPOrderId   = array_unique($tempbl_sPOrderId);

            $CG_mStockId        = implode(",", $tempCG_mStockId);
            $Price_mStockId     = implode(",", $tempPrice_mStockId);
            $bl_sPOrderId       = implode(",", $tempbl_sPOrderId);

            $cgCount = count($tempCG_mStockId);
            if($cgCount>0 && in_array($ActionId, $ActionIdArray) && $OperationResult == "Y"){
                //内部加工单自动下采购单,外发发料必须两个料都发完再下采购单
            	$bl_mStockId ="";
            	if($fromPage =="3") {
            		$blSql = "SELECT SC.POrderId,SC.sPOrderId,SC.mStockId,SC.Qty,(CG.addQty+CG.FactualQty) AS xdQty
            		FROM $DataIn.yw1_scsheet SC 
            		LEFT  JOIN $DataIn.cg1_stocksheet    CG ON CG.StockId = SC.mStockId
            		WHERE SC.sPOrderId IN ($bl_sPOrderId) ";

            		$blResult = $myPDO->query($blSql);
            		while($blRow = $blResult->fetch(PDO::FETCH_ASSOC)){
            			$blPOrderId = $blRow["POrderId"];
            			$blsPOrderId = $blRow["sPOrderId"];
            			$blmStockId = $blRow["mStockId"];
            			$blQty = $blRow["Qty"];
            			$blxdQty = $blRow["xdQty"];
            			$blRelation=$blQty/$blxdQty;


            			$checkOrderQtyResult = $myPDO->query("SELECT SUM(ROUND(A.OrderQty*$blRelation,U.Decimals)) AS OrderQty
            				FROM  $DataIn.cg1_semifinished   A 
            				INNER JOIN $DataIn.cg1_stocksheet G  ON G.StockId = A.StockId
            				LEFT JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
            				LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
            				WHERE  A.POrderId='$blPOrderId' AND A.mStockId='$blmStockId' AND G.blSign=1");

            			$checkOrderQtyRow  = $checkOrderQtyResult->fetch(PDO::FETCH_ASSOC);
            			$blOrderQty = $checkOrderQtyRow["OrderQty"];
            			$checkOrderQtyResult = null;
            			$checkOrderQtyRow = null;

            			$checkllResult=$myPDO->query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet 
            				WHERE sPOrderId = '$blsPOrderId'");
            			$checkllRow  = $checkllResult->fetch(PDO::FETCH_ASSOC);
            			$llQty = $checkllRow["llQty"];
            			$checkllResult  = null;
            			$checkllRow = null;

            			if($blOrderQty==$llQty){
            				$bl_mStockId.= $bl_mStockId==""?$blmStockId:",".$blmStockId;
            			}
            		}
            		$blResult = null;
            		$blRow = null;
                    //料齐才能够自动下采购单
            		if($bl_mStockId=="")$allblSign=0;
            		else{
            			$CG_mStockId = $bl_mStockId;
            		}
            	}

            	if($allblSign ==1)include "item5_3_auto_cg.php";

            }

            $priceCount = count($tempPrice_mStockId);
            if($priceCount>0 && in_array($ActionId, $ActionIdArray) && $OperationResult == "Y"){
                //自动更新半成品价格
            	include "item5_3_updateprice.php";
            }

            // include_once "../weixin/weixin_api.php";

            // $weixin = new weixin_api();

            // $touser = 'op_Tyw_8h2hzceNzjvmkDMICU60s'; //微信徐琴 open_id

            // $next_user = '徐琴';//发送给的用户名字，与$touser相对应

            // $login_user = $_SESSION['Login_Name'];  //当前登录用户

            // $Log_Item = '生产派单';  //当前操作

            // $login_time = date('Y-m-d H:i:s');//操作时间

            // $time = explode(' ', $login_time);

            // $time = $time[1];

            // $login_detail = $login_user.'于今日'.$time.'完成'.$Log_Item.'流程。现需要您完成下一步"工单领料确认"工作，请及时登录研砼治筑运营平台进行操作。';//登录详情

            // $remark = "\n 流程测试，如有疑问，请及时联系".$login_user."或ＩＴ部。";//备注

            // $res = $weixin->send_login_temp_msg($touser, $login_user, $next_user, $Log_Item, $login_time, $login_detail, $remark);


        }
        else{
        	$OperationResult="N";
        }
        break;
    }
    include "../basic/quit_pdo.php";

    echo $OperationResult;
    ?>