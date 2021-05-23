<?php 

$isModifing = false;
if ($info_0 == "modify" && (int)$editStuffId>0) {
	$isModifing = true;
//	echo "mmmm ".$editStuffId;
}
if ($isModifing == true) {
	$editStuffInfo = mysql_query("
	select S.TypeId,S.Unit,S.StuffCname,S.Price,S.Spec,S.Weight,S.DevelopState,B.CompanyId from $DataIn.stuffdata S 
   left join $DataIn.bps B on B.StuffId=S.StuffId 
   where S.StuffId=$editStuffId
	");
	$editTypeId= $editUnit= $editStuffCname = $editPrice = $editDevelopState = $editCompanyId = "";
	if ($editStuffInfoRow = mysql_fetch_assoc($editStuffInfo)) {
		$editTypeId       = $editStuffInfoRow["TypeId"];
		$editUnit         = $editStuffInfoRow["Unit"];
		$editStuffCname   = $editStuffInfoRow["StuffCname"];
		$editPrice        = $editStuffInfoRow["Price"];
		$editDevelopState = $editStuffInfoRow["DevelopState"];
		$editCompanyId    = $editStuffInfoRow["CompanyId"];
		$editSpec         = $editStuffInfoRow["Spec"];
		$editWeight       = $editStuffInfoRow["Weight"];
	} 
	
		$stuffpropertys = array();
    $propertysql = mysql_query("SELECT property,stuffid FROM $DataIn.stuffproperty WHERE StuffId=$editStuffId");
			while ($propertysqlRow = mysql_fetch_assoc($propertysql)) {
				$stuffpropertys[]=$propertysqlRow["property"];
			}
			
			$DevelopTargetWeek = "";
			$DevelopCompany = "";
			if (in_array("11",$stuffpropertys)) {
					$developRow = mysql_fetch_assoc(mysql_query("SELECT yearweek(Targetdate,1) as targetWeek,CompanyId FROM $DataIn.stuffdevelop WHERE StuffId=$editStuffId"));
					$DevelopTargetWeek = $developRow["targetWeek"];
			$DevelopCompany = $developRow["CompanyId"];
			}
			
	
}


$stuffPropTypeList = array();
$stuffUnitList     = array();
$stuffTypeList     = array();

/*
    EnumSelectionItemCellTypeImage = 1,
    EnumSelectionItemCellTypeTitle = 2,
    EnumSelectionItemCellTypePoint = 3
	*/

$sqlPropType       = mysql_query("select Id,TypeName from $DataIn.stuffpropertytype 
									 where Estate>0");
$propIdImagePrefix = "property_";

$selectedProps = array();
$index = 0;
while ($sqlPropTypeRow = mysql_fetch_assoc($sqlPropType)) {
	
	$propId              = $sqlPropTypeRow["Id"];
	$propTypeName        = $sqlPropTypeRow["TypeName"];
	$stuffPropTypeList[] = array("headImage"=>"$propIdImagePrefix"."$propId",
									"point"    =>"0",
									"title"    =>"$propTypeName",
									"Id"       =>"$propId",
									"CellType" =>"1",
									"selected" =>"0"
									); 
									
	if ($isModifing==true && in_array($propId, $stuffpropertys)) {
		$selectedProps []="$index";
	}
	$index++;
}

$sqlStuffUnit = mysql_query("select Id,Name from $DataIn.stuffunit 
						       where Estate>0");

$selectedUnit = "";
$selected = false;
$index = 0;
while ($sqlStuffUnitRow = mysql_fetch_assoc($sqlStuffUnit)) {
	
	$unitId          = $sqlStuffUnitRow["Id"];
	$unitName        = $sqlStuffUnitRow["Name"];
	$stuffUnitList[] = array("headImage"   =>"",
									"point"   =>"1",
									"title"   =>"$unitName",
									"Id"      =>"$unitId",
									"CellType"=>"3",
									"selected"=>"0"
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
$sqlStuffType = mysql_query("select TypeId,TypeName,NameRule from $DataIn.stufftype 
						       where Estate>0");

while ($sqlStuffTypeRow = mysql_fetch_assoc($sqlStuffType)) {
	
	$typeId          = $sqlStuffTypeRow["TypeId"];
	$BlockTriger     = $typeId == 9040 ? "1":"0";
	$typeName        = $sqlStuffTypeRow["TypeName"];
	$NameRule        = $sqlStuffTypeRow["NameRule"];
	//$NameRule        = str_replace("\n","",$NameRule);
	$stuffTypeList[] = array("headImage"=>"",
							   "title"    =>"$typeName",
							   "Id"       =>"$typeId",
							   "CellType" =>"2",
							   "selected" =>"0",
							   "infos"    =>"$NameRule",
							   "BLock"    =>"$BlockTriger"
							   ); 
	if ($selected == false && $isModifing==true && $typeId==$editTypeId) {
		$selectedType="$index";
		$selected = true;
	}
	$index++;
}

$stuffProviderList = array();
$selectedProvider = "";
$index = 0;$selected = false;
$sqlStuffProvider = mysql_query("select CompanyId,Forshort from $DataIn.providerdata 
						       where Estate>0");

while ($sqlStuffProviderRow = mysql_fetch_assoc($sqlStuffProvider)) {
	
	$CompanyId          = $sqlStuffProviderRow["CompanyId"];
	$Forshort        = $sqlStuffProviderRow["Forshort"];
	$stuffProviderList[] = array("headImage"=>"",
							   "title"    =>"$Forshort",
							   "Id"       =>"$CompanyId",
							   "CellType" =>"2",
							   "selected" =>"0",
							   "infos"    =>""
							   ); 
	if ($selected == false && $isModifing==true && $CompanyId==$editCompanyId) {
		$selectedProvider="$index";
		$selectedProviderNm="$Forshort";
			$selected = true;
	}
	$index++;
}



$weekList = array();
$selected =false;
$COUNTWEEK = 16;
$dateNow = date("Y-m-d");
$selectedWeeks = "";
for ($i = 0; $i < $COUNTWEEK; $i++) {
$hasS = $i==1 ? "":"s";
$aDate=date("Y-m-d",strtotime("+$i week$hasS",strtotime($dateNow)));
$aWeekRow = mysql_fetch_array(mysql_query("select yearweek('$aDate',1) as Weeks limit 1;"));
$weekTitle = $aWeekRow["Weeks"];	
if ($selected == false && $isModifing==true && (int)$DevelopTargetWeek == (int)$weekTitle) {
	$selectedWeeks = $i;
	$selected = true;
}
$dateArray= GetWeekToDate($weekTitle,"m/d");
$dateSTR=$dateArray[0] . "-" .  $dateArray[1];
$weekString = substr($weekTitle,4,2);
$weekList[]=array("headImage"=>"",
							   "WeekTitle"    =>"$dateSTR",
							   "Id"       =>"$weekTitle",
							   "Week"     =>"$weekString",
							   "CellType" =>"4",
							   "selected" =>"0",
							   "infos"    =>"$weekString"."周"
							   ); 

}
/*SELECT 
A.Id,A.CompanyId,A.Letter,A.Forshort,A.ProviderType,A.GysPayMode,A.Estate,A.Date,A.Operator,A.Locks,A.ExpNum,A.PayType,A.CompanySignXY,B.Tel,B.Fax,B.Website,B.Remark,B.Area,C.Symbol,A.Judge,A.PackFile,A.TipsFile,A.Prepayment,A.LimitTime,K.Title AS BankTitle,E.Name AS staff_Name ,A.PriceTerm,F.Name AS PayMode ,F.eName AS ePayMode,A.ObjectSign ,A.UpdateReasons,A.ReturnReasons,A.ChinaSafe
FROM $DataIn.trade_object  A
LEFT JOIN $DataIn.companyinfo B ON B.CompanyId=A.CompanyId AND A.Type=8
LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency
LEFT JOIN $DataPublic.staffmain E ON E.Number=A.Staff_Number
LEFT JOIN $DataPublic.my2_bankinfo K ON K.Id=A.BankId 
LEFT JOIN $DataPublic.clientpaymode F ON F.Id=A.PayMode
WHERE 1  $SearchRows ORDER BY A.Estate DESC ,A.Letter*/


$stuffClientList = array();
$selected = "";
$selectedDevCompany = "";
$index = 0;$selected = false;
$sqlStuffClient = mysql_query("select CompanyId,Forshort from $DataIn.trade_object 
						       where Estate>0 and ObjectSign in (1,2)");

while ($sqlStuffClientRow = mysql_fetch_assoc($sqlStuffClient)) {
	
	$CompanyId          = $sqlStuffClientRow["CompanyId"];
	$Forshort        = $sqlStuffClientRow["Forshort"];
	$stuffClientList[] = array("headImage"=>"",
							   "title"    =>"$Forshort",
							   "Id"       =>"$CompanyId",
							   "CellType" =>"2",
							   "selected" =>"0",
							   "infos"    =>""
							   ); 
		if ($selected==false && $isModifing==true && $CompanyId == $DevelopCompany) {
			$selectedDevCompany = "$index";
			$selected = false;
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
					 "StuffType"=>$stuffTypeList,
					 "StuffUnit"=>$stuffUnitList,
					 "StuffProp"=>$stuffPropTypeList,
					 "StuffProvider"=>$stuffProviderList,
					 "Weeks"=>$weekList,
					 "Client"=>$stuffClientList,
					 "ShowHiddenField"=>array("TriggerIndex"=>"0",
					 							 "Id"=>array("9040"),
												 "HiddenIndex"=>array("3","4")),
					 "ShowHiddenField2"=>array("TriggerIndex"=>"6",
					 							 "Id"=>array("11"),
												 "HiddenIndex"=>array("9","8")),
					"EditInfo"=>$editInfoList
					 );
 
?>