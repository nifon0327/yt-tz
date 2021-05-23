<?php   
//电信-EWEN
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=500;
ChangeWtitle("$SubCompany 订单删除审核列表");
$funFrom="yw_order_del";
$From=$From==""?"verify":$From;
$Th_Col="选项|60|序号|40|客户名称|80|订单流水号|80|订单号|80|产品ID|60|产品名称|180|Code|150|删单数量|60|价格|60|删除原因|120|附件|50|备注|50|删单日期|80|操作|50";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 500;							//每页默认记录数量
$ActioToS="17,34";
//步骤3：
$nowWebPage=$funFrom."_verify";
include "../model/subprogram/read_model_3.php";

//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows=" AND D.Estate=1";
	
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr	";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT C.Forshort,D.Id,D.OrderPO,D.POrderId,P.cName,P.TestStandard,D.Qty,D.Price,
        D.Attached,D.Estate,D.Remark,D.Date,D.Operator,T.TypeName,P.ProductId,P.eCode 
        FROM $DataIn.yw1_orderdeleted D
		LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=D.POrderId
		LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
		LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
		LEFT JOIN $DataIn.productdata P ON P.ProductId=D.ProductId
		LEFT JOIN $DataPublic.yw1_orderdeltype T ON T.Id=D.delType
		WHERE 1 $SearchRows";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$Dir=anmaIn("download/orderdelcause/",$SinkOrder,$motherSTR);	
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Forshort=$myRow["Forshort"];
		$OrderPO=$myRow["OrderPO"];
		$POrderId=$myRow["POrderId"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$TestStandard=$myRow["TestStandard"];
		$Qty=$myRow["Qty"];
		$eCode=$myRow["eCode"];
		$Price=$myRow["Price"];
		$Attached=$myRow["Attached"];
		//echo $Attached;
		if($Attached!=""){
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<a href=\"openorload.php?d=$Dir&f=$f&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>view</a>";
			}
		else{
			$Attached="-";
			}
		$Estate=$myRow["Estate"];
		$Remark=$myRow["Remark"];
		$Remark=$Remark==""?"&nbsp;":"<img src='../images/remark.gif' title='$Remark' width='18' height='18'>";
		$TypeName=$myRow["TypeName"];
		$Date=substr($myRow["Date"],0,10);
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
			$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
			title='显示相关配件' width='13' height='13' style='CURSOR: pointer'>";
			$StuffListTB="
				<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		$ValueArray=array(
			array(0=>$Forshort, 1=>"align='left'"),
			array(0=>$POrderId, 1=>"align='center'"),
			array(0=>$OrderPO,  1=>"align='center'"),
			array(0=>$ProductId,1=>"align='center'"),
			array(0=>$cName),
			array(0=>$eCode),
			array(0=>$Qty,		1=>"align='right'"),
			array(0=>$Price,	1=>"align='right'"),
			array(0=>$TypeName),
			array(0=>$Attached, 1=>"align='center'"),
			array(0=>$Remark,   1=>"align='center'"),
			array(0=>$Date,     1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'")
	
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