<?php
$MyPDOEnabled=1;
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="生产类配件置换";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 15://记录退回
			$Log_Funtion = "退回";
			$Lens=count($checkid);
			for($i=0;$i<$Lens;$i++){
				$Id=$checkid[$i];
				if ($Id!=""){
						 
					 $DelSql = "DELETE FROM $DataIn.yw1_stuffchange WHERE Id = $Id AND Estate>0 ";
					 $DelCount = $myPDO->exec($DelSql);
					 if ($DelCount){
						$Log="&nbsp;&nbsp;ID在( $Id )的生产类配件置换退回 成功.<br>";
						}
					else{
						$OperationResult="N";
						$Log="<div class='redB'>ID在( $Id )的生产类配件置换退回 失败.</div><br>";
					 }
				}
			}
			$fromWebPage=$funFrom."_m";
		break;
	case 17: //审核通过
		$Log_Funtion = "审核";
		
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if ($Id!=""){
				  //更新配件
				    $CheckStuffSql = "SELECT C.POrderId,C.StockId,C.NewStuffId,C.NewRelation,
				    D2.StuffCname AS NewStuffCname,D2.Price AS NewPrice,G.Level
				    FROM $DataIn.yw1_stuffchange C
				    INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = C.StockId
				    INNER JOIN $DataIn.stuffdata D2 ON D2.StuffId = C.NewStuffId
				    WHERE C.Id = $Id";
				    
				    $CheckStuffResult=$myPDO->query($CheckStuffSql); 
	                $CheckStuffRow = $CheckStuffResult->fetch(PDO::FETCH_ASSOC);
	     
				    $StockId=$CheckStuffRow["StockId"];
				    $POrderId=$CheckStuffRow["POrderId"];
				    $NewStuffId=$CheckStuffRow["NewStuffId"];
					$NewRelation=$CheckStuffRow["NewRelation"];
					$NewStuffCname=$CheckStuffRow["NewStuffCname"];
					$NewPrice=$CheckStuffRow["NewPrice"];
					$Level=$CheckStuffRow["Level"];
					if($StockId>0 && $NewStuffId>0){
						
						$UpdateSql1 = "UPDATE $DataIn.cg1_stocksheet  SET StuffId='$NewStuffId',Price='$NewPrice',CostPrice='$NewPrice',modified='$DateTime' WHERE StockId ='$StockId'";
						$UpdateResult1 = $myPDO->exec($UpdateSql1);
						
						if($UpdateResult1){
							$Log="&nbsp;&nbsp;采购流水号为:$StockId 生产类配件置换审核成功.<br>";
							$UpdateResult3 = $myPDO->exec("UPDATE $DataIn.yw1_stuffchange  SET Estate='0' WHERE Id ='$Id'");
							if($UpdateResult3 && $Level>1){
								  $UpdateResult4 = $myPDO->exec("UPDATE $DataIn.cg1_semifinished  SET StuffId='$NewStuffId' WHERE StockId ='$StockId'");
								  $DelProcessSql = "DELETE FROM $DataIn.cg1_processsheet WHERE  StockId ='$StockId' AND POrderId='$POrderId'";
								  $DelProcessResult = $myPDO->exec($DelProcessSql);
								  $InProcessSql ="INSERT INTO $DataIn.cg1_processsheet 
					              SELECT NULL,'$POrderId','$StockId',StuffId,ProcessId,BeforeProcessId,Relation,'$toDate','0'  
					              FROM  $DataIn.process_bom WHERE StuffId='$NewStuffId'";
					              $InProcessResult = $myPDO->exec($InProcessSql);
								 
							}
							$UpdateResult5 = $myPDO->query("CALL proc_cg1_stocksheet_cmprice_updated('$StockId');");

						}else{
							$OperationResult="N";
			                $Log="<div class='redB'>&nbsp;&nbsp;采购流水号为:$StockId 生产类配件置换审核失败.$UpdateSql1</div><br>";
						}
					}
				}
			}
		
		
		$fromWebPage=$funFrom."_m";
		break;
	default:
		
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=$myPDO->exec($IN_recode);
include "../model/logpage.php";
?>