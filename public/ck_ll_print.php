<?php 
//电信-zxq 2012-08-01

include "../model/modelhead.php";
//读取配件库存值
//读取配件库存值
$CheckDay=$CheckDay==""?date("Y-m-d"):$CheckDay;
$SearchRows=" AND M.Date='$CheckDay'";
//读取客户简称
$result = mysql_query("SELECT S.Id,S.Mid,S.StockId,S.StuffId,S.Qty,D.StuffCname,G.OrderQty,P.cName,Y.OrderPO
FROM $DataIn.ck5_llsheet S
LEFT JOIN $DataIn.ck5_llmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
WHERE 1 $SearchRows ORDER BY M.Date DESC,M.Id DESC",$link_id);
?>
<link rel="stylesheet" href="../model/style/orderprint.css">
</head>
<script LANGUAGE="JavaScript">
function window.onload() {
	factory.printing.header ="";
  	factory.printing.footer ="";
  	factory.printing.portrait = true ;//纵向,false横向
	factory.printing.leftMargin =5;
  	factory.printing.topMargin = 1.5;
  	factory.printing.rightMargin =5;
  	factory.printing.bottomMargin = 0.5;
	}
//  End -->
</script>
<body lang=ZH-CN>
<object id="factory" viewastext  style="display:none"
  classid="clsid:1663ed61-23eb-11d2-b92f-008048fdd814"
  codebase="http://www.middlecloud.com/basic/smsx.cab#Version=6,2,433,70">
</object> <form name="form1" method="post" action="ck_ll_print.php">
<table width="740" cellpadding="1"  cellspacing="0">
  <tr valign="top">
    <td height="16" colspan="9" scope="col" align="center">领料明细列表</td>
  </tr>
  <tr>
    <td height="21" colspan="3" class="A0100">&nbsp;</td>
    <td height="21" colspan="4" class="A0100" align="center">&nbsp;</td>
    <td class="A0100" colspan="2" align="right" valign="bottom">领料日期:<input name="CheckDay" type="text" id="CheckDay" size="10" maxlength="10" class="I0000L" value="<?php  echo $CheckDay?>" onChange="javascript:document.form1.submit()" title="改变日期，查询不同日期的领料记录">
    </td>
  </tr>
  <tr>
    <td width="20" class="A0111" align="center" height="20">领序</td>
	<td width="30" class="A0101" align="center">序号</td>
    <td width="40" class="A0101" align="center">配件ID</td>
    <td width="200"  class="A0101" align="center">配件名称</td>
	<td width="40"  class="A0101" align="center">需领数量</td>
	<td width="40" class="A0101" align="center">本次领料</td>
    <td width="80" class="A0101" align="center">需求单流水号</td>
    <td width="185" class="A0101" align="center">所属产品名称</td>
	 <td width="65" class="A0101" align="center">订单PO</td>
  </tr></table>
  <?php 
   if($mainRows = mysql_fetch_array($result)){
	$i=1;
	$j=1;
	$tbDefalut=0;
	$midDefault="";
	do{
		$m=1;
		//主单信息
		$Mid=$mainRows["Mid"];
		$Materieler=$mainRows["Materieler"];
		$StuffId=$mainRows["StuffId"];		
		$StuffCname=$mainRows["StuffCname"];
		$Qty=$mainRows["Qty"];
		$OrderQty =$mainRows["OrderQty"];
		$StockId=$mainRows["StockId"];
		$cName=$mainRows["cName"];
		$OrderPO=$mainRows["OrderPO"];
		//领料总数
			////////////////////////////////////////////////////
			if($tbDefalut==0 && $midDefault==""){//首行
				//并行列
				echo"<table width='740' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0110' width='22' align='center'>$i</td>";		//领料人
				echo"<td class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr>";
				echo"<td class='A0001' width='30' valign='top' align='center'>$j</td>";			//序号
				echo"<td class='A0001' width='41' valign='top' align='center'>$StuffId</td>";	//配件ID
				echo"<td class='A0001' width='190' valign='top'>$StuffCname</td>";				//配件
				echo"<td class='A0001' width='40' valign='top' align='right'>$OrderQty </td>";	//需领料数
				echo"<td class='A0001' width='40' valign='top' align='right'>$Qty</td>";			//本次领料
				echo"<td class='A0001' width='78' valign='top' align='center'>$StockId</td>";	//需求流水号
				echo"<td class='A0001' width='184' valign='top'>$cName</td>";					//产品名称
				echo"<td width='55' valign='top'>$OrderPO</td>";					//订单PO
				echo"</tr></table>";
				$j++;
				}
			else{
				//新行开始
				$midDefault=$Mid;$i++;
				echo"</td></tr></table>";//结束上一个表格
				//并行列
				echo"<table width='740' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0110' width='22' align='center'>$i</td>";		//领料人
				echo"<td class='A0101'>";
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr>";
				echo"<td class='A0001' width='30' valign='top' align='center'>$j</td>";			//序号
				echo"<td class='A0001' width='41' valign='top' align='center'>$StuffId</td>";	//配件ID
				echo"<td class='A0001' width='190' valign='top'>$StuffCname</td>";				//配件
				echo"<td class='A0001' width='40' valign='top' align='right'>$OrderQty </td>";	//需领料数
				echo"<td class='A0001' width='40' valign='top' align='right'>$Qty</td>";			//本次领料
				echo"<td class='A0001' width='78' valign='top' align='center'>$StockId</td>";	//需求流水号
				echo"<td class='A0001' width='184' valign='top'>$cName</td>";					//产品名称
				echo"<td width='55'>$OrderPO</td>";					//订单PO
				echo"</tr></table>";
				$j++;
				}

		}while ($mainRows = mysql_fetch_array($result));
		
	}

?>  
</form>
</body>
</html>