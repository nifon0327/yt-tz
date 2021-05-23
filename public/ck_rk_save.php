<?php  
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
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$chooseDate=substr($rkDate,0,7);
$ALType="CompanyId=$CompanyId";
$DateTemp = date("Ymd");

//保存主单资料
$inRecode="INSERT INTO $DataIn.ck1_rkmain (Id,BillNumber,CompanyId,Remark,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId','$Remark','0','$rkDate','$Operator')";
$inAction=@mysql_query($inRecode);
$Mid=mysql_insert_id();
//分割字符串
$valueArray=explode("|",$AddIds);
$Count=count($valueArray);
for($i=0;$i<$Count;$i++){
	$valueTemp=explode("!",$valueArray[$i]);
	$StockId=$valueTemp[0];
	$StuffId=$valueTemp[1];	
	$Qty=$valueTemp[2];	
	$Price=0.0;
	// 1 加入入库明细
	$addRecodes="INSERT INTO $DataIn.ck1_rksheet (`Mid`, `sPOrderId`, `StockId`, `StuffId`, `Price`, `Qty`, `llQty`, `llSign`, `gys_Id`, `Type`, `Locks`, `Estate`, `PLocks`, `creator`, `created`,  `Date`,`Operator`) VALUES ('$Mid','','$StockId','$StuffId','$Price','$Qty','0','1',NULL,'1','0','1','0','$Operator','$DateTime','$Date','$Operator')";
	$addAction=@mysql_query($addRecodes);
	if($addAction){
		$Log.="$StockId 入库成功(入库数量 $Qty).<br>";
		// 3 入库状态:有入库则2，最后才统一更新状态?
		$uprkSign="UPDATE $DataIn.cg1_stocksheet SET rkSign=(CASE 
    		WHEN 
			FactualQty+AddQty>(
							SELECT SUM( Qty ) AS Qty FROM $DataIn.ck1_rksheet 
							WHERE StockId = '$StockId'
						 ) THEN 2
    			ELSE 0 END) WHERE StockId='$StockId'";
		$upRkAction=mysql_query($uprkSign);	
		if($upRkAction){
			$Log.="&nbsp;&nbsp;&nbsp;&nbsp;需求单 $StockId 的入库标记更新成功.<br>";
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;&nbsp;&nbsp;需求单 $StockId 的入库标记更新失败. $uprkSign </div><br>";
			}
		}
	else{
		$Log.="<div class='redB'>$StockId 入库失败. $addRecodes </div><br>";
		$OperationResult="N";
		}
	}


$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
