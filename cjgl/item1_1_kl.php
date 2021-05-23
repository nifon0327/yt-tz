<?php
$nowInfo="当前:开料登记";
$Th_Col="配件|30|序号|30|期限|60|工单流水号|90|半成品名称|280|片材ID|60|片材配件|320|单位|30|刀模|160|生产数量|65|完成数量|65|登记|65";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$wField=$Field;
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$wField[$i];
	$tableWidth+=$wField[$i];
	}

if (isSafari6()==1){
   $tableWidth=$tableWidth+ceil($Count*1.5)+1;
}
$checkScSign=3;//可生产标识

$SearchRows=" AND SC.WorkShopId='$fromWorkshop' AND SC.ActionId='$fromActionId' AND SC.scFrom>0 AND SC.Estate>0 ";
//echo $SearchRows;
if (strlen($tempStuffCname)>0){
	$SearchRows.=" AND D.StuffCname LIKE '%$tempStuffCname%' ";
	$searchList1="<input class='ButtonH_25' type='button'  id='cancelQuery' value='取消' onclick='ResetPage(1,1)'/>";
    }
else{
	$searchList1="<input type='text' name='tempStuffCname' id='tempStuffCname' value='' width='20'/> &nbsp;<span class='ButtonH_25' id='okQuery' onclick='ResetPage(1,1)'>查询</span>";
 }
//步骤5：
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td colspan='6' height='40px' class=''> $scList  </td><td colspan='5' align='right' class=''>$searchList1<input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";
	//输出表格标题
for($i=0;$i<$Count;$i=$i+2){
	$Class_Temp=$i==0?"A1111":"A1101";
	$j=$i;
	$k=$j+1;
	echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
	}
echo"</tr>";
$DefaultBgColor=$theDefaultColor;
$i=1;$j=1;$s=1;$Rows=0;
$mySql= "
SELECT SC.Id,SC.POrderId,SC.sPOrderId,SC.Qty,SC.Estate,SC.ActionId,SC.StockId,SC.Remark,
D.StuffId,D.StuffCname,D.Picture,G.DeliveryDate,G.DeliveryWeek,M.mStockId,M.mStuffId,
U.Name AS UnitName,SD.TypeId,G.Mid,Y.OrderPO,(G.addQty+G.FactualQty) AS xdQty
FROM  $DataIn.yw1_scsheet SC 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=SC.POrderId 
LEFT JOIN $DataIn.cg1_semifinished M ON M.StockId = SC.StockId
LEFT JOIN $DataIn.stuffdata SD ON SD.StuffId = M.StuffId
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = SC.mStockId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
WHERE 1 $SearchRows AND getCanStock(SC.sPOrderId,$checkScSign)=$checkScSign  GROUP BY SC.Id  ORDER BY  G.DeliveryWeek ASC ";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	do{
			$m=1;
			$cutTableSTR="";$NoCutQtyCount=0;
	        $StockId=$myRow["StockId"];
			$POrderId=$myRow["POrderId"];
			$OrderPO=$myRow["OrderPO"];
	        $sPOrderId=$myRow["sPOrderId"];
	        $PQty=$myRow["Qty"];
	        $xdQty = $myRow["xdQty"];
	        $Relation = $PQty/$xdQty;
	        $StuffId=$myRow["StuffId"];
	        $Picture=$myRow["Picture"];
	        $StuffCname=$myRow["StuffCname"];
	        $UnitName =$myRow["UnitName"];
		    include "../model/subprogram/stuffimg_model.php";
		    include"../model/subprogram/stuff_Property.php";//配件属性
		    $DeliveryDate=$myRow["DeliveryDate"];
            $DeliveryWeek=$myRow["DeliveryWeek"];


			$mStuffCname=$StuffCname;
	        $mStuffId = $myRow["mStuffId"];
	        $mStockId = $myRow["mStockId"];
	        $TypeId   = $myRow["TypeId"];

            include "../pt/slice_cutdie_show.php";


			//已完成的工序数量
			$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty 
			FROM $DataIn.sc1_cjtj C 
			WHERE  C.sPOrderId = '$sPOrderId' AND C.StockId = $StockId ",$link_id));
			$scQty=$CheckscQty["scQty"];
		    $noscQty  = $PQty - $scQty;


		    /*
            **备料,领料情况
            */
            $llSign = 0 ; $blSign = 0 ;
            $k = 0 ; $tempblK = 0 ;
            $templlK = 0 ;
            $CheckllResult = mysql_query("SELECT ROUND(A.OrderQty*$Relation,1) AS OrderQty,G.StockId 
            FROM $DataIn.cg1_semifinished   A 
            INNER JOIN $DataIn.cg1_stocksheet G  ON G.StockId = A.StockId
            WHERE A.POrderId='$POrderId' AND A.mStockId='$mStockId'  AND G.blsign = 1",$link_id);
            while($CheckllRow  = mysql_fetch_array($CheckllResult)){

                $llOrderQty  =  $CheckllRow["OrderQty"];
                $llStockId   =  $CheckllRow["StockId"];
                //备料情况
                $checkblQtyResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS blQty FROM $DataIn.ck5_llsheet 
                WHERE sPOrderId = $sPOrderId AND StockId='$llStockId'",$link_id));
                $thisblQty=$checkblQtyResult["blQty"];
                if($thisblQty >0)$blSign = 1; //部分领料
                if($llOrderQty ==$thisblQty)$tempblK++;

                //领料情况
                $checkllQtyResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet 
                WHERE sPOrderId = $sPOrderId AND StockId='$llStockId' AND Estate=0",$link_id));
		        $thisllQty=$checkllQtyResult["llQty"];
                if($thisllQty >0)$llSign = 1; //部分领料
                if($llOrderQty ==$thisllQty)$templlK++;
                $k++;
            }

            if($tempblK == $k)$blSign = 2 ;//全部备料
            if($templlK == $k)$llSign = 2 ;//全部领料
            $bgColor="";
            if($blSign ==2 && $llSign >0){
	           $bgColor = "bgcolor='#FFB6C1'";
            }else if($blSign ==2 && $llSign ==0){
               $bgColor = "bgcolor='#A2CD5A'";
            }else if($blSign == 1){
	           $bgColor = "bgcolor='#BFEFFF'";
            }


		    $cgMid = $myRow["Mid"];

		    if($cgMid==0 ){
			    include "item1_1_auto_cg.php";
		    }

		   $UpdateIMG = "&nbsp;";
	       if( $noscQty>0 ){
               $UpdateIMG = "<input name='addQty$i' type='text' id='addQty$i' size='6' value='' onchange='addSliceQty(this,$i,\"$POrderId\",\"$sPOrderId\",\"$StockId\",$noscQty)'>";
              }


	           if($blSign ==0){
		            $UpdateIMG = "<span class='redB'>未备料</span>";

	            }else if ($blSign ==1){
		            $UpdateIMG = "<span class='blueB'>部分备料</span>";
	            }else if ($blSign ==2 && $llSign == 0){
		            $UpdateIMG = "<span class='greenB'>未领料</span>";
	            }

	          $ShuiPrintIMG="<a href='slicebom_report.php?POrderId=$POrderId&sPOrderId=$sPOrderId&mStockId=$mStockId&Qty=$Qty' target='_blank'><img src='../images/printer.png' width='16' height='16'></a>";



           //取最上层半成品的交期
		    $DeliveryWeekRow=mysql_fetch_array(mysql_query("SELECT 
		    G.DeliveryWeek,G.DeliveryDate,D.StuffCname,D.Picture  
		    FROM $DataIn.cg1_semifinished  S   
		    LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.mStockId
		    LEFT JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
		    WHERE S.POrderId='$POrderId' AND S.StockId='$mStockId' AND G.Level = 1 LIMIT 1",$link_id));
		    $SemiStuffCname = $DeliveryWeekRow["StuffCname"];
		    $SemiPicture= $DeliveryWeekRow["Picture"];
		    $Picture = $SemiPicture;
		    $StuffCname =$SemiStuffCname;
		    include "../model/subprogram/stuffimg_model.php";
            //$DeliveryWeek = $DeliveryWeekRow["DeliveryWeek"];
            //$DeliveryDate = $DeliveryWeekRow["DeliveryDate"];
	        include "../model/subprogram/deliveryweek_toweek.php";



			$showPurchaseorder="[ + ]";
			$ListRow="<tr  id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";
			echo"<tr $bgColor ><td class='A0111' align='center' id='theCel$i' height='25' valign='middle' $ColbgColor onClick='ShowOrHide(ListRow$i,theCel$i,$i,\"$POrderId\",\"$sPOrderId\",\"showScOrder\");' >$showPurchaseorder</td>";
			echo"<td class='A0101' align='center' >$i</td>";
			echo"<td class='A0101' align='center'>$DeliveryWeek</td>";
			echo"<td class='A0101' align='center'>$sPOrderId</td>";
			echo"<td class='A0101' >$SemiStuffCname</td>";
			echo"<td class='A0101' align='center'>$StuffId</td>";
			echo"<td class='A0101' >$mStuffCname $ShuiPrintIMG</td>";
			echo"<td class='A0101' align='center'>$UnitName</td>";
			echo"<td class='A0101' >$CutStr</td>";
			echo"<td class='A0101' align='center'>$PQty</td>";
			echo"<td class='A0101' align='center' id='cutedQty$i' >$scQty</td>";
            echo"<td class='A0101' align='center' >$UpdateIMG</td>";
			echo"</tr>";
			$i++;
           echo $ListRow;
       }while ($myRow = mysql_fetch_array($myResult));
}else{
        echo"<tr><td colspan='11' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
}
echo "</table>";
?>
<script language='javascript'>
 function addSliceQty(e,t,POrderId,sPOrderId,StockId,DengQty){
	var cutedQtyId='cutedQty'+t;
	if (e.value=="") return;
	var CS =/^[1-9]+[0-9]*]*$/;   //判断字符串是否为数字
	if (CS.test(e.value)){
		var addQty=parseInt(e.value);
		if(addQty>DengQty){alert("超过允许登记的数量,不能超过:"+DengQty);e.value="";e.focus();return false;}
		msgStr="添加生产数量确认？";
		if (confirm(msgStr)){
           var url="item1_1_scdj_ajax.php?POrderId="+POrderId+"&sPOrderId="+sPOrderId+"&StockId="+StockId+"&Qty="+addQty;
          var ajax=InitAjax();
	      ajax.open("GET",url,true);
	      ajax.onreadystatechange =function(){
		   if(ajax.readyState==4){// && ajax.status ==200
			 if(ajax.responseText=="Y"){//更新成功
			    var scQty=parseInt(document.getElementById(cutedQtyId).innerHTML);
				 if (!CS.test(scQty)) scQty=0;
			     document.getElementById(cutedQtyId).innerHTML=scQty+addQty;
				 e.disabled=true;
				}
			  else{alert ("生产登记失败！"+ajax.responseText); }
			}
		  }
	     ajax.send(null);
		}
		else{
		  e.value="";
		}
	}else{
		alert ('提示:请输入大于零的数字！');
		 e.value="";e.focus();
		return;
	}
 }
</script>