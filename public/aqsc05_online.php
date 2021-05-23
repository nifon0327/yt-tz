<?php 
include "../model/modelhead.php";
$ColsNumber=7;				
$tableMenuS=600;
$From=$From==""?"online":$From;
$funFrom="aqsc05";
$nowWebPage=$funFrom."_online";
ChangeWtitle("$SubCompany 在线测试");

?>
<style type="text/css"> 
body{
	font-family: tahoma; 
	font-size: 12px; 
	} 
input[type=checkbox] {
	vertical-align: middle; 
	padding: 2px; 
	}
input[type=radio]{
	vertical-align: middle; 
	padding: 2px; 
	} 
label {
vertical-align: middle; 
}
.Title{
	font-size:36px}
</style>
<form id="form1" name="form1" method="post" action="aqsc05_updated.php"><input type="hidden" name="ActionId" id="ActionId" value="<?php echo $ActionId?>"/><input type="hidden" name="funFrom" id="funFrom" value="<?php echo $funFrom?>"/>
<table width="1000">
<tr><td colspan="2" align="center" class="Title">安全生产知识在线考核</td></tr>
<tr><td colspan="2" bgcolor="#FFFFFF">
<?php
echo "<p>一、单选题(每题2.5分,共20题)</p>";

//步骤6：需处理数据记录处理
$i=1;
$mySql="SELECT A.Id,A.TestQuestions
FROM $DataPublic.aqsc05 A
WHERE A.TypeId='1' ORDER BY RAND() limit 20";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Id=$myRow["Id"];
		$TestQuestions="<pre>".$i."、".$myRow["TestQuestions"]."</pra>";
		$TestArray = explode("\n",$TestQuestions);
	
		echo $TestArray[0];
		echo "<input type='hidden' name='KtIdA[]' id='KtIdA$i' value='$Id'/>";
		echo "<input type='radio' name='radio$i' id='A1_$i' value='A' /><label for='A1_$i'>".$TestArray[1]."</label>";
		echo "<input type='radio' name='radio$i' id='A2_$i' value='B' /><label for='A2_$i'>".$TestArray[2]."</label>";
		echo "<input type='radio' name='radio$i' id='A3_$i' value='C' /><label for='A3_$i'>".$TestArray[3]."</label>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}

echo "<p>二、多选题(每题5分,共10题)</p>";
$i=1;
$mySql="SELECT A.Id,A.TestQuestions
FROM $DataPublic.aqsc05 A
WHERE A.TypeId='2' ORDER BY RAND() limit 10";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Id=$myRow["Id"];
		$TestQuestions="<pre>".$i."、".$myRow["TestQuestions"]."</pra>";
		$TestArray = explode("\n",$TestQuestions);
	
		echo $TestArray[0];
		echo "<input type='hidden' name='KtIdB[]' id='KtIdB$i' value='$Id'/>";
		echo "<input type='checkbox' name='checkbox".$i."[]' id='B1_$i' value='A' />".$TestArray[1];
		echo "<input type='checkbox' name='checkbox".$i."[]' id='B2_$i' value='B' />".$TestArray[2];
		echo "<input type='checkbox' name='checkbox".$i."[]' id='B3_$i' value='C' />".$TestArray[3];
		echo "<input type='checkbox' name='checkbox".$i."[]' id='B4_$i' value='D' />".$TestArray[4];
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
?>
		</td>
    </tr>
	<tr>
    	<td width="915" height="50" align="right">姓名：<input type="text" name="Name" id="Name" style="width:100px" /></td>
    	<td width="73" align="center"><input type="submit" name="submit" id="submit" value="提交" /></td>
	</tr>
</table>
<br />
</form>