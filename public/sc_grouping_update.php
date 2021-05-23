<?php 
//电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新车间小组");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$GroupId=$Id;
$upData = mysql_fetch_array(mysql_query("SELECT GroupLeader,GroupName  FROM $DataIn.staffgroup 	WHERE GroupId='$GroupId'",$link_id));
//$GroupId=$upData["GroupId"];
$GroupLeader=$upData["GroupLeader"];
$GroupName=$upData["GroupName"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
		<tr>
            <td width="150" height="40" align="right" scope="col">小组名称</td>
            <td scope="col"><input name="GroupName" type="text" id="GroupName" size="60" value="<?php  echo $GroupName?>" maxlength="16" title="可输入2-16个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="16" Min="2" Msg="没有填写或字符不在2-16个字节内"></td>
		</tr>
		<tr>
		  <td height="40" align="right" scope="col">班长</td>
		  <td scope="col"><select name="GroupLeader"  id="GroupLeader" style="width:330px" >
            <?php 
			 //可选范围：组内成员，或未分组的员工
			 $Result1 = mysql_query("SELECT Number,Name FROM $DataPublic.staffmain WHERE Estate=1  AND (GroupId=0 OR GroupId='$GroupId')ORDER BY KqSign DESC,JobId",$link_id);
			 if($myRow1 = mysql_fetch_array($Result1)){
				do{
					if($GroupLeader==$myRow1["Number"]){
						echo" <option value='$myRow1[Number]' selected>$myRow1[Name]</option>";
						}
					else{
						echo" <option value='$myRow1[Number]'>$myRow1[Name]</option>";
						}
					}while($myRow1 = mysql_fetch_array($Result1));
				}
			 ?>
          </select></td>
	    </tr>
      </table>
      </td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>