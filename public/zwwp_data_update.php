<?php 
//代码数据共享-EWEN 2012-11-25
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新总务用品资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT GoodsName,TypeId,Attached FROM $DataPublic.zwwp3_data WHERE Id='$Id' LIMIT 1",$link_id));
$upName=$upData["GoodsName"];
$TypeId=$upData["TypeId"];
$Attached=$upData["Attached"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="750" height="95" border="0" align="center" cellspacing="5">
            <tr>
           <td align="right">物品类别</td>
           <td>
            <?php 
			include "../model/subselect/zwwp_sType.php";
			?>
           </td>
         </tr>
          <tr>
            <td width="200" height="40" valign="middle" scope="col" align="right">物品名称</td>
            <td valign="middle" scope="col"><input name="GoodsName" type="text" id="GoodsName" title="必填项,2-30个字节的范围" value="<?php  echo $upName?>"  style="width: 380px;" maxlength="30" datatype="LimitB" max="30" min="2" msg="没有填写或超出2-30个字节的范围">
            </td><input name="OldGoodsName" type="hidden" id="OldGoodsName" value="<?php  echo $upName?>" >
          </tr>
          <tr>
            <td height="40" valign="middle" scope="col" align="right">物品图片</td>
            <td valign="middle" scope="col"><input name="Attached" type="file" id="Attached"  style="width: 380px;" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="1" Cel="1"></td>
          </tr>
		  <?php 
		  if($Attached==1){
		  	echo"<tr><td height='40' scope='col'>&nbsp;</td>
			  <td scope='col'><input name='oldAttached' type='checkbox' id='oldAttached' value='1'><LABEL for='oldAttached'> 删除已传图片</LABEL></td></tr>";
			}
		  ?>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>