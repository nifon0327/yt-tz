<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw11_jjsheet
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新奖金资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT S.ItemName,S.Number,S.Month,S.MonthS,S.MonthE,S.Divisor,S.Rate,S.Amount,M.Name FROM $DataIn.cw11_jjsheet S,$DataPublic.staffmain M WHERE S.Id='$Id' AND M.Number=S.Number",$link_id));
$ItemName=$upData["ItemName"];
$theYear=substr($ItemName,0,4);
$ItemName=str_replace($theYear,"",$ItemName);
switch($ItemName){
	case "端午节奖金":$ItemName1="selected";break;
	case "中秋节奖金":$ItemName2="selected";break;
	case "年终奖金":$ItemName3="selected";break;
	}
//拆分
$Number=$upData["Number"];
$Month=$upData["Month"];
$MonthS=$upData["MonthS"];
$MonthE=$upData["MonthE"];
$Divisor=$upData["Divisor"];
$Rate=$upData["Rate"]*100/100;
$Amount=$upData["Amount"];
$Name=$upData["Name"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="0">
      <tr>
        <td width="70" height="32" align="right">员 工&nbsp;ID</td>
        <td width="576"><?php  echo $Number?></td>
      </tr>
      <tr>
        <td height="32" align="right">员工姓名</td>
        <td><?php  echo $Name?></td>
      </tr>
      <tr>
        <td height="32" align="right">请款月份</td>
        <td><input name="Month" type="text" id="Month" value="<?php  echo $Month?>" size="48" maxlength="7" dataType="Require" msg="未填写请款月份"></td>
      </tr>
      <tr>
        <td height="32" align="right">计算公式</td>
        <td align="center" valign="top">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" align="right" valign="top"><table width="100%" height="32" border="0" cellspacing="0">
          <tr>
            <td align="center" class="A1111">
				<input name="theYear" type="text" id="theYear" size="4" value="<?php  echo $theYear?>">
				&nbsp;
				<select name="ItemName" id="ItemName" title="奖金项目" dataType="Require"  msg="参数错误">
				  <option value="端午节奖金" <?php  echo $ItemName1?>>端午节奖金</option>
				  <option value="中秋节奖金" <?php  echo $ItemName2?>>中秋节奖金</option>
				  <option value="年终奖金" <?php  echo $ItemName3?>>年终奖金</option>
				</select> =(
				<input name="MonthS" type="text" id="MonthS" title="计薪起始月" size="10" value=<?php  echo $MonthS?>> 
				~
				<input name="MonthE" type="text" id="MonthE" title="计薪终止月" size="10" value=<?php  echo $MonthE?>> 
				) /
				<input name="Divisor" type="text" id="Divisor" title="除数(月份)" size="10" value=<?php  echo $Divisor?>> 
				*
				<input name="Rate" type="text" id="Rate" title="比率" size="10" value=<?php  echo $Rate?>> 
				%
              </td>
            </tr>
        </table></td>
        </tr>
    </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>