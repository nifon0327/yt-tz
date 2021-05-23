<?php
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增车辆记录");//需处理
$nowWebPage =$funFrom."_add";
$toWebPage  =$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;
$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
   <!-- <tr>
     <td class='A0011'>
	  <table width="700" border="0" align="center" cellspacing="0">
	    <tr>
	      <td align="right">使用标识</td>
	      <td>
	        <?php
				include "../model/subselect/cSign.php";
		  	?>
	       </td>
        </tr>-->

      <tr>
        <td align="right">车辆类型</td>
        <td>
           <?php
          include "../model/subselect/CarType.php";
		  ?>
		  </td>
        </tr>
		 <tr>
        <td align="right">车辆品牌</td>
        <td>
           <?php
              include "../model/subselect/CarBrand.php";
		  ?>
		  </td>
        </tr>
		<tr>
        <td width="101" align="right">车主</td>
        <td><input name="Maintainer" type="text" id="Maintainer" style="width:380px" maxlength="10" dataType="Require"  msg="未填写"></td>
         </tr>

		<tr>
        <td width="101" align="right">车牌</td>
        <td><input name="CarNo" type="text" id="CarNo" style="width:380px" maxlength="10" dataType="Require"  msg="未填写"></td>
         </tr>

		<tr>
        <td width="101" align="right">CarListNo</td>
        <td><input name="CarListNo" type="text" id="CarListNo" style="width:380px" maxlength="10"></td>
         </tr>

         <tr>
        <td width="101" align="right">使用人</td>
        <td><input name="User" type="text" id="User" style="width:380px" maxlength="10"></td>
         </tr>

         <td align="right">购车时间</td>
         <td><input name="BuyDate" type="text" id="BuyDate" style="width:380px" onfocus="WdatePicker()" readonly>
         </td>
         </tr>

		<tr>
         <td width="101" align="right">保险期限</td>
         <td><input name="InsuranceDate" type="text" id="InsuranceDate" style="width:380px" onfocus="WdatePicker()" readonly></td>
         </tr>

		 <tr>
         <td width="101" align="right">物流公司名称</td>
         <td><input name="BuyStore" type="text" id="BuyStore" style="width:380px" maxlength="10"></td>
         </tr>

         <tr>
         <td width="101" align="right">车主电话</td>
         <td><input name="StoreNum" type="text" id="StoreNum" style="width:380px" maxlength="14"></td>
         </tr>

		 <tr>
		  <td height="13" align="right" valign="top" scope="col">行驶证</td>
		  <td scope="col"><input name="DriveLic" type="file" id="DriveLic" style="width:380px" DataType="Filter" Accept="jpg" Msg="文件格式不对,限jpg" Row="5" Cel="1"></td>
	    </tr>

		<tr>
		  <td height="13" align="right" valign="top" scope="col">登记证书</td>
		  <td scope="col"><input name="Enrollment" type="file" id="Enrollment" style="width:380px" DataType="Filter" Accept="jpg" Msg="文件格式不对,限jpg" Row="5" Cel="1"></td>
	    </tr>

	    <tr>
		  <td height="13" align="right" valign="top" scope="col">保险单</td>
		  <td scope="col"><input name="Insurance" type="file" id="Insurance" style="width:380px" DataType="Filter" Accept="jpg" Msg="文件格式不对,限jpg" Row="5" Cel="1"></td>
	    </tr>

         <tr>
		  <td height="13" align="right" valign="top" scope="col">粤通卡</td>
		  <td scope="col"><input name="YueTong" type="file" id="YueTong" style="width:380px" DataType="Filter" Accept="jpg" Msg="文件格式不对,限jpg" Row="5" Cel="1"></td>
	    </tr>

	    <tr>
		  <td height="13" align="right" valign="top" scope="col">加油卡</td>
		  <td scope="col"><input name="OilCard" type="file" id="OilCard" style="width:380px" DataType="Filter" Accept="jpg" Msg="文件格式不对,限jpg" Row="5" Cel="1"></td>
	    </tr>

		<tr>
        <td width="101" align="right">物流公司地址</td>
         <td><textarea name="BuyAddress" style="width:380px" rows="3" id="BuyAddress"></textarea>
		 </td>
         </tr>
     </table>
    </td>
  </tr>
</table>

<?php
//步骤5：
include "../model/subprogram/add_model_b.php";
?>