<?php 
//已更新电信---yang 20120801
include "../model/modelhead.php";
//include "subprogram/productUpDelImg.php";

include "../model/subprogram/sys_parameters.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=11;

$tableMenuS=800;
$funFrom="sc_order";
$nowWebPage=$funFrom."_progress";
$scFrom=$scFrom==""?1:$scFrom;
$unColorCol=16;//不着色列
$Th_Col="操作|55|序号|30|PO|80|中文名|235|Product Code|150|Unit|35|Qty|50|Remark|110|Product info|110|期限|40|How to Ship|90|交货日期|80|操作员|55";//出货时间|80|
$myTask=0;
$sumCols="6";
//更新
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//步骤3：
include "../model/subprogram/business_authority.php";//看客户权限
$searchtable="productdata|P|cName|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
include "../model/subprogram/read_model_3.php";
$subTableWidth=$tableWidth-30;
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";	
	$ClientResult= mysql_query("SELECT M.CompanyId,C.Forshort 
	FROM $DataIn.yw1_ordermain M 
	LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId WHERE S.Estate>0 $ClientStr 
	GROUP BY M.CompanyId order by M.CompanyId ,M.OrderDate desc",$link_id);
	if ($ClientRow = mysql_fetch_array($ClientResult)){
		echo"<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
		do{
			$theCompanyId=$ClientRow["CompanyId"];$theForshort=$ClientRow["Forshort"];
			$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$theForshort</option>";$SearchRows="and M.CompanyId='$theCompanyId'";$DefaultClient=$theForshort;
				}
			else{
				echo"<option value='$theCompanyId'>$theForshort</option>";
				}
			}while($ClientRow = mysql_fetch_array($ClientResult));
		echo"</select>&nbsp;";
		}
	//分类
	$TypeResult= mysql_query("SELECT P.TypeId,T.TypeName 
	FROM $DataIn.yw1_ordermain M,$DataIn.yw1_ordersheet S,$DataIn.productdata P,$DataIn.producttype T
	WHERE S.Estate>0 AND M.OrderNumber=S.OrderNumber AND P.ProductId=S.ProductId AND T.TypeId=P.TypeId $SearchRows GROUP BY P.TypeId ORDER BY M.CompanyId,M.OrderDate desc",$link_id);
	if ($TypeRow = mysql_fetch_array($TypeResult)){
		echo"<select name='TypeId' id='TypeId' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>全部</option>";
		do{
			$theTypeId=$TypeRow["TypeId"];$TypeName=$TypeRow["TypeName"];
			if($TypeId==$theTypeId){
				echo"<option value='$theTypeId' selected>$TypeName</option>";$SearchRows.=" AND P.TypeId='$theTypeId'";
				}
			else{
				echo"<option value='$theTypeId'>$TypeName</option>";
				}
			}while($TypeRow = mysql_fetch_array($TypeResult));
		echo"</select>&nbsp;";
		}
	}
echo"$CencalSstr";
include "../model/subprogram/QuickSearch.php";
//步骤5：
include "../model/subprogram/read_model_5.php";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:300px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤6：
$sumQty=0;
$sumSaleAmount=0;
$sumTOrmb=0;
$DefaultBgColor=$theDefaultColor;
$i=1;
$sRow=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
S.Id,S.OrderPO,S.POrderId,S.ProductId,S.Qty,S.PackRemark,S.DeliveryDate,S.ShipType,S.scFrom,S.Estate,S.Locks,M.Operator,M.OrderDate,P.cName,P.eCode,P.TestStandard,P.CompanyId,P.pRemark,U.Name AS Unit,PI.PI,PI.Leadtime
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN $DataIn.yw2_orderexpress T ON T.POrderId=S.POrderId
WHERE 1 and S.Estate>0 $SearchRows ORDER BY P.CompanyId,S.scFrom";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$thisTOrmbOUTsum=0;
	do{

	  	//初始化计算的参数
		$m=1;$AskDay="";
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
		$Id=$myRow["Id"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);
		$TestStandard=$myRow["TestStandard"];
		$POrderId=$myRow["POrderId"];
	    include "../admin/Productimage/getOnlyPOrderImage.php";
		$Unit=$myRow["Unit"];
		$Qty=$myRow["Qty"];
		$PackRemark=$myRow["PackRemark"];
		$DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
		$Leadtime=$myRow["Leadtime"]=="&nbsp;"?"":$myRow["Leadtime"];
		$pRemark=$myRow["pRemark"]==""?"&nbsp;":$myRow["pRemark"];
		$ShipType=$myRow["ShipType"];
		//读取操作员姓名
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$OrderDate=$myRow["OrderDate"];
		//如果超过30天
		$AskDay=AskDay($OrderDate);
		$BackImg=$AskDay==""?"":"background='../images/$AskDay'";
		$OrderDate=CountDays($OrderDate,0);
		$Estate=$myRow["Estate"];
		$LockRemark=$Estate==4?"已生成出货单.":"";
		$Locks=0;
		$scFrom=$myRow["scFrom"];
        $czSign=1;
		$checkColor=mysql_query("SELECT G.Id FROM $DataIn.cg1_stocksheet G 
		LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
		WHERE 1 AND T.mainType=1 AND G.Mid='0' and (G.FactualQty>'0' OR G.AddQty>'0' ) and G.PorderId='$POrderId' LIMIT 1",$link_id);
		if($checkColorRow = mysql_fetch_array($checkColor)){
			$OrderSignColor="bgColor='#FFFFFF'";//有未下需求单
                        $czSign=0;//操作标记:不可操作
			$LockRemark="有未下需求单";
			}
		else{//已全部下单为黄色，如果生产数量完，则绿色		
			//计算生产数量与加工总数，如果相等为绿色，否则为黄色
			$OrderSignColor="bgColor='#339900'";	//设默认绿色
			//生产数量与工序数量不等时，黄色
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//工序总数
			$CheckgxQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS gxQty 
			FROM $DataIn.cg1_stocksheet G
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
			WHERE G.POrderId='$POrderId' AND T.mainType=3",$link_id));
			$gxQty=$CheckgxQty["gxQty"];
			//已完成的工序数量
			$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C WHERE C.POrderId='$POrderId'",$link_id));
			$scQty=$CheckscQty["scQty"];

			if($gxQty!=$scQty){
				$OrderSignColor="bgColor='#FFCC00'";
				}
			}
		$ColbgColor="";
		//加急订单
		$checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' ORDER BY Id",$link_id);
		if($checkExpressRow = mysql_fetch_array($checkExpress)){
			do{
				$Type=$checkExpressRow["Type"];
				switch($Type){
					case 1:$ColbgColor="bgcolor='#0066FF'";break;	//自有产品标识
					case 2:$ColbgColor="bgcolor='#FF00'";break;		//未确定产品
					case 7:$theDefaultColor="#FFA6D2";break;		//加急
					}
				}while ($checkExpressRow = mysql_fetch_array($checkExpress));
			}
		//动态读取 $thisTOrmbINo
		$showPurchaseorder="<img onClick='ShowProgress(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏订单进度.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		//0:内容	1：对齐方式		2:单元格属性		3：截取
		//检查权限:订单备注、出货方式的权限
			 
			$ValueArray=array(
				array(0=>$OrderPO),
				array(0=>$TestStandard),
				array(0=>$eCode,			3=>"..."),
				array(0=>$Unit,				1=>"align='center'"),
				array(0=>$Qty, 				1=>"align='right'"),
				array(0=>$PackRemark,		3=>"..."),
				array(0=>$pRemark, 			3=>"..."),
                array(0=>$OrderDate,		1=>"align='center'"),
				array(0=>$ShipType, 		1=>"align='center'", 3=>"..."),
				array(0=>$Leadtime,			3=>"..."),
				array(0=>$Operator,			1=>"align='center'")
				
				);
		$checkidValue=$Id;
		include "../admin/subprogram/read_model_6_yw.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth,"");
	}
//步骤7：
echo '</div>';
$myResult1 = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult1);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
ChangeWtitle($SubCompany.$DefaultClient."客户订单进度明细");
if($From!="slist"){
	$ActioToS="1";
	}
include "../model/subprogram/read_model_menu.php";
?>
<script language="javascript">
function ShowProgress(e,f,Order_Rows,POrderId,RowId){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		if(POrderId!=""){			
			var url="../public/sc_progress_ajax.php?POrderId="+POrderId+"&RowId="+RowId; 
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;					
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
		}
	}
</script>