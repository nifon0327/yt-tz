<?php 
//电信-zxq 2012-08-01
//MC、DP共享代码
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数	
$ColsNumber=11;				
$tableMenuS=750;
ChangeWtitle("$SubCompany 现金流水帐");
$sumCols="5";			//求和列,需处理
$funFrom="cw_cashblotter";
$nowWebPage=$funFrom."_read";

$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ShowType=$ShowType==""?1:$ShowType;
$otherAction=$ShowType==3?"银行现金结余统计":"现金流水帐列表";
$ChooseMonth=$ChooseMonth==""?date("Y-m"):$ChooseMonth;
$PreMonthS="2016-01-01";
$PreMonthE=$ChooseMonth."-01";

$MonthSum=(date("Y")-2016)*12+date("n")+5;
if($ShowType==2){
	$tableWidth=1110;
	}

//步骤3：
echo"<body onkeydown='unUseKey()' oncontextmenu='event.returnValue=false' onhelp='return false;'>
<form name='form1' id='checkFrom' enctype='multipart/form-data' method='post' action=''>
<input name='funFrom' type='hidden' id='funFrom' value='$funFrom'>
<input name='fromWebPage' type='hidden' id='fromWebPage' value='$nowWebPage'>
<input name='From' type='hidden' id='From' value='$From'>";
//显示方式
	$selectedStr="strType".$ShowType;
	$$selectedStr="selected";
	echo "<select name='ShowType' id='ShowType' onchange='document.form1.submit()'>";
	echo "<option value='1' $strType1>货币</option>";
	echo "<option value='2' $strType2>银行</option>";
	echo "<option value='3' $strType3>结余统计</option>";
	echo "</select>";
if($ShowType<3){
	if($ShowType==2){//结付的银行
		echo"<select name='BankId' id='BankId' onchange='document.form1.submit()'>";
		$checkBankSql=mysql_query("SELECT Id,Title FROM $DataPublic.my2_bankinfo WHERE Estate=1  ORDER BY Id",$link_id);//AND cSign='$Login_cSign'
		if($checkBankRow=mysql_fetch_array($checkBankSql)){
			$i=1;
			do{
				$theBankId=$checkBankRow["Id"];
				$BankTitle=$checkBankRow["Title"];
				$BankId=$BankId==""?$theBankId:$BankId;
				if($theBankId==$BankId){
					echo "<option value='$theBankId' selected>$i - $BankTitle</option>";
					}
				else{
					echo "<option value='$theBankId'>$i - $BankTitle</option>";
					}
				$i++;
				}while($checkBankRow=mysql_fetch_array($checkBankSql));
			}
			echo"</select>";
		}
	else{
		echo"<select name='Currency' id='Currency' onchange='document.form1.submit()'>";
		$checkCurrency=mysql_query("SELECT Id,Symbol FROM $DataPublic.currencydata WHERE Estate=1 AND Id<6 ORDER BY Id",$link_id);
		if($CurrencyRow=mysql_fetch_array($checkCurrency)){
			do{
				$Id=$CurrencyRow["Id"];
				$Symbol=$CurrencyRow["Symbol"];
				$Currency=$Currency==""?$Id:$Currency;
				if($Currency==$Id){
					echo"<option value='$Id' selected>$Symbol</option>";
					}
				else{
					echo"<option value='$Id'>$Symbol</option>";
					}
				}while ($CurrencyRow=mysql_fetch_array($checkCurrency));
			}
		echo"</select>&nbsp;";
		}
	//月份
	echo"<select name='ChooseMonth' id='ChooseMonth' onchange='document.form1.submit()'>";
	for($i=0;$i<=$MonthSum;$i++){
		$dateValue=date("Y-m",strtotime("$i month",strtotime($PreMonthS)));
		$dateStr=date("Y年m月",strtotime("$i month",strtotime($PreMonthS)));
		if($ChooseMonth==$dateValue){
			echo "<option value='$dateValue' selected>$dateStr</option>";
			}
		else{
			echo "<option value='$dateValue'>$dateStr</option>";					
			}
		}
	echo "</select>&nbsp;";
	}
include "cw_cashblotter_".$ShowType.".php";
//echo "cw_cashblotter_".$ShowType.".php";
?>
<script language="JavaScript" type="text/JavaScript">
function ShowSheet(Row){//隐藏行ID,隐藏行DIV,项目ID
 ShowDiv=eval("DivShow"+Row);//DivShow
 ShowTr=eval("TrShow"+Row);//TrShow
ShowValue =eval("Remark"+Row);//Remark
TempValue=ShowValue.value;
 ShowTr.style.display=(ShowTr.style.display=="none")?"":"none";
 ShowDiv.style.display=(ShowDiv.style.display=="none")?"":"none";
var url="cw_cashblotter_1_ajax.php?TempValue="+TempValue;
 var ajax=InitAjax();
 ajax.open("GET",url,true);
 ajax.onreadystatechange =function(){
 　　if(ajax.readyState==4 && ajax.status ==200 && ajax.responseText!=""){
 　　　 var BackData=ajax.responseText;
   ShowDiv.innerHTML=BackData;
   }
  }
 ajax.send(null);
 }
 </script>