<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw2_fkdjsheet
$DataIn.trade_object
$DataPublic.currencydata
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新预付订金记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT S.CompanyId,S.PurchaseID,S.TypeId,S.Amount,S.Remark,S.Date FROM $DataIn.cw2_fkdjsheet S WHERE S.Id=$Id ORDER BY S.Id LIMIT 1",$link_id));
$CompanyId=$upData["CompanyId"];
$PurchaseID=$upData["PurchaseID"];

//echo "CompanyId:$CompanyId <Br>";
$TypeId=$upData["TypeId"];
$Amount=$upData["Amount"];
$Remark=$upData["Remark"];
$Date=$upData["Date"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">

      <tr>
    	<td height="35" valign="middle" class='A0010' align="right">供 应 商：</td>
	      <?php 
			$checkSql = "SELECT P.CompanyId,P.Forshort,P.Letter,C.Symbol 
			FROM $DataIn.trade_object P 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency WHERE  P.CompanyId='$CompanyId' AND P.Estate=1 ORDER BY P.Letter";
			$checkResult = mysql_query($checkSql); 
			//echo "CompanyId:$CompanyId <Br>";
			if($checkRow = mysql_fetch_array($checkResult)){
					$CompanyIdTemp=$checkRow["CompanyId"];
					$Forshort=$checkRow["Forshort"];
					$Letter=$checkRow["Letter"];
					$Symbol=$checkRow["Symbol"];
					$Forshort=$Letter." - ($Symbol) - ".$Forshort."";
					
				
			}
			?>
        
        <td valign="middle" class='A0001'><input name="Forshort" type="text" id="Forshort" size="94"  value="<?php  echo $Forshort ?>" dataType="Require"  msg="未选择" readonly="readonly">
        <input name='CompanyId' type='hidden' id='CompanyId' value="<?php  echo $CompanyId ?> ">
        </td>
        
    </tr>    
     <tr>
    	<td height="35" valign="middle" class='A0010' align="right">采购单号：</td>
	    <td valign="middle" class='A0001'><input name="PurchaseID" type="text" id="PurchaseID" size="94" onClick='ViewShipId()' value="<?php  echo $PurchaseID ?>" dataType="Require"  msg="未选择" readonly="readonly"></td>
    </tr>    
<tr>
    	<td height="35" valign="middle" class='A0010' align="right">请款日期：</td>
	    <td valign="middle" class='A0001'><input name="Date" type="text" id="Date" size="94" value="<?php  echo $Date?>" dataType="Date" format="ymd" msg="日期未选择或格式不正确" onfocus="WdatePicker()" readonly></td>
    </tr>    <tr>
    	<td height="35" valign="middle" class='A0010' align="right">预付金额：</td>
	    <td valign="middle" class='A0001'><input name="Amount" type="text" id="Amount" size="94" value="<?php  echo $Amount?>" dataType="Currency" msg="没有输入金额或金额格式不正确"></td>
    </tr>
    <tr>
      <td height="47" align="right" valign="top" class='A0010'><br>预付备注：</td>
      <td valign="middle" class='A0001'><textarea name="Remark" cols="60" rows="6" id="Remark" dataType="Require" msg="没有输入备注"><?php  echo $Remark?></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>

<script language = "JavaScript"> 
function ViewShipId(){
	var r=Math.random();  
	var CompanyId=document.form1.CompanyId.value 
	var BackData=window.showModalDialog("cw_fkdj_s1.php?r="+r+"&tSearchPage=cw_fkdj&fSearchPage=ch_shipforward&SearchNum=1&CompanyId="+CompanyId+"&Action=12","BackData","dialogHeight =500px;dialogWidth=1000px;center=yes;scroll=yes");
	//window.open("cw_fkdj_s1.php?r="+r+"&tSearchPage=cw_fkdj&fSearchPage=ch_shipforward&SearchNum=1&Action=12");

	if(BackData){
		var CL=BackData.split("^^");
		//document.form1.chId.value=CL[0];
		document.form1.PurchaseID.value=CL[1];
		}
	}
</script> 