<?php 
/*
已更新$DataIn.电信---yang 20120801
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=19;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 配件分析");
$funFrom="stuffreport";
$nowWebPage=$funFrom."_read";
$Th_Col="操作|40|序号|40|配件Id|50|配件名称|210|历史订单|60|QC图|40|参考买价|60|单位|40|初始库存|60|最低库存|60|在库|60|可用库存|60|订单总数|60|配件订单数|60|采购总数|60|入库总数|60|订单领料数|60|备品入库|60|报废总数|60|退换总数|60|补仓总数|60|在库库存金额|100|可用库存金额|100";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 200;
$ActioToS="1";

//步骤3：
include "../model/subprogram/read_model_3.php";

//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";

//增加检索条件 物料编码  物料规格 物料名称
echo "&nbsp;物料编号:<input name='searchId' type='text' id='searchId' value='".$searchId."' autocomplete='off' style='width:100' />";
echo "&nbsp;物料规格:<input name='searchSpec' type='text' id='searchSpec' value='".$searchSpec."' autocomplete='off' style='width:100' />";
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

echo "$CencalSstr  &nbsp;&nbsp;&nbsp;&nbsp;<a href='stuffreport_error.php' target='_blank' style='color: red'><b>数据有误配件</b></a>";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理 AND K.oStockQty>0
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.StuffId,S.StuffCname,S.Price,S.Picture,S.TypeId,K.dStockQty,K.tStockQty,K.mStockQty,K.oStockQty,S.Gfile,S.Gstate,U.Name AS UnitName
FROM $DataIn.stuffdata S
LEFT JOIN $DataPublic.stuffunit U ON U.Id=S.Unit 
LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
LEFT JOIN $DataIn.stuffmaintype M On T.mainType = M.Id
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
WHERE 1 AND S.Estate>0 AND  M.blSign=1 $SearchRows
ORDER BY S.Id";
//echo $mySql;
$SumoStockQty=0;
$SumKcOAmount=0;
$SumKctAmount = 0;
$SumtStockQty = 0;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
      $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
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
		$dStockQty=$myRow["dStockQty"];
		$tStockQty=$myRow["tStockQty"];
		$oStockQty=$myRow["oStockQty"];
		$mStockQty=$myRow["mStockQty"];
                
        $TypeId=$myRow["TypeId"];
        //配件QC检验标准图
        include "../model/subprogram/stuffimg_qcfile.php";
                 
		$kc_tAmount=$tStockQty*$Price;  
        $kc_tAmount = sprintf("%.3f", $kc_tAmount);
        $SumKctAmount+=$kc_tAmount;  
        $SumtStockQty+=  $tStockQty; 
		$kc_oAmount=$oStockQty*$Price;
		$kc_oAmount = sprintf("%.3f", $kc_oAmount);
		$SumKcOAmount+=$kc_oAmount;
		$SumoStockQty=$SumoStockQty+$oStockQty;
		$Price = sprintf("%.4f", $Price);
		//订单总数
		$orderQty=0;$cgQty=0;
		$OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>查看</a>";
		$CheckGSql=mysql_query("SELECT SUM(OrderQty) AS orderQty,SUM(FactualQty+AddQty) AS cgQty FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId' ",$link_id);//AND POrderId!=''
		if($CheckGRow=mysql_fetch_array($CheckGSql)){
			$orderQty=$CheckGRow["orderQty"]==""?0:$CheckGRow["orderQty"];
			$cgQty=$CheckGRow["cgQty"]==""?0:$CheckGRow["cgQty"];
		}
		//入库总数
		$UnionSTR3=mysql_query("SELECT SUM(Qty) AS rkQty FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId' and Type=1",$link_id);
		$rkQty=mysql_result($UnionSTR3,0,"rkQty");
		$rkQty=$rkQty==""?0:$rkQty;

		//领料总数
		$UnionSTR4=mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE StuffId='$StuffId'",$link_id);
		$llQty=mysql_result($UnionSTR4,0,"llQty");
		$llQty=$llQty==""?0:$llQty;

		//备品转入数量
		$UnionSTR5=mysql_query("SELECT SUM(Qty) AS bpQty FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId' AND  Type=2 ",$link_id);
		$bpQty=mysql_result($UnionSTR5,0,"bpQty");
		$bpQty=$bpQty==""?0:$bpQty;
		
		//报废数量,只有审核通过的才算 
		$UnionSTR6=mysql_query("SELECT SUM(Qty) AS bfQty FROM $DataIn.ck8_bfsheet WHERE StuffId='$StuffId' AND (Estate=0 OR Estate=3)  ",$link_id);
		$bfQty=mysql_result($UnionSTR6,0,"bfQty");
		$bfQty=$bfQty==""?0:$bfQty;
		
		//退换数量
		$UnionSTR7=mysql_query("SELECT SUM(Qty) AS thQty FROM $DataIn.ck2_thsheet WHERE StuffId='$StuffId'",$link_id);
		$thQty=mysql_result($UnionSTR7,0,"thQty");
		$thQty=$thQty==""?0:$thQty;
		
		//补仓数量
		$UnionSTR8=mysql_query("SELECT SUM(Qty) AS bcQty FROM $DataIn.ck3_bcsheet WHERE StuffId='$StuffId'",$link_id);
		$bcQty=mysql_result($UnionSTR8,0,"bcQty");
		$bcQty=$bcQty==""?0:$bcQty;


		
		$tValue=$dStockQty+$rkQty+$bpQty-$llQty-$bfQty-$thQty+$bcQty-$shipstuffllQty;		//初始库存+入库数量+备品转入-领料数量-报废数量-退换数量+补仓数量-配件出货领料数
		$oValue=$dStockQty+$cgQty+$bpQty-$orderQty-$bfQty-$shipstuffQty;	//初始库存+采购数量+备品转入-订单数量-报废数量-配件出货
		$OrderSignColor="";$WarnRemark="";
		//echo "$tValue-$tStockQty-$oValue-$oStockQty";
		if($tValue-$tStockQty>0.01 || $oValue-$oStockQty>0.01){
			$OrderSignColor="bgcolor='#FF6633'";
			$WarnRemark="实物库存:$tStockQty<>$tValue(计算); 订单库存:$oStockQty<>$oValue(计算)";
			}
                        
		
		$ChooseOut="N";
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
			array(0=>$dStockQty,
					 1=>"align='right'"),
			array(0=>$mStockQty,
					 1=>"align='right'"),
			array(0=>$tStockQty,					
					 1=>"align='right'"),
			array(0=>$oStockQty,
					 1=>"align='right'"),
			array(0=>$orderQty,
					 1=>"align='right'"),
			array(0=>$shipstuffQty,
					 1=>"align='right'"), 
			array(0=>$cgQty,
					 1=>"align='right'"),
			array(0=>$rkQty,
					 1=>"align='right'"),
			array(0=>$llQty,
					 1=>"align='right'"),
			array(0=>$bpQty,
					 1=>"align='right'"),
			array(0=>$bfQty,
					 1=>"align='right'"),
			array(0=>$thQty,
					 1=>"align='right'"),
			array(0=>$bcQty,
					 1=>"align='right'"), 
			array(0=>$kc_tAmount,
					 1=>"align='right'"),
		    array(0=>$kc_oAmount,
					 1=>"align='right'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	//合计
       
	$ValueArray=array(
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
                array(0=>"&nbsp;"	),
				
				array(0=>$SumtStockQty,1=>"align='right'"),
				array(0=>$SumoStockQty,1=>"align='right'"),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>$SumKctAmount,1=>"align='left'"),
				array(0=>$SumKcOAmount,1=>"align='left'")
				);
				
			$ShowtotalRemark="合计";
			$isTotal=1;$m=1;
			include "../model/subprogram/read_model_total.php";
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