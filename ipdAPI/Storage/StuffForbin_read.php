<?php
	
	include_once "../../basic/parameter.inc";
	
	$mySql="SELECT S.Id,S.StuffId,S.StuffCname,S.StuffEname,S.Gfile,S.Gstate,S.Picture,S.Gremark,S.Estate,
			P.Forshort,P.CompanyId,P.Letter,S.GfileDate,C.oStockQty,C.tStockQty ,U.Name AS UnitName,D.Reason,T.TypeId,T.Letter,T.TypeName
			FROM $DataIn.stuffdata S 
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
			LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId
			LEFT JOIN $DataPublic.stuffunit U ON U.Id=S.Unit
			LEFT JOIN $DataIn.ck9_stocksheet C ON C.StuffId=S.StuffId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
			LEFT JOIN $DataIn.stuffdisable  D ON D.StuffId=S.StuffId
			WHERE S.Estate='0' 
			AND C.oStockQty>0 
			order by S.Id DESC";
	
	//echo $mySql;
	$forbidStuffList = array();
	$myResult = mysql_query($mySql);
	while($myRow = mysql_fetch_assoc($myResult))
	{
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$StuffEname=$myrow["StuffEname"]==""?"":$myrow["StuffEname"];
		$Picture=$myRow["Picture"];
		$UnitName=$myRow["UnitName"];
		$Gfile=$myRow["Gfile"];
		$Gstate=$myRow["Gstate"];  //状态
		$Gremark=$myRow["Gremark"];
		$CompanyId = $myRow["CompanyId"];
		$Forshort=$myRow["Forshort"];
		$typeId = $myRow["TypeId"];
		$typeName = $myRow["TypeName"];
		$oStockQty=$myRow["oStockQty"];
        $tStockQty=$myRow["tStockQty"];
        $Qty=$oStockQty>$tStockQty?$tStockQty:$oStockQty;
		$Reason=$myRow["Reason"];
		
		$forbidStuffList[] = array("Id"=>"$Id", "StuffId"=>"$StuffId", "StuffCname"=>"$StuffCname", "StuffEname"=>"$StuffEname", "Picture"=>"$Picture", "UnitName"=>"$UnitName", "Gfile"=>"$Gfile", "Gstate"=>"$Gstate", "Gremark"=>"$Gremark", "CompanyId"=>"$CompanyId", "CompanyName"=>"$Forshort", "TypeId"=>"$typeId", "TypeName"=>"$typeName", "oStockQty"=>"$oStockQty", "tStockQty"=>"$tStockQty", "Qty"=>"$Qty", "Reason"=>"$Reason");
		
	}
	
	echo json_encode($forbidStuffList);
	
?>