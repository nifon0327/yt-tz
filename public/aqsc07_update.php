<?php 
//2013-09-25 ewen
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 安全生产培训计划更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
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
$EstateSTR=$Estate==1?"":"checked";

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5"  id="NoteTable">
		<tr>
            <td width="100"  align="right" scope="col">培训日期</td>
            <td scope="col"><INPUT name="DefaultDate" class=textfield id="DefaultDate" style="width:380px;" onfocus="WdatePicker()" value="<?php echo $DefaultDate;?>" readonly></td>
		</tr>
        <tr>
            <td width="100"  align="right" scope="col">培训类型</td>
            <td scope="col"><?
            include "../model/subselect/aqsc07type.php";
			?></td>
		</tr>
        <tr>
            <td width="100"  align="right" scope="col">培训对象</td>
            <td scope="col"><?
            include "../model/subselect/aqsc07object.php";
			?></td>
		</tr>
        <tr>
            <td width="100"  align="right" scope="col">培训内容</td>
            <td scope="col"><input name="ItemName" type="text" id="ItemName" style="width:380px" dataType="Require" value="<?php echo $ItemName;?>" Msg="未填写"></td>
		</tr>
        <tr>
            <td width="100"  align="right" scope="col">培训工时</td>
            <td scope="col"><input name="ItemTime" type="text" id="ItemTime" style="width:380px" dataType="Double" value="<?php echo $ItemTime;?>" Msg="未填写或格式不对"></td>
		</tr>
        <tr>
            <td width="100"  align="right" scope="col">组织单位</td>
            <td scope="col"><?
            include "../model/subselect/aqsc07ou.php";
			?></td>
		</tr>
        <tr>
            <td width="100"  align="right" scope="col">授课方式</td>
            <td scope="col"><?
            include "../model/subselect/aqsc07teach.php";
			?></td>
		</tr>
        <tr>
            <td width="100"  align="right" scope="col">是否考核</td>
            <td scope="col"><select name="ExamId" id="ExamId" style="width:380px" dataType="Require" msg='未选择'>
            <option value='0' selected>否</option>
             <option value='1'>是</option>
             </select>
             </td>
		</tr>
        <tr>
            <td width="100"  align="right" scope="col">培训教程</td>
            <td scope="col">
            <select name="Tutorial" id="Tutorial" style="width:380px" dataType="Require" msg='未选择'>
            <option value='0' selected>无</option>
            <?php
			$checkResult = mysql_query("SELECT A.Id,A.Caption FROM $DataPublic.aqsc04 A WHERE A.Estate=1 ORDER BY A.Id",$link_id);
			if($checkRow = mysql_fetch_array($checkResult)){
				$i=1;
				do{
					if($Tutorial==$checkRow["Id"]){
						echo"<option value='$checkRow[Id]' selected>$i $checkRow[Caption]</option>";
						}
					else{
						echo"<option value='$checkRow[Id]'>$i $checkRow[Caption]</option>";
						}
					$i++;
					}while($checkRow = mysql_fetch_array($checkResult));
				}
            ?>
            </select>
            </td>
		</tr>
        <tr>
          <td  align="right" scope="col">状态</td>
          <td scope="col"><input name="Estate" type="checkbox" id="Estate" value="1" checked="<?php echo $EstateSTR;?>"/>已执行</td>
        </tr>
        <tr>
          <td  align="right" scope="col">讲师</td>
          <td scope="col"><input name="Lecturer" type="text" id="Lecturer" style="width:380px" maxlength="10"  value="<?php echo $Lecturer;?>" /></td>
        </tr>
        <tr>
          <td  align="right" scope="col">核实人</td>
          <td scope="col"><input name="Reviewer" type="text" id="Reviewer" style="width:380px" maxlength="10"  value="<?php echo $Reviewer;?>" /></td>
        </tr>
        <tr>
          <td  align="right" scope="col">现场图片(pdf)</td>
          <td scope="col"><input name="Img" type="file" id="Img" style="width:380px" dataType="Filter" Msg="非法的文件格式" Accept="pdf" Row="11" Cel="1"></td>
        </tr>
        <tr>
          <td  align="right" scope="col">现场视频(mp4)</td>
          <td scope="col"><input name="Movie" type="file" id="Movie" style="width:380px" dataType="Filter" Msg="非法的文件格式" Accept="mp4" Row="12" Cel="1"></td>
        </tr>
        <tr>
          <td  align="right" scope="col">签到名单(pdf)</td>
          <td scope="col"><input name="List" type="file" id="List" style="width:380px" dataType="Filter" Msg="非法的文件格式" Accept="pdf" Row="13" Cel="1"></td>
        </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>