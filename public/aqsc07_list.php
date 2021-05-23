<?php 
//2013-09-25 ewen
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 员工受训名单登记");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_list";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.aqsc07 WHERE Id='$Id' ORDER BY Id LIMIT 1",$link_id));
$DefaultDate=$upData["DefaultDate"];
$ItemName=$upData["ItemName"];
$ItemTime=$upData["ItemTime"];

$Tutorial=$upData["Tutorial"];
$Lecturer=$upData["Lecturer"];
$Img=$upData["Img"];
$Movie=$upData["Movie"];
$List=$upData["List"];
$Reviewer=$upData["Reviewer"];

$TeachId=$upData["TeachId"];
$ExamId=$upData["ExamId"];
$OUId=$upData["OUId"];
$ObjectId=$upData["ObjectId"];
$TypeId=$upData["TypeId"];

$Estate=$upData["Estate"];
$EstateSTR=$Estate==1?"已执行":"未执行";

$ObjectFrom=$ObjectId;
$OUFrom=$OUId;
$TeachFrom=$TeachId;
$TypeFrom=$TypeId;

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,$ActionId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5"  id="NoteTable">
		<tr>
            <td width="100"  align="right" scope="col">培训日期</td>
            <td scope="col"><?php echo $DefaultDate;?></td>
		</tr>
        <tr>
            <td width="100"  align="right" scope="col">培训类型</td>
            <td scope="col"><?
           include "../model/subselect/aqsc07type.php";
			echo $TypeName;
			?></td>
		</tr>
        <tr>
            <td width="100"  align="right" scope="col">培训对象</td>
            <td scope="col"><?
            include "../model/subselect/aqsc07object.php";
			echo $ObjectName;
			?></td>
		</tr>
        <tr>
            <td width="100"  align="right" scope="col">培训内容</td>
            <td scope="col"><?php echo $ItemName;?></td>
		</tr>
        <tr>
            <td width="100"  align="right" scope="col">培训工时</td>
            <td scope="col"><?php echo $ItemTime;?></td>
		</tr>
        <tr>
            <td width="100"  align="right" scope="col">组织单位</td>
            <td scope="col"><?
            include "../model/subselect/aqsc07ou.php";
			echo $OUName;
			?></td>
		</tr>
        <tr>
            <td width="100"  align="right" scope="col">授课方式</td>
            <td scope="col"><?
            include "../model/subselect/aqsc07teach.php";
			echo $TeachName;
			?></td>
		</tr>
        <tr>
          <td  align="right" scope="col">状态</td>
          <td scope="col"><?php echo $EstateSTR;?></td>
        </tr>
        <tr>
          <td height="32"  align="right" scope="col">&nbsp;</td>
          <td valign="bottom" scope="col">选定如下受训员工</td>
        </tr>
        <tr>
          <td  align="right" scope="col">考核结果</td>
          <td scope="col"><select name="Exam" id="Exam" style="width:380px">
	<option value="" selected="selected">无</option>
	<option value="优">优</option>
    <option value="良">良</option>
    <option value="及格">及格</option>
    <option value="不及格">不及格</option>
    
    </select></td>
        </tr>
        <tr>
          <td  align="right" scope="col">部门</td>
          <td scope="col"><select name="BranchId" id="BranchId" style="width:380px">
	<option value="">全部</option>
	<?php 
	$outResult=mysql_query("SELECT Id,Name FROM $DataPublic.branchdata 
						    WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);
	if($outRow = mysql_fetch_array($outResult)) {
		do{
			$outId=$outRow["Id"];
			$outName=$outRow["Name"];
			echo "<option value='$outId'>$outName</option>";
			}while ($outRow = mysql_fetch_array($outResult));
		}
	?>
    </select></td>
        </tr>
        <tr>
          <td  align="right" scope="col">职位</td>
          <td scope="col"><select name="JobId" id="JobId" style="width:380px">
	<option value="">全部</option>
	<?php 
	$outResult=mysql_query("SELECT Id,Name FROM $DataPublic.jobdata 
						     WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);
	if($outRow = mysql_fetch_array($outResult)) {
		do{
			$outId=$outRow["Id"];
			$outName=$outRow["Name"];
			echo "<option value='$outId'>$outName</option>";
			}while ($outRow = mysql_fetch_array($outResult));
		}
	?>
    </select></td>
        </tr>
        <tr>
          <td  align="right" scope="col" valign="top">员工</td>
          <td scope="col"><select name="ListId[]" size="18" multiple id="ListId" style="width: 180px;" onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,3)" dataType="autoList" readonly></select></td>
        </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>