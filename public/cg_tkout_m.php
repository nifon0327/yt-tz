<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=19;
$tableMenuS=600;
$sumCols="7,8,9,10,11,14";		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 客户退款配件待审核列表");
$funFrom="cg_tkout";
$Th_Col="选项|40|序号|40|采购流水号|90|配件名称|230|图档|30|历史订单|60|QC图|40|订单数|55|使用库存|55|需求数|55|增购数|55|实购数|55|单价|55|单位|45|金额|60|客户|80|出货日期|80|Invoice|80|状态|40|采购员|50";
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
	$MonthResult = mysql_query("SELECT S.Month FROM $DataIn.cw1_tkoutsheet S WHERE S.Estate='2' AND S.Month<>'' GROUP BY S.Month ORDER BY S.Month",$link_id);
	if ($MonthRow = mysql_fetch_array($MonthResult)) {
		echo"请款月份 <select name='chooseMonth' id='chooseMonth' onchange='zhtj(this.name)'>";
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
		$SearchRows.=" and S.Month='无效'";
		}
	//客户
	$clientSql= mysql_query("SELECT 
	M.CompanyId,P.Forshort,P.Letter 
	FROM $DataIn.cw1_tkoutsheet S 
	LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = S.POrderId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	WHERE 1  $SearchRows   GROUP BY M.CompanyId ORDER BY P.Letter",$link_id);
	
	if($clientRow = mysql_fetch_array($clientSql)){
		echo "<select name='CompanyId' id='CompanyId' onchange='zhtj(this.name)'>";
		do{
			$Letter=$clientRow["Letter"];
			$Forshort=$clientRow["Forshort"];
			$Forshort=$Letter.'-'.$Forshort;
			$thisCompanyId=$clientRow["CompanyId"];
			$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;				
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort </option>";
				$SearchRows.=" and M.CompanyId='$thisCompanyId'";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($clientRow = mysql_fetch_array($clientSql));
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
$mySql="SELECT 	S.Id,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.BuyerId,P.Forshort,SM.Name,A.StuffId,A.StuffCname,A.TypeId,A.Gfile,A.Gstate,A.Picture,U.Name AS UnitName,H.Date as OutDate,S.Amount,H.InvoiceNO,H.InvoiceFile,Count(*) AS ShipCount 
 	FROM $DataIn.cw1_tkoutsheet S 
 	LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = S.POrderId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	LEFT JOIN $DataIn.staffmain SM ON SM.Number=S.BuyerId	
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
	LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit
    Left JOIN $DataIn.ch1_shipsheet C ON C.PorderId=S.PorderId
    Left JOIN $DataIn.ch1_shipmain H ON H.Id=C.Mid		
	WHERE 1 and S.Estate=2 $SearchRows GROUP BY S.StockId ORDER BY S.Month DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);

if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);	
	$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StockId=$myRow["StockId"];//采购流水号
		$OutStockId=$StockId;
		$StuffCname=$myRow["StuffCname"];//配件名称
		$Gfile=$myRow["Gfile"];
		$Gstate=$myRow["Gstate"];  //状态
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
		$Qty=$FactualQty+$AddQty;
		$Price=$myRow["Price"];	//价格
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$Estate="<div class='yellowB' title='请款中'>×.</div>";
		$OutDate=$myRow["OutDate"]==""?"&nbsp":$myRow["OutDate"]; 
		
		$POrderId=$myRow["POrderId"];
		$ShipCount=$myRow["ShipCount"];
		if ($ShipCount>1){
				//分批出货
				$InvoiceNOSTR="";
				$chResult=mysql_query("SELECT H.InvoiceNO,H.InvoiceFile FROM $DataIn.ch1_shipsheet E 
			                               LEFT JOIN $DataIn.ch1_shipmain H ON H.Id=E.Mid  
			                               WHERE E.PorderId='$POrderId' order by H.Date",$link_id);
			  while($chRow = mysql_fetch_array($chResult)){
				    $InvoiceNO=$chRow["InvoiceNO"];
	                $InvoiceFile=$chRow["InvoiceFile"];
			        $f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
			        $InvoiceNOSTR.=$InvoiceFile==0?"":"<div><a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$InvoiceNO</a></div>";
				} 
				  $InvoiceNO=$InvoiceNOSTR==""?"&nbsp;":$InvoiceNOSTR;
			}
			
			else{
	           $InvoiceNO=$myRow["InvoiceNO"];
		        $InvoiceFile=$myRow["InvoiceFile"];
				$f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
				$InvoiceNO=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$InvoiceNO</a>";
		    }
   	
        
		$Amount=sprintf("%.2f",$OrderQty*$Price);//本记录金额合计
		//$Amount=$myRow["Amount"];
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
			array(0=>$Price,1=>"align='right'"),
			array(0=>$UnitName,1=>"align='center'"),
			array(0=>$Amount,1=>"align='right'"),
             array(0=>$Forshort,1=>"align='center'"),
			array(0=>$OutDate,1=>"align='center'"),	
			array(0=>$InvoiceNO,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Buyer,1=>"align='center'")
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
<script>
function zhtj(obj){
	switch(obj){
		case "chooseMonth":
			if(document.all("CompanyId")!=null){
				document.forms["form1"].elements["CompanyId"].value="";
				}
		break;
		}
	document.form1.action="cg_tkout_m.php";
	document.form1.submit();
	}
</script>