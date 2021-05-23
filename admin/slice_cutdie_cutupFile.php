<?php 
/*
 * 鼠宝皮套专用 zhongxq-2012-08-17
 */
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 片材刀模切割图档上传");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_cutupFile";	
$toWebPage  =$funFrom."_imageload";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$stuffResult=mysql_query("SELECT S.StuffCname   FROM   $DataIn.stuffdata S  WHERE S.StuffId='$Id' ",$link_id);
if($stuffRow=mysql_fetch_array($stuffResult)){
	  $cName=$stuffRow["StuffCname"];
}
//步骤4：
$tableWidth=950;$tableMenuS=550;$spaceSide=15;
$SelectCode="($Id) $cName";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,ActionId,$ActionId,StuffId,$Id";

//步骤5：//需处理
 // if ($Picture!="") echo "<font style='color:#F00;font-weight:bold;'>提示：图档已上传!重新上传将替换原有文件。</font>";
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
   <table width="750" border="0" align="center" cellspacing="0" id="NoteTable">
	 <tr>
         <td  class="A1111"  colspan='2'  align="center"  height="30"> <span style="color:#FF6600; font-size:14px; font-weight:bold">图档上传</span>(包含格柏/纸格图档等)</td>  
		</tr>
		<?php
		$stuffResult=mysql_query("SELECT A.StuffId,A.CutId,C.cutSign,C.CutName,A.Picture   
		             FROM $DataIn.slice_cutdie   A
					 LEFT JOIN $DataIn.pt_cut_data C ON C.Id=A.CutId 
		             WHERE A.StuffId='$Id' ORDER BY StuffId",$link_id);
		while($stuffRow=mysql_fetch_array($stuffResult)){
			  $CutName=$stuffRow["CutName"];
			  $cutSign=$stuffRow["cutSign"];
			  $CutId=$stuffRow["CutId"];
			  include "../pt/subprogram/getCuttingIcon.php";
			  $Picture=$stuffRow["Picture"];
		      if ($Picture!="") $hintTitle =  "<font style='color:#F00;font-weight:bold;'>(图档已上传!)</font>";
		?>
		   <tr>
	       <td  class="A0111"   align="left" width='180' height="35"> <?php  echo $CutIconFile . $CutName.$hintTitle?> </td>  
		       <td class="A0101"  align="center"><input name="docFile[]" type="file"  size="100" ><input name="CutIdArray[]" type="hidden"  value="<?php echo $CutId?>"></td>    
		   </tr>
		<?
		  }
		?>
</table>
</td></tr></table>
<?php 
include "../model/subprogram/add_model_b.php";
?>
