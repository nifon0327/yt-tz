<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增QC不良原因");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table width="<?php  echo $tableWidth?>" height="138" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td align="right" class='A0010'>所属类别:</td>
    <td class='A0001'><select name="TypeId" id="TypeId" style="width:400px" dataType="Require"  msg="未选择分类">
      <option value='' selected>分类列表</option>
      <option value='1'>默认类别</option>;
      <?php 
				$result = mysql_query("SELECT TypeId,TypeName,Letter FROM $DataIn.stufftype WHERE Estate='1' AND mainType='1' order by Letter",$link_id);
				while ($StuffType = mysql_fetch_array($result)){
					$TypeId=$StuffType["TypeId"];
					$Letter=$StuffType["Letter"];
					$TypeName=$StuffType["TypeName"];
                                       if ($Type==$TypeId){
                                           echo"<option value='$TypeId' selected>$Letter-$TypeName</option>";  
                                       }else{
					   echo"<option value='$TypeId'>$Letter-$TypeName</option>";
					}
                                }
				?>
    </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </td>
  </tr>
  <tr>
    <td width="100" align="right" class='A0010'>不良原因:</td>
    <td class='A0001'>      <input name="Cause" type="text" id="Cause" value="" style="width:400px" dataType="Require" Msg="未填写"></td>
  </tr>
    <tr>
      <td class='A0010' align="right">图片上传: </td>
    <td class='A0001'><input name="Attached" type="file" id="Attached" style="width:400px" dataType="Filter" msg="文件格式不对" accept="jpg" Row="3" Cel="1"></td>
    </tr>
</table>
 <input name="Type" type="hidden" id="Type" value="<?php  echo $Type ?>"/>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>