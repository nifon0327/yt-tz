<?php   
//电信-zxq 2012-08-01
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="物料补仓";			//需处理
$Log_Funtion="数据更新";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
// ActionId=1 新增记录 ,2 修改记录,3删除记录
switch($ActionId){
	case 1:
//保存主单资料
$inRecode="INSERT INTO $DataIn.ck3_bcmain (Id,BillNumber,CompanyId,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId','0','$DateTime','$Operator')";
$inAction=@mysql_query($inRecode);
$Mid=mysql_insert_id();
if($Mid>0){
	$Lens=count($thQTY);
	for($i=0;$i<$Lens;$i++){
		$Id=$thQTY[$i];
		if($Id!=""){
			$StuffId=$thStuffId[$i];
			$Qty=$thQTY[$i];
			$Remark=$thRemark[$i];
			/////////////////////////////////////////////////////
			// 1 库存足够的情况下加入入库明细
			/*$addRecodes="INSERT INTO $DataIn.ck3_bcsheet (Id,Mid,StuffId,Qty,Remark,Locks) VALUES (NULL,'$Mid','$StuffId','$Qty','$Remark','0')";
			$addAction=@mysql_query($addRecodes);
			if($addAction){
				$Log.="$StuffId 补仓成功(补仓数量 $Qty).<br>";
				// 2 更新在库
				$upCk="UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty+$Qty WHERE StuffId='$StuffId' LIMIT 1";
				$upCkAction=mysql_query($upCk);		
				if($upCkAction){
					$Log.="&nbsp;&nbsp;配件 $StuffId 在库增加成功(数量 $Qty).<br>";
					}
				else{
					$Log.="<div class='redB'>&nbsp;&nbsp;配件 $StuffId 在库扣增加失败(数量 $Qty).</div><br>";
					$OperationResult="N";
					}
				}
			else{
				$Log.="<div class='redB'>$StuffId 补仓失败(补仓数量 $Qty). </div><br>";
				$OperationResult="N";
				}*/		
			/////////////////////////////////////////////////////
			}
		}
	}
else{
	$Log.="<div class='redB'>补仓操作失败.</div><br>";
	$OperationResult="N";
	}
   $alertLog=$Log_Item . "数据保存成功";$alertErrLog=$Log_Item . "数据保存失败";
  break;
  
  case 2:
  		$Log_Funtion="补仓数据更新";
		$upDataSheet="$DataIn.ck3_bcsheet";	
		$thSTR="";
		/*if($Operators<0){	//减少补仓数量的条件 在库>=增加的数量
			$thSTR=" and K.tStockQty>=$changeQty";
			}
		
		$upSql = "UPDATE $upDataSheet T LEFT JOIN $DataIn.ck9_stocksheet K ON T.StuffId=K.StuffId SET T.Qty=T.Qty+$changeQty*$Operators,K.tStockQty=K.tStockQty+$changeQty*$Operators WHERE T.Id=$Id $thSTR";
		$upResult = mysql_query($upSql);		
		if($upResult && mysql_affected_rows()>0){
			$Log="补仓数据更新成功.<br>";
			}
		else{
			$Log="<div class='redB'>补仓数据更新失败!</div><br>";
			$OperationResult="N";
			}*/
		$alertLog=$Log_Item . "数据更新成功";$alertErrLog=$Log_Item . "数据更新失败";
		break;	
/////////////////////////////////////////////////////////////////////////////////////////////
  case 3:
    $upSql="UPDATE $DataIn.ck3_bcsheet S 
		LEFT JOIN $DataIn.ck9_stocksheet K ON S.StuffId=K.StuffId 
		SET K.tStockQty=K.tStockQty-S.Qty,S.StuffId='0' WHERE S.Id=$Id AND K.tStockQty>=S.Qty";
$upResult = mysql_query($upSql);		
if($upResult && mysql_affected_rows()>0){
	//删除此补仓记录
	$delSql = "DELETE FROM $DataIn.ck3_bcsheet WHERE Id=$Id"; 
	$delRresult = mysql_query($delSql);
	//删除全部没有明细的主单
	$delMainSql = "DELETE $DataIn.ck3_bcmain M FROM $DataIn.ck3_bcmain M 
		LEFT JOIN $DataIn.ck3_bcsheet S ON M.Id=S.Mid
		WHERE S.Id IS NULL"; 
	$delMianRresult = mysql_query($delMainSql);
	$Log.="&nbsp;&nbsp;记录 $Ids 补仓资料删除操作成功!<br>";
	}
else{//不能删除，原因有补仓记录
	$Log.="<div class='redB'>&nbsp;&nbsp;记录 $Ids 补仓资料删除操作失败!</div><br>";
	$OperationResult="N";
	}

	   
	   $alertLog=$Log_Item . "数据已成功删除";$alertErrLog=$Log_Item . "数据删除失败";
	break;
	
  case 20:
  		$Log_Funtion="主补仓单更新";
		$upSql = "UPDATE $DataIn.ck3_bcmain SET Date='$bcDate',BillNumber='$BillNumber' WHERE Id='$Mid'";
		$upResult = mysql_query($upSql);		
		if($upResult && mysql_affected_rows()>0){
			$Log="补仓主单资料更新成功.<br>";
			}
		else{
			$Log="<div class='redB'>补仓主单资料更新失败!</div><br>";
			$OperationResult="N";
			}
		$alertLog=$Log_Item . "数据更新成功";$alertErrLog=$Log_Item . "数据更新失败";	
		break;
}

//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

if ($OperationResult=="N"){
       echo "<SCRIPT LANGUAGE=JavaScript>alert('$alertErrLog');</script>";
      }
   else{
	  echo "<SCRIPT LANGUAGE=JavaScript>alert('$alertLog');parent.closeWinDialog();parent.ResetPage(1,5);</script>";
   }

?>��主单资料更新失败!</div><br>";
			$OperationResult="N";
			}
		$alertLog=$Log_Item . "数据更新成功";$alertErrLog=$Log_Item . "数据更新失败";	
		break;
}

//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

if ($OperationResult=="N"){
       echo "<SCRIPT LANGUAGE=JavaScript>alert('$alertErrLog');</script>";
      }
   else{
	  echo "<SCRIPT LANGUAGE=JavaScript>alert('$alertLog');parent.closeWinDialog();parent.ResetPage(1,5);</script>";
   }

?>