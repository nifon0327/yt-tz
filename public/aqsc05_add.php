<?php 
//步骤1 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增考核试题");//需处理
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
            <td width="100"  align="right" scope="col">试题类型</td>
            <td scope="col">
            <?
            include "../model/subselect/aqsc05type.php";
			?>
            </td>
          </tr>
          <tr>         
            <td align="right" valign="top">试题内容</td>
            <td><pre><textarea name="TestQuestions" style="width:380px" rows="6" id="TestQuestions"></textarea></pre></td>
          </tr>
          <tr>
            <td align="right" valign="top">试题答案</td>
            <td><input type="text" name="Answer" id="Answer" style="width:380px;" /></td>
          </tr>
          
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>