<?php 
//EWEN 2013-03-01 OK
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非bom配件申领记录";		//需处理
$upDataSheet="$DataIn.nonbom8_outsheet";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	 case 17://审核通过
		$Log_Funtion="审核通过";
		$SetStr="Estate=1";
		include "../model/subprogram/updated_model_3d.php";
		$fromWebPage=$funFrom."_m";
		break;
	case 34:
		$Log_Funtion="审核退回";
		$SetStr="Estate=3,Locks=1,ReturnReasons='$ReturnReasons'";
		include "../model/subprogram/updated_model_3d.php";
		$fromWebPage=$funFrom."_m";
		break;
	case 130://ewen 2013-03-27 更新：发放时，须要有足够的实物库存
		   $Log_Funtion="物品发放";
            $tempArray=explode("|",$SIdList);
            $tempCount=count($tempArray);
           for($k=0;$k<$tempCount;$k++){
                      $dataArray=explode("@", $tempArray[$k]);  
                      $BarCode=$dataArray[0];
                      $LyMan=$dataArray[1];
                      $Remark=$dataArray[2];
                     $IN_recode="INSERT INTO $DataIn.nonbom8_outfixed(Id,OutId,GoodsId,BarCode,Estate,Date,LyMan,Remark)
                     VALUES(Null,'$Ids','$GoodsId','$BarCode','1','$DateTime','$LyMan','$Remark')";
                      $IN_res=@mysql_query($IN_recode);
                      if($IN_res && mysql_affected_rows()>0){
                               $Log.="&nbsp;资产编号为 $BarCode 的固定资产发放给 $LyMan 成功!<br>";
                               $UpdateSql="UPDATE  $DataIn.nonbom7_code SET Estate=2,Number='$LyMan' WHERE BarCode=$BarCode";   
                               $UpdateResult=@mysql_query($UpdateSql);
                              }
                        else{
                              $Log.="<div class='redB'>&nbsp;资产编号为 $BarCode 的固定资产发放给 $LyMan 失败!<br></div>";
                              }
                  }
			if($Ids!=""){
				$updateSQL = "UPDATE $upDataSheet A
				LEFT JOIN $DataPublic.nonbom5_goodsstock B ON A.GoodsId=B.GoodsId
				SET A.Estate=0,A.OutOperator='$Operator',A.OutDate='$DateTime',
				B.wStockQty=B.wStockQty-A.Qty,B.oStockQty=B.oStockQty-A.Qty,B.lStockQty=B.lStockQty+A.Qty
				WHERE A.Id IN ($Ids) AND A.Estate='1' AND B.wStockQty>=A.Qty AND B.oStockQty>=A.Qty";
				$updateResult = mysql_query($updateSQL);
				if ($updateResult && mysql_affected_rows()>0){
					$Log.=$Log_Item.$Log_Funtion."成功.<br>";
					}
				else{
					$Log.="<div class='redB'>".$Log_Item.$Log_Funtion."失败,库存可能不足,请检查.</div><br>$updateSQL <br>";
					$OperationResult="N";
					}
				}
		break;
	default://ewen 2013-03-27 更新：不限制数量的变化
		$updateSQL = "UPDATE $upDataSheet A SET A.Qty='$Qty',A.WorkAdd='$WorkAdd',A.Remark='$Remark',A.Date='$slDate',A.Operator='$Operator',A.Estate='2',A.Locks='0' WHERE A.Id='$Id' AND A.Locks='1'";
		$updateResult = mysql_query($updateSQL);
		if ($updateResult && mysql_affected_rows()>0){
			$Log=$Log_Item.$Log_Funtion."成功.<br>";
			}
		else{
			$Log="<div class='redB'>".$Log_Item.$Log_Funtion."失败. $updateSQL</div><br>";
			$OperationResult="N";
			}
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&OperatorSign=$OperatorSign&Estate=$Estate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>