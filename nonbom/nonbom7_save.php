<?php  
//ewen 2013-03-04 OK
include "../model/modelhead.php";
//步骤2：
$Log_Item="入库资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$rkDate=$rkDate==""?$DateTime:$rkDate;
//保存主单资料
$inRecode="INSERT INTO $DataIn.nonbom7_inmain (Id,CompanyId,BuyerId,BillNumber,Bill,Remark,Locks,Date,Operator) VALUES (NULL,'$CompanyId','$BuyerId','$BillNumber','0','$Remark','0','$rkDate','$Operator')";

$inAction=@mysql_query($inRecode);
$Mid=mysql_insert_id();
//分割字符串
$valueArray=explode("|",$AddIds);
$Count=count($valueArray); $rkGoodIds="";
if ($Mid>0) {
	for($i=0;$i<$Count;$i++){
		$valueTemp=explode("!",$valueArray[$i]);
		$cgId=$valueTemp[0];
		$GoodsId=$valueTemp[1];	
		$Qty=$valueTemp[2];	
		// 1 加入入库明细
		$addRecodes="INSERT INTO $DataIn.nonbom7_insheet (Id,Mid,GoodsId,cgId,Qty,Remark,Locks) VALUES (NULL,'$Mid','$GoodsId','$cgId','$Qty','','0')";
		$addAction=@mysql_query($addRecodes);
		if($addAction){
             $rkId=mysql_insert_id();
			 $Log.="$cgId 入库成功(入库数量 $Qty).<br>";
			 // 2 更新在库
			 $upCk="UPDATE $DataPublic.nonbom5_goodsstock SET wStockQty=wStockQty+$Qty WHERE GoodsId='$GoodsId' LIMIT 1";
			$upCkAction=mysql_query($upCk);		
			if($upCkAction){
				  $Log.="&nbsp;&nbsp;配件 $GoodsId 在库入库成功(入库数量 $Qty).<br>";
				   $rkGoodIds.=$rkGoodIds==""?$GoodsId:",$GoodsId";
				}
			else{
				  $Log.="<div class='redB'>&nbsp;&nbsp;配件 $GoodsId 在库入库失败(入库数量 $Qty). $upCk </div><br>";
				  $OperationResult="N";
				}
			  // 3 入库状态:有入库则2，最后才统一更新状态?
			 $uprkSign="UPDATE $DataIn.nonbom6_cgsheet SET rkSign=(CASE 
				WHEN Qty>(
								SELECT SUM( Qty ) AS Qty FROM $DataIn.nonbom7_insheet WHERE cgId = '$cgId'
							 ) THEN 2
					ELSE 0 END) WHERE Id='$cgId'";
			    $upRkAction=mysql_query($uprkSign);	
			   if($upRkAction){
					 $Log.="&nbsp;&nbsp;&nbsp;&nbsp;需求单 $cgId 的入库标记更新成功.<br>";
					
				   }
			  else{
					 $Log.="<div class='redB'>&nbsp;&nbsp;&nbsp;&nbsp;需求单 $cgId 的入库标记更新失败. $uprkSign </div><br>";
					 $OperationResult="N";
				    }
			}
		else{
			$Log.="<div class='redB'>$cgId 入库失败. $addRecodes </div><br>";
			$OperationResult="N";
			}
	}
	
	if ($rkGoodIds!=""){
		  include "nonbom7_fixedsave.php";
	}

	if($Bill!=""){//有上传文件
		$FileType=".pdf";
		$OldFile=$Bill;
		$FilePath="../download/nonbom_rk/";
		if(!file_exists($FilePath)){//检查目录是否存在，不存在则先创建
			makedir($FilePath);
			}
		$PreFileName=$Mid.$FileType;
		$BillUP=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($BillUP){
			$updateSQL = "UPDATE $DataIn.nonbom7_inmain SET Bill='1' WHERE Id='$Mid'";
			$updateResult = mysql_query($updateSQL);
			if ($updateResult && mysql_affected_rows()>0){
				$Log.="入库凭证已更新.<br>";
				}
			else{
				$Log.="<div class=redB>入库凭证生成失败！</div><br>";
				$OperationResult="N";			
				}
			}
		}//end if($Bill!="")
}
else {
	$Log.="<div class=redB>主单入库入库凭证失败！ $inRecode </div><br>";
}
include "../model/logpage.php";
?>
