<?php
      include_once "tasks_function.php";
      include "../basic/parameter.inc";
      
      $Line=$Line==""?"C":$Line;
      $ListSTR="";
      $SC_TYPE='7100';
 //品检任务
$myResult=mysql_query("SELECT U.POrderId,U.sPOrderId,S.ProductId,H.Qty,C.Forshort,P.cName,A.Name  AS Operator   
    FROM  $DataIn.sc_currentmission U  
    LEFT JOIN $DataIn.yw1_scsheet H   ON H.sPOrderId=U.sPOrderId
    LEFT JOIN $DataIn.yw1_ordersheet S   ON S.POrderId=H.POrderId 
    LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
	LEFT JOIN $DataPublic.staffmain A ON A.Number=M.Operator 
    WHERE U.LineNumber='$Line' ORDER BY U.Id DESC LIMIT 1",$link_id);

if($myRow = mysql_fetch_array($myResult)) {
           $ProductId=$myRow["ProductId"];
           $POrderId=$myRow["POrderId"];
           $sPOrderId=$myRow["sPOrderId"];
           $cName=$myRow["cName"];
           $Forshort=$myRow["Forshort"];
           $Operator=$myRow["Operator"];
           $Qty=$myRow["Qty"];
           
           //已完成的工序数量
		  $CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C WHERE C.sPOrderId='$sPOrderId' ",$link_id));
		  $ScQty=$CheckscQty["scQty"]==""?0:$CheckscQty["scQty"];
		  
		  //已出货数量
		 $checkShipQty= mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS ShipQty,COUNT(*) AS ShipCount FROM $DataIn.ch1_shipsheet WHERE ProductId='$ProductId'",$link_id));
		 $ShipQty=$checkShipQty["ShipQty"]==""?0:number_format($checkShipQty["ShipQty"]);
		 $ShipCount=$checkShipQty["ShipCount"]==""?0:$checkShipQty["ShipCount"];
		 
		  //装箱数量
		  $StuffResult =mysql_fetch_array(mysql_query("SELECT A.Relation   
				FROM $DataIn.pands A,$DataIn.stuffdata D where A.ProductId='$ProductId' and D.StuffId=A.StuffId AND  D.TypeId='9040' LIMIT 1",$link_id));	
		  $arr_temp=explode("/",$StuffResult["Relation"]);
		  $BoxPcs=count($arr_temp)==2?round($arr_temp[1]/$arr_temp[0]):$arr_temp[0];
				
           include "../basic/downloadFileIP.php";
           $ImagePath="../download/teststandard/T".$ProductId.".jpg";
           
           $img_mtime=date("y-m-d",filemtime($ImagePath));
           
           $img_info = getimagesize($ImagePath); 
           $img_width=$img_info[0];
           $img_height=$img_info[1];
           $wScale=$img_width/1080;
           $hScale=$img_height/1660;
           
           if($wScale>=$hScale){
	           $SizeStr="width='1075' ";
           }
           else{
	           $SizeStr="height='1660' ";
           }
           
           $ListSTR="<center><img src='$ImagePath' $SizeStr></center>";
}
$upTime=date("H:i:s");

 if ($ProductId!=$OProductId || ($OPOrderId!="" && $sPOrderId!=$OPOrderId)){
 ?>
		 <input type='hidden' id='ProductId' name='ProductId' value='<?php echo $ProductId; ?>'>
		 <input type='hidden' id='POrderId' name='POrderId' value='<?php echo $sPOrderId; ?>'>
		<div id='headdiv' style='height:180px;'>
		   <div class='cName2'> <?php echo "<span class='blue_color'>$Forshort-</span>$cName";?></div>
		   <div class='cName2' style='height:60px;margin-top:-15px;'><span class='blue_color'><?php echo $Operator;?></span></div>
		   <ul class='sc_ul'>
		       <li style='text-align:right;'><span id='Qty1'><?php echo $ScQty; ?></span></li>
		       <li style='width:5px;'><div></div></li>
			   <li><?php echo number_format($Qty); ?></li>
		 	</ul>
		 </div>
		 	<ul class='info'>
		       <li style=' border-right: 3px rgba(2,115,178,0.25) solid;'><img  src='image/mtime.png' /><?php echo $img_mtime; ?></li>
			   <li style=' border-right: 3px rgba(2,115,178,0.25) solid;'><img  src='image/chqty.png'/><?php echo $ShipQty . "($ShipCount)"; ?></li>
			   <li><img  src='image/box.png'/><?php echo $BoxPcs . "Pcs"; ?></li>
		 	</ul>
		<div id='listdiv' style='overflow: hidden;height:1660px;width:1080px;'>
		<?php echo $ListSTR;?>
</div>
<?php
     }else{  echo $upTime . "|" . $ScQty; }
?>
