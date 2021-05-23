<?php
//电信-zxq 2012-08-01
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//读取入库资料
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$upSql=mysql_query("SELECT B.Id,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname,D.TypeId,(G.AddQty+G.FactualQty) AS cgQty 
                FROM $DataIn.qc_badrecord B 
		LEFT JOIN $DataIn.gys_shsheet S ON S.Mid=B.shMid AND B.StockId=S.StockId AND B.StuffId=S.StuffId 
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=B.StockId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=B.StuffId 
WHERE B.Id=$Id LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upSql)){
	$StuffId=$upData["StuffId"];
	$StockId=$upData["StockId"];
	$Qty=$upData["Qty"];
	$cgQty=$upData["cgQty"];
	$StuffCname=$upData["StuffCname"];
        $TypeId=$upData["TypeId"];
        $SendSign=$upData["SendSign"];
        switch ($SendSign){
           case 1:
               $StockId="本次补货";
               break;
           case 2:
               $StockId="本次备品";
               break;
        }
 }
 $saveWebPage="item2_4_ajax.php?ActionId=3";
?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe>
<form action="<?php    echo $saveWebPage?>" method="post"  enctype="multipart/form-data"  target="FormSubmit" name="saveForm" id="saveForm" onsubmit= "if(Validator.Validate(this,3) && checkObadValue()){return true}else{return false;}">
  <table width="880" height="70" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr align="center" bgcolor="#d6efb5">
    <td width="100" height="30" class="A1111">流水号<input name="Id" type="hidden" id="Id" value="<?php    echo $Id?>"></td>
    <td width="60" class="A1101">配件ID</td>
    <td width="340" class="A1101">配件名称</td>
    <td width="60" class="A1101">采购数量</td>
    <td width="60" class="A1101">送货数量</td>
  </tr>
  <tr align="center">
    <td height="30" class="A0111"><?php    echo $StockId?></td>
    <td class="A0101"><?php    echo $StuffId?></td>
    <td class="A0101"><?php    echo $StuffCname?></td>
    <td class="A0101"><?php    echo $cgQty?></td>
    <td class="A0101"><?php    echo $Qty?></td>
  </tr>
</table>
<br/>
<input name="CheckAQL" type="hidden" id="CheckAQL" value="">
<input name="ReQty" type="hidden" id="ReQty" value=""/>
<input name="CheckQty" type="hidden" id="CheckQty" value="<?php    echo $Qty?>"/>
<table width="880" height="100%" border="0" cellpadding="0" cellspacing="0">
    <tr align="center">
    <td width="80"height="30" bgcolor="#d6efb5" class="A0111">序号</td>
    <td width="400" bgcolor="#d6efb5" class="A0101">不良原因</td>
    <td width="100" bgcolor="#d6efb5" class="A0101">不良数量</td>
    <td  colspan="3" bgcolor="#d6efb5" class="A0101">不良图片</td>
  </tr>
 <?php
   $Cause_Str="";$i=1;$sumQty=0;$PictureName="";
   $check_Result = mysql_query("SELECT Id FROM $DataIn.qc_causetype WHERE Type=$TypeId AND Estate=1 LIMIT 1",$link_id);
   if($check_row = mysql_fetch_array($check_Result)){
       $cause_Result = mysql_query("SELECT Id,Cause,Picture FROM $DataIn.qc_causetype WHERE Type=$TypeId AND Estate=1",$link_id);
       }
   else{
       $cause_Result = mysql_query("SELECT Id,Cause,Picture FROM $DataIn.qc_causetype WHERE Type=1 AND Estate=1",$link_id);
   }

    while ( $cause_row = mysql_fetch_array($cause_Result)){
	$cId=$cause_row["Id"];
	$Cause=$cause_row["Cause"];

        $Bid=0;
        $sheet_Result=mysql_query("SELECT B.Id,B.Qty,B.Picture  FROM $DataIn.qc_badrecordsheet B 
                            WHERE B.Mid='$Id' AND B.CauseId='$cId'",$link_id);
         if( $sheet_row = mysql_fetch_array($sheet_Result)){
              $CauseQty=$sheet_row["Qty"];
              $sumQty+=$CauseQty;

              $Bid=$sheet_row["Id"];
              $Picture=$sheet_row["Picture"];
             if ($Picture==1){
                   $PictureName="Q".$Bid.".jpg";
	               $BadPicture="<a href='#' onClick='OpenOrLoad(\"\",\"\",\"$Bid\",\"qcbad\")' style='CURSOR: pointer;'>已上传</a>";
                  $delPicture="&nbsp;&nbsp;<a href='#' onClick='deleteImg(\"$PictureName\",\"$Bid\")' style='CURSOR: pointer;'>×</a>";
                   $IsDisplayed="none";
            }
           else{
               $checkPicSql=mysql_query("SELECT F.Picture  FROM $DataIn.qc_badrecordfile F  WHERE F.Mid='$Bid' ",$link_id);
               if($checkPicRow=mysql_fetch_array($checkPicSql)){
                       $BadPicture="<a href='#' onClick='OpenOrLoad(\"\",\"\",\"$Bid\",\"qcbad\")' style='CURSOR: pointer;'>已上传</a>";
                       $PictureName="";
                       $delPicture="";
                       $IsDisplayed="";
                         }
               else{
                   $BadPicture="未上传";
                   $PictureName="";
                   $delPicture="";
                   $IsDisplayed="";
                 }
             }
         }
         else{
              $PictureName="";
              $CauseQty=0;
              $BadPicture="";
              $delPicture="";
              $IsDisplayed="none";
         }

    $tempId=$Bid>0?$Id.",".$Bid:"";

    if ($thEstate==0) $showKeyFlag=""; else $showKeyFlag="showKeyboard(this,$Qty,$i)";
?>
  <tr align="center">
    <td width="80" height="25" bgcolor="#FFFFFF" class="A0101"><?php    echo $i?><input name="CauseId[]" type="hidden" id="CauseId<?php    echo $i?>" value="<?php    echo $cId?>"></td>
    <td width="400" bgcolor="#FFFFFF" align="left" class="A0101" onclick="CauseClick(this)"><?php    echo $Cause?></td>
    <td width="100" bgcolor="#FFFFFF" class="A0101">
    <input name='badQty[]' id='badQty<?php    echo $i?>' type='text' style='border:0;text-align:right;background:#EEE;' value="<?php    echo $CauseQty?>"  size='6'  onclick='<?php    echo $showKeyFlag?>' readonly >
    <td width="80" bgcolor="#FFFFFF" class="A0100" valign="middle"><?php    echo $BadPicture.$delPicture?><input name="PictureName[]" type="hidden" id="PictureName<?php    echo $i?>" value="<?php    echo $PictureName?>"></td>
    <td width="120" bgcolor="#FFFFFF" class="A0100" valign="middle">
        <input type="file" name="fileinput[]" id="fileinput<?php    echo $i?>" style="width:145px;height: 22px;display: <?php    echo $IsDisplayed?>;" >
    </td>
 <?php
if ($tempId==""){
?>
    <td width="100" bgcolor="#FFFFFF" class="A0101" valign="middle">&nbsp;</td></tr>
 <?php
}else{
?>
    <td width="100" bgcolor="#FFFFFF" class="A0101" valign="middle"><input type="button" name="upFiles[]" value="多图上传" onclick="ShowUpFilesDiv(<?php   echo $tempId ?>)"></td></tr>
  <?php
   }
      $i++;
   }
   $sheet_Result=mysql_query("SELECT B.Id,B.Qty,B.Reason,B.Picture FROM $DataIn.qc_badrecordsheet B 
                            WHERE B.Mid='$Id' AND B.CauseId='-1'",$link_id);
         if( $sheet_row = mysql_fetch_array($sheet_Result)){
              $otherbadQty=$sheet_row["Qty"];
              $otherCause=$sheet_row["Reason"];
              $sumQty+=$otherbadQty;

              $Bid=$sheet_row["Id"];
              $Picture=$sheet_row["Picture"];
             if ($Picture==1){
                       $PictureName="Q".$Bid.".jpg";
	                   $BadPicture="<a href='#' onClick='OpenOrLoad(\"\",\"\",\"$Bid\",\"qcbad\")' style='CURSOR: pointer;'>已上传</a>";
                       $IsDisplayed="none";
                        $delPicture="&nbsp;&nbsp;<a href='#' onClick='deleteImg(\"$PictureName\",\"$Bid\")' style='CURSOR: pointer;'>×</a>";
                  }
           else{
                $checkPicSql=mysql_query("SELECT F.Picture  FROM $DataIn.qc_badrecordfile F  WHERE F.Mid='$Bid' ",$link_id);
               if($checkPicRow=mysql_fetch_array($checkPicSql)){
                       $BadPicture="<a href='#' onClick='OpenOrLoad(\"\",\"\",\"$Bid\",\"qcbad\")' style='CURSOR: pointer;'>已上传</a>";
                       $PictureName="";
                       $IsDisplayed="";
                         $delPicture="";
               }
               else{
                   $BadPicture="未上传";
                   $PictureName="";
                   $IsDisplayed="";
                   $delPicture="";
               }
           }
         }
         else{
             $IsDisplayed="none";
             $otherbadQty=0;
            $PictureName="";
             $otherCause="";
            $delPicture="";
         }
   $Cause_Str.="<option value='-1'>其它原因</option>";
   $tempId=$Bid>0?$Id.",".$Bid:"";

   if ($thEstate==0) $showKeyFlag=""; else $showKeyFlag="showKeyboard(this,$Qty,\"\")";
  ?>
   <tr align="center">
    <td width="80" height="25" bgcolor="#FFFFFF" class="A0101"><?php    echo $i?></td>
    <td width="400" bgcolor="#FFFFFF" align="left" class="A0101">
      <!--  <select id="otCause" name="otCause" onchange="otherCauseClick(this)" multiple><?php   //=$Cause_Str?></select>
        <input name="otherCause" type="hidden" id="otherCause" value="">-->
        其它原因:<input name="otherCause" type="text" id="otherCause" value="<?php    echo $otherCause?>" style="width:320px;">

    </td>
    <td width="100" bgcolor="#FFFFFF" class="A0101">
        <input name='otherbadQty' id='otherbadQty' type='text'  style='border:0;text-align:right;background:#EEE;' value="<?php    echo $otherbadQty?>"  size='6' onclick='<?php    echo $showKeyFlag?>' readonly>
    </td>
      <td width="80" bgcolor="#FFFFFF" class="A0100" valign="middle"><?php    echo $BadPicture.$delPicture?><input name="otherPictureName" type="hidden" id="otherPictureName" value="<?php    echo $PictureName?>"></td>
    <td width="120" bgcolor="#FFFFFF" class="A0100"  valign="middle">
        <input type="file" name="otherfileinput" id="otherfileinput" style="width:145px;height: 22px;display:<?php    echo $IsDisplayed?>;" >
    </td>
     <?php
   if ($tempId==""){
     ?>
    <td width="100" bgcolor="#FFFFFF" class="A0101" valign="middle">&nbsp;</td>
     <?php     }else{  ?>
    <td width="100" bgcolor="#FFFFFF" class="A0101" valign="middle"><input type="button" name="upFiles[]" value="多图上传" onclick="ShowUpFilesDiv(<?php   echo $tempId ?>)"></td>
      <?php    } ?>

  </tr>

  <tr align="center">
    <td colspan="2" height="30" bgcolor="#d6efb5" class="A0111">合&nbsp;&nbsp;&nbsp;&nbsp;计</td>
    <td width="100" bgcolor="#d6efb5" class="A0101"> <input name='sumQty' id='sumQty' type='text' value="<?php    echo $sumQty?>" style='border:0;text-align:right;' size='6' readonly></input></td>
     <td colspan="2" bgcolor="#d6efb5" class="A0100">&nbsp;</td>
      <td  bgcolor="#d6efb5" class="A0101">&nbsp;</td>
  </tr>
</table>
</br>
<table height="61" colspan="7"  border="0" cellpadding="0" cellspacing="8" align="right" width=880" class="A0000" bgcolor="#d6efb5">
      <tr align="center">
         <td class="A0000" height="45" id="InfoBack" >&nbsp;</td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="button" name="Submit" value="取消" onclick="closeMaskDiv()"></td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="submit" name="Submit" value="提交" ></td>
  </tr>
</table>
