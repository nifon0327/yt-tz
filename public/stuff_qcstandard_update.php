<?php 
//已更新$DataIn.电信---yang 20120801
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新配件QC检验标准图");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT TypeId,Title,Picture,IsType,Estate FROM $DataIn.stuffqcstandard WHERE Id=$Id order by Id",$link_id));
$theTypeId=$upData["TypeId"];
$Title=$upData["Title"];
$Estate=$upData["Estate"];
$Attached=$upData["Picture"];
$IsType=$upData["IsType"];
$IsType=$IsType==1?"checked":"";

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,OldAttached,$Attached";
//步骤5：//需处理
?>
<table width="<?php  echo $tableWidth?>" height="180" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td align="right" class='A0010'>所属类别: </td>
    <td class='A0001'><select name="TypeId" id="TypeId" style="width:260px" dataType="Require"  msg="未选择分类">
      <?php 
			    $result = mysql_query("SELECT TypeId,TypeName,Letter FROM $DataIn.stufftype WHERE Estate='1' order by Letter",$link_id);
				if($StuffType = mysql_fetch_array($result)){
				do{
					$TypeId=$StuffType["TypeId"];
					$TypeName=$StuffType["TypeName"];
					$Letter=$StuffType["Letter"];
					if($TypeId==$theTypeId){
						echo"<option value='$TypeId' selected>$Letter-$TypeName</option>";}
					else{
						echo"<option value='$TypeId'>$Letter-$TypeName</option>";}
					}while ($StuffType = mysql_fetch_array($result));
					}
				?>
    </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input name="IsType" type="checkbox" id="IsType" <?php  echo $IsType?>>设为【类】QC标准图
    </td>
  </tr>

  <tr>
    <td width="100" align="right" class='A0010'>标准说明: </td>
    <td class='A0001'>      <input name="Title" type="text" id="Title" value="<?php  echo $Title?>" size="80" dataType="Require" Msg="未填写"></td>
  </tr>
    <tr>
		<td class='A0010' align="right">有效状态: </td>
	    <td class='A0001'><select name="Estate" id="Estate" style="width:433px" dataType="Require"  msg="未选择有效状态">
        <?php 
		  if ($Estate==1){
			  echo "<option value='1' selected>有效</option>";
			  echo "<option value='0'>无效</option>";
		  }else{
			  echo "<option value='1'>有效</option>";
			  echo "<option value='0'  selected>无效</option>"; 
			  }
		?>
         </select>
        </td>
    </tr>
    <tr>
      <td class='A0010' align="right">标准图存档: </td>
    <td class='A0001'><input name="Attached" type="file" id="Attached" size="67" dataType="Filter" msg="文件格式不对" accept="jpg" Row="3" Cel="1"></td>
    </tr>
     <input name="oldAttached" type="hidden" id="oldAttached" value="<?php  echo $Attached?>">
    <tr>
         <td align="right"  class='A0010'><font color='red'>注意：</font></td>
         <td class='A0001'>"新设为【类】QC标准图"将清除已与配件存在的对应连接关系。</td>
     </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>