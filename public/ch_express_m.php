<?php 
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=17;
$tableMenuS=600;
$sumCols="8";		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 快递费用列表");
$funFrom="ch_express";
$Th_Col="选项|50|序号|40|所属公司|60|寄件日期|80|快递公司|80|提单号码|100|件数|40|重量|50|金额|60|寄/到付|60|经手人|60|备注|250|状态|40|操作|60";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="17,15";
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";

if($From!="slist"){
	$pResult = mysql_query("SELECT P.CompanyId,P.Forshort  
		FROM $DataIn.ch9_expsheet F  
		LEFT JOIN $DataPublic.freightdata P ON P.CompanyId=F.CompanyId WHERE 1 AND F.Estate='2' GROUP BY P.CompanyId ORDER BY P.CompanyId",$link_id);
		if($pRow = mysql_fetch_array($pResult)){
			echo"<select name='CompanyId' id='CompanyId' onchange='document.form1.submit()'>";
			echo"<option value='' selected>全部</option>";
			do{
				$Forshort=$pRow["Forshort"];
				$thisCompanyId=$pRow["CompanyId"];
				if($CompanyId==$thisCompanyId){
					echo"<option value='$thisCompanyId' selected>$Forshort </option>";
					$SearchRows.=" and E.CompanyId='$thisCompanyId'";
					}
				else{
					echo"<option value='$thisCompanyId'>$Forshort</option>";
					}
				}while($pRow = mysql_fetch_array($pResult));
			echo"</select>&nbsp;";
			}
		}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//步骤4：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
E.Id,E.Mid,E.Date,E.ExpressNO,E.BoxQty,E.Weight,E.Amount,E.Type,E.Operator,E.Remark,
E.Estate,E.Locks,P.Name AS HandledBy,D.Forshort,E.cSign 
FROM $DataIn.ch9_expsheet E
LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=E.CompanyId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=E.HandledBy
WHERE 1 $SearchRows AND E.Estate='2'
ORDER BY E.Date DESC,E.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d1=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$Forshort=$myRow["Forshort"];
		$ExpressNO=$myRow["ExpressNO"];
		$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
		$ExpressNO=$ExpressNO==""?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";
		$BoxQty=$myRow["BoxQty"];
		$Weight=$myRow["Weight"];
		$Amount=$myRow["Amount"];
		$Type=$myRow["Type"]==1?"到付":"寄付";		
		$HandledBy=$myRow["HandledBy"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$Estate=$myRow["Estate"];
		switch($Estate){
			case "1":
				$Estate="<div align='center' class='redB' title='未处理'>×</div>";
				break;
			case "2":
				$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
				break;
			case "3":
				$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
				break;
			case "0":
				$Estate="<div align='center' class='greenB' title='已结付'>√</div>";
				break;
			}
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$ValueArray=array(
		    array(0=>$cSign,    1=>"align='center'"),
			array(0=>$Date,		1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$ExpressNO),
			array(0=>$BoxQty,	1=>"align='center'"),
			array(0=>$Weight,	1=>"align='right'"),
			array(0=>$Amount,	1=>"align='right'"),
			array(0=>$Type,		1=>"align='center'"),
			array(0=>$HandledBy,1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'"),
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