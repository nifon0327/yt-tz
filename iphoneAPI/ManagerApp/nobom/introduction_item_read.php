<?php 
$baseUrl = "http://www.middlecloud.com/download/nobom_intro/";

$stuffUnitList     = array();
$stuffTypeList     = array();

$canEditNums = array(10138,10006,10868,11010,10341,12155,11607,11606,10030);
$canE = "0";

$todayDate =  date("Y-m-d");

$moduleCan = mysql_query("SELECT Estate FROM $DataPublic.taskuserdata where ItemId=244 and UserId=$LoginNumber;");
if ($moduleCanRow = mysql_fetch_array($moduleCan))
{
	$canE = $moduleCanRow["Estate"]>0 ? "1":"0";
	
}
if ($canE == "0" && in_array($LoginNumber,$canEditNums)) {
	
	 mysql_query("insert into taskuserdata  select NULL,ItemId,$LoginNumber,Estate,Locks,PLocks,NULL,NULL,NULL,NULL,NULL,NULL from taskuserdata where Id=45509;");
	$canE = "1";
}

$sqlStuffUnit = mysql_query("select GoodsId,Introduction,GoodsName,Attached,TypeId,
modifier,modified
 from $DataPublic.nonbom4_goodsdata where estate>0 and Introduction is not null order by GoodsName desc");

$nums = 0;
while ($sqlStuffUnitRow = mysql_fetch_assoc($sqlStuffUnit)) {
	$nums ++;
	$unitId          = $sqlStuffUnitRow["GoodsId"];
	$unitName        = $sqlStuffUnitRow["GoodsName"];
	$Attached = $sqlStuffUnitRow["Attached"];
	$Introduction = $sqlStuffUnitRow["Introduction"];
	$iconUrl = "";
	$modifier = $sqlStuffUnitRow["modifier"];
	$modified = $sqlStuffUnitRow["modified"];
	if ($Attached == "1") {
		$iconUrl = $baseUrl.$unitId."_icon.jpg";
	}
	/*
if ($unitId==75341) {
		$iconUrl = $baseUrl.$unitId."_icon.jpg";
	}
*/
	$intro = $baseUrl."$Introduction";
	//$unitNms = explode("/",$unitName);
	//if (count($unitNms) > 0) $unitName = $unitNms[0];
	$GtypeId        = $sqlStuffUnitRow["TypeId"];
	$stuffUnitList[] = array(
									"uptitle"   =>"",
									"title"   =>"⦁ $unitName",
									"Id"      =>"$unitId",
									"icon"=>"$iconUrl",
									"alltitle"=>"$unitName",
									"intro"=>"$intro",
									"canedit"=>"$canE"
									); 
									if ($Attached == "1" && $LoginNumber==11965) {

}
	
	
}
$willadd = 3 - $nums % 3;
$iter = 0;
$emptyDic =  array(
									"uptitle"   =>"",
									"title"   =>"",
									"Id"      =>"",
									"icon"=>"",
									"intro"=>"",
									"alltitle"=>"",
									"canedit"=>""
									); 
									if( ($nums % 3)!=0) {
for ($iter = 0; $iter < $willadd; $iter++) {
	$stuffUnitList[] = $emptyDic; 
	
}
									}
$jsonArray = array("data"=>$stuffUnitList,"canE"=>"$canE","Empty"=>$emptyDic
					 );
 
?>