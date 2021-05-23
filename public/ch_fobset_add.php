<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增FOB计算参数");//需处理
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
      <table width="760" border="0" align="center" cellspacing="5">
		<tr>
            <td width="150" height="30" align="right" scope="col">客户</td>
            <td scope="col"><select name="CompanyId" id="CompanyId" size="1" style="width: 338px;" dataType="Require"  msg="未选客户">
			<option value=''>请选择</option>
  			<?php  
			$result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE Estate=1 AND cSign=$Login_cSign AND CompanyId NOT IN(SELECT CompanyId FROM formula_fob) ORDER BY CompanyId",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
					} while ($myrow = mysql_fetch_array($result));
				}
			  ?>			  
      		</select></td>
		</tr>
		<tr>
		  <td height="30" align="right" scope="col">计算箱数</td>
		  <td scope="col"><input name="Boxs" type="text" id="Boxs" size="60" dataType="Number" Msg="未填写或格式不对"></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">中港运费单价</td>
		  <td scope="col"><input name="UnitYf" type="text" id="UnitYf" size="60" dataType="Currency" Msg="未填写或格式不对"></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">杂费单价</td>
		  <td height="43" scope="col"><input name="UnitZf" type="text" id="UnitZf" size="60" dataType="Currency" Msg="未填写或格式不对"></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">固定杂费</td>
		  <td height="43" scope="col"><input name="Gdzf" type="text" id="Gdzf" size="60" dataType="Currency" Msg="未填写或格式不对"></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">入仓费</td>
		  <td height="43" scope="col"><input name="Rcf" type="text" id="Rcf" size="60" dataType="Currency" Msg="未填写或格式不对"></td>
	    </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>