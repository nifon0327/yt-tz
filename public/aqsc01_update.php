<?php 
//2013-10-14 ewen
include "../model/modelhead.php";
include "../model/livesearch/modellivesearch.php";//用于在文本框输入文字显示与之相关的内容
//步骤2：
ChangeWtitle("$SubCompany 更新安全管理制度汇编记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT A.Name AS AddName,IFNULL(B.Name,'无') AS Name FROM $DataPublic.aqsc01 A 
									   LEFT JOIN $DataPublic.aqsc01 B ON B.Id=A.PreItem
									   WHERE A.Id='$Id'",$link_id));
$AddName=$upData["AddName"];
$Name=$upData["Name"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5" id="NoteTable">
    <tr>
            <td width="100" align="right" valign="top">上级分类</td>
            <td><input name="Name" type="text" id="Name" title="必填项,2-100个字节的范围"  style="width: 380px;" maxlength="30" datatype="LimitB" max="100" min="2" msg="没有填写或超出字节的范围" onkeyup="showResult(this.value,'Name','aqsc01','1','Estate=1')" onblur="LoseFocus()" onchange="clearData()" autocomplete="off" value="<?php  echo $Name?>"></td>
          </tr>
          <tr>
            <td align="right" valign="top">分类名称</td>
            <td><input name="AddName" type="text" id="AddName" Value='<?php  echo $AddName?>' style="width:380px" maxlength="50" title="必选项,在50个汉字内." dataType="LimitB" min="1" max="100" msg="没有填写或超出许可范围"></td>
          </tr>
          <tr>
            <td align="right" valign="top">&nbsp;</td>
            <td>如果没有上级分类，即新增1级分类，则上级分类填写&quot;无&quot;</td>
          </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>