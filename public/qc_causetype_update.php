<?php 
//电信-EWEN
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新QC不良原因");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理

$upData =mysql_fetch_array(mysql_query("SELECT Type,Cause,Picture,Estate FROM $DataIn.qc_causetype Q WHERE Id=$Id order by Id",$link_id));
$Type=$upData["Type"];
$Cause=$upData["Cause"];
$Estate=$upData["Estate"];
$oldAttached=$upData["Picture"];

if ($oldAttached==""){
     $Picture="";
    }
else{
     $File=anmaIn($oldAttached,$SinkOrder,$motherSTR);
     $Dir="download/qccause/";
     $Dir=anmaIn($Dir,$SinkOrder,$motherSTR);			
     $Picture="<a href='#' onClick='OpenOrLoad(\"$Dir\",\"$File\")' style='CURSOR: pointer;'>查看</a>";
}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,OldAttached,$Attached";
//步骤5：//需处理
?>
<table width="<?php  echo $tableWidth?>" height="180" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td align="right" class='A0010'>所属类别: </td>
    <td class='A0001'><select name="TypeId" id="TypeId" style="width:400px" dataType="Require"  msg="未选择分类">
      <?php 
			 echo"<option value='1'>默认类别</option>";
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
    <td width="100" align="right" class='A0010'>不良原因: </td>
    <td class='A0001'>      <input name="Title" type="text" id="Title" style="width:400px" value="<?php  echo $Cause?>" size="80" dataType="Require" Msg="未填写"></td>
  </tr>
    <tr>
		<td class='A0010' align="right">有效状态: </td>
	    <td class='A0001'><select name="Estate" id="Estate" style="width:400px" dataType="Require"  msg="未选择有效状态">
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
    <td class='A0001'><input name="Attached" type="file" id="Attached" style="width:400px" dataType="Filter" msg="文件格式不对" accept="jpg" Row="3" Cel="1"/>
      <?php  echo $Picture?>
    </td>
    </tr>
</table>
   <input name="oldAttached" type="hidden" id="oldAttached" value="<?php  echo $oldAttached?>"/>
   <input name="Type" type="hidden" id="Type" value="<?php  echo $Type ?>"/>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>