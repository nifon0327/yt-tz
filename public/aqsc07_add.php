<?php 
//2013-09-25 ewen
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增安全生产培训计划");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">
		<tr>
            <td width="100"  align="right" scope="col">培训日期</td>
            <td scope="col"><INPUT name="DefaultDate" class=textfield id="DefaultDate" style="width:380px;" onfocus="WdatePicker()" value="<?php echo date("Y-m-d");?>" readonly></td>
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
            <td scope="col"><input name="ItemName" type="text" id="ItemName" style="width:380px" dataType="Require" Msg="未填写"></td>
		</tr>
        <tr>
            <td width="100"  align="right" scope="col">培训工时</td>
            <td scope="col"><input name="ItemTime" type="text" id="ItemTime" style="width:380px" dataType="Double" Msg="未填写或格式不对"></td>
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
					echo"<option value='$checkRow[Id]'>$i $checkRow[Caption]</option>";
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