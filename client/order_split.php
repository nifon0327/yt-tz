<?php   
include "../model/modelhead.php";
?>

<table width="570" border="0" cellpadding="0" cellspacing="0" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;margin-top:40px;'>
  <tr  bgcolor="#CCCCCC">
    <td width="40" class="A1111" height="25" align="center">No.</td>
    <td width="80" class="A1101" align="center">qty</td>
    <td width="80" class="A1101" align="center">shiptype</td>
    <td width="120" class="A1101" align="center">InvoiceNO</td>
	<td width="150" class="A1101" align="center">Destination</td>
  </tr>
  
  
<?

  

   $CheckResult = mysql_query("SELECT M.InvoiceNO,SP.ShipType,SP.Qty,SP.Id 
   FROM $DataIn.ch1_shipsplit SP  
   LEFT JOIN $DataIn.ch1_shipsheet S  ON S.Id = SP.ShipId
   LEFT JOIN $DataIn.ch1_shipmain M ON M.Id = S.Mid 
   WHERE SP.POrderId ='$POrderId' ORDER BY M.InvoiceNO",$link_id);
   $i=1;
   while($CheckRow = mysql_fetch_array($CheckResult)){
	   $InvoiceNO = $CheckRow["InvoiceNO"]==""?"&nbsp;":$CheckRow["InvoiceNO"];
	   $ShipType = $CheckRow["ShipType"];
	   $Qty = $CheckRow["Qty"];
	   $Id  = $CheckRow["Id"];
	   
	   if (strlen(trim($ShipType))>0){
	      $CheckShipType=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.ch_shiptype WHERE Id='$ShipType'  LIMIT 1",$link_id));
	      $ShipName=$CheckShipType["Name"];
		    $ShipType="<image src='../images/ship$ShipType.png' style='width:20px;height:20px;' title='$ShipName' />";
	    }else{
		    $ShipType ="&nbsp;";
	    }
	    
	    
	    $ToOutName="&nbsp;";
		
		$OutResult = mysql_query("SELECT D.ToOutName  FROM $DataIn.yw7_clientOutData O
								  LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
								  WHERE O.MId='$Id'",$link_id);
		if ($Outmyrow = mysql_fetch_array($OutResult)) {
			
			$ToOutName=$Outmyrow["ToOutName"];
		}else{
			$OutResult = mysql_query("SELECT D.ToOutName  FROM $DataIn.yw7_clientOutData O
						  LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
						  WHERE  O.POrderId='$POrderId' AND O.Mid=0 ",$link_id);
			//echo "";
			if ($Outmyrow = mysql_fetch_array($OutResult)) {
				
				$ToOutName=$Outmyrow["ToOutName"];
			}
		}
	   echo " <tr >
			    <td class='A0111' height='25' align='center'>$i</td>
			    <td class='A0101' align='center'>$Qty</td>
			    <td class='A0101' align='center'>$ShipType</td>
			    <td class='A0101' align='center'>$InvoiceNO</td>
				<td class='A0101' align='center'>$ToOutName</td>
			  </tr>";
	   $i++;
   }

?> 
</table>