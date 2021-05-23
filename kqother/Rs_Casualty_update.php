<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新意外险缴费记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$MyRow = mysql_fetch_array(mysql_query("SELECT S.Id,S.Month,S.mAmount,S.cAmount,S.Estate,M.Name,S.TypeId 
									   FROM $DataIn.sbpaysheet S 
									   LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
									   WHERE S.Id=$Id",$link_id));
$Id=$MyRow["Id"];
$Month=$MyRow["Month"];
$mAmount=$MyRow["mAmount"];
$cAmount=$MyRow["cAmount"];
$Name=$MyRow["Name"];
$Estate=$MyRow["Estate"];
if($Estate==0){	
	$SaveSTR="NO";
	}
  switch($MyRow["TypeId"]){
                    case 1: $TypeName="社保";break;
                    case 2: $TypeName="公积金";break;
                    case 3: $TypeName="意外险";break;
}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,chooseMonth,$chooseMonth,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="600" height="120" border="0" align="center" cellspacing="5">
		<tr>
			<td width="146" height="18" align="right" valign="middle" scope="col">缴费类型</td>
			<td width="435" valign="middle" scope="col"><?php  echo $TypeName?></td>
		</tr>
		<tr>
			<td height="18" align="right" valign="middle" scope="col">员工姓名</td>
			<td  valign="middle" scope="col"><?php  echo $Name?></td>
		</tr>
		<tr>
		  <td height="18" align="right" valign="middle" scope="col">缴费月份</td>
		  <td valign="middle" scope="col"><?php  echo $Month?></td>
	    </tr>
		<tr>
		  <td height="18" align="right" valign="middle" scope="col">个人缴费金额</td>
		  <td valign="middle" scope="col"><?php  echo $mAmount?></td>
	    </tr>
		<tr>
		  <td height="18" align="right" valign="middle" scope="col">公司缴费金额</td>
		  <td valign="middle" scope="col"><input name="cAmount" type="text" id="cAmount" value="<?php  echo $cAmount?>" dataType="Currency" msg="金额格式不对">
	      </td>
	    </tr>
		<?php 
		if($Estate!=1){
			echo"<tr><td height='18' colspan='2' align='center' scope='col' ><div class='redB'>(资料非未处理状态，需审核或结付退回方可更新)</div></td></tr>";
			}
		?>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>