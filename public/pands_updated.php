<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-19
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 产品BOM保存");
$fromWebPage="pands_read";
$nowWebPage="pands_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$url="pands_read";
$Log_Item="产品BOM";
$Log_Funtion="设置产品BOM";
$ALType="From=$From&CompanyId=$CompanyId&ProductType=$ProductType";
//锁定表
//===========更新productiondata表中的字段Code为空。
//echo "CompanyId:$CompanyId";

//echo $SIdList;
$checkRow= mysql_fetch_array(mysql_query("SELECT   GROUP_CONCAT(P.StuffId,'^',P.Relation,'^',IF(P.bpRate=0,'',P.bpRate),'^',IFNULL(U.uStuffId,'')) AS oldList 
FROM  $DataIn.pands P 
LEFT JOIN (
           SELECT StuffId,GROUP_CONCAT(uStuffId) AS uStuffId FROM $DataIn.pands_unite 
           WHERE ProductId='$ProductId' GROUP BY StuffId
          ) U ON U.StuffId=P.StuffId 
WHERE P.ProductId='$ProductId' ",$link_id));
$oldList=$checkRow['oldList']==""?"":str_replace(',', "|",$checkRow['oldList']);
if($oldList!=$SIdList){
   if ($oldList!=""){
		//记录旧的bom
	    $checkVersion=mysql_fetch_array(mysql_query("SELECT MAX(VersionNo) AS VersionNo FROM $DataIn.pands_oldmain WHERE ProductId='$ProductId' ",$link_id));
	    $VersionNo=$checkVersion['VersionNo'];
	    $VersionNo=$VersionNo==""?1.00:$VersionNo+0.10;
	    
	    $IN_recode="INSERT INTO $DataIn.pands_oldmain(Id,ProductId,VersionNO,Remark,Estate,Locks,Date,Operator) VALUES (NULL,'$ProductId','$VersionNo','','1','0',CURDATE(),'$Operator')";
	    //echo $IN_recode;
	    $IN_res=@mysql_query($IN_recode);
	    $Mid=mysql_insert_id();
	    
	    $IN_recode2="INSERT INTO $DataIn.pands_oldsheet(Id,Mid,ProductId,StuffId,Relation,Diecut,Cutrelation,bpRate,Date,Operator,creator,created) SELECT NULL,$Mid,ProductId,StuffId,Relation,'','0',bpRate,Date,Operator,creator,created FROM $DataIn.pands WHERE ProductId='$ProductId'";
	    $IN_res2=@mysql_query($IN_recode2);
	    
	    $VersionNo=number_format($VersionNo,2);
	    $Log.="&nbsp;&nbsp;$ProductId - 保存原半成品BOM记录,Version:$VersionNo; <br>";
	}	
		
		
	$newDataArray = explode("|",$SIdList);
	//没清除数据之前取出元素数据
	
	$originalDataArray = array();
	$originalPandsSql= "SELECT * FROM $DataIn.pands A Where A.ProductId = '$ProductId' ORDER BY A.Id";
	$originalPandsResult = mysql_query($originalPandsSql);
	while($originalRow = mysql_fetch_assoc($originalPandsResult))
	{
		$oldStuffId = $originalRow["StuffId"];
		$oldRelation = $originalRow["Relation"];
		$oldBpRate = ($originalRow["bpRate"] == "0")?"":$originalRow["bpRate"];
		$originalStuffUnitResult = mysql_query("Select uStuffId From pands_unite Where ProductId = '$ProductId' and StuffId = '$oldStuffId'");
		$unitArray = array();
		while($originalStuffUnitRows = mysql_fetch_assoc($originalStuffUnitResult))
		{
			$unitArray[] = $originalStuffUnitRows["uStuffId"];
		}
		$originalStuffUnit = implode(",", $unitArray);
		
		$originalDataArray[] = "$oldStuffId^$oldRelation^$oldBpRate^$originalStuffUnit";
	}
	
	
	
	
	mysql_query("BEGIN");
	//original和new比较
	foreach($originalDataArray as $originalKey => $originalValue)
	{
	
		
		$comparedOrignalValue = hasStuffInArray($originalValue, $newDataArray);
		$originalValueArray = explode("^", $originalValue);
		$tmpOldStuffId = $originalValueArray[0];
		$tmpOldRelation = $originalValueArray[1];
		$tmpOldBpRate = $originalValueArray[2] == ""?"0":$originalValueArray[2];
		$tmpOldUnit = $originalValueArray[3];
		
		$insertPandsChargeSql = "";
		if($comparedOrignalValue == 0)
		{
			//在新数据里不存在:删除
			$insertPandsChargeSql = "Insert Into $DataIn.pandscharge (Id, ProductId, newStuffId, oldStuffId, relation, diecut, 	cutrelation, bpRate, unitStuffs, ChargeDate, Estate, Operator) Values (NULL, '$ProductId', '0', '$tmpOldStuffId', '$tmpOldRelation', '', '0', '$tmpOldBpRate', '$tmpOldUnit', '$DateTime', '1', '$Operator')";
		}
		else if($comparedOrignalValue == 2)
		{
			$insertPandsChargeSql = "Insert Into $DataIn.pandscharge (Id, ProductId, newStuffId, oldStuffId, relation, diecut, 	cutrelation, bpRate, unitStuffs, ChargeDate, Estate, Operator) Values (NULL, '$ProductId', '$tmpOldStuffId', '$tmpOldStuffId', '$tmpOldRelation', '', '0', '$tmpOldBpRate', '$tmpOldUnit', '$DateTime', '1', '$Operator')";
		}
		
		if($insertPandsChargeSql != "")
		{
			mysql_query($insertPandsChargeSql);
		}
		
	}
	
	foreach($newDataArray as $newKey => $newValue)
	{
		$newValueArray = explode("^", $newValue);
		$comparedNewVaule = hasStuffInArray($newValue, $originalDataArray);
		
		$tmpNewStuffId = $newValueArray[0];
		$tmpNewRelation = $newValueArray[1];
		$tmpNewBpRate = $newValueArray[2] == ""?"0":$newValueArray[2];
		$tmpNewUnit = $newValueArray[3];
		
		if($comparedNewVaule == 0)
		{
			$insertPandsChargeSql = "Insert Into $DataIn.pandscharge (Id, ProductId, newStuffId, oldStuffId, relation, diecut, 	cutrelation, bpRate, unitStuffs, ChargeDate, Estate, Operator) Values (NULL, '$ProductId', '$tmpNewStuffId', '0', '$tmpNewRelation', '', '0', '$tmpNewBpRate', '$tmpOldUnit', '$DateTime', '1', '$Operator')";
			//echo $insertPandsChargeSql."<br>";
	
			mysql_query($insertPandsChargeSql);
		}	
	}
	
	if(mysql_errno())
	{
		mysql_query("rollback");
	}
	else
	{
		mysql_query("commit");
	}
	
	mysql_query("END"); 
	
	//exit();
	//结束比较
	
	$CodeSTR=strpos($APP_CONFIG['PANDS_CODE_NOUPDADE'],$CompanyId) !== false?"":" ,Code='' "; 
	$upProduct="UPDATE $DataIn.productdata  SET Estate='2' $CodeSTR  where ProductId='$ProductId' ";
	
	$upResult=mysql_query($upProduct);
	$DelSql = "DELETE FROM $DataIn.pands WHERE ProductId='$ProductId'"; 
	$DelResult = mysql_query($DelSql);
	$DelSql2 = "DELETE FROM $DataIn.pands_unite WHERE ProductId='$ProductId'"; 
	$DelResult2 = mysql_query($DelSql2);
	
	$dataArray=explode("|",$SIdList);
	$Count=count($dataArray);
	$x=1;
	$Date=date("Y-m-d");
	for ($i=0;$i<$Count;$i++){
		$tempArray=explode("^",$dataArray[$i]);
		$StuffId=$tempArray[0];
		$Relation=$tempArray[1];
		//$sDiecut=$tempArray[2];
		//$sCutrelation=$tempArray[3]==""?0:$tempArray[3];
		$bpRate=$tempArray[2]==""?0:$tempArray[2];
		$Unite=$tempArray[3];
	
		//插入新的关系	
		$IN_recodeN="INSERT INTO $DataIn.pands (Id,ProductId,StuffId,Relation,Diecut,Cutrelation,bpRate,Date,Operator) VALUES (NULL,'$ProductId','$StuffId','$Relation','','0','$bpRate','$Date','$Operator')";
	    $resN=@mysql_query($IN_recodeN);
		if($resN){
			    $Log.="&nbsp;&nbsp; $x -配件ID号为 $StuffId 的配件已加入产品 $ProductId 的BOM!</br>";
			    //新增关联配件
			    if ($Unite!=""){
				    $UniteArray=explode(",", $Unite);
				    for ($n=0;$n<count($UniteArray);$n++){
					    $UniteId=$UniteArray[$n];
					    if ($UniteId>0){
						     $IN_recode="INSERT INTO $DataIn.pands_unite (Id,ProductId,StuffId,uStuffId,Relation,Date,Operator) VALUES (NULL,'$ProductId','$StuffId','$UniteId',0,'$Date','$Operator')";
	                         $res=@mysql_query($IN_recode);
					    }
				    }
			    }
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp; $x -配件ID号为 $StuffId 的配件未能加入产品 $ProductId 的BOM!</div></br>";
			}
		$x++;
		}
}else{
    $OperationResult="N";
	$Log.="<div class='redB'>&nbsp;&nbsp;未检查到有bom记录需更新！</div>";
}
	
	
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

<?php
	
function hasStuffInArray($targeValue, $dataValueArray)
{
		//完全没有
		$hasStuff = 0;
		$targetValueArray = explode("^", $targeValue);
		foreach($dataValueArray as $dataVaule)
		{
			$dataValues = explode("^", $dataVaule);
			if($targetValueArray[0] == $dataValues[0])
			{
				//有相同
				$hasStuff = 1;
				if($targeValue != $dataVaule)
				{
					//有相同配件但不等
					$hasStuff = 2;
				}
				break;
			}
		}
		
		return $hasStuff;
	}
	
?>

