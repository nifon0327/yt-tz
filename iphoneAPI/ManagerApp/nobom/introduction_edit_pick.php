<?php 

$isModifing = false;
$info_0 = $info[0];
$editStuffId = $info[1];
if ($info_0 == "modify" && (int)$editStuffId>0) {
	$isModifing = true;
//	echo "mmmm ".$editStuffId;
}
if ($isModifing == true) {
		$editInfoList = array();
	$baseUrl = "http://www.middlecloud.com/download/nobom_intro/";
	$editStuffInfo = mysql_query("
	select G.GoodsId,G.Introduction,G.GoodsName,G.Attached,G.TypeId,B.TypeName
	from $DataPublic.nonbom4_goodsdata G 
	LEFT JOIN $DataPublic.nonbom2_subtype B ON B.Id=G.TypeId
	where G.estate>0 and G.GoodsId=$editStuffId
	");
	$editTypeId= $editUnit= $editStuffCname = $editPrice = $editDevelopState = $editCompanyId = "";
	if ($editStuffInfoRow = mysql_fetch_assoc($editStuffInfo)) {
		$editTypeId       = $editStuffInfoRow["TypeId"];
		$editGoodsId        = $editStuffInfoRow["GoodsId"];
		$editIntroduction   = $editStuffInfoRow["Introduction"];
		$editTypeName   = $editStuffInfoRow["TypeName"];
		$editGoodsName   = $editStuffInfoRow["GoodsName"];
		
		$editIcon = $baseUrl."$editGoodsId"."_icon_s.jpg";
		$editIntro = $baseUrl."$editIntroduction";
		
	
	
	$editInfoList[]=array("ContentTxt"=>"$editTypeName","FieldVal"=>"$editTypeId");
	$editInfoList[]=array("ContentTxt"=>"$editGoodsName","FieldVal"=>"$editGoodsId");
	
	$editInfoList[]=array("imgUrl"=>"$editIntro");
	$editInfoList[]=array("imgUrl"=>"$editIcon");
	} 
	
	
	
	$jsonArray = array(
					 "EditInfo"=>$editInfoList
					 );
 	
	
} else {


$stuffUnitList     = array();
$stuffTypeList     = array();

/*
    EnumSelectionItemCellTypeImage = 1,
    EnumSelectionItemCellTypeTitle = 2,
    EnumSelectionItemCellTypePoint = 3
	*/

$sqlStuffUnit = mysql_query("select GoodsId,GoodsName,TypeId from $DataPublic.nonbom4_goodsdata where estate>0 and Introduction is null");

$selectedUnit = "";
$selected = false;
$index = 0;
while ($sqlStuffUnitRow = mysql_fetch_assoc($sqlStuffUnit)) {
	
	$unitId          = $sqlStuffUnitRow["GoodsId"];
	$unitName        = $sqlStuffUnitRow["GoodsName"];
	$GtypeId        = $sqlStuffUnitRow["TypeId"];
	$stuffUnitList[] = array("headImage"   =>"",
									"point"   =>"1",
									"title"   =>"$unitName",
									"Id"      =>"$unitId",
									"CellType"=>"2",
									"selected"=>"0",
									"infos"=>"$GtypeId"
									); 
	if ($selected == false && $isModifing==true && $unitId==$editUnit) {
		$selectedUnit="$index";
		$selected = true;
	}
	$index++;
}

$selectedType = "";
$index = 0;
$selected = false;
$sqlStuffType = mysql_query("SELECT  A.TypeId,B.TypeName FROM $DataPublic.nonbom4_goodsdata A LEFT JOIN $DataPublic.nonbom2_subtype B ON B.Id=A.TypeId WHERE 1  GROUP BY A.TypeId ORDER BY A.TypeId,B.TypeName");

while ($sqlStuffTypeRow = mysql_fetch_assoc($sqlStuffType)) {
	
	$typeId          = $sqlStuffTypeRow["TypeId"];

	$typeName        = $sqlStuffTypeRow["TypeName"];

	$stuffTypeList[] = array("headImage"=>"",
							   "title"    =>"$typeName",
							   "Id"       =>"$typeId",
							   "CellType" =>"2",
							   "selected" =>"0"
							   ); 
	if ($selected == false && $isModifing==true && $typeId==$editTypeId) {
		$selectedType="$index";
		$selected = true;
	}
	$index++;
}



$editInfoList = array();
if ($isModifing== true) {
	$editInfoList[] = array("row"=>"0","select"=>"$selectedType","ContentTxt"=>"$selectedProviderNm","FieldVal"=>"$editTypeId");
	$editInfoList[] = array("row"=>"1","ContentTxt"=>"$editStuffCname","FieldVal"=>"$editStuffCname");
	$editInfoList[] = array("row"=>"2","ContentTxt"=>"$editPrice","FieldVal"=>"$editPrice");
	$editInfoList[] = array("row"=>"3","ContentTxt"=>"$editSpec","FieldVal"=>"$editSpec");
	$editInfoList[] = array("row"=>"4","ContentTxt"=>"$editWeight","FieldVal"=>"$editWeight");
	
	
	$editInfoList[] = array("row"=>"5","select"=>"$selectedUnit","FieldVal"=>"$editUnit");
	$editInfoList[] = array("row"=>"6","selects"=>$selectedProps,"FieldVals"=>$stuffpropertys);
	$editInfoList[] = array("row"=>"7","ContentTxt"=>$editDevelopState==1?"是":"否","FieldVal"=>"$editDevelopState");
	$editInfoList[] = array("row"=>"8","select"=>"$selectedWeeks","FieldVal"=>"");
	$editInfoList[] = array("row"=>"9","select"=>"$selectedDevCompany","FieldVal"=>"");
		$editInfoList[] = array("row"=>"10","select"=>"$selectedProvider","FieldVal"=>"$editCompanyId");

}
$jsonArray = array(
					 "Types"=>$stuffTypeList,
					 "Names"=>$stuffUnitList
					 );
 
 
}
?>