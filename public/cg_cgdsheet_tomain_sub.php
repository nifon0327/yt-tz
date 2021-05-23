<?php
//调用生成采购单 传入参数：$Ids ,$Operator,$Remark
/* web端、iphone端共用*/

chdir(dirname(__FILE__));//切换至此目录

$Date=date("Y-m-d");
$DateTemp=date("Y");

$checkSql=mysql_query("SELECT S.StockId FROM $DataIn.cg1_stocksheet S WHERE S.Id IN ($Ids) AND EXISTS(
  SELECT G.StockId FROM cg1_semifinished G WHERE G.mStockId=S.StockId)",$link_id);
$semiSign=mysql_num_rows($checkSql);

//echo $semiSign;
if($semiSign>0 && $Login_P_Number!=10341 && $Login_P_Number!=10868 && $Login_P_Number!=10871){// && $Login_P_Number!=10868
  $Log=$Log."<div class=redB>$TitleSTR 失败! 半成品不能直接下采购单！</div><br>";
  $OperationResult="N";
  $fromWebPage="cg_cgdsheet_read";
}
else{
		if (($CompanyId=="") or ($Number=="")) {
		  $GetCidRow=mysql_fetch_array(mysql_query("SELECT CompanyId,BuyerId FROM $DataIn.cg1_stocksheet WHERE Id IN ($Ids) LIMIT 1",$link_id));
		  $CompanyId=$GetCidRow["CompanyId"];
		  $Number=$GetCidRow["BuyerId"];
		}

		$Bill_Temp=mysql_query("SELECT MAX(PurchaseID) AS maxID FROM $DataIn.cg1_stockmain WHERE PurchaseID LIKE '$DateTemp%'",$link_id);
		$PurchaseID =mysql_result($Bill_Temp,0,"maxID");
		if ($PurchaseID ){
		  $PurchaseID =$PurchaseID+1; //四位时到9999就变成下一年了。
		   $thisyear= substr($PurchaseID,0,4);
		   $thisarray= substr($PurchaseID,4);
		  if($thisyear>$DateTemp){
		           $PurchaseID =$DateTemp."1".$thisarray;
		          }
		}
		else{
			$PurchaseID =$DateTemp."0001";
		}
		//保存主采购单资料
    if ($TypeId == 8001) {
        $TypeId = 'PCB';
    }elseif($TypeId == 8010){
        $TypeId = 'PCLT';

    }elseif($TypeId == 8011){
        $TypeId = 'PCQ';
    }
		$insertFlag=0;
		$Remark=$Remark==""?"":$Remark;
		$inRecode="INSERT INTO $DataIn.cg1_stockmain (Id,CompanyId,BuyerId,PurchaseID,DeliveryDate,Remark,Date,Operator,purchaseOrderNo) VALUES (NULL,'$CompanyId','$Number','$PurchaseID','0000-00-00','$Remark','$Date','$Operator','$OrderPO-$TypeId')";
		$inAction=@mysql_query($inRecode);
		$Mid=mysql_insert_id();
		if($inAction && $Mid>0){
			$Log="$TitleSTR 成功!<br>";
			$Sql = "UPDATE $DataIn.cg1_stocksheet SET Mid='$Mid',Locks=0 WHERE Id IN ($Ids) AND Estate='0' and CompanyId='$CompanyId'";
			$Result = mysql_query($Sql);
			if($Result && mysql_affected_rows()>0){
				$Log.="需求单明细 ($Ids) 加入主采购单 $Mid 成功!<br>";
		        $insertFlag=1;
		        $OperationResult="Y";

		        if($semiSign>0){
			         $upSql="UPDATE cg1_stocksheet G 
							INNER JOIN yw1_scsheet S ON S.mStockId=G.StockId 
							INNER JOIN cg1_stocksheet A ON A.StockId=S.StockId
							SET G.Price=A.Price 
							WHERE G.Id IN($Ids) AND A.Id>0";
				    $upResult = mysql_query($upSql);
		        }
		        //备份初始采购单
		        echo"<script language='javascript'>retCode1=openUrl('cg_cgdmain_tohtml.php?Id=$Mid');</script>";
		        //生成PDF采购单
		        $fromWebPageSign = 1;
		        $Id = $Mid;
		        include "PurchaseToPDF.php";

                include_once "../weixin/weixin_api.php";

                $weixin = new weixin_api();

                $touser = 'op_Tyw_8h2hzceNzjvmkDMICU60s'; //微信徐琴 open_id

                $next_user = '徐琴';//发送给的用户名字，与$touser相对应

                $login_user = $_SESSION['Login_Name'];  //当前登录用户


                $login_time = date('Y-m-d H:i:s');//操作时间

                $time = explode(' ', $login_time);

                $time = $time[1];

                $login_detail = $login_user.'于今日'.$time.'完成'.$Log_Item.'流程。现需要您完成下一步"送货单审核"工作，请及时登录研砼治筑运营平台进行操作。';//登录详情

                $remark = "\n 流程测试，如有疑问，请及时联系".$login_user."或ＩＴ部。";//备注

                $res = $weixin->send_login_temp_msg($touser, $login_user, $next_user, $Log_Item, $login_time, $login_detail, $remark);

                if ($res){
                    $Log.="<br>已通知 <span style='color: red'>$next_user</span> 进行下一步操作. <br>";
                }

			}
			else{
				$Log.="<div class=redB>需求单明细 ($Ids) 加入主采购单 $Mid 失败!(检查是否未审核)</div><br>";
				$OperationResult="N";$fromWebPage="cg_cgdsheet_read";
				//删除主单
				$DelSql="DELETE FROM $DataIn.cg1_stockmain WHERE Id='$Mid' LIMIT 1";
				$DelResult=mysql_query($DelSql);
				if($DelResult && mysql_affected_rows()>0){
					$Log.="主采购单 $Mid 已取消<br>";
					}
				else{
					$Log.="<div class=redB>主采购单 $Mid 未取消,请手动清除!( $DelSql )</div><br>";
					$OperationResult="N";

					}
				}
		    }
		else{
			$Log=$Log."<div class=redB>$TitleSTR 失败!  请选择 供应商 或 采购员 后再生成订单！ </div><br>";
			$OperationResult="N";
			$fromWebPage="cg_cgdsheet_read";
		}
}
?>
