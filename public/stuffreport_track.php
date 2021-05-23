<?php 
//步骤1$DataIn.电信---yang 20120801
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=500;
//$Th_Col="操作|30|序号|30|项目|80|日期|80|相应单号|150|订单号|120|中文名|235|采购流水号<br>需求流水号|120|数量|80";
$Th_Col="序号|30|项目|80|日期|80|相应单号|150|订单号|120|中文名|235|采购流水号<br>需求流水号|120|数量|80";
include "../model/subprogram/read_model_3.php";
include "../model/stuffcombox_function.php";

echo "$StuffId-$StuffCname"; 
List_Title($Th_Col,"1",1);
//$StuffId="102163";
$subStuffbox=check_stuffbox_sub('',$StuffId,$DataIn,$link_id);
//订单总数
$UnionSTR="SELECT 1 AS Sign,'订单' as Item, M.OrderDate AS Date,S.OrderPO as No, IFNULL(S.POrderId, 'Error' ) as POrderId,P.cName as Name,G.StockId as StockId, G.OrderQty AS Qty 
FROM $DataIn.cg1_stocksheet G
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=G.POrderId
LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
WHERE G.StuffId='$StuffId' AND G.cgSign=0 AND G.OrderQty >0 ";

$UnionSTR.="
UNION ALL
SELECT 2 AS Sign,'配件订单' as Item, G.OrderDate AS Date,G.OrderPO as No, '&nbsp;' as POrderId,G.Description as Name,G.StockId as StockId, G.Qty AS Qty 
FROM $DataIn.ch1_shipstuffmain  G
WHERE G.StuffId='$StuffId' AND G.Qty >0 ";

//采购已下采购单    //和没下采购单
$UnionSTR.="
UNION ALL
SELECT 3 AS Sign,'采购-已下' as Item,M.Date AS Date,M.PurchaseID as No,IFNULL(Y.POrderId, 'Error' ) as POrderId,P.cName as Name,S.StockId as StockId,(S.FactualQty+S.AddQty) AS Qty 
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
WHERE  S.StuffId='$StuffId' AND S.Mid>0  AND S.cgSign=0  and (S.FactualQty+S.AddQty)>0" ;  //S.POrderId=''为空的为特采

 //采购没下采购单
$UnionSTR.="
UNION ALL
SELECT 4  AS Sign,'采购-未下' as Item,'&nbsp;' AS Date,'&nbsp;' as No,IFNULL(Y.POrderId, 'Error' ) as POrderId,P.cName as Name,S.StockId as StockId,(S.FactualQty+S.AddQty) AS Qty 
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
WHERE  S.StuffId='$StuffId' AND S.Mid=0 AND S.cgSign=0  and (S.FactualQty+S.AddQty)>0" ;  //S.POrderId=''为空的为特采

//已下采购单的特采总数
$UnionSTR.="
UNION ALL
SELECT 5  AS Sign,'特采-已下' as Item, M.Date as Date,'&nbsp;' as No,'&nbsp;' as PorderId,'&nbsp;' as Name,S.StockId  as StockId,S.FactualQty AS Qty 
FROM $DataIn.cg1_stocksheet S,$DataIn.cg1_stockmain M WHERE S.StuffId='$StuffId' AND (S.POrderId='' OR  S.cgSign=1) AND S.FactualQty>0 AND M.Id=S.Mid ";

//未下采购单的特采总数
$UnionSTR.="
UNION ALL
SELECT 6  AS Sign,'特采-未下' as Item, concat('0000-00-00') AS Date,'&nbsp;' as No,'&nbsp;' as PorderId,'&nbsp;' as Name,StockId  as StockId,FactualQty AS Qty  
FROM $DataIn.cg1_stocksheet WHERE Mid=0 AND StuffId='$StuffId' AND (POrderId='' OR cgSign=1)";


//入库总数
$UnionSTR.="
UNION ALL
SELECT 7 AS Sign,'入库' as Item,M.Date as Date,M.BillNumber as No,Y.POrderId as POrderId,P.cName as Name,R.StockId as StockId,R.Qty AS Qty 
FROM $DataIn.ck1_rksheet R 
LEFT JOIN $DataIn.ck1_rkmain M ON R.Mid=M.Id
LEFT JOIN $DataIn.cg1_stocksheet K ON K.StockId=R.StockId 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=K.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
WHERE R.StuffId='$StuffId' ";

/*
//备品转入总数
$UnionSTR.="
UNION ALL
SELECT 8  AS Sign,'备品转入' as Item,Date,'&nbsp;' as No,'&nbsp;' as PorderId,'&nbsp;' as Name, '&nbsp;' as StockId,Qty AS Qty FROM $DataIn.ck7_bprk WHERE StuffId='$StuffId' AND  Estate=0 ";
*/
//领料总数
$UnionSTR.="
UNION ALL
SELECT 9 AS Sign,'领料' as Item,S.Date as Date,'&nbsp;' as No,IFNULL(Y.POrderId, IFNULL(K.POrderId,'Error') ) as POrderId,P.cName as Name,S.StockId as StockId,S.Qty AS Qty 
FROM $DataIn.ck5_llsheet S 
LEFT JOIN $DataIn.cg1_stocksheet K ON K.StockId=S.StockId 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=K.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
WHERE S.StuffId='$StuffId' AND Type=1";


$UnionSTR.="
UNION ALL
SELECT 10 AS Sign,CASE WHEN Type=2 THEN '报废' WHEN TYPE=4 THEN '已领料删单' ELSE '车间退料' END as Item,Date,'&nbsp;' as No,'&nbsp;' as PorderId,'&nbsp;' as Name, '&nbsp;' as StockId,Qty AS Qty FROM $DataIn.ck5_llsheet WHERE  StuffId='$StuffId' AND Type>1";


/*
$UnionSTR.="
UNION ALL
SELECT 11 AS Sign,'退换' as Item,M.Date as Date,'&nbsp;' as No,'&nbsp;' as PorderId,'&nbsp;' as Name, '&nbsp;' as StockId,S.Qty AS Qty FROM $DataIn.ck2_thsheet S LEFT JOIN $DataIn.ck2_thmain M ON S.Mid=M.Id WHERE S.StuffId='$StuffId'  ";




$UnionSTR.="
UNION ALL
SELECT 12 AS Sign,'补仓' as Item,M.Date as Date,M.BillNumber as No,'&nbsp;' as PorderId,'&nbsp;' as Name, '&nbsp;' as StockId,Qty AS Qty FROM $DataIn.ck3_bcsheet S LEFT JOIN $DataIn.ck3_bcmain M ON S.Mid=M.Id WHERE S.StuffId='$StuffId' ";

*/
//echo "$UnionSTR";
$UnionSTR="select * from ($UnionSTR) A order by Sign, Date ";
$myResult = mysql_query($UnionSTR,$link_id);



$j=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Sign=$myRow["Sign"];
		
		$Item=$myRow["Item"]==NULL?"&nbsp;":$myRow["Item"];
		//$Date=$myRow["Date"];
		$Date=$myRow["Date"]==NULL?"&nbsp;":$myRow["Date"];
		//$No=$myRow["No"];		
		$No=$myRow["No"]==NULL?"&nbsp;":$myRow["No"];
		$POrderId=$myRow["POrderId"];		
		//$cName=$myRow["Name"];
		$cName=$myRow["Name"]==NULL?"&nbsp;":$myRow["Name"];
		//$StockId=$myRow["StockId"];
		$StockId=$myRow["StockId"]==NULL?"&nbsp;":$myRow["StockId"];
		
		$Qty=$myRow["Qty"];			  
		//if($Sign in(1,2,3,8) && $POrderId==NULL)  //订单、采购、领料的 PorderId为空，说明有问题
                $delclick="";
        if ($subStuffbox){
	           $comboxResult=mysql_fetch_array(mysql_query("SELECT mStockId FROM $DataIn.cg1_stuffcombox WHERE StockId='$StockId' and StuffId='$StuffId' LIMIT 1",$link_id));
	           $POrderId=$comboxResult["mStockId"];
        }
        else{
			if($POrderId=='Error'){
	                    if ($myRow["StockId"]==NULL){
				$POrderId="<div id='PId' name='PId' class='redB'>×</div>";
	                    }else{
	                        $POrderId="<div id='PId' name='PId' class='redB'>×</div>"; 
	                        $delclick=" onclick='delLlQty(this,$StockId)'";
	                    }
			}
		}
		$ChooseOut="N";	
		$ValueArray=array(
			0=>array(0=>$Item,
					 1=>"align='Left'"),
			1=>array(0=>$Date,
					 1=>"align='center'"),
			2=>array(0=>$No,
					 1=>"align='Left'"),
			3=>array(0=>$POrderId,
					 1=>"align='center'  $delclick"),
			4=>array(0=>$cName,
					 1=>"align='Left'"),
			5=>array(0=>$StockId,
					 1=>"align='center'"),
			6=>array(0=>$Qty,
					 1=>"align='Left'"),	
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
List_Title($Th_Col,"0",1);

?>
<script  type='text/javascript' >
 function delLlQty(e,Id){
  if(confirm("确认要删除该记录吗？")) {
        var url="stuffreport_track_ajax.php?Id="+Id+"&ActionId=41"; 
        var ajax=InitAjax(); 
	    ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
		 if(ajax.readyState==4){// && ajax.status ==200
			 if(ajax.responseText=="Y"){//更新成功
			     e.innerHTML="<div style='color:#FF0000;'>已删除</div>";
			     //e.style.color="#FF0000";
			     e.onclick="";
				//document.form1.submit();
				}
			 else{
			    alert ("数据删除失败！"); 
			  }
			}
		 }
	   ajax.send(null); 
       } 
 }   
    
</script>
