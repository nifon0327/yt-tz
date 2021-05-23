<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 在线测试-答卷");
$checkRow=mysql_fetch_array(mysql_query("SELECT A.ExamDate,A.Results,B.Name FROM $DataPublic.aqsc09 A LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number WHERE A.Id='$Id'",$link_id));
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
<table width="1000">
<tr><td align="center" class="Title">安全生产知识在线考核-答卷</td></tr>
<tr><td>
考核时间：<?php echo $checkRow["ExamDate"];?>
<p>员工姓名：<?php echo $checkRow["Name"];?></p>
考核成绩：<?php echo $checkRow["Results"];?><br />
</td></tr>
<tr><td bgcolor="#FFFFFF">
<?php
echo "<p>一、单选题(每题2.5分,共20题)</p>";
//步骤6：需处理数据记录处理
$i=1;
$mySql="
SELECT A.Aqsc05Id,A.theAnswer,A.DefaultAnswer,A.Grade,B.TestQuestions,B.Answer FROM 
$DataPublic.aqsc09_sheet A
LEFT JOIN $DataPublic.aqsc05 B ON B.Id=A.Aqsc05Id WHERE A.ExamId='$Id' AND A.TypeId='1' ORDER BY A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Aqsc05Id=$myRow["Aqsc05Id"];
		$theAnswer=$myRow["theAnswer"];
		$DefaultAnswer=$myRow["DefaultAnswer"];
		$Grade=$myRow["Grade"];
		$TestQuestions=$myRow["TestQuestions"];
		$TestQuestions="<pre>".$i."、".$TestQuestions."</pra>";
		$TestArray = explode("\n",$TestQuestions);
		echo $TestArray[0];
		echo "&nbsp;&nbsp;&nbsp;&nbsp;".$TestArray[1];
		echo "&nbsp;&nbsp;&nbsp;&nbsp;".$TestArray[2];
		echo "&nbsp;&nbsp;&nbsp;&nbsp;".$TestArray[3]."<br>";
		if($Grade==0){
			echo "<span class='redB'>&nbsp;&nbsp;&nbsp;&nbsp;答题错误:".$theAnswer.",正确答案是:".$DefaultAnswer."</span></br>";
			}
		else{
			echo "<span class='greenB'>&nbsp;&nbsp;&nbsp;&nbsp;答题正确:".$theAnswer."</span></br>";
			}	
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}

echo "<p>二、多选题(每题5分,共10题)</p>";
$i=1;
$mySql="SELECT A.Aqsc05Id,A.theAnswer,A.DefaultAnswer,A.Grade,B.TestQuestions,B.Answer FROM 
$DataPublic.aqsc09_sheet A
LEFT JOIN $DataPublic.aqsc05 B ON B.Id=A.Aqsc05Id WHERE A.ExamId='$Id' AND A.TypeId='2' ORDER BY A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Aqsc05Id=$myRow["Aqsc05Id"];
		$theAnswer=$myRow["theAnswer"];
		$DefaultAnswer=$myRow["DefaultAnswer"];
		$Grade=$myRow["Grade"];
		$TestQuestions=$myRow["TestQuestions"];
		$TestQuestions="<pre>".$i."、".$TestQuestions."</pra>";
		$TestArray = explode("\n",$TestQuestions);
		echo $TestArray[0];
		echo "&nbsp;&nbsp;&nbsp;&nbsp;".$TestArray[1];
		echo "&nbsp;&nbsp;&nbsp;&nbsp;".$TestArray[2];
		echo "&nbsp;&nbsp;&nbsp;&nbsp;".$TestArray[3]."<br>";
		if($Grade==0){
			echo "<span class='redB'>&nbsp;&nbsp;&nbsp;&nbsp;答题错误:".$theAnswer.",正确答案是:".$DefaultAnswer."</span></br>";
			}
		else{
			echo "<span class='greenB'>&nbsp;&nbsp;&nbsp;&nbsp;答题正确:".$theAnswer."</span></br>";
			}	
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
?>
		</td>
    </tr>
</table>