<?php
//$DataIn.pands 二合一已更新
include "../model/modelhead.php";

ChangeWtitle("$SubCompany 半成品BOM保存");
$fromWebPage=$funFrom . "_read";
$nowWebPage=$funFrom . "_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$url=$funFrom . "_read";
$Log_Item="半成品BOM";
$Log_Funtion="设置半成品BOM";
$ALType="From=$From";

//锁定表
switch($ActionId){


	case 17:
		$Log_Funtion="审核";		
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}
		$sql = "UPDATE $DataIn.stuffdata SET bomEstate = 0 WHERE StuffId IN ($Ids) ";
		$result = mysql_query($sql);
		if($result){
			$Log="ID号在 $Ids 的记录成功 $Log_Funtion. </br>";
			}
		else{
			$Log="ID号为 $Ids 的记录$Log_Funtion 失败! $sql</br>";
			$OperationResult="N";
			}
		
	    $fromWebPage=$funFrom . "_m";
		break;
	
	case "DeliveryDate"://ajax
	    $checkResult= mysql_query("SELECT * FROM  $DataIn.semifinished_deliverydate WHERE mStuffId='$mStuffId'",$link_id);
	    if($checkRow=mysql_fetch_array($checkResult)){
	       $upSql="UPDATE $DataIn.semifinished_deliverydate SET ReduceWeeks='$ReduceWeeks',modifier='$Operator',modified=NOW() WHERE mStuffId='$mStuffId'";
	    }
	    else{
		  $upSql="INSERT INTO $DataIn.semifinished_deliverydate(Id,mStuffId,ReduceWeeks,Date,Operator)VALUES (NULL,'$mStuffId','$ReduceWeeks',CURDATE(),'$Operator')";  
	    }
	    $upResult=@mysql_query($upSql);
	break;
	
	default:
	    
		$checkRow= mysql_fetch_array(mysql_query("SELECT GROUP_CONCAT(StuffId,'^',Relation,'^','1') AS oldList FROM  $DataIn.semifinished_bom WHERE mStuffId='$mStuffId'",$link_id));
		
		$oldList=$checkRow['oldList']==""?"":str_replace(',', "|",$checkRow['oldList']);
		if($oldList!=$SIdList){
		    //记录旧的bom
		    if ($oldList!=""){
			    $checkVersion=mysql_fetch_array(mysql_query("SELECT MAX(VersionNo) AS VersionNo FROM $DataIn.semifinished_oldbom_main WHERE mStuffId='$mStuffId'",$link_id));
			    $VersionNo=$checkVersion['VersionNo'];
			    $VersionNo=$VersionNo==""?1.00:$VersionNo+0.10;
			    
			    $IN_recode="INSERT INTO $DataIn.semifinished_oldbom_main(Id,mStuffId,VersionNO,Remark,Estate,Locks,Date,Operator) VALUES (NULL,'$mStuffId','$VersionNo','','1','0',CURDATE(),'$Operator')";
			    //echo $IN_recode;
			    $IN_res=@mysql_query($IN_recode);
			    $Mid=mysql_insert_id();
			    
			    $IN_recode2="INSERT INTO $DataIn.semifinished_oldbom_sheet(Id,Mid,mStuffId,StuffId,Relation,Date,Operator,creator,created) SELECT NULL,$Mid,mStuffId,StuffId,Relation,Date,Operator,creator,created FROM $DataIn.semifinished_bom WHERE mStuffId='$mStuffId'";
			    $IN_res2=@mysql_query($IN_recode2);
			    
			    $VersionNo=number_format($VersionNo,2);
			    $Log.="&nbsp;&nbsp;$gStuffId - 保存原半成品BOM记录,Version:$VersionNo; <br>";
		    }
		    //删除旧的bom
			$DelSql = "DELETE FROM $DataIn.semifinished_bom WHERE mStuffId='$mStuffId'"; 
			$DelResult = mysql_query($DelSql);
			
			//新增或更新版本
			$IN_recode3 = "INSERT INTO semifinished_bom_main(mStuffId,VersionNO,Remark,Estate,Locks,Date,Operator,creator,created) 
			                        VALUES('$mStuffId','1.00','','1','0',CURDATE(),'$Operator','$Operator',NOW()) 
			                        ON DUPLICATE KEY UPDATE VersionNo=VersionNo+0.10,modifier='$Operator',modified=NOW()";
			$IN_res3=@mysql_query($IN_recode3);
			                        
			$dataArray=explode("|",$SIdList);
			$Count=count($dataArray);
			$x=1;
			$Date=date("Y-m-d");
			for ($i=0;$i<$Count;$i++){
				$tempArray=explode("^",$dataArray[$i]);
				$StuffId=$tempArray[0];
				$Relation=$tempArray[1];
			
				//插入新的关系	
				$IN_recodeN="INSERT INTO $DataIn.semifinished_bom (Id,mStuffId,StuffId,Relation,Date,Operator) VALUES (NULL,'$mStuffId','$StuffId','$Relation','$Date','$Operator')";
				//echo "$IN_recodeN";
			    $resN=@mysql_query($IN_recodeN);
				if($resN){  
				   $Log.="&nbsp;&nbsp; $x -配件ID号为 $StuffId 的配件已设成为半成品配件 $mStuffId 的原材料!</br>";
				}
				else{
					  $Log.="<div class='redB'>&nbsp;&nbsp; $x -配件ID号为 $StuffId 的配件未设成半成品配件 $mStuffId 的原材料!</div></br>";
				} 
				$x++;
			} 
			 
			//更新半成品单价
			 $upSql = "UPDATE  $DataIn.stuffdata SET Price = '$taxtPrice',CostPrice='$cbHZ',bomEstate = 1 WHERE StuffId='$mStuffId' ";
			 $upResult = mysql_query($upSql);
			 if ($upResult){
				 $Log.="配件 ($mStuffId) 的成本价格已更新为:$cbHZ 含税价格已更新为:$taxtPrice </br>";
			 }
			 
			//更新半成品单价NEW
			 $MyPDOEnabled=1;
             include "../basic/parameter.inc";

			 $myPDO->query(" START TRANSACTION;");
             $myResult=$myPDO->query("SELECT setNewStuffCostPrice($mStuffId) AS Counts");
             $myPDO->query(" COMMIT;");
             
		}else{
		    $OperationResult="N";
			$Log.="<div class='redB'>&nbsp;&nbsp;未检查到有bom记录需更新！</div>";
		}
		
		
	 break;
	
}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";

?>