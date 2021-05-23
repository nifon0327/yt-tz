<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw1_fkoutsheet
$DataIn.trade_object
$DataIn.ck1_rksheet
$DataIn.ck5_llsheet
$DataPublic.staffmain
$DataIn.stuffdata
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=19;
$tableMenuS=600;
$sumCols="7,8,9,10,11,14";		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 采购请款待审核列表");
$funFrom="cg_cgdmainP";
$Th_Col="选项|60|序号|40|采购流水号|90|配件名称|230|图档|30|历史订单|60|QC图|40|订单数|55|使用库存|55|需求数|55|增购数|55|实购数|55|含税价|55|单位|45|金额|60|未收货|55|未补|55|供应商|80|最后出货日期|80|状态|40|采购员|50|请款<br>方式|40";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,17,15";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消,16审核通过
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
//过滤条件
if($From!="slist"){
	$SearchRows="";
	//月份
	$MonthResult = mysql_query("SELECT S.Month FROM $DataIn.cw1_fkoutsheet S WHERE S.Estate='2' AND S.Month<>'' GROUP BY S.Month ORDER BY S.Month",$link_id);
	if ($MonthRow = mysql_fetch_array($MonthResult)) {
		echo"请款月份 <select name='chooseMonth' id='chooseMonth' onchange='zhtj(this.name)'>";
		/*$MonthValue=$MonthRow["Month"];
		if ($MonthValue==="" || $chooseMonth=="no") {
		    $MonthValue="no";
			echo"<option value='no' selected>无请款月份</option>";
		}
		 */
		do{
			$MonthValue=$MonthRow["Month"];
			$chooseMonth=$chooseMonth==""?$MonthValue:$chooseMonth;
			if($chooseMonth==$MonthValue){
				echo"<option value='$MonthValue' selected>$MonthValue</option>";
				$SearchRows.=" and S.Month='$MonthValue'";
				}
			else{
				echo"<option value='$MonthValue'>$MonthValue</option>";					
				}
			}while($MonthRow = mysql_fetch_array($MonthResult));
		echo"</select>&nbsp;";
		}
	else{
		//无月份记录
		//$SearchRows.=" and M.Month='无效'";
		}
	$GysSql= mysql_query("SELECT S.CompanyId,P.Forshort,P.Letter 
	FROM $DataIn.cw1_fkoutsheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	WHERE S.Estate='2' $SearchRows GROUP BY S.CompanyId ORDER BY P.Letter",$link_id);

	if($GysRow = mysql_fetch_array($GysSql)){
		echo "<select name='CompanyId' id='CompanyId' onchange='document.form1.submit()'>";
		do{
			$Letter=$GysRow["Letter"];
			$Forshort=$GysRow["Forshort"];
			$Forshort=$Letter.'-'.$Forshort;
			$thisCompanyId=$GysRow["CompanyId"];
			$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;				
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort </option>";
				$SearchRows.=" and S.CompanyId='$thisCompanyId'";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($GysRow = mysql_fetch_array($GysSql));
		echo"</select>&nbsp;";
		}		
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤4：
include "../model/subprogram/read_model_5.php";
//步骤5：需处理数据记录处理

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
	S.Id,S.StockId,S.POrderId,S.CompanyId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.BuyerId,P.Forshort,M.Name,A.StuffId,A.StuffCname,A.TypeId,A.Gfile,A.Gstate,A.Picture,U.Name AS UnitName,U.Decimals,H.Date as OutDate,S.AutoSign 
 	FROM $DataIn.cw1_fkoutsheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId	
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
	LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
    Left Join $DataIn.ch1_shipsheet C ON C.PorderId=S.PorderId
    Left Join $DataIn.ch1_shipmain H ON H.Id=C.Mid
	WHERE 1 and S.Estate=2 $SearchRows ORDER BY S.Month desc,H.Date desc ";
//echo $mySql;
$tmpStockId="";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);	
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StockId=$myRow["StockId"];//采购流水号
        if($tmpStockId==$StockId) {  //表示同一个StockId，分批出货，造成重复显示，显示最后一个出货的就行
			continue;
		}
		$tmpStockId=$StockId;
		
		$OutStockId=$StockId;
		$StuffCname=$myRow["StuffCname"];//配件名称

		$Gfile=$myRow["Gfile"];
		$Gstate=$myRow["Gstate"];  //状态
		$Decimals=$myRow["Decimals"];  
		//加密
		/*
		if($Gfile!=""){
			$f=anmaIn($Gfile,$SinkOrder,$motherSTR);
			$Gfile="<img onClick='OpenOrLoad(\"$d\",\"$f\",6)' src='../images/down.gif' alt='$Gremark' width='18' height='18'>";
			}
		else{
			$Gfile="&nbsp;";
			}
		*/
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
		
		$StuffId=$myRow["StuffId"];
        $TypeId=$myRow["TypeId"];
		$Picture=$myRow["Picture"];
        include "../model/subprogram/stuffimg_model.php";
        
	       //配件QC检验标准图
                include "../model/subprogram/stuffimg_qcfile.php";	
                $OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId' target='_blank'>查看</a>"; 
                
		$Buyer=$myRow["Name"];//采购		
		$Forshort=$myRow["Forshort"];//供应商
		$OrderQty=$myRow["OrderQty"];		//订单数量		
		$StockQty=$myRow["StockQty"];	//需求数量
		$FactualQty=$myRow["FactualQty"];	//需求数量
		$AddQty=$myRow["AddQty"];			//增购数量	
		$Qty=$FactualQty+$AddQty;	//采购总数
		//echo $TypeId;
		if($TypeId=='9104'){//如果是客户退款，请款总额为订单数*价格
		    $AmountQty=$OrderQty;	//采购总数
			$AddQty=0;//增购不显示
			}
		else{
		    $AmountQty=$FactualQty+$AddQty;
		    }
		$Price=$myRow["Price"];	//采购价格
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$Estate="<div class='yellowB' title='请款中'>×.</div>";
		$OutDate=$myRow["OutDate"]==""?"&nbsp":$myRow["OutDate"];    	
			//收货情况				
			$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' ",$link_id);
			$rkQty=mysql_result($rkTemp,0,"Qty");
			$rkQty=$rkQty==""?0:$rkQty;
			//领料情况
			$llTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' ",$link_id); 
			$llQty=mysql_result($llTemp,0,"Qty");
			$llQty=$llQty==""?0:$llQty;
			$llBgColor="";
			if($tdBGCOLOR==""){
				if($llQty==$OrderQty){
					$llBgColor="class='greenB'";
					}
				else{
					$llBgColor="class='yellowB'";
					}
				}
			else{
				$llBgColor="class='greenB'";
				}
		//统计
		$Amount=sprintf("%.2f",$AmountQty*$Price);//本记录金额合计
		//行标色
		$ordercolor="bgcolor='FFFFFF'";$Fontcolor="class='redB'";
		$Mantissa=round($Qty-$rkQty,$Decimals);
		if($Mantissa<$Qty){//如果尾数《采购数：黄色
			$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			$StockId="<a href='ck_rk_list.php?Sid=$Sid' target='_blank' title='点击查看收货记录'>$StockId</a>";
			if($Mantissa==0){//如果尾数=0：绿色
				$Mantissa="&nbsp;";
				}
			else{
				$Mantissa="<div class='yellowB'>$Mantissa</div>";
				}
			}
		else{
			$Mantissa="<div class='redB'>$Mantissa</div>";
			}
			
	    /*
		//最后出货日期
		$OutDate="";
 		$DateResult = mysql_query("select M.Date from $DataIn.cg1_stocksheet C
		      Left Join $DataIn.ch1_shipsheet S ON S.PorderId=C.PorderId
			  Left Join $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			  Where C.StockId='$OutStockId' Order by M.Date",$link_id);
			  
		if ($DateRow = mysql_fetch_array($DateResult)) {
			$OutDate=$DateRow["Date"];
		}
		
		$OutDate=$OutDate==""?"&nbsp":$OutDate;       			
        */
		//退补数量计算
		$CompanyId=$myRow["CompanyId"];
		$sSearch1=" AND S.StuffId='$StuffId'  AND M.CompanyId = '$CompanyId'";
		$checkSql=mysql_query("
		SELECT (B.thQty-A.bcQty) AS wbQty
			FROM (
				SELECT IFNULL(SUM(S.Qty),0) AS thQty,'$StuffId' AS StuffId FROM $DataIn.ck2_thsheet S 
				LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid 
				WHERE 1 $sSearch1
				)B
			LEFT JOIN (
				SELECT IFNULL(SUM(Qty),0) AS bcQty,'$StuffId' AS StuffId FROM $DataIn.ck3_bcsheet  S
				LEFT JOIN $DataIn.ck3_bcmain M ON M.Id=S.Mid 
				WHERE 1 $sSearch1
				) A ON A.StuffId=B.StuffId",$link_id);

		$wbQty=mysql_result($checkSql,0,"wbQty");
		if($wbQty!=0){
			$wbQty="<a href='stuffreport_result.php?Idtemp=$StuffId' target='_blank'>$wbQty</a>";
			}
		else{
			$wbQty="&nbsp;";
			}
		
		$POrderId=$myRow["POrderId"];	
		if ($POrderId!=""){
		$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
		title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30' align='left'><br><div id='showStuffTB$i' width='$XsubTableWidth'>&nbsp;</div><br></td></tr></table>";
		}
		else{
			$showPurchaseorder="";$StuffListTB="";
		}
		
		//1表示手动请款审核,2.表示手动请款自动通过,3表示自动请款审核,4表示自动请款自动通过
		$Autobgcolor="";
		$AutoSign=$myRow["AutoSign"];
		switch($AutoSign){
			case 3:
			    $AutoSign="<image src='../images/AutoCheck.png' style='width:20px;height:20px;' title='系统请款'/>";
				//$Autobgcolor="bgcolor='##FF0000'";
				break;
			default:
				$AutoSign="&nbsp;";
				break;
			
		}		
		
		//默认单价
		$priceRes=mysql_query("SELECT S.Price FROM $DataIn.stuffdata S WHERE S.StuffId='$StuffId'",$link_id);
		if($priceRow=mysql_fetch_array($priceRes)){
			$DefaultPrice=$priceRow["Price"];
		}
		if($DefaultPrice!=$Price){
			$Price="<div class='redB'>$Price</div>";
			$PriceTitle="Title=\"默认单价：$DefaultPrice\"";
		}

		$ValueArray=array(
			array(0=>$StockId,1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Gfile,1=>"align='center'"),
                        array(0=>$OrderQtyInfo,1=>"align='center'"),
                        array(0=>$QCImage,1=>"align='center'"),
			array(0=>$OrderQty,1=>"align='right'"),
			array(0=>$StockQty,1=>"align='right'"),
			array(0=>$FactualQty,1=>"align='right'"),
			array(0=>$AddQty,1=>"align='right'"),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$Price,1=>"align='right' $PriceTitle"),
			array(0=>$UnitName,1=>"align='center'"),
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$Mantissa,1=>"align='right'"),
			array(0=>$wbQty,1=>"align='right'"),
                        array(0=>$Forshort,1=>"align='center'"),
			array(0=>$OutDate,1=>"align='center'"),	
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Buyer,1=>"align='center'"),
			array(0=>$AutoSign,1=>"align='center' $Autobgcolor ")
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
<script>
function zhtj(obj){
	switch(obj){
		case "chooseMonth":
			if(document.all("CompanyId")!=null){
				document.forms["form1"].elements["CompanyId"].value="";
				}
		break;
		}
	document.form1.action="cg_cgdmainp_m.php";
	document.form1.submit();
	}
</script>