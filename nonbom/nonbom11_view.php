<?php 
//ewen 2013-03-18 OK
include "../model/modelhead.php";
//步骤2：需处理
$tableMenuS=500;

$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 预付订金列表");

$Th_Col="选项|40|序号|40|抵付日期|80|结付日期|80|供应商|80|采购单号|60|预付说明|400|货币|40|预付金额|60|请款人|50|请款日期|75";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
include "../model/subprogram/read_model_3.php";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.Mid,A.Did,A.CompanyId,A.PurchaseID,A.Amount,A.Remark,A.Date,A.Estate,A.Locks,A.Operator,
		B.Id AS cgMid,B.Date AS cgDate,
		C.Forshort,D.Symbol,E.PayDate AS dfDate,F.PayDate
 	FROM $DataIn.nonbom11_djsheet A 
	LEFT JOIN $DataIn.nonbom6_cgmain B ON B.PurchaseID=A.PurchaseID 
	LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=A.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	LEFT JOIN $DataIn.nonbom12_cwmain E ON E.Id=A.Did
	LEFT JOIN $DataIn.nonbom11_djmain F ON F.Id=A.Mid
	WHERE A.Did='$d' ORDER BY A.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$dfDate=$myRow["dfDate"];
		$PayDate=$myRow["PayDate"];
		$CompanyId=$myRow["CompanyId"];
		$Forshort=$myRow["Forshort"];
		$CompanyId=anmaIn($CompanyId,$SinkOrder,$motherSTR);		
		$Forshort="<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
			
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
		$cgMid=$myRow["cgMid"];
		$cgMidSTR=anmaIn($cgMid,$SinkOrder,$motherSTR);
		$PurchaseID="<a href='nonbom6_view.php?f=$cgMidSTR' target='_blank'>$PurchaseID</a>";
		switch($Estate){
				case "1":
					$Estate="<div align='center' class='redB'>未处理</div>";
					$LockRemark="";
					break;
				case "2":
					$Estate="<div align='center' class='yellowB'>请款中</div>";
					$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
					$Locks=0;
					break;
				case "3":
					$Estate="<div align='center' class='yellowB'>待结付</div>";
					$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
					$Locks=0;
					break;
				case "4":
					$Estate="<div align='center' class='redB'>审核退回</div>";
					$LockRemark="";
					break;
					break;
				case "0":
					$Estate="<div align='center' class='greenB'>已结付</div>";
					$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
					$Locks=0;
					break;
				}
		$Did=$myRow["Did"];
		if($Did==0){
			$Did="<span class='redB'>未抵付</span>";
			}
		else{
			//连接至抵付的结付单？？
			$Did="<span class='greenB'>已抵付</span>";
			$LockRemark="记录已经抵付，锁定操作！";
			}
		//财务强制锁:非未处理皆锁定
		$ValueArray=array(
			array(0=>$dfDate,1=>"align='center'"),
			array(0=>$PayDate,1=>"align='center'"),
			array(0=>$Forshort,1=>"align='center'"),
			array(0=>$PurchaseID,1=>"align='center'"),				
			array(0=>$Remark,3=>"..."),
			array(0=>$Symbol,1=>"align='center'"),
			array(0=>$Amount,1=> "align='right'"),
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'")
			);
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
?>