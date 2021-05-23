<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw2_fkdjsheet
$DataIn.trade_object
$DataPublic.currencydata
二合一已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=500;
$sumCols="5";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 预付订金列表");
$funFrom="cw_fkdj";
//$Th_Col="选项|40|序号|40|供应商|80|采购单号|60|采购流水号|100|预付说明|400|预付金额|60|货币|40|分类|80|状态|40|请款人|50|请款日期|75";
$Th_Col="选项|40|序号|40|供应商|80|采购单号|60|预付说明|400|预付金额|60|货币|40|分类|80|状态|40|请款人|50|请款日期|75";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,14,4,7,8";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消结付,16审核通过，17结付
$TempEstateSTR="EstateSTR".strval($Estate); 
$$TempEstateSTR="selected";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件
if($From!="slist"){	
	$SearchRows=$Estate==""?"":"and S.Estate=$Estate";
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.cw2_fkdjsheet S group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
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
	//结付状态
	$EstateResult = mysql_query("SELECT S.Estate FROM $DataIn.cw2_fkdjsheet S WHERE 1 $SearchRows GROUP BY S.Estate ORDER BY S.Estate DESC",$link_id);
	if($EstateRow = mysql_fetch_array($EstateResult)) {
		echo"<select name='Estate' id='Estate' onchange='document.form1.submit()'>";
		echo"<option value='' $EstateSTR>全  部</option>";
		do{
			$Estate=$EstateRow["Estate"];
			
			switch($Estate){
				case "0":
					echo"<option value='0' $EstateSTR0>已结付</option>";
				break;
				case "1":
					echo"<option value='1' $EstateSTR1>未处理</option>";
				break;
				case "2":
					echo"<option value='2' $EstateSTR2>请款中</option>";
				break;
				case "3":
					echo"<option value='3' $EstateSTR3>请款通过</option>";
				break;
				}
			}while($EstateRow = mysql_fetch_array($EstateResult));
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
//echo "$mySql"; 	
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Forshort=$myRow["Forshort"];
		//$StockId=$myRow["StockId"]==""?"&nbsp;":$myRow["StockId"];
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$Symbol=$myRow["Symbol"];
		$Amount=$myRow["Amount"];
		$TypeId=$myRow["TypeId"];
		$Type=$TypeId==1?"订金":($TypeId==2?"多付平衡帐":"少付平衡帐");
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];		
		
		$PurchaseID=$myRow["PurchaseID"];
		if($PurchaseID!=""){
			$Mid=$myRow["Mid"];
			$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
			$PurchaseIDStr="<a href='../model/subprogram/purchaseorder_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";  //显示采购单
		}
		else {
			$PurchaseIDStr="&nbsp;";
		}
		
 			$Estate=$myRow["Estate"];

			switch($Estate){
				case "1":
					$Estate="<div align='center' class='redB' title='未处理'>×</div>";
					$LockRemark="";
					break;
				case "2":
					$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
					$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
					$Locks=0;
					break;
				case "3":
					$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
					$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
					$Locks=0;
					break;
				case "0":
					$Estate="<div align='center' class='greenB' title='已结付'>√</div>";
					$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
					$Locks=0;
					break;
				}
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
			array(0=>$Symbol,
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