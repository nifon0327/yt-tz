<?php   
//电信-EWEN
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=13;
$tableMenuS=500;
ChangeWtitle("$SubCompany 订单删除列表");
$funFrom="yw_order_del";
$From=$From==""?"read":$From;
$Th_Col="选项|60|序号|40|订单流水号|100|订单号|100|产品ID|60|产品名称|320|Code|150|删单数量|60|价格|60|删除原因|120|附件|50|备注|50|删单日期|80|操作|50";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 500;							//每页默认记录数量
$ActioToS="1";
//步骤3：
$nowWebPage=$funFrom."_verify";
include "../model/subprogram/read_model_3.php";

//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	$date_Result = mysql_query("SELECT D.Date
		FROM $DataIn.yw1_orderdeleted D
		WHERE 1 $SearchRows group by DATE_FORMAT(D.Date,'%Y-%m') order by D.Id DESC",$link_id);
		if ($dateRow = mysql_fetch_array($date_Result)) {
			echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
			echo "<option value='' selected>全部</option>";
			do{
				$dateValue=date("Y-m",strtotime($dateRow["Date"]));
				if($chooseDate==$dateValue){
					echo"<option value='$dateValue' selected>$dateValue</option>";
					$SearchRows.=" and  DATE_FORMAT(D.Date,'%Y-%m')='$dateValue'";
					}
				else{
					echo"<option value='$dateValue'>$dateValue</option>";					
					}
				}while($dateRow = mysql_fetch_array($date_Result));
			echo"</select>&nbsp;";
		}
		//客户
		$ClientResult=mysql_query("SELECT C.CompanyId,C.Forshort
		FROM $DataIn.yw1_orderdeleted D
		LEFT JOIN $DataIn.productdata P ON P.ProductId=D.ProductId
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		WHERE 1 $SearchRows AND C.CompanyId IS NOT NULL GROUP BY C.CompanyId",$link_id);
		if($ClientRow=mysql_fetch_array($ClientResult)){
		echo"<select name='CompanyId' id='CompanyId' onchange='document.form1.submit()'>";
		echo "<option value='' selected>全部</option>";
		     do{
			    $ThisCompanyId=$ClientRow["CompanyId"];
				$Forshort=$ClientRow["Forshort"];
				if($ThisCompanyId==$CompanyId){
				    echo "<option value='$ThisCompanyId' selected>$Forshort</option>";
					$SearchRows.=" and  P.CompanyId='$ThisCompanyId'";
				    }
				else{
				    echo "<option value='$ThisCompanyId'>$Forshort</option>";
				    }
		       }while($ClientRow=mysql_fetch_array($ClientResult));
			   echo"</select>&nbsp;";
		  }
	
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr	";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT D.Id,D.OrderPO,D.POrderId,P.cName,P.TestStandard,D.Qty,D.Price,
        D.Attached,D.Estate,D.Remark,D.Date,D.Operator,T.TypeName,P.ProductId,P.eCode 
        FROM $DataIn.yw1_orderdeleted D
		LEFT JOIN $DataIn.productdata P ON P.ProductId=D.ProductId
		LEFT JOIN $DataPublic.yw1_orderdeltype T ON T.Id=D.delType
		WHERE 1 AND D.Estate=0 $SearchRows order by D.Date desc";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$Dir=anmaIn("download/orderdelcause/",$SinkOrder,$motherSTR);	
	do{
		$m=1;
		$Id=$myRow["Id"];
		$OrderPO=$myRow["OrderPO"]==""?"&nbsp;":$myRow["OrderPO"];
		$POrderId=$myRow["POrderId"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$TestStandard=$myRow["TestStandard"];
		$Qty=$myRow["Qty"];
		$eCode=$myRow["eCode"];
		$Price=$myRow["Price"];
		$Attached=$myRow["Attached"];
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
		
		$checkDelResult = mysql_query("SELECT Id FROM $DataIn.cg1_stocksheet_del WHERE POrderId ='$POrderId'",$link_id);
		if($checkDelRow=mysql_fetch_array($checkDelResult)){
			$showPurchaseorder="<img onClick='ShowOrHideDel(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
			title='显示相关配件' width='13' height='13' style='CURSOR: pointer'>";
			$StuffListTB="
				<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		}

		$ValueArray=array(
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