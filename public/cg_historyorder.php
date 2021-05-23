<?php 
//电信-zxq 2012-08-01
//共享代码

include "../model/modelhead.php";
//首次下单
$checkFirst=mysql_fetch_array(mysql_query("
SELECT M.OrderDate, G.OrderQty
FROM $DataIn.yw1_ordermain M
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber = S.OrderNumber
LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId = G.POrderId
WHERE G.StuffId = '$StuffId'
ORDER BY M.OrderDate LIMIT 1",$link_id));
$FirstDate=$checkFirst["OrderDate"];

//订单总数

$subResult=mysql_query("SELECT mStuffId,Relation FROM $DataIn.stuffcombox_bom WHERE StuffId='$StuffId' LIMIT 1 ",$link_id);  
if($subRow = mysql_fetch_array($subResult))	{

    //订单总数
	$orderQty=0;
	$CheckGSql=mysql_query("SELECT IFNULL(SUM(G.OrderQty),0) AS orderQty 
			FROM $DataIn.cg1_stuffcombox G
			WHERE G.StuffId='$StuffId' ",$link_id);
	if($CheckGRow=mysql_fetch_array($CheckGSql)){
		$orderQty=$CheckGRow["orderQty"];
	}
    //采购总数
	$cgQty=0;
	$CheckGSql=mysql_query("SELECT IFNULL(SUM(FactualQty+AddQty),0) AS cgQty,IFNULL(SUM(AddQty),0) AS cgAddQty 
	FROM $DataIn.cg1_stuffcombox WHERE StuffId='$StuffId'",$link_id);
	if($CheckGRow=mysql_fetch_array($CheckGSql)){
		$cgQty=$CheckGRow["cgQty"];
		$cgAddQty=$CheckGRow["cgAddQty"];
	}
	
	

}else{
	
	
	  
    //订单总数
	$orderQty=0;
	$CheckGSql=mysql_query("SELECT IFNULL(SUM(OrderQty),0) AS orderQty,SUM(FactualQty+AddQty) AS cgQty 
	FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId' ",$link_id);
	if($CheckGRow=mysql_fetch_array($CheckGSql)){
		$orderQty=$CheckGRow["orderQty"];
		}
	//采购总数
	$cgQty=0;
	$CheckGSql=mysql_query("SELECT IFNULL(SUM(FactualQty+AddQty),0) AS cgQty,IFNULL(SUM(AddQty),0) AS cgAddQty FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId'",$link_id);
	if($CheckGRow=mysql_fetch_array($CheckGSql)){
		$cgQty=$CheckGRow["cgQty"];
		$cgAddQty=$CheckGRow["cgAddQty"];
		}
	//入库总数
	$UnionSTR3=mysql_query("SELECT IFNULL(SUM(Qty),0) AS rkQty FROM $DataIn.ck1_rksheet 
	WHERE StuffId='$StuffId' AND Type=1 ",$link_id);
	$rkQty=mysql_result($UnionSTR3,0,"rkQty");
	
	//领料总数
	$UnionSTR4=mysql_query("SELECT IFNULL(SUM(Qty),0) AS llQty FROM $DataIn.ck5_llsheet 
	WHERE StuffId='$StuffId' AND Type IN (1,5)",$link_id);
	$llQty=mysql_result($UnionSTR4,0,"llQty");
	
	//备品转入数量
	$UnionSTR5=mysql_query("SELECT IFNULL(SUM(Qty),0) AS bpQty FROM $DataIn.ck7_bprk 
	WHERE StuffId='$StuffId' AND  Estate=0 ",$link_id);
	$bpQty=mysql_result($UnionSTR5,0,"bpQty");
	
	//报废数量,只有审核通过的才算 
	$UnionSTR6=mysql_query("SELECT IFNULL(SUM(Qty),0) AS bfQty FROM $DataIn.ck8_bfsheet 
	WHERE Estate=0 AND StuffId='$StuffId'",$link_id);
	$bfQty=mysql_result($UnionSTR6,0,"bfQty");
	
	//退换数量
	$UnionSTR7=mysql_query("SELECT IFNULL(SUM(Qty),0) AS thQty FROM $DataIn.ck2_thsheet 
	WHERE StuffId='$StuffId' AND Estate=0 ",$link_id);
	$thQty=mysql_result($UnionSTR7,0,"thQty");
	//补仓数量
	$UnionSTR8=mysql_query("SELECT IFNULL(SUM(Qty),0) AS bcQty FROM $DataIn.ck1_rksheet 
	WHERE StuffId='$StuffId' AND Type=3",$link_id);
	$bcQty=mysql_result($UnionSTR8,0,"bcQty");

}


	//出货数量
	$UnionSTR10=mysql_query("SELECT IFNULL(SUM(S.Qty),0) AS Qty 
	FROM $DataIn.ck5_llsheet S 
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
	LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
	WHERE S.StuffId='$StuffId' AND Y.Estate=0 ",$link_id);
	$chQty=mysql_result($UnionSTR10,0,"Qty");

	//目前可用库存
	$UnionSTR9=mysql_query("SELECT dStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId'",$link_id);
	$dStockQty=mysql_result($UnionSTR9,0,"dStockQty");
	//目前可用库存
	$NowoValue=round($dStockQty+$cgQty+$bpQty-$orderQty-$bfQty,1);
	$NowtValue=round($dStockQty+$rkQty+$bpQty+$bcQty-$llQty-$bfQty-$thQty,1);
?>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	}
-->
</style>
<table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    	<td colspan="2"><?php  echo"<img src='cg_historyordertoimg.php?StuffId=$StuffId'><br>";?></td>
  	</tr>
	<tr>
    	<td width="45px">&nbsp;</td>
  		<td>
			<table width="940" border="0" cellpadding="0" cellspacing="0">
   			  <tr align="center">
	   				 <td width="80" height="30" class="A1111">分析报告</td>
					 <td width="80" class="A1101">首单日期</td>
				 	 <td width="80" bgcolor="#FFCCFF" class="A1101">采购总数</td>
				  	 <td width="80" bgcolor="#ffcfcf" class="A1101">其中增购</td>
			         <td width="80" bgcolor="#FFCCFF" class="A1101">初始库存</td>
					 <td width="80" bgcolor="#FFCCFF" class="A1101">备品转入</td>
					 <td width="80" bgcolor="#AAFFAA" class="A1101">订单总数</td>
					 <td width="80" bgcolor="#AAFFAA" class="A1101">出货数量</td>
					 <td width="80" bgcolor="#FFD300" class="A1101">报废数量</td>
					 <td width="80" bgcolor="#ebd6d6" class="A1101">退换数量</td>
				     <td width="80" bgcolor="#ffebd6" class="A1101">补仓数量</td>
				     <td width='80' bgcolor='#CCCCCC' class='A1101'>可用库存</td>
				     <td width='80' bgcolor='#CCCCCC' class='A1101'>实物库存</td>
   			      </tr>
    			  <tr align="center">
      				<td height="30" class="A0111"><a href='stuffreport_result.php?Idtemp=<?php  echo $StuffId?>' target='_blank'>查看</a></td>
      				<td class="A0101"><?php  echo $FirstDate?>&nbsp;</td>
      				<td class="A0101"><?php  echo $cgQty?>&nbsp;</td>
      				<td class="A0101"><?php  echo $cgAddQty?>&nbsp;</td>
					<td class="A0101"><?php  echo $dStockQty?>&nbsp;</td>
					<td class="A0101"><?php  echo $bpQty?>&nbsp;</td>
					<td class="A0101"><?php  echo $orderQty?>&nbsp;</td>
					<td class="A0101"><?php  echo $chQty?>&nbsp;</td>
					<td class="A0101"><?php  echo $bfQty?></td>					
					<td class="A0101"><?php  echo $thQty?>&nbsp;</td>
					<td class="A0101"><?php  echo $bcQty?>&nbsp;</td>
					<td class="A0101"><?php  echo $NowoValue?>&nbsp;</td>
					<td class="A0101"><?php  echo $NowtValue?>&nbsp;</td>
    			</tr>
  			</table>
		</td>
	</tr>
	<tr>
	  <td>&nbsp;</td>
	  <td>&nbsp;<br>增购情况分析:<br>
      1、删除订单后转化而成的特采单<br>
	  2、拆分订单后其中一张单生成增购数量<br>
	  3、重置需求单生成增购数量(将多张需求单集中至一张需求单上做采购)<br>
      4、采购员单独下的特采单(买备品或提前下采购单)<br>
      5、采购员在需求单上增购的数量，用于购买备品或供应商要求最低订购量或出于价格的考虑<br>
	  <div class="redB">其中4和5的情况需注意判断增购的数量是否合理和必要</div><br>
	  判断方式(供参考):<br>
	  1、参考趋势图的走向，如果持续走低或总体向下，则需谨慎增购<br>
	  2、参考产品的生命周期，如果产品生命周期短或即将过去的，也需谨慎增购<br>
	  3、参考现有配件可用库存的多少、配件损耗率的大小、采购交货期的快慢等综合信息做判断</td>
  </tr>
	<tr>
	  <td>&nbsp;</td>
	  <td>
	  <br>
	  <table width="780" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
        <tr>
          <td height="25" colspan="6"  class='A0100'><div class="rmbB">历史单价</div></td>
        </tr>
		<tr class="">
          <td class='A0111' align="center" width="40" height="25">序号</td>
          <td class='A0101' align="center" width="100">配件ID</td>
          <td class='A0101' align="center">配件名称</td>
          <td class='A0101' align="center"  width="100">供应商</td>
          <td class='A0101' align="center" width="100">购买单价</td>
          <td class='A0101' align="center" width="100">购买日期</td>
        </tr>
        
        <?php 
	 $PriceResult = mysql_query("SELECT S.Price,D.StuffCname,D.Picture,M.Date,C.PreChar,P.Forshort 
	 FROM $DataIn.cg1_stocksheet S
	 LEFT JOIN $DataIn.stuffdata D ON S.StuffId=D.StuffId 
	 LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id
	 LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	 LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	 WHERE S.StuffId=$StuffId and S.Mid!=0 group by S.Price order by M.Date",$link_id);
	 if($PriceRows = mysql_fetch_array($PriceResult)){
		$i=1;
		$hPrice=0;
		$lPrice=0;
                $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
		do{
			$Date=$PriceRows["Date"];
			$Price=$PriceRows["Price"];
			$PreChar=$PriceRows["PreChar"];
                        $Forshort=$PriceRows["Forshort"];
			if($i==1){
				$hPrice=$Price;
				$lPrice=$Price;
				}
			else{
				$hPrice=$Price>$hPrice?$Price:$hPrice;
				$lPrice=$Price<$lPrice?$Price:$lPrice;
				}
			$StuffCname=$PriceRows["StuffCname"];
                        $Picture=$PriceRows["Picture"];
                        include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
			$Price=$PreChar=="￥"?($PreChar." ".$Price):"<span class=\"redN\">$PreChar $Price</span>";
			echo"<tr>
					<td class='A0111' align='center' height='25'>$i</td>
					<td class='A0101' align='center'>$StuffId</td>
					<td class='A0101'>$StuffCname</td>
                                        <td class='A0101' align='center'>$Forshort</td>
					<td class='A0101' align='center'>$Price</td>
					<td class='A0101' align='center'>$Date</td>
				</tr>";
			$i++;
			}while($PriceRows = mysql_fetch_array($PriceResult));
			echo"<tr>
				<td class='A0111' align='right' colspan='6' height='25'><span class='redB'>最高历史价格：$hPrice </span> &nbsp;&nbsp;<span class='greenB'>最低历史价格：$lPrice</span>&nbsp;&nbsp;</td>
				</tr>";
		}
	else{
		echo"<tr><td class='A0111' align='center' colspan='6' height='25'>无历史价格记录</td></tr>";
		}
?>
      </table></td>
  </tr>
 <?php 
 //有不良报告的。
  $badResult=mysql_query("SELECT COUNT(*) AS sCount,SUM(Qty) AS Qty,SUM(shQty) AS shQty 
                         FROM $DataIn.qc_badrecord WHERE StuffId='$StuffId' GROUP BY StuffId",$link_id);
  if($badRow=mysql_fetch_array($badResult)){
      $Date=date("Y-m-d"); 
      $Qty=$badRow["Qty"]; 
      $sCount=$badRow["sCount"]; 
      $shQty=$badRow["shQty"]; 
      echo"<tr><td>&nbsp;</td>";
      echo"<td><br>
	  <table width='780' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' id='NoteTable1'>
          <tr>
		<td height='20' colspan='4'><div class=\"rmbB\">品检报告</div></td>
         </tr>";
      
      echo "<tr>
          <td  class='A0100' colspan=2  height='20'>统计日期：$Date</td>
          <td  class='A0100' width='110'>品检数量：$shQty</td>
          <td  class='A0100' width='110'>品检次数：<a href=\"cg_historyorder_report.php?StuffId=$StuffId\" target=\"_blank\">$sCount</a></td>
        </tr>";  
      echo  "<tr class='' align='center'>
         	 <td class='A0111' height='25' width=\"60\">序号</td>
	  		<td  class='A0101'>不良原因</td>
          	<td  class='A0101'>不良数量</td>
          	<td  class='A0101'>不良比例</td>
        </tr>";
      $badResult1=mysql_query("SELECT SUM(S.Qty) AS badQty,IF(S.CauseId='-1',5656565,S.CauseId) AS CauseId,T.Cause,T.Picture 
                         FROM $DataIn.qc_badrecord B 
                         LEFT JOIN $DataIn.qc_badrecordsheet S ON S.Mid=B.Id 
                         LEFT JOIN $DataIn.qc_causetype T ON T.Id=S.CauseId  
                         WHERE B.StuffId='$StuffId' GROUP BY S.CauseId order by S.CauseId",$link_id);
    // $rowNums=mysql_numrows($badResult);
      if($badRow1=mysql_fetch_array($badResult1)){
          $i=1;
         do{
	   $badQty=$badRow1["badQty"];
	   $CauseId=$badRow1["CauseId"];
           $Cause=$badRow1["Cause"];
           $Picture=$badRow1["Picture"];
           if ($Picture!=""){
                   $File=anmaIn($Picture,$SinkOrder,$motherSTR);
		   $Dir="download/qccause/";
		   $Dir=anmaIn($Dir,$SinkOrder,$motherSTR);			
		   $Cause="<a href='#' onClick='OpenOrLoad(\"$Dir\",\"$File\")' style='CURSOR: pointer;'>$Cause</a>";
           }
           if ($CauseId=='5656565'){
              $Cause="其它原因"; 
           }
	   $badRate=sprintf("%.1f",$badQty/$shQty*100)."%";
	   if ($badRate>0){
             echo  "<tr>
                 	<td class='A0111' align='center' height='25'>$i</td>
	         		<td class='A0101'>$Cause</td>
                 	<td class='A0101' align='center'>$badQty</td>
                 	<td class='A0101' align='center'>$badRate</td>
                 </tr>";
            $i++;
           }
	  }while($badRow1=mysql_fetch_array($badResult1));
       }
      if ($i==1){
        echo  "<tr><td class='A0111' colspan=6 height='50'>无不良记录</td></tr> "; 
      }
      $goodRate=sprintf("%.2f",($shQty-$Qty)/$shQty*100);
      $badRate=sprintf("%.2f",100-$goodRate);
       echo  "<tr>
                 <td  class='A0111' align='center' height='25'>合格率</td>
                 <td  class='A0101'  align='center'><div style='color:#0A0;'>$goodRate%</div></td>
                 <td  class='A0101' align='center'>$Qty</td>
                 <td  class='A0101'  align='center'><div style='color:#F00;'>$badRate%</div></td>
         </tr>";
  }
//
/*$badResult=mysql_query("SELECT B.Qty,B.Reason,B.Date,B.Operator,B.StockId,B.shQty
                         FROM $DataIn.qc_badrecord B
                         WHERE B.StuffId='$StuffId' GROUP BY B.StockId",$link_id);
 if($badRow=mysql_fetch_array($badResult)){
 $j=1;
 echo"<tr><td>&nbsp;</td>";
 echo"<td><br>
	  <table width='780' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' id='NoteTable1'>
        <tr class=''>
		<td height='25' colspan='6' align='center' class='A1111'>不良报告</td>
        </tr>
		<tr class=''>
          <td class='A0111' align='center' width='40' height='25'>序号</td>
          <td class='A0101' align='center' width='100'>报告日期</td>
          <td class='A0101' align='center'>需求单流水号</td>
		  <td class='A0101' align='center' width='200'>不良原因</td>
		  <td class='A0101' align='center' width='100'>送货数量</td>
          <td class='A0101' align='center' width='100'>不良数量</td>
          <td class='A0101' align='center' width='100'>不良比例</td>
        </tr>";
    do{
	  
	   $Qty=$badRow["Qty"];
	   $StockId=$badRow["StockId"];
	   $Reason=$badRow["Reason"];
	   $Date=$badRow["Date"];
	   $Operator=$badRow["Operator"];
	   $shQty=$badRow["shQty"];
	   $badRate=sprintf("%.1f",$Qty/$shQty*100)."%";
	   
	   echo "<tr><td class='A0111' align='center' height='25'>$j</td>
	             <td class='A0101' align='center'>$Date</td>
				 <td class='A0101' align='center'>$StockId</td>
				 <td class='A0101' align='center'>$Reason</td>
				 <td class='A0101' align='center'>$shQty</td>
				 <td class='A0101' align='center'>$Qty</td>
				 <td class='A0101' align='center'>$badRate</td>
				 ";
	   
	   
	   $j++;
	  }while($badRow=mysql_fetch_array($badResult));	 
	}
*/
 
 ?> 
  
  
</table>
</body>
</html>