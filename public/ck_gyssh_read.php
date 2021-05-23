<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
include "../model/subprogram/UpdateCode.php"; //更新条码 add by zx 20100701

//步骤2：需处理
$ColsNumber=9;
$sumCols="10,11";
$tableMenuS=650;
ChangeWtitle("$SubCompany 待审核送货单");
$funFrom="ck_gyssh";
$From=$From==""?"read":$From;

$Th_Col="选项|60|序号|40|送货单生成日期|100|需求单流水号|100|配件ID|60|配件名称|300|单位|45|历史订单|60|状态审核|80|采购总数|60|未收总数|60|本次送货|60|装框图|50|仓管备注|150";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,17";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){
	$SearchRows="";
	switch($SignType){
	  case 1:
		$SearchRows.=" AND S.Estate=1"; 
		break;
	  case 2:
		$SearchRows.=" AND S.Estate='2'"; 
		break;
	  default:
		$SearchRows.=" AND S.Estate in (1,2) ";
	  break;
	}
	echo "<select name='CompanyId' id='CompanyId' onchange='zhtj(this.name)'>";
	//过滤供应商
	$checkGysSql = mysql_query("SELECT M.CompanyId,P.Forshort 
	FROM $DataIn.gys_shsheet S
	LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid
	LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId
	WHERE 1 $SearchRows GROUP BY M.CompanyId ORDER BY M.CompanyId",$link_id);
	if($checkGysRow=mysql_fetch_array($checkGysSql)){
		do{
			$ProviderTemp=$checkGysRow["CompanyId"];
			$CompanyId=$CompanyId==""?$ProviderTemp:$CompanyId;
			$Forshort=$checkGysRow["Forshort"];
			if ($ProviderTemp==$CompanyId){
				echo"<option value='$ProviderTemp' selected>$ProviderTemp $Forshort</option>";
				$SearchRows.=" AND M.CompanyId='$ProviderTemp'";
				}
			else{
				echo"<option value='$ProviderTemp'>$Forshort</option>";
				}
			}while($checkGysRow=mysql_fetch_array($checkGysSql));
		}

	echo"</select>
		<select name='BillNumber' id='BillNumber' style='width: 80px;' onchange='zhtj(this.name)'>";
	//过滤送货单
	$checkNumSql = mysql_query("SELECT M.BillNumber FROM $DataIn.gys_shmain M 
	LEFT JOIN $DataIn.gys_shsheet S ON S.Mid=M.Id
	WHERE 1 AND M.CompanyId='$CompanyId' $SearchRows GROUP BY S.Mid ORDER BY M.BillNumber DESC",$link_id);
	$TheTChange=1;
	$theFirstBillNumber="";
	if($checkNumRow = mysql_fetch_array($checkNumSql)){
		do{
			$theBillNumber=$checkNumRow["BillNumber"];
			$theFirstBillNumber=$theFirstBillNumber==""?$theBillNumber:$theFirstBillNumber;
			$BillNumber=$BillNumber==""?$theBillNumber:$BillNumber;
			if($theBillNumber===$BillNumber){
				echo"<option value='$theBillNumber' selected>$theBillNumber</option>";
				$SearchRows.=" AND M.BillNumber='$theBillNumber'";
				$TheTChange=0;
				}
			else{
				echo"<option value='$theBillNumber'>$theBillNumber</option>";
				}
			}while($checkNumRow = mysql_fetch_array($checkNumSql));
		} 
		echo"</select>&nbsp;";
		if ($TheTChange==1)
			{
				$BillNumber=$theFirstBillNumber;
			}
		//echo "SELECT S.BillNumber FROM $DataIn.gys_shmain S WHERE 1 AND S.CompanyId='$CompanyId' ORDER BY S.BillNumber <br>";
	$TempEstateSTR="SignTypeStr".strval($SignType); 
	$$TempEstateSTR="selected";	
	echo"</select>
		<select name='SignType' id='SignType' style='width: 100px;' onchange='zhtj(this.name)'>";
		echo"<option value=''  $SignTypeStr0>全部</option>";
		echo"<option value='1' $SignTypeStr1>仓管未审核</option>";
        echo"<option value='2' $SignTypeStr2>品质未审核</option>";
    echo "<select>";


}

echo"<input name='TempValue' type='hidden' id='TempValue'>";
  echo"$CencalSstr	";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$SearchRows=$SearchRows==""?"AND S.Estate in (1,2)":$SearchRows;
$mySql= "SELECT M.CompanyId,M.BillNumber,
		S.Id,S.StockId,S.Qty,S.StuffId,S.Estate,S.SendSign,D.StuffCname,D.Picture,U.Name AS UnitName,(G.AddQty+G.FactualQty) AS cgQty,M.Date,
                G.POrderId,Y.OrderPO,Y.ProductId,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,C.Forshort AS Client,P.cName,P.TestStandard   
		FROM $DataIn.gys_shsheet S
		LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
        LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
        LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
        LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
		WHERE 1  $SearchRows  AND S.Estate>0 AND NOT EXISTS (SELECT CG.StockId FROM $DataIn.cg1_stuffcombox CG WHERE CG.StockId=S.StockId) 
	UNION ALL
	    SELECT M.CompanyId,M.BillNumber,
		S.Id,S.StockId,S.Qty,S.StuffId,S.Estate,S.SendSign,D.StuffCname,D.Picture,U.Name AS UnitName,(G.AddQty+G.FactualQty) AS cgQty,M.Date,
                G.POrderId,Y.OrderPO,Y.ProductId,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,C.Forshort AS Client,P.cName,P.TestStandard   
		FROM $DataIn.gys_shsheet S
		LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
		LEFT JOIN $DataIn.cg1_stuffcombox G ON G.StockId=S.StockId 
        LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
        LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
        LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
		WHERE 1  $SearchRows  AND S.Estate>0 AND G.Id>0 
		 ORDER BY Id";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$StockId=$myRow["StockId"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Picture=$myRow["Picture"];
		include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
		$BuyerId=$myRow["BuyerId"];
		
        
		$cgQty=$myRow["cgQty"];
		$Qty=$myRow["Qty"];
		$Estate=$myRow["Estate"];
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"]; 
		$LockRemark="";
                
                $POrderId=$myRow["POrderId"];
                $ProductId=$myRow["ProductId"];
                $OrderPO=$myRow["OrderPO"];
                $PQty=$myRow["PQty"];
		$PackRemark=$myRow["PackRemark"];
		$sgRemark=$myRow["sgRemark"];
		$ShipType=$myRow["ShipType"];
		$Leadtime=$myRow["Leadtime"];
                $cName=$myRow["cName"];
	        $Client=$myRow["Client"];
                $TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";   
                
		switch($Estate){//判断品质审核状态
	      case 1:
		    $LockRemark="";
			$Estate="<div class='greenB'>仓管未审核</div>";
			break;
		  case 2:
		     $LockRemark="品质未审核";
			 $Estate="<div class='redB'>品质未审核</div>";
		     break;
		  default:
		     $LockRemark=$LockRemark==""?"未知错误，请与管理员联系":$LockRemark;
		     $Estate="<div class='redB'>$LockRemark</div>";
			break;
		}
		
		//add by zx 2011-0427  begin
		$CompanyId=$myRow["CompanyId"];
		
		$BillNumber=$myRow["BillNumber"];
		
		$SendSign=$myRow["SendSign"];
		$SignString="";
		//if ($SendSign==1) // SendSign: 0送货，1补货, 2备品 
		switch ($SendSign){
			case 1:
				//$LockRemark="";
				$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
											   LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
											   WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
				$thQty=mysql_result($thSql,0,"thQty");
				$thQty=$thQty==""?0:$thQty;
				
				//补货的数量 add by zx 2011-04-27
				$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
											   LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
											   WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
				$bcQty=mysql_result($bcSql,0,"bcQty");	
				$bcQty=$bcQty==""?0:$bcQty;
				
				
				//相同的已入库,则锁定
				$HavebcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
											   LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
											   WHERE M.CompanyId = '$CompanyId' AND M.BillNumber = '$BillNumber'  AND S.StuffId = '$StuffId' ",$link_id);
				$havebcQty=mysql_result($HavebcSql,0,"bcQty");	
				$havebcQty=$havebcQty==""?0:$havebcQty;
				//$noQty=$havebcQty;

				//
				$cgQty=$thQty-$bcQty;
				$noQty=$cgQty;
				if ($havebcQty>0){  //如果同一单号已出现，则说明已入库
					$noQty=0;
				}	
				
				$SignString="(补货)";
				$StockId="本次补货";
			 break;
			case 2:
			  
				//相同的已入库,则锁定
				$HavebpSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck11_bpmain M 
											   LEFT JOIN $DataIn.ck11_bpsheet S ON S.Mid = M.Id
											   WHERE M.CompanyId = '$CompanyId' AND M.BillNumber = '$BillNumber'  AND S.StuffId = '$StuffId' ",$link_id);
				$havebpQty=mysql_result($HavebpSql,0,"bcQty");	
				$havebpQty=$havebpQty==""?0:$havebpQty;
				//$noQty=$havebcQty;
			 	 $cgQty=0;
			     $noQty=$Qty;
				if ($havebpQty>0){  //如果同一单号已出现，则说明已入库
					$noQty=0;
				}				  
			  $SignString="(备品)";
			  $StockId="本次备品";
			 break;
			default :
				$rkTemp=mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS Qty FROM $DataIn.ck1_rksheet R 
					LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
					WHERE R.StockId='$StockId'",$link_id);
				//$LockRemark="";
				$rkQty=mysql_result($rkTemp,0,"Qty");
				$noQty=$cgQty-$rkQty;//全部未送货
							
				
			 break;
		}
		//echo "$noQty=$cgQty-$rkQty-$shQty";
		 // end;
		
                //仓管备注
                 $remarkSql=mysql_query("SELECT Remark FROM $DataIn.ck6_shremark WHERE ShId='$Id' LIMIT 1",$link_id);
                 if($remarkRow=mysql_fetch_array($remarkSql)){
                       $Remark=$remarkRow["Remark"];
                     }
                 else{
                       $Remark="&nbsp;";
                     }
		/*
		//已收货总数
		$rkTemp=mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS Qty FROM $DataIn.ck1_rksheet R 
			LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
			WHERE R.StockId='$StockId'",$link_id);
		$LockRemark="";
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$noQty=$cgQty-$rkQty;//全部未送货
		*/
		//echo "$SignString";
		if($noQty<=0 ){  //当前已全部入库，则显示，入库数量
			$LockRemark="错误，请通知供应商:该需求单已全部入库，请核查该送货单！";
			}
		else {
			if($noQty<$Qty && $SendSign!=2){  //当前送货量比未送货量还大,则强行要改
				$LockRemark="错误，请通知供应商:本次送货的数量多于未送货的总数，送货数量需更新！";
				}
			else {
				//$QtyStr="<input name='QTY[$i]' type='text' id='QTY$i' value='$Qty' size='9' class='QtyRight' onfocus='toTempValue(this);this.select()' onBlur='Indepot(this,$noQty)'>";
				//不在公司内修改，直接通知供应商修改
				$QtyStr=$Qty;
			}
		}
		$Locks=1;
        $OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>查看</a>";      
		$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
			title='显示或隐藏订单信息资料.' width='13' height='13' style='CURSOR: pointer'>";
			$StuffListTB="
				<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
                                <tr bgcolor='#B7B7B7'>
				  <td class='A0111' ><br> &nbsp;<span class='redB'>订单PO：</span>$OrderPO&nbsp;&nbsp;<span class='redB'>业务单流水号：</span>$POrderId ($Client : $TestStandard)&nbsp;&nbsp;<span class='redB'>数量：</span>$PQty &nbsp; &nbsp;<span class='redB'>订单备注：</span>$PackRemark &nbsp;&nbsp;<span class='redB'>出货方式：</span>$ShipType &nbsp;&nbsp;<span class='redB'>生管备注：</span>$sgRemark &nbsp;&nbsp;<span class='redB'>PI交期：</span>$Leadtime</td>
				</tr>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";

		
		$ValueArray=array(
			array(0=>$Date,			1=>"align='center'"),
			array(0=>$StockId,			1=>"align='center'"),
			array(0=>$StuffId,			1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$UnitName,			1=>"align='center'"),
			array(0=>$OrderQtyInfo,			1=>"align='center'"),
			array(0=>$Estate,			1=>"align='center'"),
			array(0=>$cgQty."&nbsp;", 	1=>"align='right'"),
			array(0=>"<div class='redB'>".$noQty."</div>",			1=>"align='right'"),
			array(0=>$Qty, 	1=>"align='right'"),
                        array(0=>$ClientImage,			1=>"align='center'"),
                        array(0=>$Remark)
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
function toTempValue(thisE){
	document.form1.TempValue.value=thisE.value;
	}
function Indepot(thisE,SumQty){
	var oldValue=document.form1.TempValue.value;
	var thisValue=thisE.value;
	var CheckSTR=fucCheckNUM(thisValue,"");
	if(CheckSTR==0){
		alert("不是规范的数字！");
		thisE.value=oldValue;
		return false;
		}
	else{
		if((thisValue>SumQty) || thisValue==0){
			alert("不在允许值的范围！");
			thisE.value=oldValue;
			return false;
			}
		}
	}
	
function zhtj(obj){
	switch(obj){
		case "CompanyId"://改变采购
			//document.forms["form1"].elements["GysPayMode"].value="";
			var BillNumber= document.getElementById("BillNumber");
			if(BillNumber!=null){
				BillNumber.value="";
				}
       
		break;
		 /*
		case "GysPayMode":
			if(document.all("CompanyId")!=null){
				document.forms["form1"].elements["CompanyId"].value="";
				}
			if(document.all("chooseDate")!=null){
				document.forms["form1"].elements["chooseDate"].value="";
				}
		break;
		case "CompanyId":
			if(document.all("chooseDate")!=null){
				document.forms["form1"].elements["chooseDate"].value="";
				}
		break;
		*/
		}
	document.form1.action="ck_gyssh_read.php";
	document.form1.submit();
}	
</script>