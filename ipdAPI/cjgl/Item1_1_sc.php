<?php 
//更新OK
$Th_Col="配件|40|ID|30|PO|80|中文名|230|Product Code|180|检讨|30|货运|35|Qty|50|生管备注|165|期限|40|打印|50|已生产|50|登记|50";
$Field=explode("|",$Th_Col);
$Count=count($Field);
//步骤4：需处理-条件选项
$SearchRows=" AND A.TypeId='$TypeId' AND S.scFrom=2 AND S.Estate>0";//生产分类里的ID
$ClientList="";

$ClientResult= mysql_query("SELECT M.CompanyId,C.Forshort 
FROM $DataIn.yw1_ordermain M 
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId 
WHERE 1 $SearchRows GROUP BY M.CompanyId order by M.CompanyId",$link_id);

if ($ClientRow = mysql_fetch_array($ClientResult)){
	$ClientList="<select name='CompanyId' id='CompanyId' style='width:150px' onChange='restPage();'>";
	$i=1;
	do{
		$theCompanyId=$ClientRow["CompanyId"];
		$theForshort=$ClientRow["Forshort"];
		$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
		if($CompanyId==$theCompanyId){
			$ClientList.="<option value='$theCompanyId' selected>$i 、$theForshort</option>";
			$SearchRows.=" AND M.CompanyId='$theCompanyId'";
			$nowInfo="当前:".$ItemRemark." - ".$theForshort;
			}
		else{
			$ClientList.="<option value='$theCompanyId'>$i 、$theForshort</option>";
			}
		$i++;
		}while($ClientRow = mysql_fetch_array($ClientResult));
		$ClientList.="</select>";
	}
//分类
	$TypeResult= mysql_query("SELECT P.TypeId,T.TypeName
FROM $DataIn.yw1_ordermain M 
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId 
WHERE 1 $SearchRows GROUP BY P.TypeId ORDER BY T.SortId",$link_id);
	if ($TypeRow = mysql_fetch_array($TypeResult)){
		$TypeList="<select name='ProductTypeId' id='ProductTypeId' onchange='restPage();'>";
		do{
			$theTypeId=$TypeRow["TypeId"];
			$TypeName=$TypeRow["TypeName"];
			$ProductTypeId = ($ProductTypeId=="")?$theTypeId:$ProductTypeId;
			if($ProductTypeId==$theTypeId){
				$TypeList.="<option value='$theTypeId' selected>$TypeName</option>";$SearchRows.=" AND P.TypeId='$theTypeId'";
				}
			else{
				$TypeList.="<option value='$theTypeId'>$TypeName</option>";
				}
			}while($TypeRow = mysql_fetch_array($TypeResult));
		$TypeList.="</select>&nbsp;";
		}

echo "<div id='infoSelect'>$ClientList $TypeList</div>";		

//步骤5：
	echo"<table id='ListTable'>";
	///////////////////////////////////////////////////////////////
$DefaultBgColor=$theDefaultColor;
$j=2;$i=1;
$mySql="SELECT M.CompanyId,S.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.sgRemark,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.TestStandard,P.pRemark,U.Name AS Unit
	FROM $DataIn.yw1_ordermain M
	LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit
	LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
	LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId
	WHERE 1  $SearchRows ORDER BY M.OrderDate";
//echo "$mySql"	;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$czSign=1;//操作标记
		$AskDay="";
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
			
		$Id=$myRow["Id"];
		$POrderId=$myRow["POrderId"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getProductImage.php";
		//include "../admin/subprogram/product_teststandard.php";
		$ShipType=$myRow["ShipType"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];
		$sgRemark=$myRow["sgRemark"]==""?"&nbsp;":$myRow["sgRemark"];
		$OrderDate=$myRow["OrderDate"];
		//如果超过30天
		$AskDay=AskDay($OrderDate);
		$BackImg=$AskDay==""?"":"background='../../images/cj$AskDay'";
			
		$OrderDate=CountDays($OrderDate,0);
		//$POrderId=$myRow["POrderId"];
		$Estate=$myRow["Estate"];
		$LockRemark=$Estate==4?"已生成出货单.":"";
		$Locks=$myRow["Locks"];
			
		$sumQty=$sumQty+$Qty;
	
		//订单状态色：有未下采购单，则为白色
		$checkColor=mysql_query("SELECT G.Id FROM $DataIn.cg1_stocksheet G 
			LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
			WHERE 1 AND T.mainType=1 AND G.Mid='0' and (G.FactualQty>'0' OR G.AddQty>'0' ) and G.PorderId='$POrderId' LIMIT 1",$link_id);
		//WHERE 1 AND D.TypeId>9000 AND G.Mid='0' and (G.FactualQty>'0' OR G.AddQty>'0' ) and G.PorderId='$POrderId' LIMIT 1",$link_id);
		if($checkColorRow = mysql_fetch_array($checkColor)){
			$OrderSignColor="bgColor='#69B7FF'";//有未下需求单
			//$czSign=0;//不能操作
			//echo "Here1:11111 <br>";
			}
		else{//已全部下单	
			$OrderSignColor="bgColor='#FFCC00'";	//设默认绿色
			//生产数量与工序数量不等时，黄色
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//工序总数
			$CheckgxQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS gxQty 
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId
				WHERE G.POrderId='$POrderId' AND T.mainType=3",$link_id));
			//WHERE G.POrderId='$POrderId' AND A.TypeId<8000",$link_id));
			$gxQty=$CheckgxQty["gxQty"];
			//已完成的工序数量
			$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C 
			LEFT JOIN $DataIn.stufftype T ON C.TypeId=T.TypeId
			WHERE C.POrderId='$POrderId' AND T.Estate=1 ",$link_id));
			$scQty=$CheckscQty["scQty"];
	
			if($gxQty==$scQty){//生产完毕
				$OrderSignColor="bgColor='#339900'";
				$czSign=0;//不能操作
				}
				////////////////////////////////////////////////////////////////
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
		$ListRow="<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none;'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i' class='acessoryStyle'>&nbsp;</div><br></td></tr>";
		//此工序总数
		$CheckStuffQty=mysql_fetch_array(mysql_query("SELECT ifnull(SUM(G.OrderQty),0) AS sQty 
		FROM $DataIn.cg1_stocksheet G
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId
		WHERE G.POrderId='$POrderId' AND A.TypeId='$TypeId'",$link_id));
		$SumGXQty=$CheckStuffQty["sQty"];
		//已完成的工序数量
		$CheckCfQty=mysql_fetch_array(mysql_query("SELECT ifnull(SUM(C.Qty),0) AS cfQty FROM $DataIn.sc1_cjtj C WHERE C.POrderId='$POrderId' AND C.TypeId='$TypeId'",$link_id));
		$OverPQty=$CheckCfQty["cfQty"];
		
		//已生产数字显示方式
		switch($OverPQty){
			case 0:$OverPQty="&nbsp;";break;
			default://生产数量非0
				if($SumGXQty==$OverPQty){//生产完成
					$OverPQty="<div class='greenB'>$OverPQty</div>";$czSign=0;//不能操作
					}
				else{
					if($SumGXQty>$OverPQty){//未完成
						$OverPQty="<div class='yellowB'>$OverPQty</div>";
						}
					else{//多完成
						$OverPQty="<div class='redB'>$OverPQty</div>";
						}
					}
				break;
				}
		//操作权限:如果权限=31 则可以操作,否则不能操作
		$UpdateIMG="&nbsp;";
		//$UpdateClick="&nbsp;";
		$UpdateClick="onclick='setProductQty($POrderId,$TypeId);'";
		$PrintIMG="&nbsp;";$PrintClick="&nbsp;";
		
		//echo "czSign:$czSign";
		//echo "SubAction:$SubAction";
		if($czSign==1){//可以操作
			//echo "if($SubAction==31 && ($Login_TypeId==$TypeId || ($Login_TypeId==7100 && $TypeId==7040)) ){ <br>";
			//此句应该改动,如($Login_TypeId==7020 && $TypeId==9101) 表示人在车缝，但要登记油边，这就不对应。
			if($SubAction==31 && ($Login_TypeId==$TypeId || ($Login_TypeId==7100 && $TypeId==7040)  ||  ($Login_TypeId==7030 && $TypeId==7100) ||  ($Login_TypeId==7020 && $TypeId==7090))||$Login_P_Number==10871||$Login_P_Number==10265){//有权限:需要是该类别下的小组成员，方有权登记
				$UpdateIMG="<img src='../../images/register.png' width='30' height='30'";$UpdateClick="onclick='RegisterQty($POrderId,$TypeId)'";
				$PrintIMG="<img src='../../images/printer.png' width='30' height='30'";$PrintClick="onclick='PrintTasks($POrderId)'";
				}
			else{//无权限
				if($SubAction==1){
					$UpdateIMG="<img src='../../images/registerNo.png' width='30' height='30'";
					$PrintIMG="<img src='../../images/printerNo.png' width='30' height='30'";
					}
				}
			}		
		if($Estate!=1){//生产完毕
			$UpdateIMG="";
			$UpdateClick="bgcolor='#339900'";
			}
			echo"<tr>
				 <td width='40px' id='theCel$i' $ColbgColor onClick='ShowOrHide(ListRow$i,theCel$i,$i,$POrderId);' >$showPurchaseorder</td>";
			echo"<td width='30px' $OrderSignColor>$i</td>";
			echo"<td width='80px'>$OrderPO</td>";
			echo"<td width='230px'>$TestStandard</td>";
			echo"<td width='180px'>$eCode</td>";
			echo"<td width='30px'>$CaseReport</td>";
			echo"<td width='35px'>$ShipType</td>";
			echo"<td width='50px'>$Qty</td>";
			echo"<td width='165px' onclick='InputRemark($j,$Id)'>$sgRemark</td>";
			echo"<td width='40px' $BackImg>$OrderDate</td>";
			echo"<td width='50px' $PrintClick>$PrintIMG</td>";
			echo"<td width='50px'>$OverPQty</td>";
			echo"<td width='50px' $UpdateClick>$UpdateIMG</td>";
			echo"</tr>";
			echo $ListRow;
			$j=$j+2;$i++;
			}while ($myRow = mysql_fetch_array($myResult));
		}
else{
	echo"<tr><td colspan='13' align='center' height='30' class='A0111'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "</table>";

?>