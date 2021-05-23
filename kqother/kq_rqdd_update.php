<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新日期对调资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$ALType="From=$From&chooseMonth=$chooseMonth";
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT D.Id,D.Number,D.GDate,D.XDate,M.Name FROM $DataIn.kqrqdd D,$DataPublic.staffmain M WHERE D.Id='$Id' and M.Number=D.Number",$link_id));
$Name=$upData["Name"];
$Number=$upData["Number"];
$GDate=$upData["GDate"];
$gMonth=substr($GDate,0,7);
$XDate=$upData["XDate"];
$xMonth=substr($XDate,0,7);
//检查
$checkG=mysql_query("SELECT Id FROM $DataIn.kqdata WHERE Number='$Number' and Month='$gMonth' LIMIT 1",$link_id);
if($Grow=mysql_fetch_array($checkG)){//如果有记录
	$Gsign=0;}
else{
	$Gsign=1;}
$checkX=mysql_query("SELECT Id FROM $DataIn.kqdata WHERE Number='$Number' and Month='$xMonth' LIMIT 1",$link_id);
if($Xrow=mysql_fetch_array($checkX)){//如果有记录
	$Xsign=0;}
else{
	$Xsign=1;}

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseMonth,$chooseMonth";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="700" border="0" cellspacing="2">
    	<tr>
            <td width="250" height="44" align="right">员工姓名</td>
            <td ><?php  echo $Name?></td>
    	</tr>
          <tr>
            <td height="49" align="right">原工作日</td>
            <td>
		  <?php 
		  if($Gsign==1){?>
		  <input name="GDate" type="text" id="GDate" value="<?php  echo $GDate?>" size="40" maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" Msg="未填写或格式不对" readonly>
		  <?php 
		  	}
		else{
			echo"$GDate (已生成统计数据，不允许修改)";
			}
		  ?>
		  </td>		  
          </tr>
          <tr>
            <td height="50" align="right">原休息日</td>
            <td height="50">
		  <?php 
		  if($Xsign==1){?>
		  <input name="XDate" type="text" id="XDate" value="<?php  echo $XDate?>" size="40" maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" Msg="未填写或格式不对" readonly></td>
		  <?php 
		  	}
		else{
			echo"$XDate (已生成统计数据，不允许修改)";
			}?>
		  </tr>
		</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>