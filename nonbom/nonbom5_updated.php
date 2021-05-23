<?php 
//EWEN 2013-02-25 OK
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非bom配件申购单";		//需处理
$upDataSheet="$DataIn.nonbom6_cgsheet";	//需处理
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
    case 17:
		$Log_Funtion="审核通过";
		$SetStr="Estate=1";//取消终审
		include "../model/subprogram/updated_model_3d.php";
		$fromWebPage=$funFrom."_m";
		break;
	case 34:
		$Log_Funtion="审核退回";
		$SetStr="Estate=4,ReturnReasons='$ReturnReasons'";
		include "../model/subprogram/updated_model_3d.php";
		$fromWebPage=$funFrom."_m";
		break;
	case 52:
		$Log_Funtion="申购";
		$SetStr="Estate=2";
		include "../model/subprogram/updated_model_3d.php";
		break;
/*	case 128:
		$Log_Funtion="终审通过";
		$SetStr="Estate=1";
		//include "../model/subprogram/updated_model_3d.php";
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}		
		$fromWebPage=$funFrom."_m";
		$SQL_Temp=mysql_query("SELECT mainType,count(*) as cs  FROM $DataIn.nonbom6_cgsheet WHERE  Id IN ($Ids) group by mainType,CompanyId ",$link_id); 
		$mainType =mysql_result($SQL_Temp,0,"mainType");	
		$cs =mysql_result($SQL_Temp,0,"cs");
		
		if($Lens==$cs) { //说明是同一类的,同一家公司的

			$sql = "UPDATE $upDataSheet SET $SetStr WHERE Id IN ($Ids) $EstateStr";
			//echo $sql;
			$result = mysql_query($sql);
			if($result){
				$Log="ID号在 $Ids 的记录成功 $Log_Funtion. </br>";
				}
			else{
				$Log="ID号为 $Ids 的记录$Log_Funtion 失败! $sql</br>";
				$OperationResult="N";
				}	
	        $Estate=3;
			if($MidSign==0){//未下单终审 自动生成采购单
				////////////////////////////////////
				//自动单号计算
				$DateTemp=date("Y");
				$Bill_Temp=mysql_query("SELECT MAX(PurchaseID) AS maxID FROM $DataIn.nonbom6_cgmain WHERE PurchaseID LIKE '$DateTemp%'",$link_id); 
				$PurchaseID =mysql_result($Bill_Temp,0,"maxID");
				if ($PurchaseID ){
					$PurchaseID =$PurchaseID+1;}
				else{
					$PurchaseID =$DateTemp."0001";
					}
					
				$inRecode="INSERT INTO $DataIn.nonbom6_cgmain (Id,mainType,PurchaseID,CompanyId,BuyerId,taxAmount,shipAmount,Attached,Remark,Locks,Date,Operator) VALUES (NULL,'$mainType','$PurchaseID','$CompanyId','$BuyerId',0,0,0,'终审后自动生成的非BOM采购单','0','$DateTime','$Operator')";
				$inAction=@mysql_query($inRecode);
				$Mid=mysql_insert_id();
				if($inAction && mysql_affected_rows()>0){ 
					$Log="$TitleSTR 成功!<br>";
					$Sql = "UPDATE $DataIn.nonbom6_cgsheet A
					LEFT JOIN $DataPublic.nonbom5_goodsstock B ON B.GoodsId=A.GoodsId
					SET A.Mid='$Mid',A.Locks='0',B.oStockQty=B.oStockQty+A.Qty WHERE A.Id IN ($Ids) AND A.Estate='1' AND A.Mid='0'";//已审核的才加入采购单，同时更新采购库存
					$Result = mysql_query($Sql);
					if($Result && mysql_affected_rows()>0 && $Mid>0){
						$Log.="需求单明细 ($Ids) 加入主采购单 $Mid 成功!<br>";
						}
					} 
				else{
					$Log.="<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
					$OperationResult="N";
					} 
				///////////////////////////////////
				}
		}
		
		break;*/
	default:
		$SetStr="fromMid='$fromMid',Qty='$Qty',Price='$Price',
		CompanyId='$CompanyId',BuyerId='$BuyerId',Date='$sgDate',
		Remark='$Remark',Operator='$Operator',Estate='2',AddTaxValue = '$AddTaxValue'";//需重新审核,
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$ALType="From=$From&Estate=$Estate&MidSign=$MidSign&singel=$singel";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>