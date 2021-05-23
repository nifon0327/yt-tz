
<?php   
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/Totalsharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/pagefun_yw.js' type=text/javascript></script>
<script src='../model/lookup.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";
$From=$From==""?"read":$From;
//需处理参数
$tableMenuS=800;
$funFrom="yw_order";
$nowWebPage=$funFrom."_read";
$Th_Col="操作|30|序号|30|Invoice名称|100|PO|70|中文名|280|Product Code|120|Unit|40|Price|55|Qty|60|DeliveryQty|60|unDeliveryQty|60|提货单号|120|提货数量|60|提货日期|70";	
$ColsNumber=28;
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
include "../model/subprogram/read_model_3.php";
$subTableWidth=$tableWidth-30;
//增加快带查询Search按钮
$SearchRows=" AND P.ProductId=$ProductId";
$searchtable="productdata|P|cName|0|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
include "../model/subprogram/QuickSearch.php";
include "../model/subprogram/read_model_5.php";

//步骤6：需处理数据记录处理FILTER: revealTrans(transition=7,duration=0.5) blendTrans(duration=0.5);
$DefaultBgColor=$theDefaultColor;
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
	$mySql="SELECT M.Id,M.Number,M.InvoiceNO,M.InvoiceFile,M.Date,M.Remark,M.ShipType,M.Ship,M.Operator,P.cName,P.eCode,P.TestStandard,U.Name AS Unit,Y.OrderPO,S.POrderId,S.Qty,S.Price,DM.DeliveryNumber,D.DeliveryQty,DM.DeliveryDate
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.ch1_shipsheet  S ON M.Id=S.Mid
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
LEFT JOIN $DataIn.ch1_shipout O ON O.ShipId=M.Id
LEFT JOIN $DataIn.ch1_deliverysheet D ON D.POrderId=Y.POrderId
LEFT JOIN $DataIn.ch1_deliverymain DM ON DM.Id=D.Mid
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
WHERE  1 AND O.Id IS NOT NULL  $SearchRows  ORDER BY S.POrderId";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
    $tbDefalut=0;
	$midDefault="";
$d1=anmaIn("download/DeliveryNumber/",$SinkOrder,$motherSTR);
	do{	
		$m=1;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
		$Id=$myRow["Id"];
       $InvoiceNO=$myRow["InvoiceNO"];
		$POrderId=$myRow["POrderId"];
       $Mid=$POrderId;
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		$Unit=$myRow["Unit"];
		$Qty=$myRow["Qty"];
		$Price=sprintf("%.3f",$myRow["Price"]);
		$DeliveryNumber=$myRow["DeliveryNumber"];
		$DeliveryDate=$myRow["DeliveryDate"];
		$thisDeliveryQty=$myRow["DeliveryQty"];
		$filename="../download/DeliveryNumber/$DeliveryNumber.pdf";
        if(file_exists($filename)){
		$f1=anmaIn($DeliveryNumber,$SinkOrder,$motherSTR);
		$Bill="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$DeliveryNumber</a>";
		}
      else     $Bill=$DeliveryNumber; 
		//读取操作员姓名
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
			//订单状态色：有未下采购单，则为白色
			$checkColor=mysql_query("SELECT G.Id,G.StockId FROM $DataIn.cg1_stocksheet G 
			LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
			WHERE 1 AND T.mainType<2 AND G.Mid='0' and (G.FactualQty>'0' OR G.AddQty>'0' ) and G.PorderId='$POrderId'",$link_id);
		  if($checkColorRow = mysql_fetch_array($checkColor)){
				$OrderSignColor="bgColor='#FFFFFF'";//有未下需求单
				}
			else{//已全部下单	
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

		$DeliveryResult=mysql_query("SELECT SUM(DeliveryQty) AS DeliveryQty  FROM $DataIn.ch1_deliverysheet WHERE POrderId='$POrderId'",$link_id);
		$DeliveryQty =mysql_result($DeliveryResult,0,"DeliveryQty");
		$unDeliveryQty=$Qty-$DeliveryQty;
         $DeliveryQty=$DeliveryQty>0?"<span class='greenB'>$DeliveryQty</span>":$DeliveryQty;
         $unDeliveryQty=$unDeliveryQty>0?"<span class='redB'>$unDeliveryQty</span>":$unDeliveryQty;
                        //拆分订单
            $checkSplit=mysql_query("SELECT SPOrderId FROM $DataIn.yw1_ordersplit WHERE OPOrderId='$POrderId' LIMIT 1",$link_id);
			if($splitRow = mysql_fetch_array($checkSplit)){
		          $SPOrderId=$splitRow["SPOrderId"]; 
                  $Qty="<a href='yw_order_split.php?Sid=$SPOrderId' target='_blank'><div style='color:#000000;Font-weight:bold;'>$Qty</div></a>";
                 }

			
			 $TempStrtitle=$TempStrtitle==""?"显示或隐藏配件采购明细资料":$TempStrtitle;
			$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
			title='$TempStrtitle' width='13' height='13' style='CURSOR: pointer'>";
			$StuffListTB="
				<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";

				/*$ValueArray=array(
					array(0=>$InvoiceNO,				1=>"align='center'"),
					array(0=>$OrderPO),
					array(0=>$TestStandard),
					array(0=>$eCode,	3=>"..."),
					array(0=>$Unit,				1=>"align='center'"),
					array(0=>$Price, 			1=>"align='right'"),
					array(0=>$Qty,				1=>"align='right'"),
					array(0=>$DeliveryQty,				1=>"align='right'"),
					array(0=>$unDeliveryQty,				1=>"align='right'"),
					array(0=>$DeliveryNumber,				1=>"align='center'"),
					array(0=>$Operator,			1=>"align='center'")
					);
			    $checkidValue=$Id;
		        include "../model/subprogram/read_model_6.php";*/


		if($tbDefalut==0 && $midDefault==""){//首行
				//并行列
				echo"<table width='$tableWidth' border='0' cellspacing='0'  id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'><tr bgcolor='$theDefaultColor' onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$showPurchaseorder</td>";//选项
				$unitWidth=$tableWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$i</td>";//序号
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$InvoiceNO</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$OrderPO</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' >$TestStandard</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$eCode</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Unit</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Price</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Qty</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$DeliveryQty</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$unDeliveryQty</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=23;
				echo"<table width='100%' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$Field[$m]'align='center'>$Bill</td>";
				$m=$m+2;
			    echo"<td  class='A0001' width='$Field[$m]' align='center'>$thisDeliveryQty</td>";
				$m=$m+2;
		         echo"<td  width='' align='center'>$DeliveryDate</td>";
				echo"</tr></table>";
				$i++;
				$j++;
				}
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
				//并行列
				echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'><tr bgcolor='$theDefaultColor' onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$showPurchaseorder</td>";//选项
				$unitWidth=$tableWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$i</td>";//序号
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$InvoiceNO</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$OrderPO</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' >$TestStandard</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$eCode</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Unit</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Price</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Qty</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$DeliveryQty</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$unDeliveryQty</td>";
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;			
				echo"<table width='100%' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
				echo"<tr>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$Field[$m]'align='center'>$Bill</td>";
				$m=$m+2;
			    echo"<td  class='A0001' width='$Field[$m]' align='center'>$thisDeliveryQty</td>";
				$m=$m+2;
		        echo"<td  width='' align='center'>$DeliveryDate</td>";
				echo"</tr></table>";
				$i++;
				$j++;
				}
			echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));		
		echo"</tr></table>";
		
	}
else{
	noRowInfo($tableWidth);
	}
//步骤7：
include "../model/subprogram/ColorInfo.php";
echo '</div>';
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
ChangeWtitle($SubCompany.$DefaultClient."客户库存提货明细列表");
?>