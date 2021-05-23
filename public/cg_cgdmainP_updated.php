<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw1_fkoutsheet 
*/
if($ipadTag != "yes") {
include "../model/modelhead.php";
}
else
{
	$DataIn = "d7";
	if(!$link_id)
	{
		@include "../basic/parameter.inc";
	}
}

$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="需求单";		//需处理
$Log_Funtion="请款";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;

if($ipadTag != "yes") {
ChangeWtitle($TitleSTR);
}

$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
$AutoSign=1;  //1表示手动请款审核,2.表示手动请款自动通过,3表示自动请款审核,4表示自动请款自动通过
if($ipadTag == "yes"){   //来自IpadAPP送货单审核 
    $Ids="";
	$AutoSign=3;
	switch ($ActionId){
		case 14:	//如果入完库的标志，则自动请款
		$SQLResult=mysql_query(" SELECT G.Id,IFNULL(G.rkSign,-1) as rkSign,Y.StockId,Y.StuffId,M.CompanyId FROM $DataIn.gys_shsheet Y
								 LEFT JOIN $DataIn.gys_shmain M ON Y.Mid=M.Id 					
							     LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=Y.StockId
							     WHERE Y.Id='$Id' 
							   ",$link_id);		
		
		if($StockIdRow2=mysql_fetch_array($SQLResult)){
			$rkSign=$StockIdRow2["rkSign"];
			$StockId=$StockIdRow2["StockId"];
			if ( ($rkSign==0)  || ($StockId==-1) ){  //如果入完库的标志，且已补完货，或补完货，把已送完未请款的也自动请款
				$Ids=$StockIdRow2["Id"];
				$Month=date("Y-m");
				
				$CompanyId=$StockIdRow2["CompanyId"];
				$StuffId=$StockIdRow2["StuffId"];
				
				/*
				//是否最后一条记录，如果是，则怕它有退货，不能自动请款
				$LastSql=mysql_query("SELECT C.Id,C.StockId FROM $DataIn.cg1_stocksheet C WHERE C.CompanyId = '$CompanyId' AND C.StuffId = '$StuffId' ORDER BY C.ID DESC LIMIT 1  ",$link_id);
				$LastStockId=mysql_result($LastSql,0,"StockId");
				$LastId=mysql_result($LastSql,0,"Id");
				if($StockId==$LastStockId){
					return false;	
				}
				*/
				
				//退货的总数量 
				$thSql=mysql_query("SELECT IFNULL(SUM( S.Qty ),0) AS thQty  FROM $DataIn.ck2_thmain M  
											   LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
											   WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
				$thQty=mysql_result($thSql,0,"thQty");
				$thQty=$thQty==""?0:$thQty;
				//补货的数量 
				$bcSql=mysql_query("SELECT IFNULL(SUM( S.Qty ),0) AS bcQty  FROM $DataIn.ck3_bcmain M 
											   LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
											   WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
				$bcQty=mysql_result($bcSql,0,"bcQty");
				$bcQty=$bcQty==""?0:$bcQty;	
				
				$wbQty=$thQty-$bcQty;
				//if ($thQty!=$bcQty) {  //未补完，不给自动请款,
				if ($wbQty>0) {
					return false;
				}
				else {
					
					if ($StockId==-1 ) {  //如果是补完货，则扫描未请款,实现倒推法，先取最后一个已请款的stockId的Id,Id大于它的id，才扫，以免扫全表，但可能会有先请款的，只能手动请
					   $Ids="";
						$ScanSQL="SELECT C.Id FROM  $DataIn.cg1_stocksheet  C
						          LEFT JOIN $DataIn.cw1_fkoutsheet D ON  D.StockId=C.StockId
						          where D.Id is null AND C.CompanyId = '$CompanyId' AND C.StuffId = '$StuffId' AND C.rkSign=0 AND  C.Id>(
                                  SELECT G.ID FROM $DataIn.cw1_fkoutsheet M
						          LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=M.StockId
						          where   M.CompanyId = '$CompanyId' AND M.StuffId = '$StuffId' ORDER BY M.ID DESC LIMIT 1)";
						$checkSql=mysql_query($ScanSQL,$link_id);
						while($checkRow=mysql_fetch_array($checkSql)){
							$Id=$checkRow["Id"];
							if($Id!=$LastId){//不是最后一单，可以请款
								$Ids=$Ids==""?$Id:($Ids.",".$Id);
							}
						}
					} //if ($StockId==-1) { 
					if($Ids==""){
						return false;	
					}					
				}
				
			}  //if ( ($rkSign==0)  || ($rkSign==-1) ){  
			else {
				return false;
			}
		}  //if($StockIdRow2=mysql_fetch_array($SQLResult)){
		else {
			return false;
		}
		break;
	 default:
	 	return false;
		break;
		
	}

}
else {
	$Lens=count($checkid);
	for($i=0;$i<$Lens;$i++){
		$Id=$checkid[$i];
		if ($Id!=""){
			$Ids=$Ids==""?$Id:($Ids.",".$Id);
			}
		}
}
	
	
$ALType="From=$From";
switch ($ActionId){
	case 14:
		$Log_Funtion="采购请款";
		//将记录复制到请款明细表(客户退款类配件只按订单数计算)
		
		/*$inRecode="INSERT INTO $DataIn.cw1_fkoutsheet SELECT NULL,'0',StockId,POrderId,StuffId,FactualQty+AddQty,Price,OrderQty,StockQty,AddQty,FactualQty,CompanyId,BuyerId,(FactualQty+AddQty)*Price,'$Month','2','1' FROM $DataIn.cg1_stocksheet WHERE Id IN ($Ids)";
		$inAction=@mysql_query($inRecode);
		if ($inAction){ 
			$Log="&nbsp;&nbsp;Id号在(".$Ids.")的".$TitleSTR."成功!<br>";
			} 
		else{ 
			$Log="<div class=redB>&nbsp;&nbsp;Id号在(".$Ids.")的".$TitleSTR."失败!</div><br>";
			$OperationResult="N";
			}*/
	 if (strlen($Month)==7){
	     $cgStockResult=mysql_query("SELECT G.StockId,G.POrderId,G.StuffId,G.Price,G.OrderQty,
		  G.StockQty,G.AddQty,G.FactualQty,G.CompanyId,G.BuyerId,S.TypeId,A.GysPayMode,S.Price as NowPrice
		  FROM $DataIn.cg1_stocksheet G
		  LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId
		  LEFT JOIN  $DataIn.trade_object A ON A.CompanyId=G.CompanyId 
		  WHERE G.Id IN ($Ids) AND G.Mid>0",$link_id);
		 /*
		 echo "SELECT G.StockId,G.POrderId,G.StuffId,G.Price,G.OrderQty,
		  G.StockQty,G.AddQty,G.FactualQty,G.CompanyId,G.BuyerId,S.TypeId
		  FROM $DataIn.cg1_stocksheet G
		  LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId
		  WHERE G.Id IN ($Ids) <br>";
		  */
		  if($cgStockRow=mysql_fetch_array($cgStockResult)){
		     do{
		        $StockId=$cgStockRow["StockId"];
				$POrderId=$cgStockRow["POrderId"];
				$StuffId=$cgStockRow["StuffId"];
				$Price=$cgStockRow["Price"];
				$OrderQty=$cgStockRow["OrderQty"];
				$StockQty=$cgStockRow["StockQty"];
				$AddQty=$cgStockRow["AddQty"];
				$FactualQty=$cgStockRow["FactualQty"];
				$CompanyId=$cgStockRow["CompanyId"];
				$BuyerId=$cgStockRow["BuyerId"];
				$TypeId=$cgStockRow["TypeId"];
				
				//Begin  zx 2013-11-6  
				$GysPayMode=$cgStockRow["GysPayMode"];  //add by zx 2013-11-6
				$NowPrice=$cgStockRow["NowPrice"];  ////add by zx 2013-11-6
				
				if($AutoSign==1) { ////1表示手动请款，3表示自动请款, 自动请款，计算在前面了
					//退货的总数量 add by zx 2013-11-6
					$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
												   LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
												   WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
					$thQty=mysql_result($thSql,0,"thQty");
					$thQty=$thQty==""?0:$thQty;
					//补货的数量 add by zx 2013-11-6
					$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
												   LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
												   WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
					$bcQty=mysql_result($bcSql,0,"bcQty");
					$bcQty=$bcQty==""?0:$bcQty;	
				}
				
				$Estate=2;
				if(($thQty==$bcQty) && ($GysPayMode!=1) && ($NowPrice==$Price) ){  //无未补货，非现金，价钱相等则不用审核
				    if ($AutoSign==1){ ////1表示手动请款审核,2.表示手动请款自动通过,3表示自动请款审核,4表示自动请款自动通过
						$AutoSign=2;
					}
				    if ($AutoSign==3){ ////1表示手动请款审核,2.表示手动请款自动通过,3表示自动请款审核,4表示自动请款自动通过
						$AutoSign=4;
					}					
					$Estate=3;
				}
				
				//End
				
				if($TypeId=='9104'){//配件为客户退款的，金额为订单数*单价。否则为实际购买数*单价
				    $Qty=$OrderQty;
					$Amount=$Qty*$Price;
					}
				else{
				     $Qty=$FactualQty+$AddQty;
					 $Amount=$Qty*$Price;
				     }
		        $inRecode="INSERT INTO $DataIn.cw1_fkoutsheet(Id, Mid, StockId, POrderId, StuffId, Qty, Price, OrderQty, StockQty, AddQty, FactualQty, CompanyId, BuyerId, Amount, Month,AutoSign,Estate, Locks)VALUES(NULL,'0','$StockId','$POrderId','$StuffId','$Qty','$Price','$OrderQty','$StockQty','$AddQty','$FactualQty','$CompanyId','$BuyerId','$Amount','$Month','$AutoSign','$Estate','1')";
				//echo $inRecode;
		        $inAction=@mysql_query($inRecode);
		          if($inAction){ 
			           $Log.="&nbsp;&nbsp;Id号在(".$Ids.")的".$TitleSTR."成功!<br>";
			          } 
		          else{ 
			          $Log.="<div class=redB>&nbsp;&nbsp;Id号在(".$Ids.")的".$TitleSTR."失败!$inRecode</div><br>";
			          $OperationResult="N";
					  }
		       }while($cgStockRow=mysql_fetch_array($cgStockResult));
		    }
		}	
		else{
		    $Log.="<div class=redB>&nbsp;&nbsp;请款月份不能为空!</div><br>";
			$OperationResult="N";
		}	
		if($funFrom=="supplierpo3"){
			$Log_Funtion="供应商请款";
			$fromWebPage="../supplier/supplierpo3_read";
			$ALType.="&Estate=2";
			}
		else{
			$ALType.="&Estate=1&CompanyId=$CompanyId&chooseDate=$chooseDate&BuyerId=$BuyerId&GysPayMode=$GysPayMode";
		}
		break;
	case 15://退回修改
		//删除记录
		$Log_Funtion="退回修改";
		$delRecode="DELETE FROM $DataIn.cw1_fkoutsheet WHERE Id IN ($Ids)";
		$delAction = mysql_query($delRecode);
		if($delAction && mysql_affected_rows()>0){
			$Log="请款ID在($Ids)的需求单请款退回成功.";
			//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.cw1_fkoutsheet");
			$ALType="From=$From&CompanyId=$CompanyId";
			}
		else{
			$Log="<div class='redB'>请款ID在($Ids)的需求单请款退回失败.</div>";
			$OperationResult="N";
			}
		break;
	case 17://审核通过
		$Log_Funtion="审核通过";
		$updateRecode="UPDATE $DataIn.cw1_fkoutsheet SET Estate='3' WHERE Id IN ($Ids)";
		$updateAction = mysql_query($updateRecode);
		if($updateAction && mysql_affected_rows()>0){
			$Log="请款ID在($Ids)的需求单审核成功.";
			//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.cw1_fkoutsheet");
			$ALType="From=$From&CompanyId=$CompanyId";
			}
		else{
			$Log="<div class='redB'>请款ID在($Ids)的需求单审核失败.</div>";
			$OperationResult="N";
			}
		break;
	}
//返回参数

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
if($ipadTag != "yes") {
	include "../model/logpage.php";
}
?>