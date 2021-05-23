<?php
	
	//步骤1 $DataIn.trade_object 二合一已更新
	include "../model/modelhead.php";
	//步骤2：
	ChangeWtitle("$SubCompany 添加扣款资料");//需处理
	$nowWebPage =$funFrom."_add";	
	$toWebPage  =$funFrom."_save";	
	$_SESSION["nowWebPage"]=$nowWebPage;
	$tableWidth=850;$tableMenuS=500;
	$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,CompanyId,$CompanyId,Estate,$Estate";
	include "../model/subprogram/add_model_t.php";
?>

	<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
		<tr><td class="A0011">
        <table width="800" border="0" align="center" cellspacing="6">
			<tr>
				<td width="100" align="right" scope="col">卡号:</td>
				<td scope="col"><input name="cardNumber" type="text" id="cardNumber" size="20" dataType="Require"  msg="未填写"></td>
			</tr>
			<tr>
				<td width="100" align="right" scope="col">车牌号码:</td>
				<td scope="col"><input name="carNum" type="text" id="carNum" size="20" dataType="Require"  msg="未填写"></td>
			</tr>
			<tr>
				<td scope="col" align="right">持卡人:</td>
				<td scope="col"><input name="cardHolder" type="text" id="cardHolder" size="20"></td>
			</tr>
			<tr>
				<td scope="col" align="right">类型:</td>
				<td scope="col">
					<select name = "carType" id = "carType">
						<?php
							
							for($i=0;$i<2;$i++)
							{	
								$value = "<option value = '$i'>";
								switch($i)
								{
									case "0":
									{
										$value .= "内部车辆";
									}
									break;
									case "1":
									{
										$value .= "外部车辆";
									}
									break;
								}
								
								$value.="</option>";
								echo $value;
							}
						?>
					</select>
				</td>
			</tr>

		</table>
		</td></tr>
	</table>
<?php   
	//步骤5：
	include "../model/subprogram/add_model_b.php";
?>