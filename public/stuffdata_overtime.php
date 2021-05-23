<?php 
/*$DataIn.电信---yang 20120801
$DataPublic.staffmain
$DataIn.stufftype
$DataIn.stuffdata
$DataIn.bps
/二合一已更新
*/
//步骤1 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 延长配件使用期限");//需处理
$fromWebPage=$funFrom."_forbidden";		
$nowWebPage =$funFrom."_overtime";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT S.StuffId,S.StuffCname,T.OverTime 
										FROM $DataIn.stuffdata S 
										LEFT JOIN $DataIn.stuffovertime T ON T.StuffId=S.StuffId 
										WHERE S.Id='$Id' LIMIT 1",$link_id));
$StuffId=$upData["StuffId"];
$StuffCname=$upData["StuffCname"];
$OverTime=$upData["OverTime"];


//步骤4：
$tableWidth=850;$tableMenuS=500;
//$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,StuffId,$StuffId,ActionId,$ActionId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<input id="PackingUnit" name="PackingUnit" type="hidden" value="1" />
	<table width="800" border="0" align="center" cellspacing="5">
		<tr>
            <td width="103" align="right" scope="col" height='30px;'>配件名称:</td>
            <td scope="col"><?php  echo "$StuffId - $StuffCname";?></td>
          </tr>
           <tr>
            <td align="right" height='30px;'>延长期限:</td>
            <td><input name="OverTime" type="text" id="OverTime" value="<?php  echo $OverTime?>" size="10" dataType="Number"  msg="月份不正确，只能是数字">月</td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
