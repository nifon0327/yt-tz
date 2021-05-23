<?php 
//ewen 2013-03-18 OK
include "../model/modelhead.php";
//步骤2：需处理
$tableMenuS=500;
$sumCols="5";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 非BOM采购预付订金审核");
$funFrom="nonbom11";
$Th_Col="选项|40|序号|40|供应商|80|采购单号|60|预付说明|400|凭证|60|预付金额|60|货币|40|状态|40|请款人|50|请款日期|75";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="17,15";
$TempEstateSTR="EstateSTR".strval($Estate); 
$$TempEstateSTR="selected";
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件
$SearchRows="AND A.Estate=2";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
$TitlePre="<br>&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.Mid,A.CompanyId,A.PurchaseID,A.Amount,A.Remark,A.Date,A.Estate,A.Locks,A.Operator,A.ContractFile,
		B.Id AS cgMid,B.Date AS cgDate,
		C.Forshort,D.Symbol
 	FROM $DataIn.nonbom11_djsheet A 
	LEFT JOIN $DataIn.nonbom6_cgmain B ON B.PurchaseID=A.PurchaseID 
	LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=A.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE 1 $SearchRows ORDER BY A.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
        $Dir=anmaIn("download/nonbomht/",$SinkOrder,$motherSTR);
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
		$Locks=$myRow["Locks"];		
 		$Estate=$myRow["Estate"];
		$Estate="<div align='center' class='yellowB'>请款中</div>";
		
		$cgMid=$myRow["cgMid"];
		$cgMidSTR=anmaIn($cgMid,$SinkOrder,$motherSTR);
		$PurchaseID="<a href='nonbom6_view.php?f=$cgMidSTR' target='_blank'>$PurchaseID</a>";
		$CompanyId=$myRow["CompanyId"];
		$CompanyId=anmaIn($CompanyId,$SinkOrder,$motherSTR);		
		$Forshort="<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
		
		$ContractFile=$myRow["ContractFile"];
		if($ContractFile==1){
			$ContractFile="C".$Id.".jpg";
			$ContractFile=anmaIn($ContractFile,$SinkOrder,$motherSTR);
			$ContractFile="<a href=\"../admin/openorload.php?d=$Dir&f=$ContractFile&Type=&Action=6\" target=\"download\">View</a>";
			}
		 else{
              $ContractFile="&nbsp;";
             }
             
		//财务强制锁:非未处理皆锁定
		$ValueArray=array(
			array(0=>$Forshort,1=>"align='center'"),
			array(0=>$PurchaseID,1=>"align='center'"),				
			array(0=>$Remark,3=>"..."),
			array(0=>$ContractFile,1=> "align='center'"),
			array(0=>$Amount,1=> "align='center'"),
			array(0=>$Symbol,1=>"align='center'"),
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