<?php 
//电信-zxq 2012-08-01
/*
$DataIn.usertable
$DataPublic.staffmain
$DataPublic.freightdata
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增快递列表");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
        <table width="800" border="0" align="center" cellspacing="6">
		<tr>
            <td width="150" scope="col" align="right">寄/到&nbsp;件</td>
            <td scope="col">
			<input name="Type" type="radio" value="0" checked>寄件<input type="radio" name="Type" value="1">到件
			</td>
		</tr>
		<tr>
        <td scope="col" align="right">所属公司</td>
        <td scope="col">
          <?php 
          include "../model/subselect/cSign.php";
		  ?>
		</td></tr>
		<tr>
		  <td height="18" scope="col" align="right">经&nbsp;手&nbsp;人</td>
		  <td scope="col">
		  <select name="HandledBy" id="HandledBy" style="width: 380px;" dataType="Require"  msg="未选择">
		  <?php
			$result = mysql_query("SELECT M.Number,M.Name From $DataPublic.staffmain M  
			WHERE  M.Estate='1' AND M.KqSign>=1 AND M.OffStaffSign=0 ORDER BY M.Name",$link_id);
			if($myrow = mysql_fetch_array($result)){
				echo "<option value=''>请选择</option>";
				do{
					$Number=$myrow["Number"];
					$Name=$myrow["Name"];
					echo "<option value='$Number'>$Name</option>";
					}while ($myrow = mysql_fetch_array($result));
				} 
			?>
			</select>
		   </td>
		  </tr>
		<tr>
		  <td scope="col"><div align="right">快递公司</div></td>
		  <td scope="col">
		  <select name="CompanyId" id="CompanyId" style="width: 380px;"  dataType="Require"  msg="未填写">
		  <?php 
			$fResult = mysql_query("SELECT CompanyId,Forshort FROM $DataPublic.freightdata WHERE Estate='1' AND MType=1 ORDER BY Id",$link_id);
			if($fRow = mysql_fetch_array($fResult)){
			echo"<option value=''>请选择</option>";
				do{
			 		echo"<option value='$fRow[CompanyId]'>$fRow[Forshort]</option>";
					} while($fRow = mysql_fetch_array($fResult));
				}
			?></select></td>
		</tr>
		<tr>
		  <td height="24" scope="col" align="right">寄件日期</td>
		  <td scope="col"><input name="SendDate" type="text" id="SendDate" style="width: 380px;"  dataType="Require"  msg="未选择" onfocus="WdatePicker()" readonly></td>
		  </tr>
		<tr>
		  <td height="-1" scope="col" align="right">提单号码</td>
		  <td scope="col"><input name="ExpressNO" type="text" id="ExpressNO" style="width: 380px;"  onclick="AddNumber(this)" dataType="Require"  msg="未填写"></td>
		  </tr>
		<tr>
		  <td height="3" scope="col" align="right">件&nbsp;&nbsp;&nbsp;&nbsp;数</td>
		  <td scope="col"><input name="BoxQty" type="text" id="BoxQty" style="width: 380px;" dataType="Number" msg="未填写或格式不对"></td>
		  </tr>
		<tr>
		  <td height="1" scope="col" align="right">重&nbsp;&nbsp;&nbsp;&nbsp;量</td>
		  <td scope="col"><input name="Weight" type="text" id="Weight" style="width: 380px;" dataType="Currency"  msg="未填写或格式不对"></td>
		</tr>
		<tr>
		  <td height="-9" scope="col" align="right">运&nbsp;&nbsp;&nbsp;&nbsp;费</td>
		  <td scope="col"><input name="Amount" type="text" id="Amount" style="width: 380px;" dataType="Currency"  msg="未填写或格式不对"></td>
		</tr>
		<tr>
		  <td height="13" scope="col"><div align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注</div></td>
		  <td scope="col"><textarea name="Remark" cols="51" rows="3" id="Remark"></textarea></td>
		</tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script language="javascript">
function AddNumber(e){
var num=Math.random(); 
var Action=1; 
	BackData=window.showModalDialog("ch_express_s1.php?r="+num+"&tSearchPage=ch_express&fSearchPage=ch_express&SearchNum=2&Action="+Action,"BackData","dialogHeight =650px;dialogWidth=1080px;center=yes;scroll=yes");
	
		if(!BackData){ 
		if(document.getElementById('SafariReturnValue')){
			var SafariReturnValue=document.getElementById('SafariReturnValue');
			BackData=SafariReturnValue.value;
			SafariReturnValue.value="";
			}
		   }	
     if(BackData){
	    var CL=BackData.split("^^");
		e.value=CL[0];
		document.getElementById('BoxQty').value=CL[1];
		document.getElementById('Weight').value=CL[2];
		document.getElementById('Remark').value=CL[3];
		}

}
</script>