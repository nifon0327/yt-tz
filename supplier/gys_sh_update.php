<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新送货记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$OperatorsSTR="";
$upSql=mysql_query("SELECT S.Id,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname,(G.FactualQty+G.AddQty) AS cgQty
FROM $DataIn.gys_shsheet S 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
WHERE S.Id=$Id ORDER BY S.Id DESC",$link_id); 
if($upData = mysql_fetch_array($upSql)){
	$Id=$upData["Id"];
	$StuffId=$upData["StuffId"];
	$StockId=$upData["StockId"];
	$Qty=$upData["Qty"];
	$cgQty=$upData["cgQty"];
	$StuffCname=$upData["StuffCname"];

	$SendSign=$upData["SendSign"];
	switch ($SendSign){
		case 1:
			$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
				   LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
				   WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' ",$link_id);
			$thQty=mysql_result($thSql,0,"thQty");
			$thQty=$thQty==""?0:$thQty;
			//补货的数量 add by zx 2011-04-27
			$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
				   LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
				   WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' ",$link_id);
			$bcQty=mysql_result($bcSql,0,"bcQty");	
			$bcQty=$bcQty==""?0:$bcQty;
			//待送货数量
			$shQty=0;
			$shSql=mysql_query("SELECT SUM( S.Qty ) AS Qty FROM $DataIn.gys_shmain M
					LEFT JOIN $DataIn.gys_shsheet S ON S.Mid = M.Id
					WHERE 1 AND M.CompanyId = '$myCompanyId' AND S.Estate>0 AND S.StuffId=$StuffId AND (S.StockId='-1' or S.SendSign='1')",$link_id);  
			$shQty=mysql_result($shSql,0,"Qty");
			$shQty=$shQty==""?0:$shQty;	
			
			$cgQty=$thQty-$bcQty;		
			$noQty=$cgQty-$shQty;
			 //echo " $noQty=$thQty-$bcQty-$shQty;";
            $unQtys=$noQty+$Qty;
			
			$StockId="本次补货";
		 break;
		case 2:
			  $cgQty=0;
			  $StockId="本次备品";
			  $noQty=0;
			  $unQtys=0;				  
		 break;
		default :
			//已收货总数
			$rkTemp=mysql_query("SELECT SUM(R.Qty) AS Qty FROM $DataIn.ck1_rksheet R 
				LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
				WHERE R.StockId='$StockId'",$link_id);
			$rkQty=mysql_result($rkTemp,0,"Qty");
			$rkQty=$rkQty==""?0:$rkQty;
			
			//待送货数量
			$shSql=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.gys_shsheet WHERE 1 AND Estate>0 AND StockId=$StockId",$link_id);
			$shQty=mysql_result($shSql,0,"Qty");
			$shQty=$shQty==""?0:$shQty;
			
			//未准备送货=订单总数-已送货数-待送货数
			 $noQty=$cgQty-$rkQty-$shQty;
			 $unQtys=$noQty+$Qty;		
		 break;
	}

	 
}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../admin/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate,TempValue,";
//步骤5：//需处理

?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td width="100" rowspan="7" class="A0010">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td width="100" rowspan="11" class="A0001">&nbsp;</td></tr>
	<tr>
	  <td width="92" height="30" align="right">流 水 号：</td>
	  <td><?php  echo $StockId?><input name="StockId" type="hidden" id="StockId" value="<?php  echo $StockId?>"></td>
	</tr>
	<tr>
	  <td height="30" align="right">配 件 ID：</td>
  		<td><input name="StuffId" type="text" id="StuffId" value="<?php  echo $StuffId?>" class="I0000L" readonly></td>
  </tr>
	<tr>
	  <td height="30" align="right">配件名称：</td>
	  <td><?php  echo $StuffCname?></td>
  </tr>
	<tr>
	  <td height="30" align="right">订单数量：</td>
	  <td><?php  echo $cgQty?></td>
  </tr>
	<tr>
	  <td height="30" align="right"><input name="unQtys" type="hidden" id="unQtys" value="<?php  echo $unQtys?>"><input name="SendSign" type="hidden" id="SendSign" value="<?php  echo $SendSign?>">
      未送货总数：</td>
	  <td><input name="noQty" type="text" id="noQty" value="<?php  echo $noQty?>" class="I0000L" readonly></td>
  </tr>
	<tr>
	  <td height="30" align="right">本次送货数量：</td>
	  <td><input name="Qty" type="text" id="Qty" value="<?php  echo $Qty?>" class="I0000L" onchange="CheckNum()" onFocus="toTempValue(this.value)"></td>
  </tr>
</table>
<?php 
//步骤5：
include "../admin/subprogram/add_model_b.php";
?>
<script>


function CheckNum(){
	var Message="";
	var oldValue=document.form1.TempValue.value;					//上次输入的送货数量
	var Qty=document.form1.Qty.value;								//新送货数量
	var CheckSTR=fucCheckNUM(Qty,"");
	
	if(CheckSTR==0 || Qty==0){
		Message="不是规范或不允许的值！";	
		alert(Message);
		document.form1.Qty.value=oldValue;
		return false;
	}
	var SendSign=Number(document.form1.SendSign.value);
	if (SendSign==2)
	{
		return true;
	}
	
	var unQtys=Number(document.form1.unQtys.value);
	Qty=Number(Qty);
	//var unQtys=Number(document.form1.unQtys.value);
	if(Qty>unQtys){
		Message="超出未送货数量的范围!"+Qty+"/"+unQtys;
		}		

	if(Message!=""){
		alert(Message);
		document.form1.Qty.value=oldValue;
		return false;
		}
	else{		
		document.form1.noQty.value=unQtys-Qty;
		}
	}

/*
function CheckNum(){
	var Message="";
	var oldValue=document.form1.TempValue.value;					//上次输入的送货数量
	var Qty=document.form1.Qty.value;								//新送货数量
	var CheckSTR=fucCheckNUM(Qty,"");
	var unQtys=Number(document.form1.unQtys.value);
	if(CheckSTR==0 || Qty==0){
		Message="不是规范或不允许的值！";		
		}
	else{
		Qty=Number(Qty);
		//var unQtys=Number(document.form1.unQtys.value);
		if(Qty>unQtys){
			Message="超出未送货数量的范围!"+Qty+"/"+unQtys;
			}		
		}
	if(Message!=""){
		alert(Message);
		document.form1.Qty.value=oldValue;
		return false;
		}
	else{		
		document.form1.noQty.value=unQtys-Qty;
		}
	}
*/	
</script>
