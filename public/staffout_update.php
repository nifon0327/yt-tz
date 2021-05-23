<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新员工离职资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("
SELECT P.Name,P.Number,D.outDate,D.Type,D.Reason 
FROM $DataPublic.dimissiondata D,$DataPublic.staffmain P  WHERE D.Id='$Id' AND P.Number=D.Number LIMIT 1",$link_id));
$Name=$upData["Name"];
$Number=$upData["Number"];
$outDate=$upData["outDate"];
$Type=$upData["Type"];
$Reason=$upData["Reason"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="700" border="0" align="center" cellspacing="5">
        <tr>
            <td width="112" height="16" scope="col">离职信息</td>
            <td scope="col"> </td>
          </tr>
          <tr>
            <td align="right" scope="col">姓&nbsp;&nbsp;&nbsp;&nbsp;名</td>
            <td scope="col"><?php  echo $Name;?></td>
          </tr>
          <tr>
            <td align="right">离职日期</td>
            <td><input name="outDate" type="text" id="outDate" value="<?php  echo $outDate?>" size="73" maxlength="10" onfocus="WdatePicker()" DataType="Date" format="ymd" Msg="日期不对或没选日期" readonly></td>
          </tr>
          <tr>
            <td align="right" valign="top">离职类别</td>
            <td><select name="Type" id="Type" style="width: 397px;">
            <?php 
			$dResult=mysql_query("SELECT Id,Name FROM $DataPublic.dimissiontype WHERE Estate='1' order by Id",$link_id);
			if($dRow = mysql_fetch_array($dResult)) {
				do{
					$dId=$dRow["Id"];
					$dName=$dRow["Name"];
					if($dId==$Type){
						echo "<option value='$dId' selected>$dName</option>";
						}
					else{
						echo "<option value='$dId'>$dName</option>";
						}
					}while ($dRow = mysql_fetch_array($dResult));
				}
			?>			  
            </select></td>
          </tr>
          <tr>
            <td align="right" valign="top">离职原因</td>
            <td><textarea name="Reason" cols="47" rows="8" id="Reason"><?php  echo $Reason?></textarea></td>
          </tr>
          <tr>
            <td height="46" valign="bottom">复职处理</td>
            <td valign="bottom">（注：工龄需要延续才使用复职，否则当新员工加入职资料）</td>
          </tr>
          <tr>
            <td align="right" valign="top">复职日期</td>
            <td><input name="BackDate" type="text" id="BackDate" size="73" maxlength="10" onfocus="WdatePicker()" datatype="Date" Require="false" format="ymd" msg="日期不对或没选日期" readonly></td>
          </tr>
          <tr>
            <td valign="top">&nbsp;</td>
            <td><input name="delM" type="checkbox" id="delM" value="1"><LABEL for="delM">工龄计算扣除离职月份（即离职期间不计入工龄）</LABEL>
            </td>
          </tr>
          <tr>
            <td valign="top"><div align="right"></div></td>
            <td>&nbsp;</td>
          </tr>
   </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>