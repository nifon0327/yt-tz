<?php
	
	//步骤1
	include "../model/modelhead.php";
	//步骤2：
	ChangeWtitle("$SubCompany 技术维护信息");//需处理
	$fromWebPage=$funFrom."_read";		
	$nowWebPage =$funFrom."_update";	
	$toWebPage  =$funFrom."_updated";	
	$_SESSION["nowWebPage"]=$nowWebPage; 
	//步骤3：
	$upSql=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.dsrc_list WHERE Id='$Id'",$link_id));
	
	$cardNumber = $upSql["CardNumber"];
	$cardHolder = $upSql["CardHolder"];
	$carNum = $upSql["CarNum"];
	$carType = $upSql["Type"];
	$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";

	$tableWidth=850;$tableMenuS=500;
	include "../model/subprogram/add_model_t.php";
	
?>

<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
		<tr><td class="A0011">
        <table width="800" border="0" align="center" cellspacing="6">
			<tr>
				<td width="100" align="right" scope="col">卡号:</td>
				<td scope="col"><input name="cardNumber" type="text" id="cardNumber" size="20" dataType="Require"  value="<?php echo $cardNumber?>"></td>
			</tr>
			<tr>
				<td width="100" align="right" scope="col">车牌号码:</td>
				<td scope="col"><input name="carNum" type="text" id="carNum" size="20" dataType="Require"  msg="未填写" value="<?php echo $carNum?>"></td>
			</tr>
			<tr>
				<td scope="col" align="right">持卡人:</td>
				<td scope="col"><input name="cardHolder" type="text" id="cardHolder" size="20"  value="<?php echo $cardHolder?>"></td>
			</tr>
			<tr>
				<td scope="col" align="right">类型:</td>
				<td scope="col">
					<select name = "carType" id = "carType">
						<?php
							
							for($i=0;$i<2;$i++)
							{	
								$value = "<option value = '$i'";
								if($carType == "$i")
								{
									$value .= " selected>";
								}
								else
								{
									$value .= ">";
								}
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