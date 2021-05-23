<?php
$ShipType="";
          $CheckShipSplitResult=mysql_query("SELECT Id,Qty,ShipId,ShipType FROM $DataIn.ch1_shipsplit WHERE POrderId=$POrderId",$link_id);
          while($CheckShipSplitRow=mysql_fetch_array($CheckShipSplitResult)){
                    $SplitShipType=$CheckShipSplitRow["ShipType"];
                    $SplitShipId=$CheckShipSplitRow["ShipId"];
                    $SplitShipQty=$CheckShipSplitRow["Qty"];
                    $SplitId=$CheckShipSplitRow["Id"];
                   	$CheckShipType=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.ch_shiptype WHERE Id='$SplitShipType'  LIMIT 1",$link_id));
	                 $SplitShipName=$CheckShipType["Name"];
	                 
                   	$CheckShipQtyResult=mysql_fetch_array(mysql_query("SELECT Qty FROM $DataIn.ch1_shipsheet WHERE Id='$SplitShipId'",$link_id));
	                 $CheckShipQty=$CheckShipQtyResult["Qty"];
	                 
	                 $ToOutName = '未转发';
	                 $OutResult = mysql_query("SELECT D.ToOutName  FROM $DataIn.yw7_clientOutData O
								  LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
								  WHERE O.MId='$SplitId'",$link_id);
					if ($Outmyrow = mysql_fetch_array($OutResult)) {
						$ToOutName='转发对象:'.$Outmyrow["ToOutName"];
					 }
	                 
                     if($CheckShipQty>0)$CheckShipQtyStr="<span class='greenB' title='$ToOutName'>$CheckShipQty</span>";
                     else{
                          $CheckShipQtyStr="<span class='yellowB' title='$ToOutName'>$SplitShipQty</span>";
                             }
	                 if ($SplitShipType!=""){
	                     if($ShipType=="")$ShipType="<image src='../images/ship$SplitShipType.png' style='width:20px;height:20px;' title='$SplitShipName'/>".$CheckShipQtyStr;
	                    else  $ShipType=$ShipType."<br>"."<image src='../images/ship$SplitShipType.png' style='width:20px;height:20px;' title='$SplitShipName'/>".$CheckShipQtyStr;
                    }
                 }
        
     if($Estate==1 && $ShipType==""){
		 //出货方式
	     $ShipType=$myRow["ShipType"];
	     if (strlen(trim($ShipType))>0){//未出里面有
	        $CheckShipType=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.ch_shiptype WHERE Id='$ShipType'  LIMIT 1",$link_id));
	        $ShipName=$CheckShipType["Name"];
		    $ShipType="<image src='../images/ship$ShipType.png' style='width:20px;height:20px;' title='$ShipName'/>";
	    }
   }
?>