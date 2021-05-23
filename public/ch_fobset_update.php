<?php 
//电信-zxq 2012-08-01

include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新FOB计算参数");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT F.Boxs,F.UnitYf,F.UnitZf,F.Gdzf,F.Rcf,C.Forshort FROM $DataIn.formula_fob F
					LEFT JOIN $DataIn.trade_object C ON F.CompanyId=C.CompanyId
					 WHERE 1 AND F.Id='$Id' LIMIT 1",$link_id));
$Boxs=$upData["Boxs"];
$UnitYf=$upData["UnitYf"];
$UnitZf=$upData["UnitZf"];
$Gdzf=$upData["Gdzf"];
$Rcf=$upData["Rcf"];
$Forshort=$upData["Forshort"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
<table width="760" border="0" align="center" cellspacing="5">
		<tr>
            <td width="150" height="30" align="right" scope="col">客户</td>
            <td scope="col"><?php  echo $Forshort?></td>
		</tr>
		<tr>
		  <td height="30" align="right" scope="col">计算箱数</td>
		  <td scope="col"><input name="Boxs" type="text" id="Boxs" value="<?php  echo $Boxs?>" size="60" dataType="Number" Msg="未填写或格式不对"></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">中港运费单价</td>
		  <td scope="col"><input name="UnitYf" type="text" id="UnitYf" value="<?php  echo $UnitYf?>" size="60" dataType="Currency" Msg="未填写或格式不对"></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">杂费单价</td>
		  <td height="43" scope="col"><input name="UnitZf" type="text" id="UnitZf" value="<?php  echo $UnitZf?>" size="60" dataType="Currency" Msg="未填写或格式不对"></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">固定杂费</td>
		  <td height="43" scope="col"><input name="Gdzf" type="text" id="Gdzf" value="<?php  echo $Gdzf?>" size="60" dataType="Currency" Msg="未填写或格式不对"></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">入仓费</td>
		  <td height="43" scope="col"><input name="Rcf" type="text" id="Rcf" value="<?php  echo $Rcf?>" size="60" dataType="Currency" Msg="未填写或格式不对"></td>
	    </tr>
      </table>	  </td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>