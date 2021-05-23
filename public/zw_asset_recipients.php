<?php 
/*
$DataIn.zw1_assetrecord
$DataIn.zw1_assetuse
$DataIn.zw1_assettypes
$DataIn.zw1_brandtypes
$DataPublic.staffmain
二合一已更新
电信-joseph
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 物品交接");//需处理
$fromWebPage="zw_asset";		
$nowWebPage =$funFrom."_recipients";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upSql = "SELECT R.Model,R.Number,U.Remark,U.Date,U.User,T.Name AS Type,B.Name AS Brand,P.Name AS Operator 
FROM $DataIn.zw1_assetrecord R 
LEFT JOIN $DataIn.zw1_assetuse U ON U.AssetId=R.Id 
LEFT JOIN $DataIn.zw1_assettypes T ON T.Id=R.TypeId 
LEFT JOIN $DataIn.zw1_brandtypes B ON B.Id=R.BrandId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=U.User 
WHERE R.Id=$Id ORDER BY U.Date DESC,U.Id DESC LIMIT 1";
$upRows= mysql_fetch_array(mysql_query($upSql)); 
$Type=$upRows["Type"];
$Model=$upRows["Model"];
$Number=$upRows["Number"];
$Brand=$upRows["Brand"];
$Remark=$upRows["Remark"];
$Date=$upRows["Date"];
$User=$upRows["User"];
$Operator=$upRows["Operator"];
if($User!=$Login_P_Number){
	$SaveSTR="NO";
	$SaveInfo="<div class='redB'>(操作员非现领用人，不能交接)</div>";
	}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,User,$User,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,37";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    	<td colspan="2" class='A0011'>&nbsp;</td>
	</tr>
	<tr>
      <td height="30" class='A0010' align="right">物品基本资料</td>
      <td class='A0001'>&nbsp;          </td>
  </tr>
	<tr>
      <td height="25" class='A0010' align="right">类&nbsp;&nbsp;&nbsp;&nbsp;型：
      </td>
      <td class='A0001'>&nbsp;<?php  echo $Type?></td>
    </tr>
    <tr>
    	<td height="25" class='A0010' align="right">品&nbsp;&nbsp;&nbsp;&nbsp;牌：</td>
	    <td class='A0001'>&nbsp;<?php  echo $Brand?></td>
    </tr>
    <tr>
      <td height="25" class='A0010'><div align="right">型&nbsp;&nbsp;&nbsp;&nbsp;号：</div></td>
      <td class='A0001'>&nbsp;<?php  echo $Model?></td>
    </tr>
    <tr>
      <td height="25" class='A0010'><div align="right">机&nbsp;身&nbsp;ID：</div></td>
      <td class='A0001'>&nbsp;<?php  echo $Number?></td>
    </tr>
    <tr valign="bottom">
      <td height="30" align="right" class='A0010'>新的交接记录</td>
      <td height="30" class='A0001'>&nbsp;<?php  echo $SaveInfo?></td>
    </tr>
    <tr>
      <td height="25" class='A0010'><div align="right">现领用人：</div></td>
      <td class='A0001'>&nbsp;<?php  echo $Operator?></td>
    </tr>
    <tr>
      <td height="30" class='A0010'><div align="right">新领用人：</div></td>
      <td class='A0001'>
	  <select name="newUser" id="newUser" style="width:414px" dataType="Require"  msg="未填写">
	  <?php 
	  	//$P_Result = mysql_query("SELECT Number,Name FROM $DataPublic.staffmain WHERE Estate=1 AND JobId<15 ORDER BY BranchId,JobId,Number",$link_id);
		$P_Result = mysql_query("SELECT Number,Name FROM $DataPublic.staffmain WHERE Estate=1  ORDER BY BranchId,JobId,Number",$link_id);
		if($P_Row = mysql_fetch_array($P_Result)){
			echo"<option value=''>请选择</option>";	
			do{
				$Number=$P_Row["Number"];
				$Name=$P_Row["Name"];
				echo"<option value='$Number'>$Name</option>";				
				}while($P_Row = mysql_fetch_array($P_Result));
			}
		?>
      </select></td>
    </tr>
    <tr>
      <td height="25" class='A0010'><div align="right">交接日期：</div></td>
      <td class='A0001'><input name="useDate" type="text" id="useDate" value="<?php  echo date("Y-m-d")?>" size="76" maxlength="10" dataType="Date" format="ymd" msg="未选择或格式不对" onfocus="WdatePicker()" readonly></td>
    </tr>
    <tr>
      <td height="30" valign="top" class='A0010'><div align="right">领用说明：</div></td>
      <td class='A0001'><textarea name="useRemark" cols="49" rows="4" id="useRemark" dataType="Require"  msg="未填写"></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>