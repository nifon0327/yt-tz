<?php      ////////////////////////////////////
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//需处理参数
$tableMenuS=600;
$sumCols="8";			//求和列,需处理
$funFrom="Orders";
$nowWebPage=$funFrom."_waitting";
$Th_Col="&nbsp;|60|Item|35|PO#|70|Product Code|150|Product Description|350|Price|50|Qty|55|Amount|65|Unit/<br>Carton|50|Air/Sea|50|OrderDate|70|DeliveryDate|70|Average Leadtime|55|Order History|80|Rejects|60|PI|100|Supplier Rating|80";
$ColsNumber=11;
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//步骤3：
include "../model/subprogram/read_model_3.php";
$subTableWidth=$tableWidth-30;
$ClientAction=0;
if($myCompanyId==1004 || $myCompanyId==1059  || $myCompanyId==1072){
       $ClientAction=1;
       $chooseAction=$chooseAction==""?0:$chooseAction;
       $chooseStr="chooseAction".$chooseAction;
       $$chooseStr="selected";
       echo "<select id='chooseAction' name='chooseAction' onchange='changePage()'>";
       echo "<option value='0' $chooseAction0> All orders</option> ";
       echo "<option value='1' $chooseAction1>Ready to ship</option> ";
       echo "<option value='2' $chooseAction2>Ready To Pack</option> ";
       echo "<option value='3' $chooseAction3>Materials Ready</option> ";
       echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;";
       echo"<a href='order_toexcel.php'>ToExcel</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
     }

$searchtable="productdata|P|eCode|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无无
include "../model/subprogram/QuickSearch.php";
include "../model/subprogram/read_model_5.php";
$sumQty=0;
$sumSaleAmount=0;
$DefaultBgColor=$theDefaultColor;
$i=1;
$sRow=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

if ($myCompanyId==1004 || $myCompanyId==1059  || $myCompanyId==1072){  //CEL-A OR CEL-B OR CEL-C
    $SearchRows.="and (M.CompanyId='1004' OR M.CompanyId='1059'  OR M.CompanyId='1072') ";
   }
else{
	if ($myCompanyId==1081 || $myCompanyId==1002 || $myCompanyId==1080 || $myCompanyId==1065) {
		$SearchRows.="and M.CompanyId in ('1081','1002','1080','1065')";
	}
	else {
    	$SearchRows.="and M.CompanyId='$myCompanyId'";
	}
}

$SearchRows.="AND S.Estate=2";
$mySql="SELECT M.CompanyId,M.OrderDate,M.ClientOrder,
S.Id,S.POrderId,S.OrderPO,S.ProductId,S.Qty,S.Price,S.PackRemark,S.ShipType,S.Estate,S.Locks,PI.PI,PI.Leadtime AS DeliveryDate,
P.cName,P.eCode,P.Description,P.TestStandard,U.Name AS Unit,CD.PreChar
FROM $DataIn.yw1_ordermain M
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
LEFT JOIN $DataPublic.currencydata  CD ON CD.Id=C.Currency
LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
WHERE 1 and S.Estate>0 $SearchRows ORDER BY M.OrderDate DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
	  	//初始化计算的参数
		$m=1;
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
		$PI=$myRow["PI"];
                $ClientOrder=$myRow["ClientOrder"];
                if($ClientOrder!=""){
                    $f1=anmaIn($ClientOrder,$SinkOrder,$motherSTR);
		    $d1=anmaIn("download/clientorder/",$SinkOrder,$motherSTR);
                    $PI_link="<span onClick='OpenOrLoad(\"$d1\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>PDF</span>";                  
                    }
                else{
                    if ($PI!=""){
                       $f1=anmaIn($PI.".pdf",$SinkOrder,$motherSTR);
		       $d1=anmaIn("download/pipdf/",$SinkOrder,$motherSTR);
                       $PI_link="<span onClick='OpenOrLoad(\"$d1\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>PDF</span>"; 
                    }
                }
		if ($PI==""){
                    $PI="&nbsp;"; 
                    }
                else{
                    $PI_link.="&nbsp;&nbsp;<a href='../admin/yw_pi_tocsv.php?Id=$PI' target='_blank'>CSV</a>";
                    $PI_link.="&nbsp;&nbsp;<a href='../admin/yw_pi_toxml.php?Id=$PI' target='_blank'>XML</a>";
                    $PI=$PI_link;
                }
		$Id=$myRow["Id"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$Description=$myRow["Description"]==""?"&nbsp;":$myRow["Description"];
		$eCode=toSpace($myRow["eCode"]);
		$TestStandard=$myRow["TestStandard"];
		if($TestStandard==1){
			$TestStandard="T".$ProductId.".jpg";
			$TestStandard=anmaIn($TestStandard,$SinkOrder,$motherSTR);
			$Dir=anmaIn("download/teststandard/",$SinkOrder,$motherSTR);
			$TestStandard="<span onClick='OpenOrLoad(\"$Dir\",\"$TestStandard\")' style='CURSOR: pointer;color:#FF6633'>$eCode</span>";
			}
		else{
			$TestStandard=$eCode;
			}
		$Unit=$myRow["Unit"];
		$Qty=$myRow["Qty"];
        $PreChar=$myRow["PreChar"];
		$Price=sprintf("%.2f",$myRow["Price"]);
		$thisSaleAmount=sprintf("%.2f",$Qty*$Price);
		$sumQty+=$Qty;
		$sumSaleAmount+=$thisSaleAmount;
		$PackRemark=$myRow["PackRemark"];
		$DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
		$pRemark=$myRow["pRemark"]==""?"&nbsp;":$myRow["pRemark"];
		$ShipType=$myRow["ShipType"];
		$OrderDate=$myRow["OrderDate"];
		$OrderDate=date("Y-m-d",strtotime($OrderDate));
		$POrderId=$myRow["POrderId"];
		$Estate=$myRow["Estate"];
		$LockRemark=$Estate==4?"已生成出货单.":"";
		$Locks=$myRow["Locks"];
////////////////毛利计算///////////////////
		$thisSaleAmount=sprintf("%.2f",$Qty*$Price);//本订单卖出金额
		/*毛利计算*////////////
		$CompanyId=$myRow["CompanyId"];
		$currency_Temp = mysql_query("SELECT A.Rate,A.Symbol FROM $DataPublic.currencydata A LEFT JOIN $DataIn.trade_object B ON A.Id=B.Currency WHERE  B.CompanyId=$CompanyId ORDER BY B.CompanyId LIMIT 1",$link_id);
		if($RowTemp = mysql_fetch_array($currency_Temp)){
			$Rate=$RowTemp["Rate"];//汇率
			$Symbol=$RowTemp["Symbol"];//货币符号
			}
		$thisTOrmbOUT=sprintf("%.4f",$thisSaleAmount*$Rate);//转成人民币的卖出金额

		//配件成本(RMB)
		$Cost_Temp=mysql_query("SELECT 
		SUM((A.FactualQty+A.AddQty)*A.Price*C.Rate) AS oTheCost,
		SUM(A.OrderQty*A.Price*C.Rate) AS pTheCost 
			FROM $DataIn.cg1_stocksheet A	
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE A.POrderId='$POrderId' ORDER BY A.Id DESC",$link_id);
		
		$thisTOrmbINo=sprintf("%.4f",mysql_result($Cost_Temp,0,"oTheCost"));
		$GrossProfit=$thisTOrmbOUT-$thisTOrmbINo;
		$thisTOrmbINp=sprintf("%.4f",mysql_result($Cost_Temp,0,"pTheCost"));
		$GrossProfitp=$thisTOrmbOUT-$thisTOrmbINp;
		//$GrossProfitSUM=$GrossProfitSUM+$GrossProfit;//毛利总额
		//单品毛利
		$profitRMB=sprintf("%.4f",$GrossProfitp/$Qty);		
		//订单状态色
		$checkColor=mysql_query("SELECT G.Id FROM $DataIn.cg1_stocksheet G 
		LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
		WHERE 1 AND T.mainType<2 AND G.Mid='0' and (G.FactualQty>'0' OR G.AddQty>'0' ) and G.PorderId='$POrderId' LIMIT 1",$link_id);
		if($checkColorRow = mysql_fetch_array($checkColor)){
			$OrderSignColor="bgColor='#FFFFFF'";//有未下需求单
			}
		else{//已全部下单，看生产数量		
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
		$ColbgColor="";
		//加急订单
		$checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' ORDER BY Id",$link_id);
		if($checkExpressRow = mysql_fetch_array($checkExpress)){
			do{
				$Type=$checkExpressRow["Type"];
				switch($Type){
					case 1:$ColbgColor="bgcolor='#0066FF'";break;
					case 7:$theDefaultColor="#FFA6D2";break;
					}
				}while ($checkExpressRow = mysql_fetch_array($checkExpress));
			}
		//AI图下载
		/*$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
		$aiSql=mysql_query("SELECT G.Gfile,Y.POrderId,G.TypeId,G.Gstate,G.Gremark,G.Picture,G.StuffId
		FROM $DataIn.stuffdata G
		LEFT JOIN $DataIn.pands P ON P.StuffId=G.StuffId
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.ProductId=P.ProductId
		WHERE POrderId='$POrderId' AND TypeId=9049",$link_id);
		if($aiRow=mysql_fetch_array($aiSql))
		{
			do{
				$StuffId=$aiRow["StuffId"];
				$Gfile=$aiRow["Gfile"];
				$Gstate=$aiRow["Gstate"];
				$Gremark=$aiRow["Gremark"];
				$Picture=$aiRow["Picture"];
			}while($aiRow=mysql_fetch_array($aiSql));
		}*/
	//检查装箱数量
		$checkNumbers=mysql_fetch_array(mysql_query("SELECT IFNULL(N.Relation,0) AS Relation
		FROM $DataIn.pands N
		LEFT JOIN $DataIn.stuffdata S ON S.StuffId=N.StuffId
		WHERE N.ProductId=$ProductId AND S.TypeId='9040'",$link_id));
		$BoxNums=$checkNumbers["Relation"];
		if($BoxNums!=0){
			   $BoxNumsArray=explode("/",$BoxNums);
			   $BoxNums=$BoxNumsArray[1];
			   }
		else{
			   $BoxNums="&nbsp;";
			   }
		//订单总数
		$checkAllQty= mysql_query("SELECT SUM(ALLQTY) AS ALLQTY,count(*) AS Orders FROM( 
									SELECT SUM(S.Qty) AS AllQty FROM $DataIn.yw1_ordersheet S
									LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
									WHERE P.ProductId='$ProductId' GROUP BY OrderPO)A",$link_id);
		$AllQtySum=toSpace(mysql_result($checkAllQty,0,"AllQty"));
		$Orders=mysql_result($checkAllQty,0,"Orders");
		//已出货数量
		$checkShipQty= mysql_query("SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE ProductId='$ProductId'",$link_id);
		$ShipQtySum=toSpace(mysql_result($checkShipQty,0,"ShipQty"));
        $tempShipQtySum=$ShipQtySum;
		//百分比
		$TempInfo="style='CURSOR: pointer;' onclick='ViewChart($ProductId,1)'";
		if($AllQtySum>0){
			 $TempInfo.="title='ShipQty:$ShipQtySum,Order Frequency:$Orders'";
			}
       $ShipQtySum="<span class='yellowB'>".$ShipQtySum."</span>";
	    if($Orders>0){
				if($Orders<2){
					   $ShipQtySum=$ShipQtySum."<span class=\"redB\">($Orders)</span>";
					   }
				else{
					if($Orders>4){
						   $ShipQtySum=$ShipQtySum."<span class=\"greenB\">($Orders)</span>";
						   }
					else{
						   $ShipQtySum=$ShipQtySum."<span class=\"yellowB\">($Orders)</span>";	
						  }
					}
				}
		include "../model/subprogram/product_chjq.php";
        $JqAvg=str_replace("days","d",$JqAvg);
		//图档显示
		include "../model/subprogram/stuffimg_Gfile.php";			
		//检查是否有图片
		include "../model/subprogram/stuffimg_model.php";	
		//动态读取
               $showPurchaseorder="<img onClick='cShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i);' name='showtable$i' src='../images/showtable.gif' alt='show or hidden the ordersheet.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		   if($ClientAction==1){//Cel的价格带上符号
                    $Price=$PreChar.$Price;
                    $thisSaleAmount=$PreChar.$thisSaleAmount;
                      include  "product_pj.php";//产品评价
             }
				$ValueArray=array(
					array(0=>$OrderPO,		1=>"align='center'"),
					array(0=>$TestStandard),
					array(0=>$Description),
					array(0=>$Price, 		1=>"align='right'"),
					array(0=>$Qty, 			1=>"align='right'"),
					array(0=>$thisSaleAmount,1=>"align='right'"),
					array(0=>$BoxNums,1=>"align='right'"),
					array(0=>$ShipType,		1=>"align='center'",3=>"..."),
					array(0=>$OrderDate,	1=>"align='center'"),
					array(0=>$DeliveryDate, 1=>"align='center'", 3=>"..."),
					array(0=>$JqAvg,	1=>"align='center'"),
				    array(0=>$ShipQtySum, 		 1=>"align='right'",2=>$TempInfo),
				    array(0=>$ReturnedQty, 		 1=>"align='right'",2=>$TempInfo2),
					array(0=>$PI, 			1=>"align='center'"),
				    array(0=>$pjgif,			                1=>"align='center'")
					);

		$checkidValue=$Id;
		include "../admin/subprogram/read_model_6.php";
		echo $StuffListTB;		
		}while ($myRow = mysql_fetch_array($myResult));
	//合计
	$sumSaleAmount=sprintf("%.2f",$sumSaleAmount);
	//$WidthSTR=$myCompanyId==1004?665:610;
	$m=1;
		$ValueArray=array(
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>$sumQty, 			1=>"align='right'"),
			array(0=>$sumSaleAmount,1=>"align='right'"),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
            array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
            array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	)
			);
	$ShowtotalRemark="TOTAL";
	$isTotal=1;
	
	include "read_model_total.php";
	
	}
else{
	noRowInfo($tableWidth);
	}
echo"<input name='sumAmout' type='hidden' id='sumAmount'>";
//步骤7：
echo '</div>';
ChangeWtitle("$SubCompany ORDER STATUS");
?>
<script>
function cShowOrHide(e,f,Order_Rows,POrderId,RowId){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(POrderId!=""){			
			var url="clientorder_ajax.php?POrderId="+POrderId+"&RowId="+RowId; 
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					var DataArray=BackData.split("`");
					show.innerHTML=DataArray[0];
					//订单状态更新
					switch(DataArray[1]){
						case "1"://白色
							eval("ListTable"+RowId).rows[0].cells[1].bgColor="#FFFFFF";
							break;
						case "2"://黄色
							eval("ListTable"+RowId).rows[0].cells[1].bgColor="#FFCC00";
							break;
						case "3"://绿色
							eval("ListTable"+RowId).rows[0].cells[1].bgColor="#339900";
							break;
						}
					}
				}
			ajax.send(null); 
			}
		}
	}
function changePage(){
             var chooseAction=document.getElementById("chooseAction").value;
              switch(chooseAction){
                   case '0':
                      document.form1.action="orderstatus.php?model=c&chooseAction="+chooseAction;
                   break;
                   case '1':
                      document.form1.action="Orders_waitting.php?chooseAction="+chooseAction;
                   break;
                   case '2':
                     document.form1.action="orders_pack.php?chooseAction="+chooseAction;
                   break;
                   case '3':
                      document.form1.action="orders_mready.php?chooseAction="+chooseAction;
                   break;
                 }
           document.form1.target="_self";
           document.form1.submit();	
    }
</script>