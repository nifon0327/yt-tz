<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw2_fkdjsheet
$DataIn.trade_object
$DataPublic.currencydata
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=500;
$sumCols="4";		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 预付订金待审核列表");
$funFrom="cw_fkdj";
$Th_Col="选项|40|序号|40|供应商|80|采购单号|60|预付说明|400|预付金额|60|分类|80|状态|40|请款人|50|请款日期|75";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="17,15";
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件
if($From!="slist"){	
	$SearchRows="and S.Estate=2";
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.cw2_fkdjsheet S WHERE 1 $SearchRows group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateText</option>";
				$SearchRows.=" and DATE_FORMAT(S.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		echo"</select>&nbsp;";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.TypeId,S.CompanyId,S.Amount,S.Remark,S.Date,S.Estate,S.Locks,S.Operator,M.Id as Mid,M.PurchaseID,
		P.Forshort,C.Symbol
 	FROM $DataIn.cw2_fkdjsheet S 
	LEFT JOIN $DataIn.cg1_stockmain M ON M.PurchaseID=S.PurchaseID 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	WHERE 1 $SearchRows order by S.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Forshort=$myRow["Forshort"];
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$Amount=$myRow["Amount"];
		$TypeId=$myRow["TypeId"];
		$Type=$TypeId==1?"订金":($TypeId==2?"多付平衡帐":"少付平衡帐");
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Date=$myRow["Date"];
		$PurchaseID=$myRow["PurchaseID"];
		if($PurchaseID!=""){
			$Mid=$myRow["Mid"];
			$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
			$PurchaseIDStr="<a href='../model/subprogram/purchaseorder_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";  //显示采购单
		}
		else {
			$PurchaseIDStr="&nbsp;";
		}
		$Locks=$myRow["Locks"];		
		$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
		$Locks=1;
		//财务强制锁:非未处理皆锁定
		$ValueArray=array(
			array(0=>$Forshort,
					 1=>"align='center'"),
			array(0=>$PurchaseIDStr,
					 1=>"align='center'"),			
			array(0=>$Remark,
					 3=>"..."),
			array(0=>$Amount,
					 1=>"align='center'"),
			array(0=>$Type,					
					  1=>"align='center'"),
			array(0=>$Estate,
					 1=>"align='center'"),
			array(0=>$Operator,
					 1=>"align='center'"),
			array(0=>$Date,
					 1=>"align='center'")
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