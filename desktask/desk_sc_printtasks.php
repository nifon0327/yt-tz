<style type="text/css">
<!--
.list{position:relative;}
.list span img{ /*CSS for enlarged image*/
border-width: 0;
padding: 2px; width:200px;
}
.list span{ 
position: absolute;
padding: 3px;
border: 1px solid gray;
visibility: hidden;
background-color:#FFFFFF;
}
.list:hover{
background-color:transparent;
}
.list:hover span{
visibility: visible;
top:0; left:70px;
}
-->
</style>

<?php   
//步骤1电信---yang 20120801
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=13;
$tableMenuS=500;
$sumCols="7";		//求和列
$From=$From==""?"printtasks":$From;
ChangeWtitle("$SubCompany 打印任务列表");
$funFrom="desk_sc";
$Th_Col="选项|60|序号|40|客户|100|订单PO|80|产品名称|250|订单数量|60|生产数量|60|任务说明|60|需求数量|60|打印份数|60|打印文件|100|JPG图片|80|状态|60|任务发布|60|发布日期|70";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Estate=$Estate==""?1:$Estate;
$Page_Size = 100;							//每页默认记录数量
$Keys=4;
if($Login_P_Number==10291 || $Login_P_Number==10002){
	$Keys=15;
	}
$ActioToS="4,20";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消结付,16审核通过，17结付
$Keywords=$Keywords==""?"":" AND P.cName LIKE '%$Keywords%' ";
//步骤3：
$nowWebPage=$funFrom."_printtasks";
include "../model/subprogram/read_model_3.php";
$Keys=31;
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows=" AND S.Estate=$Estate";
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	echo"<select name='Estate' id='Estate' onchange='zhtj(this.name)'>
	<option value='1' $EstateSTR1>未打印</option>
	<option value='0' $EstateSTR0>已打印</option>
	</select>&nbsp;";
	
	$CodeType=$CodeType==""?0:$CodeType;
	$TempCodeTypeSTR="CodeTypeStr".strval($CodeType); 
	$$TempCodeTypeSTR="selected";
	//echo  "$TempCodeTypeSTR";
	echo"<select name='CodeType' id='CodeType' onchange='zhtj(this.name)'>";
		echo"<option value='0' $CodeTypeStr0>全部分类</option>
		<option value='1' $CodeTypeStr1>背卡.PE袋.盒</option>
		<option value='3' $CodeTypeStr3>外箱标签</option>
		</select>";
	if($CodeType!=0){
		if($CodeType==3){
			$SearchRows.=" AND S.CodeType=3";
			}
		else{
			$SearchRows.=" AND S.CodeType<>3";
			}
		}

		
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr &nbsp;&nbsp;<input name='Keywords' type='text' id='Keywords' size='20'>&nbsp;<input type='submit' name='Submit' value='查询'>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$CompanyArray = array(1065,1064,1066,1069,1071,1084,100226,100262);


$mySql="SELECT S.Id,S.CodeType,S.POrderId,S.Qty,SC.Qty AS scQty,S.Date,S.Estate,S.Operator,
	Y.OrderPO,Y.Qty AS OrderQty,C.CompanyId,
	P.ProductId,P.cName,P.TestStandard,P.Code AS BoxCode,
	C.Forshort,
	F.Estate AS shEstate,IFNULL(F.ProductId,0) AS FileSign
 	FROM $DataIn.sc3_printtasks S 
 	LEFT JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
	LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
	LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
	LEFT JOIN $DataIn.file_codeandlable F ON F.ProductId=Y.ProductId  AND F.CodeType=S.CodeType
	WHERE 1 $SearchRows $Keywords  ORDER BY S.Date DESC,S.CodeType ";
//	echo $mySql;
//echo "$mySql <br>";	
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/codeandlable/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$SCodeType=$myRow["CodeType"];	//文件类型+产品ID，确定配件文件
		$Qty=$myRow["Qty"];
		$scQty=$myRow["scQty"];
		$Date=$myRow["Date"];
		$PEstate=$myRow["Estate"];
		$Estate=$myRow["Estate"]==1?"<div class='redB'>未打印</div>":"<div class='greenB'>已打印</div>";
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$OrderPO=$myRow["OrderPO"]==""?"&nbsp;":$myRow["OrderPO"];
		$OrderQty=$myRow["OrderQty"];
		$POrderId=$myRow["POrderId"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$CompanyId=$myRow["CompanyId"];
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";

		$Forshort=$myRow["Forshort"];
		
		$shEstate=$myRow["shEstate"];//文件审核状态
		$FileSign=$myRow["FileSign"];
		include "desk_sc_printcode.php";
		
		switch($SCodeType){
			case "1"://背卡条码
				$CodeTypeSTR="背卡条码";
				$PrintQty=ceil($Qty/4);
				break;
			case "2"://PE袋标签
				$CodeTypeSTR="PE袋标签";
				$PrintQty=ceil($Qty/2);
				break;
			case "3"://外箱标签
				$CodeTypeSTR="外箱标签";
				$PrintQty=ceil($Qty/4);
				break;
			case "4"://白盒/坑盒
				$CodeTypeSTR="白盒/坑盒";
				$PrintQty=$Qty;
				break;
				
			}
		//查看上传JPG图片	
		$checkFileSql=mysql_query("SELECT Estate FROM $DataIn.file_codeandlable WHERE ProductId='$ProductId' AND CodeType='7' LIMIT 1",$link_id);
		if($checkFileRow=mysql_fetch_array($checkFileSql)){	
		      $ImageFileName=$ProductId . "-7" . ".jpg";
		      $ImageFile="../download/codeandlable/" . $ImageFileName;
			  $d1=anmaIn("download/codeandlable/",$SinkOrder,$motherSTR);
			  $f1=anmaIn($ImageFileName,$SinkOrder,$motherSTR);
			  $noStatue="onMouseOver=\"window.status='none';return true\"";
			 $ImageName="<div><a class='list' href='openorload.php?d=$d1&f=$f1&Type=Product' target='_blank'><img src='$ImageFile' width='70' height='28' border='0'  $noStatue/><span><img src='$ImageFile'  $noStatue/></span></a></div>";
			}
		else{
             $ImageName="&nbsp;";
			}
		$Locks=1;			
		$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
			title='$TempStrtitle' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		//财务强制锁:非未处理皆锁定
		$ValueArray=array(
			array(0=>$Forshort),
			array(0=>$OrderPO),
			array(0=>$TestStandard),
			array(0=>$OrderQty,		1=>"align='right'"),
			array(0=>$scQty,			1=>"align='right'"),
			array(0=>$CodeTypeSTR,	1=>"align='center'"),
			array(0=>$Qty,			1=>"align='right'"),
			array(0=>$PrintQty,		1=>"align='right'"),
			array(0=>$CodeFile,		1=>"align='center'"),
			array(0=>$ImageName,	1=>"align='center'"),
			array(0=>$Estate,		1=>"align='center'"),
			array(0=>$Operator,		1=>"align='center'"),
			array(0=>$Date,			1=>"align='center'"),
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
<script language="JavaScript" type="text/JavaScript">
<!--
function zhtj(obj){
	switch(obj){
		case "Estate":
			document.forms["form1"].elements["CodeType"].value="";

		break;
	}
	document.form1.action="desk_sc_printtasks.php";
	document.form1.submit();
}

</script>