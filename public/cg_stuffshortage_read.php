<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cg1_stocksheet
$DataPublic.staffmain
$DataIn.trade_object
$DataIn.stuffdata
$DataIn.ck9_stocksheet
二合一已更新
*/
include "../model/modelhead.php";
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=500;
ChangeWtitle("$SubCompany 采购缺料单");
$funFrom="cg_stuffshortage";
$From=$From==""?"read":$From;
$sumCols="6,7,8,9";			//求和列,需处理
$Th_Col="选项|60|序号|30|配件ID|45|配件名称|300|备料需求数|80|在库|80|缺料总数|80|供应商|80|采购员|80|配件分析|60";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//$ActioToS="1";
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows="";
	//$ActioToS="1,11";
//采购
$buyerSql = mysql_query("SELECT S.BuyerId,M.Name FROM $DataIn.yw9_blsheet B 
						LEFT JOIN $DataIn.cg1_stocksheet S ON S.POrderId=B.POrderId 
						LEFT JOIN $DataPublic.staffmain M ON S.BuyerId=M.Number 
						WHERE B.Estate=1 GROUP BY S.BuyerId ORDER BY S.BuyerId
						",$link_id);

	if($buyerRow = mysql_fetch_array($buyerSql)){
		echo"<select name='Number' id='Number' onchange='document.form1.submit();'>";
		echo"<option value='' selected>全部采购</option>";
		do{
			$thisBuyerId=$buyerRow["BuyerId"];
			$Buyer=$buyerRow["Name"];
			if ($Number==$thisBuyerId){
				echo "<option value='$thisBuyerId' selected>$Buyer</option>";
				$SearchRows=" and S.BuyerId='$thisBuyerId'";
				}
			else{
				echo "<option value='$thisBuyerId'>$Buyer</option>";
				}
			}while ($buyerRow = mysql_fetch_array($buyerSql));
		echo"</select>&nbsp;";
		}

//供应商
	$providerSql= mysql_query("SELECT S.CompanyId,P.Forshort,P.Letter 
	FROM $DataIn.yw9_blsheet B 
	LEFT JOIN $DataIn.cg1_stocksheet S ON S.POrderId=B.POrderId 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	WHERE 1 $SearchRows and B.Estate=1 GROUP BY S.CompanyId ORDER BY P.Letter",$link_id);
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
				$SearchRows.=" and S.CompanyId='$thisCompanyId'";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($providerRow = mysql_fetch_array($providerSql));
		echo"</select>&nbsp;";
		}
	else{
		//无供应商记录
		$SearchRows.=" and S.CompanyId=''";
		}
	}
	
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.StuffId,A.StuffCname,SUM(S.OrderQty) AS OrderQty,K.tStockQty,A.Picture,A.TypeId,P.Forshort,M.Name    
FROM $DataIn.yw9_blsheet B 
LEFT JOIN $DataIn.cg1_stocksheet S ON S.POrderId=B.POrderId 
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
LEFT JOIN $DataPublic.staffmain M ON S.BuyerId=M.Number 
LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
WHERE 1 and  B.Estate=1  and T.mainType<2 $SearchRows GROUP BY S.StuffId  ORDER BY S.StuffId DESC";
$myResult = mysql_query($mySql,$link_id);
$tempStuffId="";
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$OrderQty=$myRow["OrderQty"];
		$tStockQty=$myRow["tStockQty"];
		$noQty=$OrderQty-$tStockQty;
	  if ($noQty>0){
		$TypeId=$myRow["TypeId"];
		$Picture=$myRow["Picture"];
		$Forshort=$myRow["Forshort"];
		$BuyName=$myRow["Name"];
		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
		//检查是否有图片
		include "../model/subprogram/stuffimg_model.php";
		$toReport="<a href='stuffreport_result.php?Idtemp=$StuffId' target='_blank'>查看</a>";
			
			$ValueArray=array(
				array(0=>$StuffId,		1=>"align='center'"),
				array(0=>$StuffCname),
				array(0=>$OrderQty,		1=>"align='right'"),
				array(0=>$tStockQty,	1=>"align='right'"),
				array(0=>"<div class='redB'>".$noQty."</div>", 1=>"align='right'"),
				array(0=>$Forshort,		1=>"align='center'"),
				array(0=>$BuyName,		1=>"align='center'"),
				array(0=>$toReport,		1=>"align='center'")
				);
			include "../model/subprogram/read_model_6.php";
			}
		}while ($myRow = mysql_fetch_array($myResult));
		if ($i==1) noRowInfo($tableWidth);
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