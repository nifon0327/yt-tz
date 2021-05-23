<?php
/*$DataIn.电信---yang 20120801
$DataIn.cg1_stocksheet
$DataIn.cg1_stockmain
$DataIn.stuffdata
$DataPublic.staffmain
$DataIn.trade_object
$DataIn.ck1_rksheet
$DataIn.ck5_llsheet
二合一已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=1480;
/*
echo"<table id='$TableId' width='$subTableWidth' cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='10' height='20'></td>
		<td width='80' align='center'>订单PO</td>
		<td width='90' align='center'>内部单号</td>
		<td width='330' align='center'>产品名称</td>
		<td width='55' align='center'>订单数</td>
		<td width='55' align='center'>本次完成</td>
		<td width='55' align='center'>总完成进度（%）</td>
		<td width='55' align='center'>组装总时间(分)</td>
		<td width='55' align='center'>人数</td>
		<td width='55' align='center'>人力(RMB)/单品</td>
		<td width='55' align='center'>人力总计(RMB)</td>
		";
*/
//width='$subTableWidth'


echo"<table id='$TableId' width='$subTableWidth'  cellspacing='1' border='1' align='center'> <tr bgcolor='#CCCCCC'>
      <td width='30' Class='A1111'>序号</td>
      <td width='80' Class='A1101'>下单日期</td>
      <td width='50' Class='A1101'>采购</td>
      <td width='120' Class='A1101'>供应商</td>
      <td width='60' Class='A1101'>采购单号</td>
      <td width='60' Class='A1101'>采购备注</td>
      <td width='60' Class='A1101'>预付金额</td>


      <td width='50' Class='A1101'>配件编码</td>
      <td width='250' Class='A1101'>非bom配件名称</td>
      <td width='80' Class='A1101'>配件条码</td>
      <td width='30' Class='A1101'>申购<br>备注</td>
      <td width='30' Class='A1101'>货币</td>
      <td width='60' Class='A1101'>单价</td>
      <td width='60' Class='A1101'>申购数量</td>
      <td width='30' Class='A1101'>单位</td>
      <td width='60' Class='A1101'>金额</td>
      <td width='50' Class='A1101'>收货数</td>
      <td width='40' Class='A1101'>货款<br>状态</td>

      <td width='50' Class='A1101'>申购人</td>
		";

echo "</tr>";
/*
       <td width='60' Class='A1101'>选项</td>
      <td width='50' Class='A1101'>欠数</td>
      <td width='60' Class='A1101'>在库</td>
      <td width='60' Class='A1101'>采购库存</td>
      <td width='60' Class='A1101'>最低库存</td>
      <td width='40' Class='A1101'>记录<br>状态</td>
      <td width='40' Class='A1101'>收货<br>状态</td>

	  <td width='80' Class='A1101'>申购时间</td>

*/

//$Th_Col="选项|45|序号|45|日期|70|订单PO|100|内部单号|80|产品名称|300|订单数|50|本次完成|50|总完成（%）|50|组装时间(分)|50|人数|50|人力(RMB)/单品|60|人力总计(RMB)|60|备注|50|登记|60";

/*
echo "SELECT M.OrderPO,S.Estate,S.ProductId,P.cName,D.Id,D.POrderId,S.Qty,D.FQty,D.AllMins,D.Workers,D.Remark,D.Date,D.Locks,D.Operator
FROM $DataIn.sc2_Pfinish D
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=D.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
WHERE 1 And D.Date=$Date ORDER BY D.ID Desc,M.OrderPO ";
*/
//echo "StuffId:$StuffId";

$i=1;

$mySql="SELECT G.Date AS cgDate,G.PurchaseID,G.Remark AS mainRemark,G.BuyerId,
		F.Id,F.Mid,F.GoodsId,F.Qty,F.Price,F.Remark,F.ReturnReasons,F.rkSign,F.Estate,F.Locks,F.Date,
		D.GoodsName,D.BarCode,D.Attached,D.Unit,
		Dd.TypeName,
		Ee.Forshort,Ee.CompanyId,Ff.Name,
		Gg.wStockQty,Gg.oStockQty,Gg.mStockQty,IFNULL(B.Estate,9) AS cwSign,C.Symbol 
	FROM $DataIn.cw2_gysskrelation R	
	LEFT JOIN $DataIn.nonbom6_cgsheet F ON R.nonbom6_sID=F.Id
	LEFT JOIN $DataIn.nonbom6_cgmain G ON F.Mid=G.Id
	LEFT JOIN $DataPublic.nonbom4_goodsdata D ON D.GoodsId=F.GoodsId 
	LEFT JOIN $DataPublic.nonbom2_subtype Dd ON Dd.Id=D.TypeId
	LEFT JOIN $DataPublic.nonbom3_retailermain Ee ON Ee.CompanyId=G.CompanyId 
	LEFT JOIN $DataPublic.staffmain Ff ON Ff.Number=F.Operator 
	LEFT JOIN $DataPublic.nonbom5_goodsstock Gg ON Gg.GoodsId=F.GoodsId
	LEFT JOIN $DataIn.nonbom12_cwsheet B ON B.cgId=F.Id
	LEFT JOIN $DataPublic.currencydata C ON C.Id=Ee.Currency  
	WHERE 1 AND R.Mid=$Id  ORDER BY G.Date DESC,G.Id DESC";
//echo $mySql;
$i=1;
$SumAmount=0;
$mainResult = mysql_query($mySql,$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$LockRemark=$rkBgColor=$wsBgColor="";
		//主单信息
		$cgDate=$mainRows["cgDate"];
		$PurchaseID=$mainRows["PurchaseID"];
		//预付订金
		$checkDj=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) AS djAmount,Estate AS djEstate FROM $DataIn.nonbom11_djsheet WHERE PurchaseID='$PurchaseID' AND Estate='0'",$link_id));

		$Mid=$mainRows["Mid"];
		$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
		$PurchaseID="<a href='nonbom6_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
		$mainRemark=$mainRows["mainRemark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$mainRows[mainRemark]' width='18' height='18'>";
		$Operator=$mainRows["BuyerId"];
		include "../model/subprogram/staffname.php";
		$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"nonbom6_upmain\",$Mid)' src='../images/edit.gif' title='更新采购主单资料' width='13' height='13'>";
		//明细资料
		$GoodsId=$mainRows["GoodsId"];
		if($GoodsId!=""){
			$checkidValue=$mainRows["Id"];
			$GoodsId=$mainRows["GoodsId"];
			$GoodsName=$mainRows["GoodsName"];
			$Attached=$mainRows["Attached"];
			$BarCode=$mainRows["BarCode"];
			$Remark=$mainRows["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$mainRows[Remark]' width='18' height='18'>";
			$Unit=$mainRows["Unit"];
			$Symbol=$mainRows["Symbol"];
			$Price=$mainRows["Price"];
			$Qty=del0($mainRows["Qty"]);
			$Amount=$Qty*$Price;
			//入库数量
			$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.nonbom7_insheet WHERE GoodsId='$GoodsId' AND cgId='$checkidValue'",$link_id);
			$rkQty=mysql_result($rkTemp,0,"Qty");
			$rkQty=$rkQty==""?0:del0($rkQty);
			$wsQty=$Qty-$rkQty;
			if($rkQty==$Qty){
				$rkBgColor="class='greenB'";
				$rkSign="<sapn class='greenB'>已收货</span>";
				$rkQty="<a href='nonbom7_list.php?cgId=$checkidValue' target='_blank' style='color:#093'>$rkQty</a>";
				//更新入库标记
					if ($mainRows["rkSign"]>0){
						 $UprkSignSql="UPDATE $DataIn.nonbom6_cgsheet SET rkSign='0' WHERE Id='$checkidValue' ";
			              $UprkSignResult = mysql_query($UprkSignSql,$link_id);
					}
				}
			else{
				$rkBgColor="class='redB'";
				$wsBgColor="class='redB'";
				if($rkQty==0){
					$rkSign="<sapn class='redB'>未收货</span>";
					$rkQty="&nbsp;";
					$rkSignVal=1;
					}
				else{
					$rkSign="<sapn class='yellowB'>部分收货</span>";
					$rkQty="<a href='nonbom7_list.php?cgId=$checkidValue' target='_blank' style='color:#F00'>$rkQty</a>";
					$rkSignVal=2;
					}
					//更新入库标记
					if ($mainRows["rkSign"]==0){
						 $UprkSignSql="UPDATE $DataIn.nonbom6_cgsheet SET rkSign='$rkSignVal' WHERE Id='$checkidValue' ";
			              $UprkSignResult = mysql_query($UprkSignSql,$link_id);
					}
				}
			$wsQty=$wsQty==0?"&nbsp;":$wsQty;
			$wStockQty=del0($mainRows["wStockQty"]);
			$oStockQty=del0($mainRows["oStockQty"]);
			$mStockQty=del0($mainRows["mStockQty"]);
			$Attached=$mainRows["Attached"];
			if($Attached==1){
				$Attached=$GoodsId.".jpg";
				$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
				$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
				}
			$Locks=$unLocks==1?1:$mainRows["Locks"];
			$cwSign=$mainRows["cwSign"];
			switch($cwSign){
				case 0://已结付
					$cwSign="<span class='greenB'>已结付</span>";
					$LockRemark="记录已经结付，强制锁定操作！";
					break;
				case 2://请款中
					$cwSign="<span class='yellowB'>请款中</span>";
					break;
				case 3://请款通过
					$cwSign="<span class='yellowB'>未结付</span>";
					$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
					break;
				case 1://审核退回
					$ReturnReasons=$mainRows["ReturnReasons"]==""?"请款退回:未填写退回原因":"请款退回:".$mainRows["ReturnReasons"];
			    	$cwSign="<img src='../images/warn.gif' title='$ReturnReasons' width='18' height='18'>";
					break;
				default:
						$cwSign="<span class='redB'>未请款</span>";
					break;
				}
			$Estate=$mainRows["Estate"];
			switch($Estate){
				case 1:
				$Estate="<span class='greenB'>已审核</span>";
				break;
				case 4://审核退回
					$ReturnReasons=$mainRows["ReturnReasons"]==""?"审核退回:未填写退回原因":"审核退回:".$mainRows["ReturnReasons"];
			    	$Estate="<img src='../images/warn.gif' title='$ReturnReasons' width='18' height='18'>";
				break;
				default:
				$LockRemark="记录有更新，更新审核中，锁定操作！";
				$Estate="<span class='redB'>需审核</span>";
				break;

				}
			$Forshort=$mainRows["Forshort"];
			$Date=$mainRows["Date"];
			$Name=$mainRows["Name"];

			$CompanyId=$mainRows["CompanyId"];
			//加密
			$CompanyId=anmaIn($CompanyId,$SinkOrder,$motherSTR);
			$Forshort="<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
			//历史单价
			$Price="<a href='nonbom4_history.php?GoodsId=$GoodsId' target='_blank'>$Price</a>";
			//配件分析
			$GoodsId="<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
			//预付金额
			$djAmount=$checkDj["djAmount"];
			$djEstate=$checkDj["djEstate"];
			if($djAmount>0){
				if($Amount==$djAmount){
					$djAmount="<span class='greenB'>$djAmount</span>";
					}
				else{
					$djAmount="<span class='redB'>$djAmount</span>";
					}
				}
			else{
				$djAmount="&nbsp;";
				}
			if($Locks==0){//锁定状态:A一种是可以操作记录（分权限）；B一种是不可操作记录（不分权限）
				if($Keys & mLOCK){
					if($LockRemark!=""){//财务强制锁定
						$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'>";
						}
					else{
						$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/lock.png' width='15' height='15'>";
						}
					}
				else{		//A2：无权限对锁定记录操作
					$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'>";
					}
				}
			else{
				if(($BuyerId==$Login_P_Number && ($Keys & mUPDATE || $Keys & mDELETE)) || $Keys & mLOCK){//有权限
					if($LockRemark!=""){
						$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'/>";
						}
					else{
						$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/unlock.png' width='15' height='15'/>";
						}
					}
				else{//无权限
					$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'/>";
					}
				}
			$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
	$SumAmount=$SumAmount+$Amount;
	echo"<tr bgcolor='$theDefaultColor'>";

	    echo "<td bgcolor='$Sbgcolor' align='right' height='20'>$i</td>";//
		echo"<td  align='Left' >$cgDate</td>";
		echo"<td  align='center'>$Operator</td>";//
		echo"<td  align='Left' >$Forshort</td>";
		echo"<td  align='Left'>$PurchaseID</td>";
		echo"<td  align='Left'>$mainRemark</td>";
        //echo"<td  align='Left'>$j</td>";
	    echo "<td align='right' >$djAmount</td>";//
		echo"<td  align='Left' >$GoodsId</td>";
		echo"<td  align='Left'>$GoodsName</td>";//
		echo"<td  align='Left' >$BarCode</td>";
		echo"<td  align='Left'>$Remark</td>";
		echo"<td  align='Left'>$Symbol</td>";
	    echo"<td  align='right'>$Price</td>";//
		echo"<td  align='right' >$Qty</td>";
		echo"<td  align='center'>$Unit</td>";//
		echo"<td  align='right' >$Amount</td>";
		echo"<td  align='right'><div $rkBgColor>$rkQty</div></td>";
		//echo"<td  align='right'><div $wsBgColor>$wsQty</div></td>";

	    //echo "<td align='right' >$wStockQty</td>";//
		//echo"<td  align='right' >$oStockQty</td>";
		//echo"<td  align='right'>$mStockQty</td>";//
		//echo"<td  align='Left' >$Estate</td>";
		//echo"<td  align='Left'>$rkSign</td>";
		echo"<td  align='Left'>$cwSign</td>";


		//echo"<td  align='Left'>$Date</td>";
		echo"<td  align='Left'>$Name</td>";


		echo"</tr>";



		$i=$i+1;

		//echo "<td width='55' align='center'>$Date</td>";
			}
		}while($mainRows = mysql_fetch_array($mainResult));
	echo"<tr><td height='20' >总计:</td>
	<td  colspan='15' align='right' ><span class='redB'>$SumAmount</span></td>
	<td  colspan='3'>&nbsp;</td>
	</tr>";
	}

else{
	echo"<tr><td height='30' colspan='19'>没有关联非BOM采购单.</td></tr>";
	}

echo"</table>"."";

?>