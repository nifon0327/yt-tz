<?php
//电信-zxq 2012-08-01
$Th_Col="配件|55|ID|30|PO|80|中文名|235|Product Code|150|检讨|30|Unit|35|Qty|50|产品信息|130|交货日期|100|期限|40|审核|50";
$Field=explode("|",$Th_Col);
$Count=count($Field);
//$SearchRows=" AND S.scFrom=0 AND S.Estate>0 AND S.Estate<3";//生产状态为0,且为未出货的订单:1、未审核  2、已审核待出	4、已生成出货单	0、已出货
$SearchRows=" AND S.scFrom=0 AND S.Estate>0 AND S.Estate<3";
$ClientList="";
$ClientResult= mysql_query("
SELECT M.CompanyId,C.Forshort 
	FROM $DataIn.yw1_ordermain M 
	LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
	WHERE 1 $SearchRows 
	GROUP BY M.CompanyId order by M.CompanyId ,M.OrderDate desc",$link_id);
if ($ClientRow = mysql_fetch_array($ClientResult)){
	$ClientList="<select name='CompanyId' id='CompanyId'  onChange='ResetPage(1,2)'>";
	$i=1;
	do{
		$theCompanyId=$ClientRow["CompanyId"];
		$theForshort=$ClientRow["Forshort"];
		$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
		if($CompanyId==$theCompanyId){
			$ClientList.="<option value='$theCompanyId' selected>$i 、$theForshort</option>";
			$SearchRows.=" AND M.CompanyId='$theCompanyId'";
			$nowInfo="当前:品检审核 - ".$theForshort;
			}
		else{
			$ClientList.="<option value='$theCompanyId'>$i 、$theForshort</option>";
			}
		$i++;
		}while($ClientRow = mysql_fetch_array($ClientResult));
		$ClientList.="</select>";
	}
//步骤5：
echo"<table  border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td colspan='8' height='40px' class=''>$ClientList</td><td colspan='4' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";


$DefaultBgColor=$theDefaultColor;
$i=1;
$mySql="SELECT M.CompanyId,S.OrderPO,M.OrderDate,M.ClientOrder,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.TestStandard,P.pRemark,U.Name AS Unit,PI.PI,PI.Leadtime
FROM $DataIn.yw1_ordersheet S 
LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId 
WHERE 1 $SearchRows ORDER BY S.Estate,M.OrderDate";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/productfile/",$SinkOrder,$motherSTR);
	do{
		$czSign=1;
		$AskDay="";
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
		//加密参数
		$ClientOrder=$myRow["ClientOrder"];
		if($ClientOrder!=""){
			$f2=anmaIn($ClientOrder,$SinkOrder,$motherSTR);
			$d2=anmaIn("download/clientorder/",$SinkOrder,$motherSTR);
			$ClientOrder="<span onClick='OpenOrLoad(\"$d2\",\"$f2\",6)' style='CURSOR: pointer;color:#FF6633'>查看</span>";
			}
		else{
			$ClientOrder="&nbsp;";
			}

		$Id=$myRow["Id"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getProductImage.php";
		//include "../admin/subprogram/product_teststandard.php";
		$Unit=$myRow["Unit"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];
		$PackRemark=$myRow["PackRemark"]==""?"&nbsp;":$myRow["PackRemark"];
		$DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
		$Leadtime=$myRow["Leadtime"]==""?"&nbsp;":$myRow["Leadtime"];
		$pRemark=$myRow["pRemark"]==""?"&nbsp;":$myRow["pRemark"];
		$OrderDate=$myRow["OrderDate"];
		//如果超过30天
		$AskDay=AskDay($OrderDate);
		$BackImg=$AskDay==""?"":"background='../images/cj$AskDay'";

		$OrderDate=CountDays($OrderDate,0);
		$POrderId=$myRow["POrderId"];
		$Estate=$myRow["Estate"];
		$LockRemark=$Estate==4?"已生成出货单.":"";
		$Locks=$myRow["Locks"];

		$sumQty=$sumQty+$Qty;

		//订单状态色
		$checkColor=mysql_query("SELECT G.Id FROM $DataIn.cg1_stocksheet G 
			LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
			WHERE 1 AND T.mainType<2 AND G.Mid='0' and (G.FactualQty>'0' OR G.AddQty>'0' ) and G.PorderId='$POrderId' LIMIT 1",$link_id);
		if($checkColorRow = mysql_fetch_array($checkColor)){
			$OrderSignColor="bgColor='#FFFFFF'";//有未下需求单
			$czSign=0;//不能审核
			//echo "d";
			}
		else{//已全部下单
			$OrderSignColor="bgColor='#339900'";	//设默认绿色
			//生产数量不等时，黄色	不能审核
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
				$OrderSignColor="bgColor='#FFCC00'";	//黄色	不能审核
				$czSign=0;//不能审核,状态出错
				//echo "c";
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
		//动态读取配件资料
			$showPurchaseorder="[ + ]";
			$ListRow="<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";;
		//0:内容	1：对齐方式		2:单元格属性		3：截取

		//检查权限
		//echo "$czSign:$TestStandardSign:$Estate:$SubAction";
		$UpdateIMG="&nbsp;";$UpdateClick="&nbsp;";
		if($czSign==1){//有权限并且订单可以做审核状态
			if($SubAction==31 && $TestStandardSign==1){//有权限
				$UpdateIMG="<img src='../images/register.png' width='30' height='30'";$UpdateClick="onclick='RegisterEstate($POrderId,this)'";
				}
			else{//无权限
				if($SubAction==1){
					$UpdateIMG="<img src='../images/registerNo.png' width='30' height='30'";
					}
				}
		}
		else{
			$UpdateIMG="";
			$UpdateClick="bgcolor='#FFCC00'";
		}

		if($Estate!=1 && $czSign==1){//生产完毕
			$UpdateIMG="";
			$UpdateClick="bgcolor='#339900'";
			}
			echo"<tr>
				 <td class='A0111' align='center' id='theCel$i' height='25' valign='middle' $ColbgColor onClick='ShowOrHide(ListRow$i,theCel$i,$i,$POrderId);' >$showPurchaseorder</td>";
			echo"<td class='A0101' align='center' $OrderSignColor>$j</td>";
			echo"<td class='A0101'>$OrderPO</td>";
			echo"<td class='A0101'>$TestStandard</td>";
			echo"<td class='A0101'>$eCode</td>";
			echo"<td class='A0101' align='center'>$CaseReport</td>";
			echo"<td class='A0101' align='center'>$Unit</td>";
			echo"<td class='A0101' align='right'>$Qty</td>";
			echo"<td class='A0101'>&nbsp;</td>";//$pRemark
			echo"<td class='A0101'>$Leadtime</td>";
			echo"<td class='A0101' align='center' $BackImg>$OrderDate</td>";
			echo"<td class='A0101' align='center' $UpdateClick>$UpdateIMG</td>";
			echo"</tr>";
			echo $ListRow;
			$j++;$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<tr><td colspan='8' align='center' height='30' class='A0111'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "</table>";
	?>
</form>
</body>
</html>
<script>
function RegisterEstate(POrderId,ee){
	var url="item2_ajax.php?POrderId="+POrderId;
	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
	　　　	alert("审核成功");
			//更新该单元格底色和内容
			ee.innerHTML="&nbsp;";
			ee.style.backgroundColor="#339900";
			ee.onclick="";
			}
		}
　	ajax.send(null);
	}
</script>