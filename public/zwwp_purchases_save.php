<?php 
//ewen 2012-12-16
include "../model/modelhead.php";
//步骤2：
$Log_Item="采购物品登记";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
if ($newCheck){//新增物品名称
	$addRecode="INSERT INTO $DataPublic.zwwp3_data (Id,GoodsName,TypeId,Attached,CompanyId,Unit,Date,Estate,Locks,Operator) VALUES (NULL,'$newGoodsName','$TypeId','0','0','','$Date','2','0','$Operator')";
  	$addAction=@mysql_query($addRecode);  
  	$GoodsId=mysql_insert_id();
	if ($GoodsId>0){
    	$Log.="新增物品名称成功!<br>";
		if($Attached!=""){//有上传文件
			$FileType=".jpg";
			$OldFile=$Attached;
			$FilePath="../download/zwwp/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
				}
			$PreFileName="Z".$GoodsId.$FileType;
			$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
			if($Attached){
				$Log.="&nbsp;&nbsp;总务采购物品图片上传成功！<br>";
				$sql = "UPDATE $DataPublic.zwwp3_data SET Attached='1' WHERE Id='$GoodsId'";
				$result = mysql_query($sql);
				}
			else{
				$Log.="<div class=redB>&nbsp;&nbsp;总务采购物品图片失败！$inRecode </div><br>";
				$OperationResult="N";			
				}
			}
		}	
      }
	else{
    	$Log.="<div class=redB>新增物品名称失败! $addRecode </div><br>";
  		}
	
if ($GoodsId>0){
	$inRecode="INSERT INTO $DataIn.zwwp4_purchase SELECT NULL,'$GoodsId','$Qty','$Remark','1',Number,'1','$Date','$Operator','0','$Operator','DateTime',null,null FROM $DataPublic.staffmain WHERE Name='$BuyerId' ";
	$inAction=@mysql_query($inRecode);
	if ($inAction){ 
		$Log.="$TitleSTR 成功!<br>";
		} 
	else{
		$Log.="<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
		$OperationResult="N";
		} 
	}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
