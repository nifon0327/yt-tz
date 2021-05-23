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
	select G.Icon,G.File,G.TypeId,G.Name,B.Name TypeName
	from $DataPublic.studysheet G 
	left join $DataPublic.studytype B on B.TypeId=G.TypeId
	where  G.Id=$editStuffId
	");
	$editTypeId= $editUnit= $editStuffCname = $editPrice = $editDevelopState = $editCompanyId = "";
	if ($editStuffInfoRow = mysql_fetch_assoc($editStuffInfo)) {
		$editTypeId       = $editStuffInfoRow["TypeId"];
		$Icon        = $editStuffInfoRow["Icon"];
		$File        = $editStuffInfoRow["File"];
		$editIntroduction   = $editStuffInfoRow["Introduction"];
		$editTypeName   = $editStuffInfoRow["TypeName"];
		$editGoodsName   = $editStuffInfoRow["Name"];
		
		$editIcon = $baseUrl."$Icon";
		$editIntro = $baseUrl."$File";
		
	
	
	$editInfoList[]=array("ContentTxt"=>"$editTypeName","FieldVal"=>"$editTypeId");
	$editInfoList[]=array("ContentTxt"=>"$editGoodsName","FieldVal"=>"$editGoodsName");
	
	$editInfoList[]=array("imgUrl"=>"$editIntro");
	$editInfoList[]=array("imgUrl"=>"$editIcon");
	$editInfoList[]=array("typeid"=>"$editTypeId");
	} 
	
	
	$sqlStuffType = mysql_query("SELECT  B.TypeId,B.Name from studytype B where B.Estate>=1");


while ($sqlStuffTypeRow = mysql_fetch_assoc($sqlStuffType)) {
	
	$typeId          = $sqlStuffTypeRow["TypeId"];

	$typeName        = $sqlStuffTypeRow["Name"];
	$selected = false;
if ( $typeId==$editTypeId) {
		$selectedType="$index";
		$selected = true;
	}
	$stuffTypeList[] = array("headImage"=>"",
							   "title"    =>"$typeName",
							   "Id"       =>"$typeId",
							   "CellType" =>"2",
							   "selected" =>$selected==true?"1":"0"
							   ); 
	
	$index++;
}

	
	
	$jsonArray = array(
					 "EditInfo"=>$editInfoList,
					  "Types"=>$stuffTypeList,
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
	$sqlStuffType = mysql_query("SELECT  B.TypeId,B.Name from studytype B where B.Estate>=1");


while ($sqlStuffTypeRow = mysql_fetch_assoc($sqlStuffType)) {
	
	$typeId          = $sqlStuffTypeRow["TypeId"];

	$typeName        = $sqlStuffTypeRow["Name"];
	$selected = false;
if ( $typeId==$editTypeId) {
		$selectedType="$index";
		$selected = true;
	}
	$stuffTypeList[] = array("headImage"=>"",
							   "title"    =>"$typeName",
							   "Id"       =>"$typeId",
							   "CellType" =>"2",
							   "selected" =>$selected==true?"1":"0"
							   ); 
	
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