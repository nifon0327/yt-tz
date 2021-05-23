<?php 
//电信-ZX  2012-08-01
//步骤1 $DataPublic.sbdata/$DataPublic.staffmain 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新社保资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT S.Number,S.sMonth,S.eMonth,M.Name,S.Type,S.Note 
FROM $DataPublic.sbdata S,$DataPublic.staffmain M WHERE 
M.Number=S.Number AND S.Id=$Id ORDER BY S.Id LIMIT 1",$link_id));
$Number=$upData["Number"];
$Type=$upData["Type"];
$Name=$upData["Name"];
$sMonth=$upData["sMonth"];
$Note=$upData["Note"];

$eMonth=$upData["eMonth"]==0?"":$upData["eMonth"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="700" border="0" align="center" cellspacing="5">
        <tr>
            <td width="112" height="16" scope="col">基本信息</td>
            <td scope="col"></td>
          </tr>
          <tr>
            <td align="right" scope="col">姓&nbsp;&nbsp;&nbsp;&nbsp;名</td>
            <td scope="col"><?php  echo $Name?></td>
          </tr>
          <tr>
            <td align="right" scope="col">社保分类</td>
            <td scope="col"><select name="Type" id="Type" style="width:200px">
              <?php 
			$tResult=mysql_query("SELECT Id,Name FROM $DataPublic.rs_sbtype WHERE Id<4  ORDER BY Id",$link_id);
			if($tRow = mysql_fetch_array($tResult)) {
				do{
					$tId=$tRow["Id"];
					$tName=$tRow["Name"];
					if($tId==$Type){
						echo "<option value='$tId' selected>$tName</option>";
						}
					else{
						echo "<option value='$tId'>$tName</option>";
						}
					}while($tRow = mysql_fetch_array($tResult));
				}
			?>
            </select></td>
          </tr>
          <tr>
            <td align="right" scope="col">社保起始月份</td>
            <td scope="col"><input name="sMonth" type="text" id="sMonth" value="<?php  echo $sMonth?>" size="33" maxlength="7" dataType="Month" msg="月份格式不对"></td>
          </tr>
          <tr>
            <td align="right" valign="top">社保结束月份</td>
            <td>
			  <input name="eMonth" type="text" id="eMonth" value="<?php  echo $eMonth?>" size="33" maxlength="7" require="false" dataType="Month" msg="月份格式不对"><td>
          </tr>
		<tr>
	  <td align="right"  scope="col">备注</td>
	  <td width="447" valign="middle" scope="col">
	  <input name="Note" type="text" id="Note" size="60" dataType="LimitB" value="<?php  echo $Note?>"  msg="必须在2-50个字节之内" title="必填项,2-50个字节内">
	  </tr>          
   </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>