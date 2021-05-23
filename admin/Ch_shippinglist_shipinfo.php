<?php   
//电信-zxq 2012-08-01
/*
$DataIn.ck1_rkmain
$DataSharing.providerdata
$DataSharing.staffmain
二合一已更新
*/
include "../model/modelhead.php";
$upDataMain="$DataIn.ch1_shipmain";
ChangeWtitle("$SubCompany 填写出货信息");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_shipinfo";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$MainResult = mysql_query("SELECT M.Id,M.CompanyId,M.InvoiceNO,T.Type,I.SoldCompany,I.SoldAddress,I.ShipCompany,I.ShipAddress 
FROM $upDataMain M
LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
LEFT JOIN $DataIn.ch1_shipinfo I ON I.ShipMid=M.Id 
WHERE M.Id='$Id' LIMIT 1",$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {
	$InvoiceNO=$MainRow["InvoiceNO"];
	$Type=$MainRow["Type"]==1?1:0;
	$CompanyId=$MainRow["CompanyId"];
	$SoldCompany=$MainRow["SoldCompany"];
	$SoldAddress=$MainRow["SoldAddress"];
	$ShipCompany=$MainRow["ShipCompany"];
	$ShipAddress=$MainRow["ShipAddress"];
	}
	
$tableWidth=850;$tableMenuS=500;
//include "../model/subprogram/add_model_t.php";
//$Parameter="ActionId,1022,Id,$Mid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Invoice,$Invoice,CheckSign,'HK'";

//步骤4：//需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="A1100">
  <tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td  height="25"  align="right" scope="col">Invoice名称:</td>
            <td scope="col"><?php    echo $InvoiceNO?></td>
		</tr>
		  
		 <tr>
          <td height="25" align="right">SOLD TO Company</td>
          <td><input name="SoldToCompany" type="text" id="SoldToCompany" style='width:600px;' value="<?php echo $SoldCompany;?>"></td>
        </tr>
        <tr>
          <td height="25" align="right">SOLD TO Address</td>
          <td><input name="SoldToAddress" type="text" id="SoldToAddress" style='width:600px;' value="<?php echo $SoldAddress?>"></td>
        </tr>
        
        <tr>
          <td height="25" align="right">SHIP TO Company</td>
          <td><input name="ShipToCompany" type="text" id="ShipToCompany"  style='width:600px;' value="<?php echo $ShipCompany?>"></td>
        </tr>  
        
        <tr>
          <td height="25" align="right">SHIP TO Address</td>
          <td><input name="ShipToAddress" type="text" id="ShipToAddress"  style='width:600px;' value="<?php echo $ShipAddress?>"></td>
        </tr> 
    
        <tr>
          <td colspan="2" height="50"><input name="tohk" type="button" id="tohk" value='下载 Invoice' onClick="toNewInvoice(<?php echo $Id?>,'<?php echo $InvoiceNO?>','<?php echo $CheckSign?>')" style='width:100px;margin:10px 0 0 50px;'/></td>
        </tr>      
    </table>
	</td></tr></table>
<?php   
//步骤6：表尾
//include "../model/subprogram/add_model_b.php";
?>

<script>
function  toNewInvoice(Id,InvoiceNo,CheckSign)
{
   var NewSoldTo='';
   var NewShipTo='';
   
   var SoldToCompany=document.getElementById('SoldToCompany').value;
   var SoldToAddress=document.getElementById('SoldToAddress').value;
   if  (SoldToCompany.length>0 && SoldToAddress.length>0){
	   NewSoldTo=SoldToCompany + '|' + SoldToAddress;
   }
   
   var ShipToCompany=document.getElementById('ShipToCompany').value;
   var ShipToAddress=document.getElementById('ShipToAddress').value;
   if  (ShipToCompany.length>0 && ShipToAddress.length>0){
	   NewShipTo=ShipToCompany + '|' + ShipToAddress;
   }

   
   self.location='ch_shippinglistBlue_toinvoice.php?Id='+Id+'&CheckSign='+CheckSign+'&NewSoldTo='+NewSoldTo+'&NewShipTo='+NewShipTo+'&InvoiceNO='+InvoiceNo;
}
</script>

