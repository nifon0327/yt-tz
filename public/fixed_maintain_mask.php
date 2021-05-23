<?php 
/*
已更新
电信-joseph
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$pArray=explode("|",$passvalue);
$FixedID=$pArray[0];
$maitainID=$pArray[1];
$DaysID=$pArray[2];
$SubName=$pArray[3];
$CycleDate=$pArray[4];
$Days=$pArray[5];
//$passvalue=urlencode($passvalue);

$count_Temp=mysql_query("SELECT S.Question,S.Solution FROM $DataPublic.fixed_m_main M
						 LEFT    JOIN $DataPublic.fixed_m_sheet S  ON S.MID=M.ID
						 WHERE M.CycleDate='$CycleDate' and M.DaysID='$DaysID'  and M.Days='$Days' AND M.FixedID='$FixedID'
						 AND S.maitainID='$maitainID'
						 ",$link_id); 
if (mysql_num_rows($count_Temp)>0){
	$Question=mysql_result($count_Temp,0,"Question");
	$Solution=mysql_result($count_Temp,0,"Solution");
}

?>
	
	<table width="450" border="0" cellspacing="0">
		<tr>
		  <td colspan="2" align="center" valign="top"><?php  echo $SubName."--".$CycleDate;  ?></td>
		</tr>
		<tr>
		  <td width="62" height="25" align="center">问题:</td>
	  	  <td><input name="Question" type="text" id="Question" value="<?php  echo $Question;  ?>"  style="width:400px" ></td>
	  </tr>
		<tr>
		  <td width="62" height="25" align="center">解决 <?php  echo "<br>"; ?>方法:</td>
	  	  
          <td valign="middle" scope="col"><textarea name="Solution" cols="53" rows="6" id="Solution" style="width:400"><?php  echo $Solution;  ?></textarea></td>
	  </tr>      

		<tr valign="bottom"><td height="27" colspan="2" align="right"><a href="javascript:CCSave('<?php  echo $passvalue?>')">确定</a> &nbsp;&nbsp; <a href="javascript:closeCCDiv()">取消</a></td></tr>
</table>
