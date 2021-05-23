<?php 
include "../model/modelhead.php";
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/pagefun_Sc.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";
$From=$From==""?"read":$From;
$ColsNumber=19;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 已出订单未领料配件分析");
$funFrom="stuff_noll";
$nowWebPage=$funFrom."_read";
$Th_Col="操作|60|序号|40|配件Id|50|配件名称|320|历史订单|60|QC图|40|参考买价|60|单位|40|在库|60|未领料数|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 200;
//$ActioToS="1,90";
$ActioToS="1";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";

//增加检索条件 物料编码  物料规格 物料名称
echo "&nbsp;物料编号:<input name='searchId' type='text' id='searchId' value='".$searchId."' autocomplete='off' style='width:50' />";
echo "&nbsp;物料规格:<input name='searchSpec' type='text' id='searchSpec' value='".$searchSpec."' autocomplete='off' style='width:50' />";
echo "&nbsp;物料名称:<input name='searchName' type='text' id='searchName' value='".$searchName."' autocomplete='off' style='width:100' />";
echo "&nbsp;<span name='Submit' value='快速查询' onClick='RefreshPage(\"$nowWebPage\")' class='btn-confirm' style='width: auto;font-size: 12px;height: 22px;line-height: 22px;'>快速查询</span>";

if ($searchId) {
    $SearchRows .= " AND S.StuffId like '%$searchId%' ";
}
if ($searchSpec) {
    $SearchRows .= " AND S.Spec like '%$searchSpec%' ";
}
if ($searchName) {
    $SearchRows .= " AND S.StuffCname like '%$searchName%' ";
}

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理 AND K.oStockQty>0
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.StuffId,S.StuffCname,S.Price,S.Picture,S.TypeId,K.tStockQty,S.Gfile,S.Gstate,U.Name AS UnitName
FROM $DataIn.stuffdata S
LEFT JOIN $DataIn.stuffunit U ON U.Id=S.Unit 
LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
WHERE 1 AND K.tStockQty>0 AND S.Estate>0 AND T.mainType<2  $SearchRows ";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	  do{
        $m=1;
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
        $Picture=$myRow["Picture"];
		$myOpration="<a href='stuffreport_result.php?Idtemp=$StuffId&Nametemp=$StuffCname' target='_blank'>分析</a>";
		include "../model/subprogram/stuffimg_model.php";
		$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
		$Gfile=$myRow["Gfile"];
		$Gstate=$myRow["Gstate"];
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
        include"../model/subprogram/stuff_Property.php";//配件属性
		$Price=$myRow["Price"];
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$tStockQty=$myRow["tStockQty"];
		$OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>查看</a>";
		//未领料的配件显示
		$OrderRow = mysql_fetch_array(mysql_query("SELECT SUM(A.OrderQty-A.llQty) AS wllQty FROM (
										SELECT G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty  
										FROM $DataIn.cg1_stocksheet G 
										LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = G.POrderId 
										LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId = G.StockId 
										WHERE G.StuffId = '$StuffId' AND G.OrderQty>0 AND Y.Estate=0 GROUP BY G.StockId 
										UNION ALL
										SELECT G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty  
										FROM $DataIn.cg1_stuffcombox G 
										LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = G.POrderId 
										LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId = G.StockId 
										WHERE G.StuffId = '$StuffId' AND G.OrderQty>0 AND Y.Estate=0 GROUP BY G.StockId 
							    )A WHERE A.llQty<A.OrderQty",$link_id));
		
		$wllQty = $OrderRow["wllQty"];
		if($tStockQty>=$wllQty){
			$tStockQty = "<span class='greenB'>$tStockQty</span>";
		}else{
			$tStockQty = "<span class='redB'>$tStockQty</span>";
		}
		
		if($wllQty>0){
		        $theParam="StuffId=$StuffId";
		        $URL="Stuff_noll_ajax.php";
		        $ListId=getRandIndex();  
				$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$ListId,showtable$ListId,StuffList$ListId,\"$URL\",\"$theParam\",$ListId,\"\",\"public\");' name='showtable$ListId' src='../images/showtable.gif' 
				alt='显示或隐藏配件关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
				$StuffListTB="
					<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$ListId' style='display:none'>
					<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'><br><div id='showStuffTB$ListId' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
			 $ValueArray=array(
					array(0=>$StuffId,
							 1=>"align='center'"),
					array(0=>$StuffCname),
					array(0=>$OrderQtyInfo, 
					         1=>"align='center'"),
		                        array(0=>$QCImage, 
					         1=>"align='center'"),
					array(0=>$Price,
							 1=>"align='right'"),
					array(0=>$UnitName,
							 1=>"align='center'"),
					array(0=>$tStockQty,					
							 1=>"align='right'"),
					array(0=>$wllQty,					
							 1=>"align='right'")
			   );
			   
		   $checkidValue=$StuffId;
		   include "../model/subprogram/read_model_6.php";
		   echo $StuffListTB;
		}
	   }while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>