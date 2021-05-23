<?php 
//电信-zxq 2012-08-01
/*
MC、DP共享代码
*/
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//步骤3：
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.cw2_gyssksheet S WHERE 1 and S.Estate='$Estate' group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)){
		$MonthSelect.="<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			$FirstValue=$FirstValue==""?$dateValue:$FirstValue;
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			if($chooseMonth==$dateValue){
				$MonthSelect.="<option value='$dateValue' selected>$dateText</option>";
				$SearchRows="and  DATE_FORMAT(S.Date,'%Y-%m')='$dateValue'";
				}
			else{
				$MonthSelect.="<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		$SearchRows=$SearchRows==""?"and  DATE_FORMAT(S.Date,'%Y-%m')='$FirstValue'":$SearchRows;
		$MonthSelect.="</select>&nbsp;";
		}
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	echo $MonthSelect;
	$SearchRows.="and S.Estate=3";
	
		// BOM、非BOM分类
		 $TempEstateSTR="GysTypeSTR".strval($GysType); 
         $$TempEstateSTR="selected";

		echo"<select name='GysType' id='GysType' onchange='document.form1.submit()'>";
		echo"<option value='' $GysTypeSTR>全  部</option>";
		echo"<option value='1' $GysTypeSTR1>BOM</option>";
		echo"<option value='2' $GysTypeSTR2>非BOM</option>";
		echo"</select>&nbsp;";
		
		switch($GysType){
		   case 1: $SearchRows.=" and S.Remark NOT LIKE '%非BOM%'";break;
		   case 2: $SearchRows.=" and S.Remark  LIKE '%非BOM%'";break;
		   default:break;
		}

	}
else{
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//结付的银行
include "../model/selectbank1.php";
echo"$CencalSstr";//步骤4：
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.Mid,S.Forshort,S.PayMonth,S.InvoiceNUM,S.InvoiceFile,S.Amount,S.Remark,S.Date,S.Estate,S.Locks,S.Operator,C.Symbol
 	FROM $DataIn.cw2_gyssksheet S 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE 1 $SearchRows ORDER BY S.Date,S.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$CompanyId=$myRow["CompanyId"];
		$Forshort=$myRow["Forshort"];
		$Symbol=$myRow["Symbol"];
		$Amount=$myRow["Amount"];
		$PayMonth=$myRow["PayMonth"];
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$InvoiceNUM=$myRow["InvoiceNUM"];
		$InvoiceFile=$myRow["InvoiceFile"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];		
 		$Estate=$myRow["Estate"];
		//有更新权限则解锁
		if($Keys & mUPDATE){
			$Locks=1;
			}
		else{
			$Locks=0;
			}	
		switch($Estate){
			case "3":
				$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
				break;
			default:
				$Estate="<div align='center' class='redB' title='状态错误'>×</div>";
				$LockRemark="状态错误";
				$Locks=0;
				break;
			}
		if($InvoiceFile==1){
			$InvoiceFile="S".$Id;
			$Dir=anmaIn("download/cwgyssk/",$SinkOrder,$motherSTR);
			$InvoiceFile=anmaIn($InvoiceFile,$SinkOrder,$motherSTR);
			//$InvoiceNUM="<span onClick='OpenOrLoad(\"$Dir\",\"$InvoiceFile\",7)' style='CURSOR: pointer;color:#FF6633'>$InvoiceNUM</span>";
		$InvoiceNUM="<a href=\"openorload.php?d=$Dir&f=$InvoiceFile&Type=&Action=7\" target=\"download\">$InvoiceNUM</a>";
			}
		//财务强制锁:非未处理皆锁定
		$URL="nonbom6_relation_ajax.php";
		//$URL="test.php";
        $theParam="Id=$Id";
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"nonbom\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏关联非BOM采购单.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
			
		$ValueArray=array(
			array(0=>$Date,1=>"align='center'"),
			array(0=>$PayMonth,1=>"align='center'"),
			array(0=>$Forshort,1=>"align='center'"),
			array(0=>$Symbol,1=>"align='center'"),				
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Remark,3=>"..."),
			array(0=>$InvoiceNUM,1=>"align='center'"),
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
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>