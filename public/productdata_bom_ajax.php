<?php 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=1000;

$dataArray=explode("|",$args);
$ProductId=$ProductId==""?$dataArray[0]:$ProductId;

echo"<table id='$TableId' width='$subTableWidth'  cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='40' height='20'>序号</td>
		<td width='60' align='center'>配件ID</td>
		<td width='350' align='center'>配件名称</td>
		<td width='40' align='center'>图档</td>		
		<td width='50' align='center'>历史订单</td>
		<td width='50' align='center'>单价</td>
		<td width='40' align='center'>单位</td>
		<td width='70' align='center'>对应关系</td>
		<td width='70' align='center'>采购</td>
		<td width='100' align='center'>供应商</td>
		</tr>";

$sListResult = mysql_query("SELECT D.StuffCname,D.CostPrice,D.Price,D.Gfile,D.Gstate,D.Picture,D.StuffId,
D.TypeId,M.Name,C.Rate,G.Forshort,G.Currency,G.Currency,G.ProviderType,P.Relation,P.Id ,P.Diecut,P.Cutrelation ,U.Name AS UnitName,MT.TypeColor
		FROM  $DataIn.pands P
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId
		LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId
		LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
		LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
		LEFT JOIN $DataIn.stuffmaintype MT ON MT.Id=ST.mainType
		LEFT JOIN $DataIn.staffmain M ON M.Number=B.BuyerId
		LEFT JOIN $DataIn.trade_object G ON G.CompanyId=B.CompanyId
		LEFT JOIN $DataIn.currencydata C ON C.Id=G.Currency
		WHERE P.ProductId='$ProductId' ORDER BY ST.mainType ASC",$link_id);
$i=1;$tId=1;
if ($cbRow = mysql_fetch_array($sListResult)) {
	do{
			$StuffId=$cbRow["StuffId"];			//配件ID
			$StuffCname=$cbRow["StuffCname"];	//配件名称
			$TypeId=$cbRow["TypeId"];			//产品分类
			$Name=$cbRow["Name"];				//采购
			$Forshort=$cbRow["Forshort"]==""?"&nbsp;":$cbRow["Forshort"];		//供应商
			$stuffPrice=$cbRow["Price"];		//价格
			$Currency=$cbRow["Currency"];		//货币ID
			$gRate=$cbRow["Rate"]==""?1:$cbRow["Rate"];				//货币汇率
			$Relation=$cbRow["Relation"];		//对应数量
			$UnitName=$cbRow["UnitName"]==""?"&nbsp;":$cbRow["UnitName"];		//单位
			$OppositeQTY=explode("/",$Relation);
			$thisRMB=$OppositeQTY[1]!=""?sprintf("%.4f",$gRate*$stuffPrice*$OppositeQTY[0]/$OppositeQTY[1]):sprintf("%.4f",$gRate*$stuffPrice*$OppositeQTY[0]);	//此配件的成本
			$BuyRmbSum+=$thisRMB;					//成本累加
			$BuyHzSum=$Currency==1?($BuyHzSum+$thisRMB):$BuyHzSum;				//自购成本累加
			$thisRMB=number_format($thisRMB,3);
			$TypeColor=$cbRow["TypeColor"];
			if($Currency==2){//美金标红色
				$stuffPrice="<span class='redB'>".$stuffPrice."</span>";
				$Relation="<span class='redB'>".$Relation."</span>";
				$thisRMB="<span class='redB'>".$thisRMB."</span>";
				$Forshort="<span class='redB'>".$Forshort."</span>";
				}
			if($bpRateName!="")$bpRateName="<a href='standbyrate_read.php'   target='_blank'>$bpRateName</a>";
			$theDefaultColor=$TypeColor;
			$ProviderType=$cbRow["ProviderType"];
					switch($ProviderType){
					   case 2:$TypeColor="style='color:#FF00FF'";break;
					 }
			$Picture=$cbRow["Picture"];			//配件照片参数
			$Gfile=$cbRow["Gfile"];				//配件图档参数
			$Gstate=$cbRow["Gstate"];  			//图档状态
			include "../model/subprogram/stuffimg_Gfile.php";	//图档显示
            include"../model/subprogram/stuff_Property.php";//配件属性    
			$OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=$Id' target='_blank'>查看</a>";


		//二级BOM表
	    $showSemiStr="&nbsp;";$showSemiTable="";$colspan=13;
	    $CheckSemiSql=mysql_query("SELECT A.Id FROM $DataIn.semifinished_bom A  WHERE  A.mStuffId='$StuffId' LIMIT 1",$link_id);
        if($CheckSemiRow=mysql_fetch_array($CheckSemiSql)){
              $showSemiStr="<img onClick='ShowDropTable(SemiTable$tId,showtable$tId,SemiDiv$tId,\"semifinishedbom_ajax\",\"$StuffId|$k\",\"admin\");'  src='../images/showtable.gif'  title='显示或隐藏二级BOM资料.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='showtable$tId'>";
              $showSemiTable="<tr id='SemiTable$tId' style='display:none;background:#83b6b9;'><td colspan='10'><div id='SemiDiv$tId' width='720'></div></td></tr>"; 
              $tId++;
            }
            
          
               $showProduct =""; $ProductTable="";
               $CheckProductSql=mysql_query("SELECT A.Id FROM $DataIn.pands A  WHERE  A.StuffId='$StuffId' LIMIT 1",$link_id);
               if($CheckProductRow=mysql_fetch_array($CheckProductSql)){
                                    
                  $showProduct="<img onClick='ShowDropTable(ProductTable$tId,showProductTable$tId,ProductDiv$tId,\"Stuffdata_Gfile_ajax\",\"$StuffId\",\"public\");'  src='../images/showtable.gif'  title='显示或隐藏所用到的产品.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='showProductTable$tId'>";
                  $ProductTable="<tr id='ProductTable$tId' style='display:none;background:#83b6b9;'><td colspan='10'><div id='ProductDiv$tId' width='720'></div></td></tr>"; 
                  $tId++;        
                              
                }

	                
	                
    	echo"<tr bgcolor='$theDefaultColor'>
		<td bgcolor='$bgcolor' align='center' height='20'>$showSemiStr $i $showProduct</td>";//
		echo"<td  align='center' >$StuffId</td>";	
		echo"<td  >$StuffCname</td>";
		echo"<td  align='center'>$Gfile</td>";
		echo"<td  align='center'>$OrderQtyInfo</td>";
		echo"<td  align='center'>$stuffPrice</td>";
		echo"<td  align='center'>$UnitName</td>";
		echo"<td  align='center'>$Relation</td>";
		echo"<td  align='center' >$Name</td>";		
		echo"<td  align='center'>$Forshort</td>";
		echo"</tr>";
		$i=$i+1;
		echo $showSemiTable;
		echo $ProductTable;
	}while ($cbRow = mysql_fetch_array($sListResult));
	
}

if ($i==1){
	echo"<tr><td height='30' colspan='10'>无相关的产品.</td></tr>";
}

echo"</table>"."";

?>