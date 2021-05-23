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
echo "<link rel='stylesheet' href='../cjgl/css/slicebom.css'>";
echo "<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
echo "<body>";
echo "<center>";


$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
$curWeek=$dateResult["CurWeek"];
/*
$QtyResult=mysql_query("SELECT D.StuffId,G.OrderQty AS Qty,D.StuffCname,G.DeliveryWeek AS Weeks,S.mStockId  
FROM $DataIn.cg1_semifinished  S   
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.mStockId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
WHERE S.POrderId='$POrderId' AND S.StockId='$mStockId'  AND G.Level = 1 LIMIT 1",$link_id); 
*/
$QtyResult=mysql_query("SELECT D.StuffId,G.OrderQty AS Qty,D.StuffCname,G.DeliveryWeek AS Weeks,S.mStockId ,G.Level,FIND_IN_SET(G.CompanyId,getSysConfig(106)) AS orderSign 
FROM $DataIn.cg1_semifinished S 
LEFT JOIN $DataIn.cg1_stocksheet G ON  FIND_IN_SET(G.StockId,S.ParentNode)
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
WHERE S.POrderId='$POrderId' AND S.StockId='$mStockId' ORDER BY orderSign,Level",$link_id);
 
if (mysql_num_rows($QtyResult)<=0){ //特采单

	$QtyResult=mysql_query("SELECT D.StuffId,G.OrderQty AS Qty,D.StuffCname ,G.DeliveryWeek AS Weeks,A.mStockId  
    FROM yw1_scsheet A   
    LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = A.mStockId
    LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
    WHERE A.sPOrderId='$sPOrderId' LIMIT 1",$link_id);
}

if($QtyRow=mysql_fetch_array($QtyResult)){

  $PmStockId=$QtyRow["mStockId"];
  $StuffCname=$QtyRow["StuffCname"];
  $StuffId=$QtyRow["StuffId"];
  
  $PQty=$QtyRow["Qty"];
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
		   $AppFileSTR="<img src='$AppFilePath'/>";
	}
        
  
}

 $code_data=$sPOrderId;
 $code_level="H";$code_size=4;
 include "phpqrcode/createqrcode.php";
 $QR  = "<img src='$qrcode_File'>";

$data1Table="<table width='296' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' style='margin-top:6px;text-align:left;table-layout:word-wrap:break-word;word-break:break-all' >

          <tr><td style='font-size:15px;WORD-WRAP: break-word'>$StuffCname</td></tr>
          <tr > <td height='20' class='font12'>数量: $PQty</td></tr>
          <tr ><td class='font12'>流水号: $PmStockId</td></tr></table><br><br>";
          
          
$today=date("Y/m/d");
$StuffSTR="";
$CutSql =  "SELECT F.StuffCname AS SliceName,B.StuffId,B.StockId,A.mStockId,B.OrderQty AS blQty,G.StuffCname,A.Qty AS OrderQty,C.StuffId AS mStuffId,F.BoxPcs,A.sPOrderId,B.Relation 
FROM $DataIn.yw1_scsheet  A 
LEFT JOIN $DataIn.cg1_semifinished B ON B.mStockId = A.mStockId 
LEFT JOIN $DataIn.cg1_stocksheet C ON C.StockId = A.mStockId
LEFT JOIN $DataIn.stuffdata F ON F.StuffId=C.StuffId
LEFT JOIN $DataIn.stuffdata G ON G.StuffId=B.StuffId
LEFT JOIN $DataIn.stufftype T ON T.TypeId=G.TypeId 
LEFT JOIN $DataIn.stuffmaintype M ON M.Id=T.mainType 
WHERE 1  AND A.POrderId='$POrderId'  AND A.ActionId ='".$APP_CONFIG['KL_ACTIONID']."' AND M.blSign=1
AND (A.mStockId='$mStockId' OR  EXISTS (SELECT CG.StockId FROM cg1_semifinished CG WHERE CG.mStockId='$PmStockId' AND  A.mStockId=CG.StockId))";

//AND getStockIdContainSign('$PmStockId',A.StockId)>0 
//echo $CutSql;   
$CutResult = mysql_query($CutSql,$link_id);
$rowsnum = mysql_num_rows($CutResult);
$TableSTR="";	$TdSTR="";		$tempMStockId=$mStockId;

$PictureArray=array();
  if($CutRow=mysql_fetch_array($CutResult)){
		  $TempStuffId1="";
		  $TableSTR="
				<table width='640' cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;margin-top:3px;page-break-after:avoid;' >
				<tr  >
				<td class='A0100' height='25' align='center' width='25'>序号</td>
				<td class='A0100' align='center' width='175'>片材名称</td>
				<td class='A0100' width='195' align='center' >原材料名称</td>
				<td class='A0100' width='90' align='center' >刀模编号</td>
				<td class='A0100' width='50' align='center'>码数</td>
				<td class='A0100' width='50' align='center'>片数/码</td>
				<td class='A0100' width='50' align='center'>裁片数量</td>
				</tr>";

	   $i=1;$SumQty=0;
	   $scTime = 1;
	    do{
			   $StuffId=$CutRow["StuffId"]; 
			   $mStuffId=$CutRow["mStuffId"]; 
			   $StockId=$CutRow["StockId"]; 
			   $mStockId=$CutRow["mStockId"]; 
		
			   $SliceName=$CutRow["SliceName"];
			   $StuffCname=$CutRow["StuffCname"];
			   $OrderQty=$CutRow["OrderQty"];
			   $tempsPOrderId  =$CutRow["sPOrderId"];
			   
	           $blQty= $CutRow["blQty"];
	           $OrderQty= floatval($OrderQty);
	           $Relation = $CutRow["Relation"];
	           $Relations = explode("/", $Relation);
	           
	           /*
	           if (count($Relations)==2){
		            $pcsQty = ceil($Relations[1]/ $Relations[0]);
	           }else{
		            $pcsQty = $Relation;
	           }
	           */
	           $pcsQty = ceil($OrderQty/$blQty);
	           if($BoxPcs==0){
		           $BoxNum ="";
		           $BoxPcs ="";
	           }else{  
	               $BoxPcs = $CutRow["BoxPcs"];
	               $BoxNum = intval($OrderQty/$BoxPcs);
	               $lastPcs = $OrderQty-$BoxNum*$BoxPcs;
	               if($lastPcs>0){
		               $BoxNum=$BoxNum."(+".$lastPcs."pcs)";
	               }
	           }        
	        $CutStr = "";
	        $drawingSql=mysql_query("SELECT D.Picture ,D.CutId,C.CutName,C.Picture AS cutPicture,C.cutSign 
            FROM $DataIn.slice_cutdie   D  
            LEFT JOIN $DataIn.pt_cut_data   C  ON C.Id = D.CutId
            WHERE  StuffId='$mStuffId' ",$link_id);
	        while($drawingRow=mysql_fetch_array($drawingSql)){
				$CutId=$drawingRow["CutId"];
				$CutName=$drawingRow["CutName"];
				$cutSign=$drawingRow["cutSign"];
				include "../pt/subprogram/getCuttingIcon.php";
				//刀模名称
				$cutPicture=$drawingRow["cutPicture"];
                if($cutPicture==1){
                  if ($tempMStockId==$mStockId){
                     $PictureName = "C".$CutId.".jpg";
                     $PictureArray[]=$PictureName;
                     }
                     $fn=anmaIn("C".$CutId.".jpg",$SinkOrder,$motherSTR);
                     $CutName="<a href=\"../admin/openorload.php?d=$dt&f=$fn&Type=&Action=6\"target=\"download\">$CutName</a>";
                  }
                  if($CutStr!=""){
                        $CutStr  = $CutStr."<br>".$CutIconFile .$CutName;
                   }else{
                       $CutStr  = $CutIconFile.$CutName;
                  }
		      }
	          $CutStr  = $CutStr==""?"&nbsp;":$CutStr; 
	          if ($tempMStockId==$mStockId && $tempsPOrderId == $sPOrderId){     
	              
	          $data2Table ="<table width='100%'  cellspacing='0'  border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' >
              <tr><td height='15' align='center' class='A0001' >数量</td>
                  <td align='center' class='A0001' >裁片</td>
                  <td align='center' class='A0001'>装框</td>
                  <td align='center' >框数</td>
               </tr>
              <tr><td height='20' align='center' class='A0001 fontBold20'>$OrderQty</td>
                  <td align='center' class='A0001 fontBold18'>$i/$rowsnum</td>
                  <td align='center' class='A0001 fontBold18'>$BoxPcs</td>
                  <td align='center' class='fontBold18'>$BoxNum</td>
              </tr></table>";
              
	                  $data1Table.= "<table width='296' cellspacing='0' border='0' 
	                                style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;'>
						<tr >
						   <td height='20'  width='70'>片材</td>
                           <td width='226' style='white-space: normal;overflow:visible;'>$mStuffId-$SliceName</td>
						</tr>
						 <tr>
						  <td height='20'>原材料</td>
						  <td style='white-space: normal;overflow:visible;'>$StuffCname</td>
                         </tr>
                         <tr>
						  <td  height='20'>刀模编号</td>
						  <td >$CutIconFile <span style='line-height:30px;'>$CutName</span></td>
                         </tr>
                         <tr>
						  <td height='20'>码数</td>
						  <td  >$blQty</td>
                         </tr>
                         <tr>
						  <td  height='20'>片数/码</td>
						  <td   >$pcsQty</td>
                         </tr>
                       </table>";
		          $TableSTR.="<tr bgcolor='#dceaf3'>";
		          $scTime = 2;
	           }
	           else{
				   $TableSTR.= "<tr bgcolor='#FFFFFF'>";
			   }
			  
			   if($TempStuffId1!=$mStuffId){
				    $TableSTR.="<td  class='A0100' align='center' rowspan='$cutNumber'>$i</td>
				                <td  class='A0100'>$SliceName</td>";
				}
				$TableSTR.="
				<td height='21' class='A0100' ><div style='margin-left:5px;'>$StuffCname</div></td>
				<td align='left'  class='A0100'>$CutIconFile <span style='line-height:15px;'>$CutName</span></td>
				<td align='center' class='A0100'>$blQty</td>
				<td align='center' class='A0100'>$pcsQty</td>
				<td align='center' class='A0100'>$OrderQty</td>
				</tr>";
				$i++;
				$TempStuffId1=$mStuffId;
			}while($CutRow=mysql_fetch_array($CutResult));
	
	   $TableSTR.="</table>";
}

for($j=0;$j<count($PictureArray);$j++){
	 $PictureName="../download/cut_data/" . $PictureArray[$j];
}


$signTable ="<table width='90'  cellspacing='0' border='1' cellpadding='0' style='table-layout: fixed; word-wrap: break-word;border-radius:8px;' >
        <tr><td height='15' >&nbsp;&nbsp;开单</td></tr>
        <tr><td height='20' class='A0100'>&nbsp;</td></tr>
        <tr><td height='15' >&nbsp;&nbsp;开料</td></tr>
        <tr><td height='20' class='A0100'>&nbsp;</td></tr>
        <tr><td height='15' >&nbsp;&nbsp;品检</td></tr>
        <tr><td height='20' class='A0100'>&nbsp;</td></tr>
        <tr><td height='15' >&nbsp;&nbsp;出货</td></tr>
        <tr><td height='20' >&nbsp;</td></tr></table>";


?>
<div class="bodyDiv">
	<div id="leftDiv">
		<div class="pngDiv"><?php echo $AppFileSTR?></div>
		<div class="weekDiv"><?php echo $WeekName?><?php echo $dateSTR?></div>
		<div class="cutDiv"><?php echo "<img src='$PictureName'/>"?></div>
		
	</div>
	<div id="rightDiv">
		<div class="data1Div"><?php echo $data1Table?></div>
		<div class="signDiv"><?php echo $QR?><?php echo $signTable?></div>
		<div class="data2Div"><?php echo $data2Table?></div>
	</div>
	<div id="middleDiv">
		<?php echo $TableSTR?>
	</div>
</div>
</body>
</html>
