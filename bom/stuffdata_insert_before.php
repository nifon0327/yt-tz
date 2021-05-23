<?php
$checkResult=mysql_fetch_array(mysql_query("SELECT T.BuyerId,T.DevelopGroupId,T.DevelopNumber,T.Position,M.CheckSign  FROM $DataIn.StuffType T
        LEFT JOIN $DataIn.base_mposition M ON M.Id=T.Position WHERE T.TypeId='$TypeId' LIMIT 1", $link_id));
$DevelopGroupId=$checkResult["DevelopGroupId"];
$DevelopNumber=$checkResult["DevelopNumber"];
//$SendFloor=$checkResult["Position"];
//$CheckSign=$checkResult["CheckSign"];

$SendFloor=$SendFloor==""?0:$SendFloor;
$CheckSign=$CheckSign==""?0:$CheckSign;
$DevelopGroupId=$DevelopGroupId==""?0:$DevelopGroupId;
$DevelopNumber=$DevelopNumber==""?0:$DevelopNumber;

$Pjobid=	$GicJobid=$DevelopGroupId;
$PicNumber=	$GicNumber=$DevelopNumber;

//添加默认
$GcheckNumber = "-1|-1"; //系统默认

$GCField=explode("|",$GcheckNumber);
$GCjobid=$GCField[0];
$GcheckNumber=$GCField[1];

$jhDays=is_numeric($jhDays)?$jhDays:0;
$jhDays=0;

//计算配件成本价，采购价格/(1+增值税率)
$checkTaxRow = mysql_fetch_array(mysql_query("SELECT T.Value FROM $DataIn.providersheet P
        LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
        WHERE P.CompanyId = '$CompanyId'",$link_id));
$AddValue = $checkTaxRow["Value"];
$AddValue=$AddValue==""?0:$AddValue;
$CostPrice = 0;
$NoTaxPrice = $NoTaxPrice==""?"0.000":$NoTaxPrice;
$PriceDetermined = $PriceDetermined ==""?0:$PriceDetermined;
