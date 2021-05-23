<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
if ($FromPage!='App'){
	include "../basic/chksession.php" ;
}
include "../basic/parameter.inc";
include "../model/modelfunction.php";
include "../basic/config.inc";
include "phpqrcode/qrcodelib.php";
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
echo "<link rel='stylesheet' href='../cjgl/lightgreen/read_line.css'>";
echo "<link rel='stylesheet' href='../cjgl/css/ckbl.css'>";
echo "<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
echo "<body>";
echo "<center>";


$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
$curWeek=$dateResult["CurWeek"];


$QtyResult=mysql_query("SELECT (S.AddQty+S.FactualQty) AS OrderQty,D.StuffCname,YEARWEEK(S.DeliveryDate,1) AS Weeks,D.StuffId,SC.Qty,Y.OrderPO   
FROM $DataIn.yw1_scsheet SC 
LEFT JOIN $DataIn.cg1_stocksheet S ON SC.mStockId = S.StockId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId 
WHERE SC.sPOrderId='$sPOrderId' ",$link_id); 
if($QtyRow=mysql_fetch_array($QtyResult)){
  $StuffCname=$QtyRow["StuffCname"];
  $PQty=$QtyRow["Qty"];
  $OrderQty=$QtyRow["OrderQty"];
  $Relation=$PQty/$OrderQty;
  $StuffId=$QtyRow["StuffId"];
  $OrderPO=$QtyRow["OrderPO"];
  $Weeks=$QtyRow["Weeks"];
  if ($Weeks>0){
      $WeekColor=$curWeek>=$Weeks?"#FF0000":"#000000";
      $week=substr($Weeks, 4,2);
      $dateArray= GetWeekToDate($Weeks,"m/d");
      $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
      
       
    $WeekName= "<ul><li><img src='../images/lcd/lcd_b" . substr($Weeks,4,1) . ".png'/></li><li><img src='../images/lcd/lcd_b" . substr($Weeks,5,1) . ".png' /></li></ul>";
    $WeekName="<div  id='weekImg'>$WeekName</div>";
    $dateSTR ="<div  id='dateImg'>$dateSTR</div>";
  }
	$AppFileJPGPath="../download/stuffIcon/" .$StuffId.".jpg";
	$AppFilePNGPath="../download/stuffIcon/" .$StuffId.".png";
	$AppFilePath ="";
    if(file_exists($AppFilePNGPath)){
       $AppFilePath  = $AppFilePNGPath;
    }else{
       if(file_exists($AppFileJPGPath)){
          $AppFilePath =  $AppFileJPGPath; 
       }
       else{
	       $AppFilePath ="";
       }
    }
	if($AppFilePath!=""){      
		   $mainAppFileSTR="<img src='$AppFilePath'/>";
	}
  
  
}
$today=date("Y/m/d");


$code_data=$sPOrderId;
 $code_level="H";$code_size=4;
 if ($code_data!=''){
	 include "phpqrcode/createqrcode.php";
	 $QR  = "<img src='$qrcode_File'>";
 }else{
	 $QR  = "";
 }
  

$data1Table="<table width='296' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' style='margin-top:6px;text-align:left;table-layout:word-wrap:break-word;word-break:break-all' >

          <tr><td style='font-size:15px;WORD-WRAP: break-word'>$StuffCname</td></tr>
          <tr > <td height='20' class='font12'>工单数量: $PQty</td></tr>
          <tr ><td class='font12'>工单流水号: $sPOrderId</td></tr>
          <tr ><td class='font12'>订单PO: $OrderPO</td></tr></table><br><br>";


 $TableSTR="<table width='640' cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;margin-top:10px;' align='center'>";
 /*
 <tr  bgcolor='#dceaf3'>
				<td class='A0100' height='30' align='center' width='30'>序号</td>
				<td class='A0100' align='center' width='300'>配件名称</td>
				<td class='A0100' width='50' align='center' >单位</td>
				<td class='A0100' width='50' align='center' >订单数</td>
				<td class='A0100' width='50' align='center'>在库</td>
				<td class='A0100' width='80' align='center'>备料数</td>
				<td class='A0100' width='60' align='center'>备料确认</td>
				</tr>
 
 */
				
$ListResult = mysql_query("SELECT S.Id,G.StockId,S.StuffId,(G.OrderQty*$Relation) AS OrderQty,A.StuffCname,U.Name AS Unit ,K.tStockQty,U.Decimals
            FROM $DataIn.cg1_semifinished G 
			LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=G.StockId 
			LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
			LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId 
			LEFT JOIN $DataIn.stuffmaintype SM ON SM.Id=ST.mainType 
	        LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit  
	        LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
			WHERE G.mStockId='$mStockId' AND S.POrderId='$POrderId'  AND SM.blSign=1  ORDER BY G.StockId",$link_id);
$i=1;	
$StuffId ="";
$sheetAppFileSTR ="";		
$tdHeight = '70px' ;
while($ListRow=mysql_fetch_array($ListResult)){
               $StockId=$ListRow["StockId"]; 
			   $StuffId=$ListRow["StuffId"];
			   $StuffCname=$ListRow["StuffCname"];
			   $Decimals=$checkStockRow["Decimals"];
			   $OrderQty= round($ListRow["OrderQty"],$Decimals);
			   $OrderQty=($OrderQty-floor($OrderQty))>0?number_format($OrderQty,1):number_format($OrderQty,0);
			   $Unit=$ListRow["Unit"];
			   $tStockQty=$ListRow["tStockQty"];
			   $tStockQty=($tStockQty-floor($tStockQty))>0?number_format($tStockQty,1):number_format($tStockQty,0);
			   
			   $checkllResult=mysql_query("SELECT SUM(Qty) AS llQty,sum(case  when Estate=1 then Estate  else 0 end) as llEstate  FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' AND sPOrderId='$sPOrderId'",$link_id);
			   if ($checkllRow=mysql_fetch_array($checkllResult)){
				   $llQty=$checkllRow["llQty"];
				   $llEstate=$checkllRow["llEstate"];
				   $llQty=($llQty-floor($llQty))>0?number_format($llQty,1):number_format($llQty,0);
				   $llEstate=$llEstate>0?"★":"";
			   }
			  else{
				  $llQty="&nbsp;";$llEstate="";
			  }
			  
			  $llEstateStr = $OrderQty."/"."<span class='llspan'>$llQty$llEstate</span>";
			  
			  	$AppFileJPGPath="../download/stuffIcon/" .$StuffId.".jpg";
				$AppFilePNGPath="../download/stuffIcon/" .$StuffId.".png";
				$AppFilePath ="";
			    if(file_exists($AppFilePNGPath)){
			       $AppFilePath  = $AppFilePNGPath;
			    }else{
			       if(file_exists($AppFileJPGPath)){
			          $AppFilePath =  $AppFileJPGPath; 
			       }
			       else{
				       $AppFilePath ="";
			       }
			    }
				if($AppFilePath!=""){      
					 $sheetAppFileSTR="<img src='$AppFilePath' width='60px' height='60px'/>";
				}
	
			   
                $showInputStr="<input type='checkbox' id='Qr$i' name='Qr[]' class='input_check'>";
			    /*$TableSTR.="<td  class='A0100' align='center' height='20'>$i</td>
                         <td  class='A0100'>$StuffCname</td>
						 <td  class='A0100' align='center'>$Unit</td>
						 <td align='right'  class='A0100' >$OrderQty</td>
						 <td align='right' class='A0100'>$tStockQty</td>
						 <td align='left' class='A0100'><span style='margin-left:40px;'>&nbsp;</span>$llEstate $llQty</td>
						<td align='center' class='A0100'>$showStr</td>
						</tr>";*/
						
				
				$showDetailDivStr = "<div id='showDetailDiv'>
				                          <div class='leftDiv'>$sheetAppFileSTR</div>
				                          <div class='middleDiv'>
				                               <div class='StuffCnameDiv'>$StuffCname</div>
				                               <div class='llEstateDiv'>$llEstateStr</div>
				                          </div>
				                          <div class='rightDiv'>$showInputStr</div>
				                          <div class='numDiv'>$i</div>
				                   </div>";
				
				if($i%2==1){
					$TableSTR.="<tr>";
				}
				if($i==1){
					$TableSTR.="<td height='$tdHeight' class='A1101' >$showDetailDivStr</td>";
				}else if ($i ==2){
					$TableSTR.="<td height='$tdHeight' class='A1100' >$showDetailDivStr</td>";
				}
				else{
					if($i%2==1){
						$TableSTR.="<td height='$tdHeight' class='A0101' >$showDetailDivStr</td>";
					}else{
						$TableSTR.="<td height='$tdHeight' class='A0100' >$showDetailDivStr</td>";
					}
				}
				
				if($i%2==0){
					$TableSTR.="</tr>";
				}
                $i++;
				 
}
if($i%2==0){
    if($i==2){
	    $TableSTR.="<td height='$tdHeight' class='A1100'>&nbsp;</td></tr>";
    }else{
	    $TableSTR.="<td height='$tdHeight' class='A0100'>&nbsp;</td></tr>";
    }
	
}

$TableSTR.="</table>";


$signTable ="<table width='100%'  cellspacing='0' border='0' cellpadding='0' style='table-layout: fixed; word-wrap: break-word;border-radius:6px;' bgcolor='#ECECEC'>
        <tr ><td height='45' style='vertical-align: top;'>开单</td>
        <td  ></td>
        <td style='vertical-align: top;'>备料</td>
        <td ></td>
        <td  style='vertical-align: top;'>品检</td>
        <td ></td>
        <td style='vertical-align: top;'>出货</td>
        <td >&nbsp;</td></tr></table>";

?>

<div class="bodyDiv">
	<div id="titleDiv">
		<div class="pngDiv"><?php echo $mainAppFileSTR?></div>
		<div class="weekDiv"><?php echo $WeekName?><?php echo $dateSTR?></div>	
		<div class="data1Div"><?php echo $data1Table?></div>
		<div class="QRDiv"><?php echo $QR?></div>	
	</div>
	<div id="signDiv">
		<?php echo $signTable?>
	</div>
	<div id="tableDiv">
		<?php echo $TableSTR?>
	</div>
</div>
</body>
</html>