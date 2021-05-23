<?php 
/*
2015-04-15 19:03:13.527 DailyManagement[2235:462871] Price=2

2015-04-15 19:03:13.527 DailyManagement[2235:462871] StuffType=9125

2015-04-15 19:03:13.528 DailyManagement[2235:462871] Spec=

2015-04-15 19:03:13.529 DailyManagement[2235:462871] Weight=

2015-04-15 19:03:13.530 DailyManagement[2235:462871] DevelopState=0

2015-04-15 19:03:13.530 DailyManagement[2235:462871] StuffName=testname

2015-04-15 19:03:13.531 DailyManagement[2235:462871] Unit=20

2015-04-15 19:03:13.531 DailyManagement[2235:462871] CompanyId=2001

2015-04-15 19:03:13.532 DailyManagement[2235:462871] Property=2
*/
$formList = array();

$TypeId = $info[0];
$needSource = $info[1];
$checkResult=mysql_fetch_array(mysql_query("SELECT T.BuyerId,P1.name as BuyName
 ,T.DevelopGroupId,GP.GroupName,T.DevelopNumber,P2.Name as DevName,T.ForcePicSign,
 T.Position,M.CheckSign  FROM $DataIn.StuffType T 
LEFT JOIN $DataIn.base_mposition M ON M.Id=T.Position 
left join $DataPublic.staffmain P1 on P1.Number=T.BuyerId
left join $DataPublic.staffmain P2 on P2.Number=T.DevelopNumber
left join $DataIn.staffgroup  GP on GP.GroupId=P2.GroupId
WHERE T.TypeId='$TypeId' LIMIT 1",$link_id));
$BuyerId=$checkResult["BuyerId"];
$DevelopGroupId=$checkResult["DevelopGroupId"];
$DevelopNumber=$checkResult["DevelopNumber"];
$SendFloor=$checkResult["Position"];
$CheckSign=$checkResult["CheckSign"];
$BuyName=$checkResult["BuyName"];
$GroupName=$checkResult["GroupName"];
$DevName=$checkResult["DevName"];

$SendFloor=$SendFloor==""?0:$SendFloor;
include "../../model/subprogram/stuff_GetFloor.php";
$CheckSign=$CheckSign==""?0:$CheckSign;
$DevelopGroupId=$DevelopGroupId==""?0:$DevelopGroupId;
$DevelopNumber=$DevelopNumber==""?0:$DevelopNumber;

$Pjobid=	$GicJobid=$DevelopGroupId;
$PicNumber=	$GicNumber=$DevelopNumber;

$GCField=array(-1,-1);	//系统默认
$ForcePicSpe = -1;
$GCjobid=$GCField[0];
$GcheckNumber=$GCField[1];


$jhDays=0;

$StuffEname="";$BoxPcs ="0";$Remark="";
/*
NSString *const Key_Dict_EditType    = @"EditType";
NSString *const Key_Dict_PlaceHolder = @"PlaceHolder";
NSString *const Key_Dict_FiledName   = @"FiledName";
NSString *const Key_Dict_FieldKey    = @"FieldKey";
NSString *const Key_Dict_FieldVal    = @"FieldVal";
NSString *const Key_Dict_Content     = @"ContentTxt";


*/$ForcePicSign = $checkResult["ForcePicSign"];
	switch($ForcePicSign){
			case 0: 
				$ForcePicSign="--";
			break;
			case 1: 
				$ForcePicSign="图片";
			break;
			case 2: 
				$ForcePicSign="图档";
			break;
			case 3: 
				$ForcePicSign="图片/图档";
			break;
			case 4: 
				$ForcePicSign="强行锁定";
			break;			
		}	
 switch($CheckSign){
                  case "0":$CheckSign="抽检";break;
                  case "1":$CheckSign="全检";break;
                  case "99":$CheckSign="--";break;
              }

$FormNames = array("下单需求","开发负责人","图档审核","采      购","送货楼层","品检方式");
$FormTexts = array("$ForcePicSign","$GroupName-$DevName","","$BuyName","$SendFloor","$CheckSign");

$countA = 6;
for ($i=0; $i < $countA; $i ++) {
	$formList[] = array("EditType"=>"6","PlaceHolder"=>"系统默认","FiledName"=>$FormNames[$i],"FieldKey"=>"-","FieldVal"=>"","ContentTxt"=>$FormTexts[$i]);
}

$searchSourceList = array();
if ($needSource == "1") {
$searchSourceSql = mysql_query("SELECT A.Id,A.GoodsId,A.GoodsName,A.Price,F.PreChar,D.Forshort
FROM $DataPublic.nonbom4_goodsdata A
LEFT JOIN $DataPublic.nonbom2_subtype B  ON B.Id=A.TypeId
LEFT JOIN $DataPublic.nonbom5_goodsstock C ON C.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom3_retailermain D ON D.CompanyId=C.CompanyId
LEFT JOIN $DataPublic.currencydata F ON F.Id=D.Currency
WHERE 1  AND B.Id in (22,23,24,25) AND A.Estate>0");
while ($searchSourceRow = mysql_fetch_assoc($searchSourceSql)) {
	$GoodsId = $searchSourceRow["GoodsId"];
	$GoodsName = $searchSourceRow["GoodsName"];
	$Price = $searchSourceRow["Price"];
	$PreChar = $searchSourceRow["PreChar"];
	$Forshort = $searchSourceRow["Forshort"];
	$Price = $PreChar.$Price;
	
	$searchSourceList[]= array("EditType"=>"1","PlaceHolder"=>"$Price","FiledName"=>"$GoodsId-$GoodsName","FieldKey"=>"nonbom","FieldVal"=>"$GoodsId","ContentTxt"=>"$Forshort");
	
}
$jsonArray = array("form"=>$formList,"searchSource"=>$searchSourceList);
} else {
$jsonArray = array("form"=>$formList);	
}


?>
