<?php
$ShipType="";
     if($Estate==2 || $Estate==4){
          $CheckShipSplitResult=mysql_query("SELECT Id as spId,Qty,ShipId,ShipType FROM $DataIn.ch1_shipsplit WHERE POrderId=$POrderId",$link_id);
          while($CheckShipSplitRow=mysql_fetch_array($CheckShipSplitResult)){
			        
					$spId=$CheckShipSplitRow["spId"];
                    $SplitShipType=$CheckShipSplitRow["ShipType"];
                    $SplitShipId=$CheckShipSplitRow["ShipId"];
                    $SplitShipQty=$CheckShipSplitRow["Qty"];
                   	$CheckShipType=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.ch_shiptype WHERE Id='$SplitShipType'  LIMIT 1",$link_id));
	                 $SplitShipName=$CheckShipType["Name"];
                   	$CheckShipQtyResult=mysql_fetch_array(mysql_query("SELECT Qty FROM $DataIn.ch1_shipsheet WHERE Id='$SplitShipId'",$link_id));
	                 $CheckShipQty=$CheckShipQtyResult["Qty"];
					 
                     if($CheckShipQty>0)$CheckShipQtyStr="<span class='greenB'>$CheckShipQty</span>";
                     else{
                          $CheckShipQtyStr="<span class='yellowB'>$SplitShipQty</span>";
                     }
					 
	                 if ($SplitShipType!=""){
	                     if($ShipType=="")$ShipType="<image src='../images/ship$SplitShipType.png' style='width:20px;height:20px;' title='$SplitShipName'/>".$CheckShipQtyStr;
	                    else  $ShipType=$ShipType."<br>"."<image src='../images/ship$SplitShipType.png' style='width:20px;height:20px;' title='$SplitShipName'/>".$CheckShipQtyStr;
                    }
					
					/*
					if($spId!=""){
						$OutResult = mysql_query("SELECT D.ToOutName  FROM $DataIn.yw7_clientOutData O
												  LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
												  WHERE O.MId='$Id'",$link_id);
						//echo ""
						if ($Outmyrow = mysql_fetch_array($OutResult)) {
							//删除数据库记录
							//$Forshort=$myRow["Forshort"]; 
							$ToOutName=$Outmyrow["ToOutName"];
						}			
					}
					if($ToOutName==""){
						$ToOutName="&nbsp;";
					}
					if($ToOutNameStr==""){
						$ToOutNameStr=$ToOutName;
					}else{
						$ToOutNameStr=$ToOutNameStr."<br>".$ToOutName;
					}
							
					*/
          }
     }
    else{
		 //出货方式
	     $ShipType=$myRow["ShipType"];
	   if (strlen(trim($ShipType))>0){//未出里面有
	        $CheckShipType=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.ch_shiptype WHERE Id='$ShipType'  LIMIT 1",$link_id));
	        $ShipName=$CheckShipType["Name"];
		    $ShipType="<image src='../images/ship$ShipType.png' style='width:20px;height:20px;' title='$ShipName'/>";
	    }
		
		//$ToOutNameStr=$ToOutName;
   }
?>