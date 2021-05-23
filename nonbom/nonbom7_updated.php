<?php 
//ewen 2013-03-04 OK
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非bom配件入库记录";		//需处理
$upDataSheet="$DataIn.nonbom7_insheet";	//需处理
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
	case 20://?
		$Log_Funtion="主入库单更新";
		$Remark=FormatSTR($Remark);
		//凭证更新
		$FilePath="../download/nonbom_rk/";
		if(!file_exists($FilePath)){//检查目录是否存在，不存在则先创建
			makedir($FilePath);
			}
		$PreFileName1=$Mid.".pdf";
		if($Bill!=""){
			$OldFile1=$Bill;
			$uploadInfo1=UploadFiles($OldFile1,$PreFileName1,$FilePath);
			$BillSTR=$uploadInfo1==""?",Bill='0'":",Bill='1'";
			}
		if($BillSTR=="" && $oldBill!=""){//没有上传文件并且已选取删除原文件
			$FilePath1=$FilePath."/$PreFileName1";
			if(file_exists($FilePath1)){
				unlink($FilePath1);
				}
			$BillSTR=",Bill='0'";
			}
		$upSql = "UPDATE $DataIn.nonbom7_inmain SET BillNumber='$BillNumber',Date='$Date',Remark='$Remark' $BillSTR WHERE Id='$Mid'";
		$upResult = mysql_query($upSql);		
		if($upResult && mysql_affected_rows()>0){
			$Log="入库主单资料更新成功.<br>";
			}
		else{
			$Log="<div class='redB'>入库主单资料更新失败! $upSql </div><br>";
			$OperationResult="N";
			}
		break;
	default:
      if($changeQty>0){
				$rkSTR="";
				if($Operators<0){	//减少入库数量的条件 在库>=减少的数量
					$rkSTR=" AND B.wStockQty>=$changeQty";
					}
					$upSql = "UPDATE $upDataSheet A LEFT JOIN $DataPublic.nonbom5_goodsstock B ON B.GoodsId=A.GoodsId SET A.Qty=A.Qty+$changeQty*$Operators,B.wStockQty=B.wStockQty+$changeQty*$Operators WHERE A.Id=$Id $rkSTR";
					$upResult = mysql_query($upSql);		
					if($upResult && mysql_affected_rows()>0){
						  $Log="非bom配件入库记录更新成功.<br>";
						 }
					else{
						  $Log="<div class='redB'>非bom配件入库记录更新失败 $upSql !</div><br>";
						  $OperationResult="N";
						}
				  //更新需求单的入库状态:2部分入库，1未入库，0已全部入库
				   $uprkSign="UPDATE $DataIn.nonbom6_cgsheet SET rkSign=(CASE 
					WHEN (SELECT IFNULL(SUM(Qty),0) AS Qty FROM $upDataSheet WHERE cgId = '$cgId')>0 THEN 2
					ELSE 1 END) WHERE Id='$cgId'";
		 		  $upRkAction=mysql_query($uprkSign);
     		 }
              $TypeSign=1;
              include "nonbom7_fixedupdated.php";
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseDate=$chooseDate&CompanyId=$CompanyId";
//$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
//$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
  