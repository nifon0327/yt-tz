<?php
$checkLoadBearing=mysql_query("SELECT A.MainWeight,A.Weight,A.MisWeight,B.Relation,C.Weight1
							 FROM $DataIn.productdata A
							 LEFT JOIN   $DataIn.pands B ON B.ProductId=A.ProductId
							 LEFT JOIN $DataIn.stuff_loadbearing C ON C.StuffId=B.StuffId
							 LEFT JOIN $DataIn.stuffdata D ON D.StuffId=B.StuffId
							 WHERE A.ProductId='$ProductId' AND C.StuffId IS NOT NULL AND A.MainWeight>0 AND C.Weight1>0 AND D.TypeId=9040
							  ",$link_id);
$LoadBearingINFO="&nbsp;";
if($checkLoadBearingRow=mysql_fetch_array($checkLoadBearing)){
	$LB_MainWeight=$checkLoadBearingRow["MainWeight"];
	$LB_Relation=$checkLoadBearingRow["Relation"];
	$LB_Relation=explode("/",$checkLoadBearingRow["Relation"]);
	$LB_Weight1=$checkLoadBearingRow["Weight1"];
	$Bom_Weight=($LB_MainWeight*$LB_Relation[1])/1000;
	if($Bom_Weight>$LB_Weight1){
		$LoadBearingINFO="<span class='redB'>$LB_Weight1</span>";
		}
	else{
		$LoadBearingINFO="<span class='greenB'>$LB_Weight1</span>";
		}
	//计算
	}
else{
	$LoadBearingINFO="<span class='redB'>资料不全</span>";
	}
?>