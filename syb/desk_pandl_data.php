<?php 
//电信
//代码共享-EWEN 2012-08-19
//非BOM费用初始化
$checkNONBOMSql=mysql_query("SELECT concat('NONBOM',Id) AS Name FROM $DataPublic.nonbom1_maintype ORDER BY Id",$link_id);
if($checkNONBOMRow=mysql_fetch_array($checkNONBOMSql)){
	do{
		$NameA=$checkNONBOMRow["Name"]."_A";$NameY=$checkNONBOMRow["Name"]."_Y";$NameW=$checkNONBOMRow["Name"]."_W";
		$TempNONBOMA=strval($NameA); 
		$TempNONBOMY=strval($NameY);
		$TempNONBOMW=strval($NameW);
		$$TempNONBOMA=0;
		$$TempNONBOMY=0;
		$$TempNONBOMW=0;
		}while($checkNONBOMRow=mysql_fetch_array($checkNONBOMSql));
	}
//行政费用初始化
$checkHZDSql=mysql_query("SELECT concat('HZ',S.TypeId) AS Name FROM $DataPublic.adminitype S WHERE 1 ORDER BY S.TypeId",$link_id);
if($checkHZDRow=mysql_fetch_array($checkHZDSql)){
	do{
		$NameA=$checkHZDRow["Name"]."_A";$NameY=$checkHZDRow["Name"]."_Y";$NameW=$checkHZDRow["Name"]."_W";
		$TempHZA=strval($NameA); $TempHZY=strval($NameY);$TempHZW=strval($NameW);
		$$TempHZA=0;$$TempHZY=0;$$TempHZW=0;
		}while($checkHZDRow=mysql_fetch_array($checkHZDSql));
	}
//其他收入初始化 EWEN 2012-09-15
$checkOTDSql=mysql_query("SELECT concat('QTSR',Id,'_Y') AS Name,concat('QTSR',Id,'_W') AS Name1 FROM $DataPublic.cw4_otherintype WHERE 1 ORDER BY Id",$link_id);
if($checkOTDRow=mysql_fetch_array($checkOTDSql)){
	do{
		$NameY=$checkOTDRow["Name"];
		$TempOTY=strval($NameY);
		$$TempOTY=0;
		$NameY1=$checkOTDRow["Name1"];
		$TempOTY1=strval($NameY1);
		$$TempOTY1=0;
		}while($checkOTDRow=mysql_fetch_array($checkOTDSql));
	}
/*//开发费用初始化 并入非BOM其他项目
$checkKFSql=mysql_query("SELECT concat('KF',Id) AS Name FROM $DataPublic.kftypedata WHERE 1 ORDER BY Id",$link_id);
if($checkKFRow=mysql_fetch_array($checkKFSql)){
	do{
		$NameY=$checkKFRow["Name"]."_Y";$NameW=$checkKFRow["Name"]."_W";
		$TempKFY=strval($NameY); $TempKFW=strval($NameW);
		$$TempKFY=0;
		$$TempKFW=0;
		}while($checkKFRow=mysql_fetch_array($checkKFSql));
	}*/

//行政费用:已结付
$checkHZSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,concat('HZ',M.TypeId,'_Y') AS Name FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND M.Date>='2008-07-01' $TempDatetj AND M.Estate=0 GROUP BY M.TypeId ORDER BY M.TypeId",$link_id);
if($checkHZRow=mysql_fetch_array($checkHZSql)){
	do{
		$Amount=sprintf("%.0f",$checkHZRow["Amount"]);
		$Name=$checkHZRow["Name"];
		$TempHZ=strval($Name); 
		$$TempHZ=$Amount;
		}while($checkHZRow=mysql_fetch_array($checkHZSql));
	}
//行政费用:未结付
$checkHZSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,concat('HZ',M.TypeId,'_W') AS Name FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND M.Date>='2008-07-01' $TempDatetj AND M.Estate=3  GROUP BY M.TypeId ORDER BY M.TypeId",$link_id);
if($checkHZRow=mysql_fetch_array($checkHZSql)){
	do{
		$Amount=sprintf("%.0f",$checkHZRow["Amount"]);
		$Name=$checkHZRow["Name"];
		$TempHZ=strval($Name); 
		$$TempHZ=$Amount;
		}while($checkHZRow=mysql_fetch_array($checkHZSql));
	}
//全部行政费用
$checkHZSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,concat('HZ',M.TypeId,'_A') AS Name FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 $TempDatetj AND (M.Estate=3 OR M.Estate=0)  GROUP BY M.TypeId ORDER BY M.TypeId",$link_id);
if($checkHZRow=mysql_fetch_array($checkHZSql)){
	do{
		$Amount=sprintf("%.0f",$checkHZRow["Amount"]);
		$Name=$checkHZRow["Name"];
		$TempHZ=strval($Name); 
		$$TempHZ=$Amount;
		}while($checkHZRow=mysql_fetch_array($checkHZSql));
	}
	

$checkSortSql=mysql_query("SELECT Id FROM $DataPublic.sys8_pandlmain WHERE Estate='1' ORDER BY SortId",$link_id);
while($checkSortRow=mysql_fetch_array($checkSortSql)){
	include "subprogram/new_desk_syb_".$checkSortRow["Id"].".php";
	//echo "new_desk_syb_".$checkSortRow["Id"].".php";
	}

if($Subscript>0){//月份才计算预估费用
	include "subprogram/new_desk_syb_11.php";
	}
?>
