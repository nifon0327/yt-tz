<?php 
//2013-09-25 ewen
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 员工受训记录更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT A.ItemId,A.Exam,B.Name FROM $DataPublic.aqsc08 A
									   LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number
									   WHERE A.Id='$Id' ORDER BY A.Id LIMIT 1",$link_id));
$ItemId=$upData["ItemId"];
$Name=$upData["Name"];
$Exam=$upData["Exam"];
switch($Exam){
	case "优":$Exam1="selected='selected'";
	break;
	case "良":$Exam2="selected='selected'";
	break;
	case "及格":$Exam3="selected='selected'";
	break;
	case "不及格":$Exam4="selected='selected'";
	break;
	default:
	$Exam0="selected='selected'";
	break;
	}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5"  id="NoteTable">
		<tr>
            <td width="100"  align="right" scope="col">受训员工</td>
            <td scope="col"><?php echo $Name;?></td>
		</tr>
        <tr>
            <td width="100"  align="right" scope="col">考核评级</td>
            <td scope="col">
            <select name="Exam" id="Exam" style="width:380px" dataType="Require" msg='未选择'>
            <option value="" <?php echo $Exam0;?>>无</option>
            <option value="优" <?php echo $Exam1;?>>优</option>
            <option value="良" <?php echo $Exam2;?>>良</option>
            <option value="及格" <?php echo $Exam3;?>>及格</option>
            <option value="不及格" <?php echo $Exam4;?>>不及格</option>
            </select>
            </td>
		</tr>
        <tr>
            <td width="100"  align="right" scope="col">培训教程</td>
            <td scope="col">
            <select name="ItemId" id="ItemId" style="width:380px" dataType="Require" msg='未选择'>
            <?php
			$checkResult = mysql_query("SELECT A.Id,A.ItemName FROM $DataPublic.aqsc07 A WHERE A.Estate=1 ORDER BY A.Id",$link_id);
			if($checkRow = mysql_fetch_array($checkResult)){
				$i=1;
				do{
					if($ItemId==$checkRow["Id"]){
						echo"<option value='$checkRow[Id]' selected>$i $checkRow[ItemName]</option>";
						}
					else{
						echo"<option value='$checkRow[Id]'>$i $checkRow[ItemName]</option>";
						}
					$i++;
					}while($checkRow = mysql_fetch_array($checkResult));
				}
            ?>
            </select>
            </td>
		</tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>