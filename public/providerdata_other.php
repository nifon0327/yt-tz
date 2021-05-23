<?php 
//电信---yang 20120801
//代码共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 供应商管理的其它功能操作");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_other";	
$toWebPage  =$funFrom."_other_up";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/other_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Orderby,$Orderby,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
     <table width="737" height="246" border="0" align="center" cellspacing="0">
    <tr>
      <td height="46" class="A0100" align="center">供应商资料管理：其它更新功能</td>
    </tr>
    <tr>
      <td height="28">1、首字母重整：供应商简称的拼音首字母重新整理，用于排序及快速查找</td>
      </tr>
    <tr>
      <td height="24" class="A0100" align="right"><input name="Submit" type="button"  value="开始字母重整" onClick="CheckForm(1)"></td>
      </tr>
    <tr>
      <td height="30">2、联系供应商的采购变更：某供应商的配件原来由A全部负责，现改由B全部负责 (操作影响产品配件和供应商关系表和以后的需求单)</td>     
      </tr>
    <tr>
      <td height="33" class="A0100" align="right">供应商
		<select name="CompanyId" id="CompanyId" style="width: 150px;">
        <?php 
		$GYS_Result = mysql_query("SELECT A.CompanyId,B.Forshort,B.Letter 
			FROM $DatdIn.cg1_stocksheet A 
			LEFT JOIN $DataIn.trade_object B ON B.CompanyId=A.CompanyId 
			GROUP BY A.CompanyId ORDER BY B.Letter",$link_id);
		while ( $GYS_Myrow = mysql_fetch_array($GYS_Result)){
			$CompanyId=$GYS_Myrow["CompanyId"];
			$Forshort=$GYS_Myrow["Forshort"];
			$Letter=$GYS_Myrow["Letter"];
			$Forshort=$Letter.'-'.$Forshort;		
			echo "<option value='$CompanyId'>$CompanyId $Forshort</option>";
			}
		?>
        </select>
		改由
		<select name="BuyerId" id="BuyerId" style="width: 80px;">
		<?php 
		$staffSql ="SELECT B.Number,B.Name FROM $DataIn.usertable A
		LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number
		WHERE A.Estate=1 AND B.Estate=1 ORDER BY B.BranchId,B.JobId,B.Number";
		$staffResult = mysql_query($staffSql); 		
		while ($staffMyrow = mysql_fetch_array($staffResult)){
			$Number=$staffMyrow["Number"];
			$Name=$staffMyrow["Name"];
			echo "<option value='$Number'>$Name</option>";
			} 
		?>	
		</select>
	  	负责(<input name="HistoryP" type="checkbox" id="HistoryP" value="1"><LABEL for='HistoryP'>包括历史采购记录</LABEL>)
		&nbsp;&nbsp;
		<input type="button" name="Submit" value="开始采购变更" onClick="CheckForm(2)"></td>
      </tr>
    <tr>
      <td height="33">3、清除无用供应商：把没有使用到的供应商删除（没有在配件关系表，也没有在需求表中出现过的）</td>
      </tr>
    <tr>
      <td height="36" class="A0100" align="right"><input type="button" name="Submit" value="开始清除分类" onClick="CheckForm(3)"></td>
      </tr>
  </table>	  
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/other_model_b.php";
?>
<script language = "JavaScript"> 
function CheckForm(Action){
	if(Action==2){//需提醒
		var message=confirm("变更过程不可恢复，请慎重操作，真的要变更吗？");
   		if (message==true){
			document.form1.action="providerdata_other_up.php?Action="+Action;document.form1.submit();
			}
		else{
			return false;
			}
		}
	else{
		var message=confirm("确定进行操作吗？");
   		if (message==true){
			document.form1.action="providerdata_other_up.php?Action="+Action;document.form1.submit();}
		else{
			return false;
			}
		}	
	}
</script>