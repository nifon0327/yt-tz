<?php 
include "../model/modelhead.php";
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
//步骤2：需处理
$ColsNumber=15;
$tableMenuS=500;
ChangeWtitle("$SubCompany 未收货配件数量统计");
$funFrom="cg_stuffqty";
$From=$From==""?"read":$From;
$sumCols="6,7,8,9,10,11";			//求和列,需处理
$Th_Col="选项|60|序号|30|配件ID|45|配件名称|200|规格|200|图档|30|单位|40|订单需求数|80|采购总数|80|已收总数|80|未收总数|80|退货总数|80|未补货总数|80|配件分析|60|历史订单|60";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1";
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRowsA="";
	$ActioToS="1,11";
//采购
	$buyerSql = mysql_query("SELECT S.BuyerId,M.Name FROM $DataIn.cg1_stocksheet S LEFT JOIN $DataPublic.staffmain M ON S.BuyerId=M.Number 
	WHERE S.Mid>0 and S.rkSign>0 GROUP BY S.BuyerId ORDER BY S.BuyerId",$link_id);
	if($buyerRow = mysql_fetch_array($buyerSql)){
		echo"<select name='Number' id='Number' onchange='zhtj()'>";
                $tempBuyerId=$buyerRow["BuyerId"];
		do{
			$thisBuyerId=$buyerRow["BuyerId"];
			$Buyer=$buyerRow["Name"];
			if ($Number==$thisBuyerId || ($Login_P_Number==$thisBuyerId && $Number=="")){
				echo "<option value='$thisBuyerId' selected>$Buyer</option>";
				$SearchRowsA=" and BP.BuyerId='$thisBuyerId'";
				}
			else{
				echo "<option value='$thisBuyerId'>$Buyer</option>";
				}
			}while ($buyerRow = mysql_fetch_array($buyerSql));
		echo"</select>&nbsp;";
                if ($SearchRowsA=="") $SearchRowsA=" and BP.BuyerId='$tempBuyerId'";
		}

//供应商 
	$providerSql= mysql_query("SELECT S.CompanyId,P.Forshort,P.Letter 
	FROM $DataIn.cg1_stocksheet S
    LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
    LEFT JOIN $DataIn.bps BP ON BP.StuffId=A.StuffId 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	WHERE 1 $SearchRowsA and S.Mid>0 and S.rkSign>0 GROUP BY S.CompanyId ORDER BY P.Letter",$link_id);
	if($providerRow = mysql_fetch_array($providerSql)){
		echo "<select name='CompanyId' id='CompanyId' onchange='document.form1.submit();'>";
		echo"<option value='' selected>全部供应商</option>";
		do{
			$Letter=$providerRow["Letter"];
			$Forshort=$providerRow["Forshort"];
			$Forshort=$Letter.'-'.$Forshort;
			$thisCompanyId=$providerRow["CompanyId"];						
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRowsA.=" and P.CompanyId='$thisCompanyId'";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($providerRow = mysql_fetch_array($providerSql));
		echo"</select>&nbsp;";
		}
	else{
		//无供应商记录
		$SearchRowsA.=" and P.CompanyId=''";
		}
		
	}
	
//检查进入者是否采购
$checkResult = mysql_query("SELECT JobId FROM $DataPublic.staffmain WHERE Number=$Login_P_Number ORDER BY Id LIMIT 1",$link_id);
if($checkRow = mysql_fetch_array($checkResult)){
	$JobId=$checkRow["JobId"];//3为采购
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT * FROM (
SELECT S.StuffId,A.StuffCname,A.Spec,A.Picture,A.Gfile,A.Gremark,A.TypeId,U.Name AS UnitName
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id  
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
LEFT JOIN $DataIn.bps BP ON BP.StuffId=A.StuffId 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=BP.CompanyId 
LEFT JOIN  $DataPublic.stuffunit U ON U.Id=A.Unit
WHERE 1 and S.Mid>0 and S.rkSign>0 $SearchRowsA  $SearchRows GROUP BY S.StuffId 
UNION ALL
SELECT StuffId,StuffCname,Spec,Picture,Gfile,Gremark,TypeId, UnitName FROM (
     SELECT  S.StuffId,A.StuffCname,A.Spec,A.Picture,A.Gfile,A.Gremark,A.TypeId,U.Name AS UnitName,IFNULL(SUM(S.Qty),0) AS thQty,B.bcQty
     FROM $DataIn.ck2_thsheet S
     LEFT JOIN (  SELECT StuffId,IFNULL(SUM(Qty),0) AS bcQty  FROM $DataIn.ck3_bcsheet WHERE 1 GROUP BY StuffId )  B ON B.StuffId=S.StuffId
     LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
     LEFT JOIN $DataIn.bps BP ON BP.StuffId=A.StuffId 
	 LEFT JOIN $DataIn.trade_object P ON P.CompanyId=BP.CompanyId 
     LEFT JOIN  $DataIn.stuffunit U ON U.Id=A.Unit  WHERE 1  $SearchRowsA $SearchRows GROUP BY A.StuffId 
 ) A  WHERE  A.thQty>A.bcQty ) B WHERE 1 GROUP BY B.StuffId";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$tempStuffId="";
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		//$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Spec = $myRow["Spec"]==""?"&nbsp;":$myRow["Spec"];;
		$TypeId=$myRow["TypeId"];
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
			$Picture=$myRow["Picture"];
			$Gfile=$myRow["Gfile"];
			$Gremark=$myRow["Gremark"];
			$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
			//加密
			if($Gfile!=""){
				$Gfile=anmaIn($Gfile,$SinkOrder,$motherSTR);
				//$Gfile="<img onClick='OpenOrLoad(\"$d\",\"$Gfile\",6)' src='../images/down.gif' alt='$Gremark' width='18' height='18'>";
				$Gfile="<a href=\"openorload.php?d=$d&f=$Gfile&Type=&Action=6\" target=\"download\"><img src='../images/down.gif' alt='$Gremark' width='18' height='18'></a>";
				}
			else{
				$Gfile="&nbsp;";
				}
			//检查是否有图片
			include "../model/subprogram/stuffimg_model.php";
		$OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=$Id' target='_blank'>查看</a>";
		//已购总数
		$cgTemp=mysql_query("SELECT SUM(OrderQty) AS odQty,SUM(S.FactualQty+S.AddQty) AS Qty
		 FROM $DataIn.cg1_stocksheet S 
		 LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
		 WHERE 1 $SearchRows and S.Mid>0 and S.StuffId='$StuffId'",$link_id);
		
		$cgQty=mysql_result($cgTemp,0,"Qty");
		$cgQty=$cgQty==""?0:$cgQty;
		$odQty=mysql_result($cgTemp,0,"odQty");
		$odQty=$odQty==""?0:$odQty;
		 
		//已收货总数
		$rkTemp=mysql_query("SELECT SUM(R.Qty) AS Qty
		 FROM $DataIn.ck1_rksheet R 
		LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
		LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
		WHERE  R.Type=1 AND R.StuffId='$StuffId' $SearchRows ",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:$rkQty;
			
		$noQty=$cgQty-$rkQty;
				
			//退货的总数量
			$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
										   LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
										   WHERE 1 AND S.StuffId = '$StuffId' ",$link_id);
			$thQty=mysql_result($thSql,0,"thQty");
			$thQty=$thQty==""?0:$thQty;
			
			//补货的数量 
			$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
										   LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
										   WHERE 1 AND S.StuffId = '$StuffId' ",$link_id);
			$bcQty=mysql_result($bcSql,0,"bcQty");
			$bcQty=$bcQty==""?0:$bcQty;
						
			$webQty=$thQty-$bcQty; //未补数量	-$bcshQty;			
				
           if($noQty!=0 || $webQty!=0){		
			$DivNum="a";
			//如果已限制采购或供应商，则需传递
			$TempId="$StuffId|$Number|$CompanyId";			
			$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"cg_stuffqty_a\",\"public\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='显示或隐藏配件需求明细资料. ' width='13' height='13' style='CURSOR: pointer'>";
			$HideTableHTML="
			<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none;'>
				<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'>
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
						<br>
					</td>
				</tr>
			</table>";
			$toReport="<a href='stuffreport_result.php?Idtemp=$StuffId' target='_blank'>查看</a>";
			//清0
			$odQty=zerotospace($odQty);
			$cgQty=zerotospace($cgQty);
			$rkQty=zerotospace($rkQty);
			$noQty=zerotospace($noQty);
			$webQty=zerotospace($webQty);
			$thQty=zerotospace($thQty);
			$ValueArray=array(
				array(0=>$StuffId,		1=>"align='center'"),
				array(0=>$StuffCname),
				array(0=>$Spec),
				array(0=>$Gfile,		1=>"align='center'"),
				array(0=>$UnitName,		1=>"align='center'"),
				array(0=>$odQty,		1=>"align='right'"),
				array(0=>$cgQty,		1=>"align='right'"),
				array(0=>$rkQty, 		1=>"align='right'"),
				array(0=>"<div class='redB'>".$noQty."</div>", 1=>"align='right'"),
                                array(0=>$thQty,1=>"align='right'"),
				array(0=>"<div class='redB'>".$webQty."</div>", 1=>"align='right'"),
				array(0=>$toReport,		1=>"align='center'"),
				array(0=>$OrderQtyInfo,		1=>"align='center'")
				);
			$checkidValue=$StuffId;
			include "../model/subprogram/read_model_6.php";
			echo $HideTableHTML;		
			}
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
//$myResult = mysql_query($mySql,$link_id);
//$RecordToTal= mysql_num_rows($myResult);
pBottom($i-1,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>