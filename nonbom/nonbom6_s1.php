<?php 
include "../model/subprogram/s1_model_1.php";
$Th_Col="选项|40|序号|30|采购|50|供应商|100|配件名称|300|申购备注|200|货币|30|单价|70|本次申购|60|单位|40|金额|70|申购总数|60|申购状态|50|申购时间|70|申购人|60";
$ColsNumber=12;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$Parameter.=",Jid,$Jid,Bid,$Bid";
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
$sSearch=$From!="slist"?"":$sSearch;
$sSearch.=$Jid==""?"":" AND A.CompanyId='$Jid'";
$sSearch.=$Bid==""?"":" AND A.BuyerId='$Bid'";

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.GoodsId,A.Qty,A.Price,(A.Qty*A.Price) AS Amount,A.Remark,A.ReturnReasons,A.Estate,A.Locks,A.Date,A.Operator,C.Forshort,C.CompanyId,D.GoodsName,D.Unit,D.Attached,F.Name AS StaffName,G.Symbol 
	FROM $DataIn.nonbom6_cgsheet A
	LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=A.CompanyId
	LEFT JOIN $DataPublic.nonbom4_goodsdata D ON D.GoodsId=A.GoodsId
	LEFT JOIN $DataPublic.staffmain F ON F.Number=A.BuyerId
	LEFT JOIN $DataPublic.currencydata G ON G.Id=C.Currency 
	WHERE 1 $sSearch $SearchRows AND A.Estate=1 AND A.Mid='0' ORDER BY A.Date DESC,A.Id DESC";
//	echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$GoodsId=$myRow["GoodsId"];
		$StaffName=$myRow["StaffName"];
		$GoodsName=$myRow["GoodsName"];
		$BarCode=$myRow["BarCode"]==""?"&nbsp;":$myRow["BarCode"];;
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$TypeName=$myRow["TypeName"]==""?"&nbsp;":$myRow["TypeName"];
		$Unit=$myRow["Unit"];
		$Symbol=$myRow["Symbol"];
		$Price=sprintf("%.3f",$myRow["Price"]);
		$Qty=del0($myRow["Qty"]);
		$Amount=sprintf("%.2f",$myRow["Amount"]);
		$Forshort=$myRow["Forshort"];
		$wStockQty=del0($myRow["wStockQty"]);
		$oStockQty=del0($myRow["oStockQty"]);
		$mStockQty=del0($myRow["mStockQty"]);
		$CompanyId=$myRow["CompanyId"];
	     $checkidValue=$Id."^^".$GoodsName."^^".$Price."^^".$Qty."^^".$Unit."^^".$Amount."^^".$Remark;
		switch($myRow["Estate"]){
			case 1:$Estate= "<div class='greenB'>已审核</div>";break;
			case 2:
			case 3:$Estate="<div class='redB'>未审核</div>";break;
			case 4:
				$ReturnReasons=$myRow["ReturnReasons"]==""?"未填写退回原因":$myRow["ReturnReasons"];
			    $Estate="<img src='../images/warn.gif' title='$ReturnReasons' width='18' height='18'>";
			break;
			}
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Attached=$myRow["Attached"];
		if($Attached==1){
			$Attached=$GoodsId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
			}
        include"../model/subprogram/good_Property.php";//非BOM配件属性


		$checkQty=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.nonbom6_cgsheet WHERE Mid=0 AND GoodsId='$GoodsId'",$link_id));
		$sgSUM=del0($checkQty["Qty"]);
		//加密
		$CompanyId=anmaIn($CompanyId,$SinkOrder,$motherSTR);		
		$Forshort="<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
		//历史单价
		$Price="<a href='nonbom4_history.php?GoodsId=$GoodsId' target='_blank'>$Price</a>";
		//配件分析
		$GoodsId="<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
		$Locks=$myRow["Locks"];
		//申购总数计算
		
		$ValueArray=array(
			array(0=>$StaffName,1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$GoodsName),
			array(0=>$Remark),
			array(0=>$Symbol,1=>"align='right'"),
			array(0=>$Price,1=>"align='right'"),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$Unit,1=>"align='center'"),
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$sgSUM,1=>"align='right'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
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
include "../model/subprogram/read_model_menu.php";
?>