<?php   
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$CountArray=explode("|",$passData);
$Count=count($CountArray);
echo "<table width='650' border='0' cellspacing='0'>";
echo"<tr><td width='50px'   height='25'>&nbsp;</td>
            <td  width='150px' class='A1110'>Product Code</td> 
            <td width='60px' class='A1111'>Qty</td>
            <td width='50px'  >&nbsp;</td></tr>";
for($k=0;$k<$Count;$k++){
      $tempcount=explode("^^",$CountArray[$k]);
        $ProductId=$tempcount[0];
        $thisQty=$tempcount[1];
        $CheckPro=mysql_fetch_array(mysql_query("SELECT P.eCode FROM $DataIn.productdata P WHERE P.ProductId=$ProductId",$link_id));
        $eCode=$CheckPro["eCode"];
         echo"<tr><td height='20'>&nbsp;</td>
           <td   class='A0110'>$eCode</td>
           <td  class='A0111'>$thisQty</td><td >&nbsp;</td>
</tr>";
}
echo "<tr><td colspan='4' height='25px'>&nbsp;</td></tr></table>";
if($ActionId==1)$StartStr=" Information Of The Bill";
else $StartStr="Information Of Reserve";
?>
	<table width="650" border="0" cellspacing="0">
		<tr><td colspan="2" align="center" valign="top"><?php    echo $StartStr?></td></tr>
		<tr><td width="150" height="25" align="right">B/D:</td>
	    <td> <?php   
	  //计算最后的Invoice编号
	  	$maxInvoiceNO=mysql_fetch_array(mysql_query("SELECT DeliveryNumber FROM $DataIn.ch1_deliverymain WHERE 1 $ModelCompanyId ORDER BY DeliveryDate DESC,DeliveryNumber LIMIT 1",$link_id));

		$maxNO=$maxInvoiceNO["DeliveryNumber"];
		//Invoice分析	
		$formatArray=explode("-",$maxNO);
		$formatLen=count($formatArray);
		if($formatLen==3){	//2.前缀+日期+编号:随日期自动变化
			$PreSTR=$formatArray[0];
			$DateSTR=date("My");
			$maxNum=trim(preg_replace("/([^0-9]+)/i","",$formatArray[2]))+1;//提取编号
			$NewInvoiceNO=$PreSTR."-".$DateSTR."-".$maxNum;
			$OnChange="onchange='changeDate()'";
			}
		else{				//1.前缀+编号
			$maxNum=trim(preg_replace("/([^0-9]+)/i","",$maxNO)); 
			$oldarray=explode($maxNum,$maxNO);
			$PreSTR=$oldarray[0];
			$maxNum+=1;
			$NewInvoiceNO=$PreSTR.$maxNum;
			}
	  ?>
	  <input name="DeliveryNumber" type="text" id="DeliveryNumber" value="<?php    echo $NewInvoiceNO?>" size="65" dataType="Require" msg="Require"></td></tr>
		<tr><td height="25" align="right">B/D Date:</td>
		  <td><input name="DeliveryDate" type="text" id="DeliveryDate" value="<?php    echo date("Y-m-d")?>" size="65" maxlength="10" dataType="Date" msg="Date"></td>
		</tr>		
		<tr><td height="25" align="right">Ship To</td>
		  <td>       <?php   
		  $checkModel=mysql_query("SELECT Id,Title,EndPlace,Address FROM $DataIn.ch8_shipmodel WHERE 1 AND CompanyId=$myCompanyId ORDER BY Id",$link_id);
		  //echo "SELECT Id,Title,EndPlace,Address FROM $DataIn.ch8_shipmodel WHERE 1 AND CompanyId=$myCompanyId ORDER BY Id";
		  if($ModelRow=mysql_fetch_array($checkModel)){
		  	echo"&nbsp;<select name='ModelId' id='ModelId' style='width:350px'  onchange='changeModel()'>";
			echo"<option value=''>Choose</option>";
			do{
				$Id=$ModelRow["Id"];
				$Title=$ModelRow["Title"];
				$EndPlace=$ModelRow["EndPlace"];
				$Address=$ModelRow["Address"];
				if(trim($Address)==''){
					echo"<option value='$Id'>$EndPlace</option>";
				}
				else{
					echo"<option value='$Id'>$EndPlace/$Address</option>";
				}
				}while($ModelRow=mysql_fetch_array($checkModel));
			echo"</select>";
			}
		  ?> <!--<input  type="checkbox" onclick="AdressNew()" id="checkbox1"> add new Address -->
        </td>
          <tr style="display:none" id='newEndPlacetr'><td align="right">Consignee</td><td ><input name="newEndPlace" type="text" id="newEndPlace" value="" size="65"></td></tr>
          <tr style="display:none" id='newAddresstr'><td align="right">Destination</td><td ><input name="newAddress" type="text" id="newAddress" value="" size="65"></td></tr>

		</tr>		
		<tr>
        <input name="ForwaderId" id="ForwaderId" type="hidden" value="3114">
        <td height="25" align="right">Forwader</td>
		  <td><input  id="ForwaderRemark" name="ForwaderRemark" type="text" size="65"></td>
</tr>	
        <tr><td height="25" align="right">ShipType:</td>
		  <td><input  id="ShipType" name="ShipType" type="text" size="65">
  		</tr>	  
</tr>	
        <tr><td height="25" align="right">Notes:</td>
		  <td><textarea name="Remark" cols="48" rows="3" id="Remark"></textarea>
  		</tr>	  
		<tr valign="bottom"><td height="30" colspan="2"  align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span onClick="saveQty(<?php echo $ActionId; ?>,<?php echo $myCompanyId; ?>)" >Save</span>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="javascript:closeMaskDiv()">Quit&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></td></tr>
</table>
