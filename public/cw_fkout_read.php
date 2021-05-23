<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//$AuthResult=mysql_query("SELECT Number FROM $DataIn.authority WHERE Estate=1",$link_id);
//$Auth_Number=mysql_result($AuthResult,0,"Number");
$ColsNumber=20;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置

$From=$From==""?"read":$From;		//必选参数：是否来自查询结果浏览
$funFrom="cw_fkout";			//必选参数：功能模块
$nowWebPage=$funFrom."_read";		//必选参数：功能页面
$Log_Item="供应商货款";
//$Estate=0;
$MergeRows=0;
$sumCols="7,8,9,10,11,14";			//求和列,需处理
ChangeWtitle($SubCompany.$Log_Item."发票信息");
$Th_Col="选项|40|序号|40|请款月份|60|采购单|60|采购流水号|100|配件ID|42|配件名称|230|订单数|55|使用库存|55|需求数|55|增购数|55|实购数|55|单价|55|单位|45|金额|60|未收货|55|未补货|55|出货日期|80|请款<br>方式|30|发票信息|80|状态|40|采购员|50";
$EstateSTR3="selected";
$ActioToS="1,178";


$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
include "../model/subprogram/read_model_3.php";
//过滤条件
if($From!="slist"){
	/*$SearchRows=" and S.Estate='3'";
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' selected>未结付货款</option></select>";		//<option value='0' >已结付货款</option>&nbsp;
	*/
   $EstateStr = 'FkEstateStr' . $FKEstate; 
   $$EstateStr	= " selected ";
   
  echo"<select name='FKEstate' id='FKEstate' onchange='ResetPage(this.name)'>
    <option value='All' $FkEstateStr>全部</option>
	<option value='3' $FkEstateStr3>未结付货款</option><option value='0' $FkEstateStr0>已结付货款</option>&nbsp;</select>";	
  
     switch($FKEstate){
			case '3': $SearchRows.=" AND  S.Estate>0  "; break;
			case '0':  $SearchRows.=" AND  S.Estate=0 "; break;
		}


	
$EstateStr = 'EstateStr' . $InvoiceEstate; 
$$EstateStr	= " selected ";

echo"<select name='InvoiceEstate' id='InvoiceEstate' onchange='document.form1.submit()'>
         <option value='' $EstateStr>全部</option>
	     <option value='3' $EstateStr3>未上传发票</option>
	     <option value='1' $EstateStr1>已上传发票</option>
	     </select>&nbsp;";	
		switch($InvoiceEstate){
			case 1: $SearchRows.=" AND  I.Estate>0  "; break;
			case 3:  $SearchRows.=" AND   I.Estate IS NULL "; break;
		}
	

	//月份
	$MonthResult = mysql_query("SELECT S.Month FROM $DataIn.cw1_fkoutsheet S
	LEFT JOIN $DataIn.cw1_fkoutinvoice I ON I.Id=S.InvoiceId  
    WHERE 1 $SearchRows GROUP BY S.Month ORDER BY S.Month desc",$link_id);
	if ($MonthRow = mysql_fetch_array($MonthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='zhtj(this.name)'>";
		do{
			     $MonthValue=$MonthRow["Month"];
		//	if ($MonthValue!=""){
					$chooseMonth=$chooseMonth==""?$MonthValue:$chooseMonth;
					if($chooseMonth==$MonthValue){
						echo"<option value='$MonthValue' selected>$MonthValue</option>";
						$SearchRows.=" and S.Month='$MonthValue'";
						}
					else{
						echo"<option value='$MonthValue'>$MonthValue</option>";					
						}
			 //  }
			}while($MonthRow = mysql_fetch_array($MonthResult));
		echo"</select>&nbsp;";
		}
	else{
		//无月份记录
		$SearchRows.=" and M.Month='无效'";
		}


$GysSql= mysql_query("SELECT S.CompanyId,P.Forshort,P.Letter 
	FROM $DataIn.cw1_fkoutsheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	LEFT JOIN $DataIn.cw1_fkoutinvoice I ON I.Id=S.InvoiceId 
	WHERE 1 $SearchRows GROUP BY S.CompanyId ORDER BY P.Letter",$link_id);

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
else{
	//$SearchRows.=" AND S.Estate='3'";
	}

	
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";

echo"$CencalSstr";

//步骤4：
include "../model/subprogram/read_model_5.php";
//步骤5：需处理数据记录处理
$InvoiceStr='';
$InvoiceIdArray=array();
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
	S.Id,S.Month,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.BuyerId,S.InvoiceId,S.Estate,
	P.Forshort,M.Name,D.StuffCname,U.Name AS UnitName,D.TypeId,S.CompanyId,S.AutoSign,I.InvoiceNo,
	I.InvoiceFile,I.Remark,ROUND((S.AddQty+S.FactualQty)*S.Price,2) AS Amount,GM.PurchaseID,G.Mid     
 	FROM $DataIn.cw1_fkoutsheet S 
 	LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
 	LEFT JOIN $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	LEFT JOIN $DataIn.staffmain M ON M.Number=S.BuyerId	
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
	LEFT JOIN $DataIn.cw1_fkoutinvoice I ON I.Id=S.InvoiceId 
	WHERE 1 $SearchRows ORDER BY S.Month DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
      $InvoiceFileDir=anmaIn("download/fkinvoice/",$SinkOrder,$motherSTR);	
	do{
		$m=1;
		$Id=$myRow["Id"];
		$PurchaseID=$myRow["PurchaseID"];
		$Mid=$myRow["Mid"];
		$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
		$PurchaseID="<a href='../model/subprogram/purchaseorder_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
		
		$StockId=$myRow["StockId"];//采购流水号
		$OutStockId=$StockId;
		$StuffCname=$myRow["StuffCname"];//配件名称
		$Buyer=$myRow["Name"];//采购		
		$Forshort=$myRow["ForshortName"];//供应商
		$OrderQty=$myRow["OrderQty"];		//订单数量		
		$StockQty=$myRow["StockQty"];	//需求数量
		$FactualQty=$myRow["FactualQty"];	//需求数量
		$AddQty=$myRow["AddQty"];			//增购数量	
		$Qty=$FactualQty+$AddQty;	//采购总数
		$TypeId=$myRow["TypeId"];
		$Month=$myRow["Month"];
		
		
		//1表示手动请款审核,2.表示手动请款自动通过,3表示自动请款审核,4表示自动请款自动通过
		$Autobgcolor="";
		$AutoSign=$myRow["AutoSign"];
		switch($AutoSign){
			case 2:
			    $AutoSign="<image src='../images/AutoCheckB.png' style='width:20px;height:20px;' title='人工请款自动通过'/>";
				break;
			case 4:
			    $AutoSign="<image src='../images/AutoCheck.png' style='width:20px;height:20px;' title='系统请款自动通过'/>";
				//$Autobgcolor="bgcolor='##FF0000'";
				break;
			default:
				$AutoSign="&nbsp;";
				break;
			
		}
		
		$AmountQty=$FactualQty+$AddQty;	//采购总数
		$Price=$myRow["Price"];	//采购价格
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$OutDate=$myRow["OutDate"]==""?"&nbsp":$myRow["OutDate"];    

		$Estate=$myRow["Estate"]>0?"<div class='redB'>未付</div>":"<div class='greenB'>已付</div>";
		//统计
		//$Amount=sprintf("%.2f",$AmountQty*$Price);//本记录金额合计	
		$Amount=$myRow["Amount"];
		//收货情况				
		$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:$rkQty;
			//领料情况
			$llTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' order by Id",$link_id); 
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
		
		//行标色
        $Mantissa=$Qty-$rkQty;
		$ordercolor="bgcolor='FFFFFF'";$Fontcolor="class='redB'";
		if($Mantissa<$Qty){//如果尾数《采购数：黄色
			$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			$StockId="<a href='ck_rk_list.php?Sid=$Sid' target='_blank' title='点击查看收货记录'>$StockId</a>";
			$Mantissa="<div class='yellowB'>$Mantissa</div>";
			if($Mantissa==0){//如果尾数=0：绿色
				$Mantissa="&nbsp;";
				}
			}
		else{
			$Mantissa="<div class='redB'>$Mantissa</div>";
			}
			
	    //最后出货日期
		$OutDate="";
 		$DateResult = mysql_query("select M.Date FROM $DataIn.cg1_stocksheet C
		      Left Join $DataIn.ch1_shipsheet S ON S.PorderId=C.PorderId
			  Left Join $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			  WHERE C.StockId='$OutStockId' Order by M.Date",$link_id);  
		if ($DateRow = mysql_fetch_array($DateResult)) {
			$OutDate=$DateRow["Date"];
		}
		$OutDate=$OutDate==""?"&nbsp":$OutDate;    
        
		//未补统计
		$StuffId=$myRow["StuffId"];//配件ID
		$sSearch1=" AND S.StuffId='$StuffId'";
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
		$Locks=1;$LockRemark="";$ColbgColor="";$PriceTitle="";
		
		$CompanyId =$myRow["CompanyId"];
		if ($CompanyId==$SubCompanyId){
			//检查鼠宝收款状态
			$tmpStockId=$myRow["StockId"];
			$checkFkResult=mysql_query("SELECT W.Id,S.Price FROM $DataOut.yw1_ordersheet Y 
			LEFT JOIN $DataOut.ch1_shipsheet S ON S.POrderId=Y.POrderId 
			LEFT JOIN $DataOut.ch1_shipmain M ON S.Mid=M.Id 
			LEFT JOIN $DataOut.cw6_orderinsheet W ON W.chId=M.Id 
			WHERE Y.OrderNumber='$tmpStockId'",$link_id);
			if ($checkFkRow = mysql_fetch_array($checkFkResult)) {
					$FkState=$checkFkRow["Id"];
					$ptPrice=$checkFkRow["Price"];
					if ($FkState>0) {
						//$Locks=0;
						$ColbgColor="bgcolor='#ff0000'";
						//$LockRemark="该单皮套显示已收款！";
					}
					if ($ptPrice-$Price!=0){
						$PriceTitle=" Title='与皮套出货价格($ptPrice)不同'";
						$Price="<div class='redB'>$Price</div>";
					}
			}
			else{
				$ColbgColor="bgcolor='#F5B50D'";
			}
		}
		
		$InvoiceId  =$myRow['InvoiceId'];
		$InvoiceFile=$myRow['InvoiceFile'];
		
		if ($InvoiceId>0){
		    $OrderSignColor = " bgColor='#93FF93' ";
		    $InvoiceNo=$myRow['InvoiceNo'];
		    $Remark = $myRow["Remark"];
		    
		    if (!in_array($InvoiceId, $InvoiceIdArray) && $Remark!=''){
		        $InvoiceStr.= $InvoiceNo . ":" . $Remark . "<br>";
		        $InvoiceIdArray[]=$InvoiceId;
		    }
		    
		    
		    $InvoiceFile=anmaIn($InvoiceFile,$SinkOrder,$motherSTR);
            		
            $InvoiceFile="<a href=\"../public/openorload.php?d=$InvoiceFileDir&f=$InvoiceFile&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>$InvoiceNo</a>"; 
		}
		else{
			$InvoiceFile="&nbsp;";
			$OrderSignColor = " bgColor='#FFFFFF' ";
		}
		
    
		$ValueArray=array(
		    array(0=>$Month,1=>"align='center'"),
		    array(0=>$PurchaseID,1=>"align='center'"),
			array(0=>$StockId,1=>"align='center'"),
			array(0=>$StuffId,1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$OrderQty,1=>"align='center'"),
			array(0=>$StockQty,1=>"align='center'"),
			array(0=>$FactualQty,1=>"align='center'"),
			array(0=>$AddQty,1=>"align='center'"),
			array(0=>$Qty,1=>"align='center'"),
			array(0=>$Price,1=>"align='right'  $PriceTitle"),
			array(0=>$UnitName,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Mantissa,1=>"align='center'"),
			array(0=>$wbQty,1=>"align='center'"),
			array(0=>$OutDate,1=>"align='center'"),
			array(0=>$AutoSign,1=>"align='center' $Autobgcolor "),
			array(0=>$InvoiceFile,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Buyer,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
		
	//检查预付订金
	$DjTable="";
	$checkDj=mysql_query("SELECT M.PayDate,S.Id,S.TypeId,S.Amount,S.Remark,S.Date,P.Name AS Operator 
	FROM $DataIn.cw2_fkdjsheet S
	LEFT JOIN $DataIn.cw2_fkdjmain M ON M.Id=S.Mid
	LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Operator
	 WHERE S.CompanyId='$CompanyId' and Did='0'",$link_id);
	if($checkRow = mysql_fetch_array($checkDj)){
		$DjTable="<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
		<tr><td class='A0111' height='30'>未抵货款订金列表</td></tr></table>
		<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
		<tr bgcolor='$Title_bgcolor'>
		<td width='40' height='25' class='A0111' align='center'>选项</td><td width='40' class='A0101' align='center'>序号</td>
		<td width='80' class='A0101' align='center'>支付日期</td>
		<td class='A0101' align='center'>预付说明</td>
		<td width='115' class='A0101' align='center'>预付金额</td>
		<td width='95' class='A0101' align='center'>类别</td>
		<td width='50' class='A0101' align='center'>请款人</td>
		</tr>
		";
		$d=1;
		do{
			$djPayDate=$checkRow["PayDate"];
			$djId=$checkRow["Id"];
			$djType=$checkRow["TypeId"]==1?"订金":($checkRow["TypeId"]==2?"多付平衡帐":"少付平衡帐");
			$djAmount=$checkRow["Amount"]<0?"<div class='redB'>$checkRow[Amount]</div>":$checkRow["Amount"];
			$djRemark=$checkRow["Remark"];
			$djDate=$checkRow["Date"];
			$djOperator=$checkRow["Operator"];
			$DjTable.="<tr>
			<td align='center' class='A0111' height='20'><input name='checkdj[]' type='checkbox' id='checkdj$d' value='$djId'></td>
			<td align='center' class='A0101'>$d</td>
			<td align='center' class='A0101'>$djPayDate</td>
			<td class='A0101'>$djRemark</td>
			<td align='right' class='A0101'>$djAmount</td>
			<td class='A0101'>$djType</td>
			<td align='center' class='A0101'>$djOperator</td>
			</tr>
			";
			$d++;
			}while ($checkRow = mysql_fetch_array($checkDj));
		$DjTable.="</table>";
		}
		//*****************************************采购单扣款抵付
		$KKTable="";
		$KKResult="";
		$KKResult=mysql_query("SELECT M.Id,M.BillNumber,M.Date,M.TotalAmount,S.PurchaseID,S.StockId,
		S.Qty,S.Price,S.Amount,S.Remark,D.StuffCname
		          FROM $DataIn.cw15_gyskksheet S
				  LEFT JOIN $DataIn.cw15_gyskkmain M ON M.Id=S.Mid
				  LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		          LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
				  WHERE S.Kid=0 AND M.Estate=0 AND M.CompanyId='$CompanyId'",$link_id);
		if($KKRow=mysql_fetch_array($KKResult)){
		   $KKTable="<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
		<tr><td class='A0111' height='40'>采购单扣款列表</td></tr></table>
		<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
		<tr bgcolor='$Title_bgcolor'>
		<td width='40' height='25' class='A0111' align='center'>选项</td>
		<td width='40' class='A0101' align='center'>序号</td>
		<td width='80' class='A0101' align='center'>扣款单号</td>
		<td width='80' class='A0101' align='center'>扣款日期</td>
		<td width='80' class='A0101' align='center'>总金额</td>
		<td width='80' class='A0101' align='center'>采购单号</td>
		<td width='150' class='A0101' align='center'>配件名</td>
		<td width='70' class='A0101' align='center'>扣款数量</td>
		<td width='70' class='A0101' align='center'>价格</td>
		<td width='70' class='A0101' align='center'>扣款金额</td>
		<td class='A0101' align='center'>扣款原因</td>
		</tr>";
		$TempId=0;
		$n=0;
		    do{
			 $KKId=$KKRow["Id"];
			 if($TempId!=$KKId){
			 $n++;
			 $KKNumberSql=mysql_query("SELECT COUNT(*) AS KKNumber FROM $DataIn.cw15_gyskksheet WHERE Mid='$KKId'",$link_id);
			 $KKNumber=mysql_result($KKNumberSql,0,"KKNumber");
			 }
			 $KKDate=$KKRow["Date"];
			 $TotalAmount=$KKRow["TotalAmount"];
			 $KKBillNumber=$KKRow["BillNumber"];
			 $KKPurchaseID=$KKRow["PurchaseID"]==0?"&nbsp;":$KKRow["PurchaseID"];
		     $KKQty=$KKRow["Qty"];
		     $KKPrice=$KKRow["Price"];
		     $KKAmount=$KKRow["Amount"];
			 $KKRemark=$KKRow["Remark"]==""?"&nbsp;":$KKRow["Remark"];
			 $KKStuffCname=$KKRow["StuffCname"]==""?"&nbsp;":$KKRow["StuffCname"];
			 if($TempId!=$KKId){
		     $KKTable.="<tr>
			<td align='center' class='A0111' height='20' rowspan='$KKNumber'>
			<input name='checkkk[]' type='checkbox' id='checkkk$n' value='$KKId'></td>
			<td align='center' class='A0101' rowspan='$KKNumber'>$n</td>
			<td align='center' class='A0101' rowspan='$KKNumber'>$KKBillNumber</td>
			<td align='center' class='A0101' rowspan='$KKNumber'>$KKDate</td>
			<td align='center' class='A0101' rowspan='$KKNumber'>$TotalAmount</td>";}
			
			$KKTable.="
			<td align='center' class='A0101'>$KKPurchaseID</td>
			<td class='A0101'>$KKStuffCname</td>
			<td align='center' class='A0101'>$KKQty</td>
			<td align='center' class='A0101'>$KKPrice</td>
			<td align='center' class='A0101'>$KKAmount</td>
			<td class='A0101'>$KKRemark</td>
			</tr>";
			$TempId=$KKId;
		    }while($KKRow=mysql_fetch_array($KKResult));
			$KKTable.="</table>";
		 }
        //货款返利
        include "cw_fkout_return.php";
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
echo $DjTable;//订金
echo $KKTable; //供应商扣款
echo $ReturnTable;//货款返利

if ($InvoiceStr!='') echo "发票备注:<div style='color:#FF0000'>" . $InvoiceStr . "</div>";//发票备注

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
	document.form1.action="";
	document.form1.submit();
	}
</script>