<?php 
//$DataIn.电信---yang 20120801
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 非BOM配件分析");
$funFrom="nonbom4";
$nowWebPage=$funFrom."_error";
$Th_Col="选项|40|序号|30|配件编号|50|非bom配件名称|350|单位|40|在库库存|60|采购库存|60|最低库存|60|领用库存|60|入库地点|70|备注|120";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 500;
$ActioToS="";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.GoodsId,A.GoodsName,A.Price,A.Unit,A.ReturnReasons,A.Attached,A.Estate,A.Locks,A.Date,A.Operator,B.TypeName,
C.wStockQty,C.oStockQty,C.mStockQty,C.lStockQty,D.Forshort,D.CompanyId,E.Name AS Buyer,F.Symbol,A.Remark ,K.Name AS rkName
FROM $DataPublic.nonbom4_goodsdata A
LEFT JOIN $DataPublic.nonbom2_subtype B  ON B.Id=A.TypeId
LEFT JOIN $DataPublic.nonbom5_goodsstock C ON C.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom3_retailermain D ON D.CompanyId=C.CompanyId
LEFT JOIN $DataPublic.staffmain E ON E.Number=B.BuyerId
LEFT JOIN $DataPublic.currencydata F ON F.Id=D.Currency
LEFT JOIN $DataPublic.nonbom0_ck K ON K.Id=A.CkId
WHERE 1 $SearchRows AND (B.cSign='0' OR B.cSign='$Login_cSign') AND  A.Estate=1";
//echo $mySql;
$SumoStockQty=0;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$GoodsId=$myRow["GoodsId"];
		$GoodsName=$myRow["GoodsName"];
		$TypeName=$myRow["TypeName"]==""?"&nbsp;":$myRow["TypeName"];
        $Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Attached=$myRow["Attached"];
		if($Attached==1){
			$Attached=$GoodsId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
			}
        include"../model/subprogram/good_Property.php";//非BOM配件属性
		$Symbol=$myRow["Symbol"];
		$Price=$myRow["Price"];
		$Unit=$myRow["Unit"];
		$Buyer=$myRow["Buyer"];
		$Forshort=$myRow["Forshort"];
		$wStockQty=$myRow["wStockQty"];
		$oStockQty=$myRow["oStockQty"];
		$mStockQty=$myRow["mStockQty"];
		$lStockQty=$myRow["lStockQty"];
		$GoodsIdStr="<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
       $rkName=$myRow["rkName"]==""?"&nbsp;":$myRow["rkName"];
		$CheckGRow=mysql_fetch_array(mysql_query("SELECT SUM(IFNULL(A.cgQty,0)) AS cgQty,
				SUM(IFNULL(A.rkQty,0)) AS rkQty,SUM(IFNULL(A.llQty,0)) AS llQty,SUM(IFNULL(A.bpQty,0)) AS bpQty,
				SUM(IFNULL(A.bfQty,0)) AS bfQty,SUM(IFNULL(A.tcQty,0)) AS tcQty,SUM(IFNULL(A.bfQty1,0)) AS bfQty1 
		FROM (
                 SELECT SUM(Qty) AS cgQty,0 AS rkQty,0 AS llQty,0 AS bpQty,0 AS bfQty,0 AS tcQty,0 AS bfQty1 
                  FROM $DataIn.nonbom6_cgsheet WHERE GoodsId='$GoodsId' 
          UNION ALL
				 SELECT 0 AS cgQty,SUM(Qty) AS rkQty,0 AS llQty,0 AS bpQty,0 AS bfQty,0 AS tcQty,0 AS bfQty1 
				 FROM $DataIn.nonbom7_insheet WHERE GoodsId='$GoodsId' 
		UNION ALL
				SELECT 0 AS cgQty,0 AS rkQty,SUM(Qty) AS llQty,0 AS bpQty,0 AS bfQty,0 AS tcQty,0 AS bfQty1 
				FROM $DataIn.nonbom8_outsheet WHERE GoodsId='$GoodsId'  AND Estate=0
		UNION ALL
				SELECT 0 AS cgQty,0 AS rkQty,0 AS llQty,SUM(Qty) AS bpQty,0 AS bfQty,0 AS tcQty,0 AS bfQty1 
				FROM $DataIn.nonbom9_insheet WHERE GoodsId='$GoodsId' 
	    UNION ALL
				SELECT 0 AS cgQty,0 AS rkQty,0 AS llQty,0,SUM(Qty) AS bfQty,0 AS tcQty,0 AS bfQty1 
				FROM $DataIn.nonbom10_outsheet WHERE  GoodsId='$GoodsId' 
		UNION ALL
				SELECT 0 AS cgQty,0 AS rkQty,0 AS llQty,0,0 AS bfQty,SUM(Qty) AS tcQty,0 AS bfQty1
				 FROM $DataIn.nonbom8_reback  WHERE GoodsId='$GoodsId' 
		UNION ALL 
				SELECT 0 AS cgQty,0 AS rkQty,0 AS llQty,0,0 AS bfQty,0 AS tcQty,SUM(Qty) AS bfQty1 
				FROM $DataIn.nonbom8_bf WHERE GoodsId='$GoodsId' 
	   )A ",$link_id));
	  
	   $orderQty=$CheckGRow["orderQty"];
       $cgQty=$CheckGRow["cgQty"];
       $rkQty=$CheckGRow["rkQty"];
       $llQty=$CheckGRow["llQty"];
       $bpQty=$CheckGRow["bpQty"];
       $bfQty=$CheckGRow["bfQty"];
       $tcQty=$CheckGRow["tcQty"];
       $bfQty1=$CheckGRow["bfQty1"];
       

		$tValue=$rkQty+$bpQty-$llQty-$bfQty;
		$oValue=$cgQty+$bpQty-$llQty-$bfQty;
		$lValue=$llQty-$bfQty1-$tcQty;
		$OrderSignColor="";
		
		if($tValue-$tStockQty!=0 || $oValue-$oStockQty!=0  || $lValue-$lStockQty!=0){
			$OrderSignColor="bgcolor='#FF6633'";
		    $ValueArray=array(
			array(0=>$GoodsIdStr,1=>"align='center'"),
			array(0=>$GoodsName),
			array(0=>$Unit,1=>"align='center'"),
			array(0=>$wStockQty,1=>"align='right'"),
			array(0=>$oStockQty,1=>"align='right'"),
			array(0=>$mStockQty,1=>"align='right'"),
			array(0=>$lStockQty,1=>"align='right'"),
			array(0=>$rkName,1=>"align='center'"),
			array(0=>$Remark,1=>"align='center'")
			);
			$checkidValue=$Id;
			include "../model/subprogram/read_model_6.php";
			}
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
pBottom($i-1,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
?>