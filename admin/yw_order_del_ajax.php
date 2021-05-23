<?php   
//电信-EWEN
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../basic/config.inc";
include "../model/modelfunction.php";
include "../model/stuffcombox_function.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=1450;
//来自于生产登记页面
$FromT=$FromT==0?"":$FromT;
          
echo"<table id='$TableId' width='$subTableWidth' cellspacing='1' border='1' align='left' style='margin-left:30px;margin-top:20px;margin-bottom:20px;'><tr bgcolor='#CCCCCC'>
			<td  colspan='2' height='20'>序号</td>
			<td width='50' align='center'>配件ID</td>
			<td width='100' align='center'>采购流水号</td>
			<td width='330' align='center'>配件名称</td>				
			<td width='40' align='center'>图档</td>
            <td width='40' align='center'>QC图</td>
			
            <td width='40' align='center'>品检</td>		
			<td width='55' align='center'>历史订单</td>
			<td width='55' align='center'>配件价格</td>
			<td width='40' align='center'>单位</td>
			<td width='55' align='center'>订单数量</td>
			<td width='55' align='center'>已用库存</td>
			<td width='55' align='center'>需购数量</td>
			<td width='55' align='center'>增购数量</td>
            <td width='55' align='center'>在库</td>
            <td width='55' align='center'>采购</td>
			<td width='125' align='center'>供应商</td></tr>";
$sListSql = "SELECT S.Id,S.StockId,S.POrderId,S.StuffId,S.Price,
(S.OrderQty-IFNULL(SM.OrderQty,0)) AS OrderQty,S.StockQty,S.AddQty,S.FactualQty,
S.CompanyId,S.BuyerId,S.DeliveryDate,
S.DeliveryWeek,A.StuffCname,A.Picture,A.Gfile,A.Gstate,A.TypeId,A.DevelopState,
B.Name,C.Forshort,C.Currency,MP.Name AS Position,ST.mainType,MT.TypeColor,MT.TitleName,S.blSign,U.Name AS UnitName,U.Decimals,K.tStockQty,A.DevelopState
		FROM $DataIn.cg1_stocksheet_del S 
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
		LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId
		LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit 
		LEFT JOIN $DataIn.stuffmaintype MT ON MT.Id=ST.mainType
		LEFT JOIN $DataIn.base_mposition MP ON MP.Id=ST.Position 
		LEFT JOIN $DataIn.staffmain B ON B.Number=S.BuyerId
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId 
        LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
        LEFT JOIN (SELECT StockId,IFNULL(SUM(OrderQty),0) AS OrderQty FROM cg1_semifinished_del WHERE POrderId = $POrderId GROUP BY StockId) SM ON SM.StockId = S.StockId
		WHERE S.POrderId='$POrderId'  AND S.Level=1 ORDER BY MT.SortId,S.StockId ";

//echo $sListSql;		           
$sListResult = mysql_query($sListSql,$link_id);	
		
	$i=1;
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	if ($StockRows = mysql_fetch_array($sListResult)) {
	
		do{
			$Mid=$StockRows["Mid"];
			$thisId=$StockRows["Id"];
			$StockId=$StockRows["StockId"];
            $ProductId=$StockRows["ProductId"];
			$Date=$StockRows["Date"];
            $OrderDate=$StockRows["OrderDate"];
			$StuffCname=$StockRows["StuffCname"];
			$Position=$StockRows["Position"]==""?"未设置":$StockRows["Position"];
			$Price=$StockRows["Price"];
			$CompanyId=$StockRows["CompanyId"];
			$Forshort=$StockRows["Forshort"];
			$Buyer=$StockRows["Name"];
			$UnitName=$StockRows["UnitName"]==""?"&nbsp;":$StockRows["UnitName"];
			$BuyerId=$StockRows["BuyerId"];
			$OrderQty=$StockRows["OrderQty"];
			$StockQty=$StockRows["StockQty"];
			$FactualQty=$StockRows["FactualQty"];
			$AddQty=$StockRows["AddQty"];
			$DeliveryDate=$StockRows["DeliveryDate"];	
			$DeliveryWeek=$StockRows["DeliveryWeek"];
			$StuffId=$StockRows["StuffId"];
			$Picture=$StockRows["Picture"];
			$TypeId=$StockRows["TypeId"];
			$mainType=$StockRows["mainType"];
			$TypeColor=$StockRows["TypeColor"];
			$Currency=$StockRows["Currency"];
			$Gfile=$StockRows["Gfile"];
			$Gstate=$StockRows["Gstate"];  //状态
            $tStockQty=$StockRows["tStockQty"];  
	     	$Operator=$StockRows["Operator"];
	     	$OrderEstate=$StockRows["Estate"];
	     	$Decimals=$StockRows["Decimals"];
	     	
		   include "../model/subprogram/stuffimg_Gfile.php"; //图档显示	
			//检查是否有图片
		   include "../model/subprogram/stuffimg_model.php";
           include"../model/subprogram/stuff_Property.php";//配件属性   
            //配件QC检验标准图
           include "../model/subprogram/stuffimg_qcfile.php";
                         
        
           
           $TitleName=$StockRows["TitleName"];
	
			//配件分类颜色
			$theDefaultColor=$TypeColor;
            if ($TypeId==$APP_CONFIG['REFUND_TYPE']) $theDefaultColor="#FFFF00";
            if ($ClientProSign==1) $theDefaultColor="#FFBBC9";
			if($Currency==2){
				$Price="<div class='redB'>$Price</div>";
				$Forshort="<div class='redB'>$Forshort</div>";
				}
		
                 //半成品配件
          $CheckSemiSql=mysql_query("SELECT * FROM $DataIn.cg1_semifinished_del G  WHERE G.mStockId ='$StockId' AND G.POrderId='$POrderId' LIMIT 1",$link_id);
          if($CheckSemiRow=mysql_fetch_array($CheckSemiSql)){
              $ListId=getRandIndex();
              $showStr="<img onClick='ShowOrHideSemi(ShowTable_$ListId,ShowGif_$ListId,showStuffTB$ListId,\"$StockId\",$ListId,\"$NewSign\");' name='ShowGif_$ListId' src='../images/showtable.gif' 
		title='显示半成品明细' width='13' height='13' style='CURSOR: pointer'>";
	         $showTable="<tr id='ShowTable_$ListId' style='display:none'><td colspan='$Colsnum'><div id='showStuffTB$ListId' width='$subTableWidth'>&nbsp;</div><br></td></tr>";
           }
         
		    $OrderQty=round($OrderQty,$Decimals);
		    $StockQty=round($StockQty,$Decimals);
		    $FactualQty=round($FactualQty,$Decimals);
		    $AddQty=round($AddQty,$Decimals);
		    $tStockQty=round($tStockQty,$Decimals);
		    echo"<tr bgcolor='$theDefaultColor'>
			     <td  align='center' height='20' width='20' >$showStr</td>
                 <td  align='center' width='20'>$i</td>";//配件状态 
			echo"<td  align='center'>$StuffId</td>";//REACH
			echo"<td  align='center'>$StockId</td>";//配件采购流水号
			echo"<td >$StuffCname</td>";//配件名称
			echo"<td  align='center'>$Gfile</td>";//配件图档
            echo"<td  align='center'>$QCImage</td>";//QC图档
            echo"<td  align='center'>$qualityReport</td>";//品检报告
			$OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=$thisId' target='_blank'>查看</a>";
			echo"<td  align='center'>$OrderQtyInfo</td>";//历史订单分析
			echo"<td align='right'>$Price</td>";//配件价格
			echo"<td  align='center'>$UnitName</td>";//单位
			echo"<td align='right'>$OrderQty</td>";//订单需求数量
			echo"<td align='right'>$StockQty</td>";//使用库存数
			echo"<td align='right'>$FactualQty</td>";//采购数量
			echo"<td align='right'>$AddQty</td>";//增购数量
            echo"<td align='right'>$tStockQty</td>";//在库
            echo"<td  align='center'>$Buyer</td>";//采购员
		    echo"<td >$Forshort</td>";//供应商
			echo"</tr>";
            echo $showTable;
			$i++;
			}while ($StockRows = mysql_fetch_array($sListResult));
			
		}
	else{
		  echo"<tr><td height='30' cols='19'>记录异常，此订单没有发现需求记录. $Tid</td></tr>";
		}
	
  echo"</table>";
?>
