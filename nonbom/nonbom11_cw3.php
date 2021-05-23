<?php 
//ewen 2013-03-18 OK
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
include "../model/subprogram/read_model_3.php";
//过滤条件
if($From!="slist"){
	$SearchRows=" AND A.Estate='3'";
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' selected>未支付订金</option><option value='0' >已支付订金</option></select>&nbsp;";		
	//月份
	$monthResult = mysql_query("SELECT DATE_FORMAT(A.Date,'%Y-%m') AS Month FROM $DataIn.nonbom11_djsheet A WHERE 1 $SearchRows GROUP BY  DATE_FORMAT(A.Date,'%Y-%m') ORDER BY A.Date DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=$monthRow["Month"];
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" AND DATE_FORMAT(A.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		echo"</select>&nbsp;";
		}
	else{
		//无月份记录
		//$SearchRows.=" and M.Month='无效'";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//结付的银行
include "../model/selectbank1.php";
echo"$CencalSstr";
//步骤4：
$TitlePre="<br>&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
include "../model/subprogram/read_model_5.php";
//步骤5：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.Mid,A.CompanyId,A.PurchaseID,A.Amount,A.Remark,A.Date,A.Estate,A.Locks,A.Operator,
		B.Id AS cgMid,B.Date AS cgDate,
		C.Forshort,D.Symbol
 	FROM $DataIn.nonbom11_djsheet A 
	LEFT JOIN $DataIn.nonbom6_cgmain B ON B.PurchaseID=A.PurchaseID 
	LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=A.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE 1 $SearchRows ORDER BY A.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Forshort=$myRow["Forshort"];
		$PurchaseID=$myRow["PurchaseID"];
		$cgDate=$myRow["cgDate"];
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$Amount=$myRow["Amount"];
		$Symbol=$myRow["Symbol"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Date=$myRow["Date"];
		$Locks=1;		
 		$Estate=$myRow["Estate"];
		$cgMid=$myRow["cgMid"];
		$cgMidSTR=anmaIn($cgMid,$SinkOrder,$motherSTR);
		$PurchaseID="<a href='nonbom6_view.php?f=$cgMidSTR' target='_blank'>$PurchaseID</a>";
		$CompanyId=$myRow["CompanyId"];
		$CompanyId=anmaIn($CompanyId,$SinkOrder,$motherSTR);		
		$Forshort="<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
		$Estate="<div align='center' class='yellowB'>待结付</div>";
		//财务强制锁:非未处理皆锁定
		$ValueArray=array(
			array(0=>$Forshort,1=>"align='center'"),
			array(0=>$PurchaseID,1=>"align='center'"),				
			array(0=>$Remark,3=>"..."),
			array(0=>$Symbol,1=>"align='center'"),
			array(0=>$Amount,1=> "align='right'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
