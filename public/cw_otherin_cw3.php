<?php 
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
include "../model/subprogram/read_model_3.php";
//过滤条件
if($From!="slist"){
	$SearchRows=" and S.Estate='3'";
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' selected>未结付货款</option><option value='0' >已结付货款</option></select>&nbsp;";		
	//月份
	$MonthResult = mysql_query("SELECT DATE_FORMAT(S.payDate,'%Y-%m') AS Month FROM $DataIn.cw4_otherinsheet S WHERE 1 $SearchRows GROUP BY DATE_FORMAT(S.payDate,'%Y-%m') ORDER BY DATE_FORMAT(S.payDate,'%Y-%m') DESC",$link_id);
	if ($MonthRow = mysql_fetch_array($MonthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$MonthValue=$MonthRow["Month"];
			$chooseMonth=$chooseMonth==""?$MonthValue:$chooseMonth;
			if($chooseMonth==$MonthValue){
				echo"<option value='$MonthValue' selected>$MonthValue</option>";
				$SearchRows.=" and DATE_FORMAT(S.payDate,'%Y-%m')='$MonthValue'";
				}
			else{
				echo"<option value='$MonthValue'>$MonthValue</option>";					
				}
			}while($MonthRow = mysql_fetch_array($MonthResult));
		echo"</select>&nbsp;";
		}
	else{
		//无月份记录
		$SearchRows.=" and M.Month='无效'";
		}

	}
else{
	$SearchRows.=" AND S.Estate='3'";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//结付的银行
include "../model/selectbank1.php";
echo"$CencalSstr";

//步骤4：
include "../model/subprogram/read_model_5.php";
//步骤5：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.getmoneyNO, S.Amount, C.Symbol AS Currency, S.payDate, S.Remark, S.Estate, S.Locks, S.Operator,T.Name AS TypeName
 	FROM $DataIn.cw4_otherinsheet S 
   LEFT JOIN $DataPublic.cw4_otherintype T ON T.Id=S.TypeId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE 1 $SearchRows ";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myResult  && $myRow = mysql_fetch_array($myResult)){
	$d1=anmaIn("download/otherin/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$Id=$myRow["Id"];
		$getmoneyNO=$myRow["getmoneyNO"];
		$Amount=$myRow["Amount"];
		$Currency=$myRow["Currency"];	
		$payDate=$myRow["payDate"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];		
		$Estate=$myRow["Estate"];		
		$TypeName=$myRow["TypeName"];	
		$Estate="<div class='redB'>未结付</div>";
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";   
		$f1=anmaIn($getmoneyNO,$SinkOrder,$motherSTR);
		$getmoneyNO="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">收款单$getmoneyNO</a>";
		$showPurchaseorder="<img onClick='sOrhOtherIn(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏明细.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
	$Locks=1;
		$ValueArray=array(
			array(0=>$TypeName,1=>"align='center'"),
			array(0=>$payDate,1=>"align='center'"),
			array(0=>$getmoneyNO,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Currency,1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	    }
else{
	    noRowInfo($tableWidth);
     	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
if ($myResult )  $RecordToTal= mysql_num_rows($myResult); else $RecordToTal=0;
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>