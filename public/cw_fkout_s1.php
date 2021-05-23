<?php 
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|60|序号|40|请款月份|60|采购流水号|100|配件名称|230|订单数|55|使用库存|55|需求数|55|增购数|55|实购数|55|单价|55|单位|45|金额|60|未收货|55|未补货|55|出货日期|80|请款<br>方式|30|发票信息|80|状态|40|采购员|50";
$ColsNumber=18;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$Parameter.=",Bid,$Bid,Jid,$Jid";
$SearchSTR=0;		//不允许搜索
$SearchRows=" and S.Estate='3'";
$CompanyId=$uType!=''?$uType:$CompanyId;

//步骤3：
include "../model/subprogram/s1_model_3.php";
//echo $Parameter;
//步骤4：可选，其它预设选项
//月份
$MonthResult = mysql_query("SELECT S.Month FROM $DataIn.cw1_fkoutsheet S WHERE 1 $SearchRows GROUP BY S.Month ORDER BY S.Month desc",$link_id);
	if ($MonthRow = mysql_fetch_array($MonthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
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
			 //  }
			}while($MonthRow = mysql_fetch_array($MonthResult));
		echo"</select>&nbsp;";
}
	
$GysSql= mysql_query("SELECT S.CompanyId,P.Forshort,P.Letter 
	FROM $DataIn.cw1_fkoutsheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
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

//步骤4：需处理-可选条件下拉框
$otherAction="<span onClick='Comeback($Action)' $onClickCSS>确定</span>&nbsp;";//自定义功能
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 	S.Id,S.Month,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.BuyerId,P.Forshort,M.Name,D.StuffCname,U.Name AS UnitName,D.TypeId,S.CompanyId,S.AutoSign,S.InvoiceId,I.InvoiceNo,I.InvoiceFile,I.Remark   
 	FROM $DataIn.cw1_fkoutsheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId	
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
	LEFT JOIN $DataIn.cw1_fkoutinvoice I ON I.Id=S.InvoiceId  
	WHERE S.Estate=3 $SearchRows ORDER BY S.Month DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
        $InvoiceIdArray=array();
	do{
		$m=1;
		$Id=$myRow["Id"];
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
		
		
		if($TypeId=='9104'){//如果是客户退款，请款总额为订单数*价格
		    $AmountQty=$OrderQty;	//采购总数
			}
		else{
		    $AmountQty=$FactualQty+$AddQty;	//采购总数
		    }
		$Price=$myRow["Price"];	//采购价格
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$OutDate=$myRow["OutDate"]==""?"&nbsp":$myRow["OutDate"];    

		$Estate="<div class='redB'>未付</div>";
		//统计
		$Amount=sprintf("%.2f",$AmountQty*$Price);//本记录金额合计	
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
			//$StockId="<a href='ck_rk_list.php?Sid=$Sid' target='_blank' title='点击查看收货记录'>$StockId</a>";
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

		
		$BackValue=$StockId . "^^" . $StuffCname. "^^" . $Amount . "^^" . $Id;
    
		$ValueArray=array(
		    array(0=>$Month,1=>"align='center'"),
			array(0=>$StockId,1=>"align='center'"),
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
		$checkidValue=$BackValue;
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
<script  type=text/javascript>
//返回选定的采购流水号
function Comeback(Action){
	var returnq="";
	var j=1;
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		if (e.type=="checkbox"){
			if(e.checked){
				if (j==1){
					returnq=e.value;j++;
					}
				else{
					returnq=returnq+"``"+e.value;j++;
					}					
				} 
			}
		}
	returnValue=returnq;
	this.close();
	}
</script>