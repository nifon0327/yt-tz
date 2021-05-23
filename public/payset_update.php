<?php 
/*电信---yang 20120801
$DataPublic.staffmain
$DataPublic.paybase
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新员工预设资金");//需处理
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Id,$Id";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
//员工资料表
$Result = mysql_query("SELECT M.Name,M.Currency,M.KqSign,ifnull(B.Jj,0) AS Jj,ifnull(B.Jtbz,0) AS Jtbz,IFNULL(B.Sbkk,0) AS Sbkk,IFNULL(B.Taxkk,0) AS Taxkk,IFNULL(B.Dx,0) AS Dx   
FROM $DataPublic.staffmain M
LEFT JOIN $DataPublic.paybase B ON B.Number=M.Number WHERE M.Number='$Id' LIMIT 1",$link_id);
if($myrow = mysql_fetch_array($Result)) {
	$Name=$myrow["Name"];
	$Jj=$myrow["Jj"];
	$Jtbz=$myrow["Jtbz"];
	$Sbkk=$myrow["Sbkk"];
	$Taxkk=$myrow["Taxkk"];
	$Dx=$myrow["Dx"];
	
	$Currency=$myrow["Currency"];
	
	$disabledSTR=$Currency==1?" disabled ":"";
	
	if ($Currency==1){
		   $KqSign=$myrow["KqSign"];
		   $B_Result = mysql_fetch_array(mysql_query("SELECT Name,Dx,Shbz,Zsbz,Jtbz FROM $DataIn.sys1_baseset WHERE KqSign='$KqSign' LIMIT 1",$link_id));
		   $Jtbz=$B_Result["Jtbz"]==""?0.00:$B_Result["Jtbz"];
		   $Dx=$B_Result["Dx"]==""?0.00:$B_Result["Dx"];
		   //社保读取
		$sbResult = mysql_fetch_array(mysql_query("SELECT T.mAmount FROM $DataPublic.sbdata S LEFT JOIN $DataPublic.rs_sbtype  T ON T.Id=S.Type WHERE 1 and S.Number='$Id' ORDER BY S.Id DESC LIMIT 1",$link_id));
		 $Sbkk=$sbResult["mAmount"]==""?0.00:$sbResult["mAmount"];
	}
}
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
        <table width="604" border="0" align="center" cellspacing="5">
		<tr>
            <td width="128" align="right" scope="col">姓&nbsp;&nbsp;&nbsp;&nbsp;名</td>
            <td width="457" scope="col"><?php  echo $Name?></td>
           </tr>
            <tr>
            <td width="128" align="right" scope="col">结付货币</td>
            <td  scope="col">
			<select name="Currency" id="Currency" style="width:150px" dataType="Require" msg="未选择" onchange="setDisabled(this)">
			<option value="">请选择</option>
			<?php 
			$Currency_Result = mysql_query("SELECT Id,Symbol,Name FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
			if($Currency_Row = mysql_fetch_array($Currency_Result)){
				do{
					$Currency_Id=$Currency_Row["Id"];
					$Currency_Name=$Currency_Row["Name"];
					$Symbol=$Currency_Row["Symbol"];
					$Currency=$Currency==""?$Currency_Id:$Currency;
					if ($Currency==$Currency_Id){
					       echo"<option value='$Currency_Id' selected>$Symbol-$Currency_Name</option>";
					}
					else{
						  echo"<option value='$Currency_Id'>$Symbol-$Currency_Name</option>";
					}
					}while ($Currency_Row = mysql_fetch_array($Currency_Result));
				}
			?>
            </select> 
			</td>
          </tr>
          
          <tr>
            <td align="right">预设奖金</td>
            <td><input name="Jj" type="text" id="Jj" value="<?php  echo $Jj?>" style="width:150px"  dataType="Currency" Msg="未填写或格式不对"></td>
          </tr>
          
           <tr>
            <td align="right">预设底薪</td>
            <td><input name="Dx" type="text" id="Dx" value="<?php  echo $Dx?>" style="width:150px"  dataType="Currency" Msg="未填写或格式不对"  <?php  echo $disabledSTR?>></td>
          </tr>
          
          <tr>
            <td align="right">交通补助</td>
            <td><input name="Jtbz" type="text" id="Jtbz" value="<?php  echo $Jtbz?>" style="width:150px"  dataType="Currency" Msg="未填写或格式不对"  <?php  echo $disabledSTR?>></td>
          </tr>
          
           <tr>
            <td align="right">社保代扣</td>
            <td><input name="Sbkk" type="text" id="Sbkk" value="<?php  echo $Sbkk?>" style="width:150px"  dataType="Currency" Msg="未填写或格式不对" <?php  echo $disabledSTR?>></td>
          </tr>
          
          <tr>
            <td align="right">个税代扣</td>
            <td><input name="Taxkk" type="text" id="Taxkk" value="<?php  echo $Taxkk?>" style="width:150px"  dataType="Currency" Msg="未填写或格式不对" <?php  echo $disabledSTR?>></td>
          </tr>
          
          <tr>
            <td>&nbsp;</td>
            <td>(其它资料到相应的功能项目修改)</td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>

<script language="JavaScript" type="text/JavaScript">
 function setDisabled(el)
 {
     var disabledSign=false;
	  if (el.value==1){
		   disabledSign=true;
	  }
	   document.getElementById("Dx").disabled=disabledSign;
	  document.getElementById("Jtbz").disabled=disabledSign;
	  document.getElementById("Sbkk").disabled=disabledSign;
	  document.getElementById("Taxkk").disabled=disabledSign;
}
</script>