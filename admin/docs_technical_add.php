<?php   
//电信-zxq 2012-08-02
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增技术维护信息");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table width="<?php    echo $tableWidth?>" height="154" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="150" height="34" align="right" class='A0010'>信息类型: </td>
    <td class='A0001'>
	<select name="Type" id="Type" style="width:500px" dataType="Require"  msg="未选择">
			  <option value="">请选择</option>
			  <?php   
			  $CheckTypeSql=mysql_query("SELECT Id,Name FROM $DataPublic.doc1_type WHERE Estate=1 ORDER BY Id",$link_id);
			  if($CheckTypeRow=mysql_fetch_array($CheckTypeSql)){
			  	do{
                                    $Id=$CheckTypeRow["Id"];
                                    $Name=$CheckTypeRow["Name"];
                                    echo"<option value='$Id'>$Name</option>";
                                    }while($CheckTypeRow=mysql_fetch_array($CheckTypeSql));
				}
			  ?>
              </select>
    </select>
	</td>
  </tr>
  <tr>
    <td height="34" align="right" class='A0010'>信息标题: </td>
    <td class='A0001'><textarea name="Title" cols="68" rows="2" id="Title" datatype="Require" msg="未填写"></textarea></td>
  </tr>
    <tr>
		<td align="right" valign="top" class='A0010'>信息内容:</td>
	  <td class='A0001'><textarea name="Content" cols="68" rows="15" id="Content" datatype="Require" msg="未填写"></textarea></td>
    </tr>
</table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>