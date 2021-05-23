<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 车辆信息更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.cardata WHERE Id='$Id'",$link_id));
$cSign=$upData["cSign"];
$TypeId=$upData["TypeId"];
$BrandId=$upData["BrandId"];
$UserSign=$upData["UserSign"];
$UserSelectStr="UserSelected" . $UserSign;
$$UserSelectStr="selected";

$CarName   =$upData["CarName"];
$CarListNo =$upData["carListNo"];
$CarNo     =$upData["CarNo"];
$Maintainer=$upData["Maintainer"];
$InsuranceDate =$upData["InsuranceDate"];
$CheckTime  =$upData["CheckTime"];
$BuyStore	=$upData["BuyStore"];
$BuyDate    =$upData["BuyDate"];
$BuyAddress =$upData["BuyAddress"];
$BuyContact =$upData["BuyContact"];
$User		=$upData["User"];
$StoreNum	=$upData["StoreNum"];
$Enrollment	=$upData["Enrollment"];
$Insurance	=$upData["Insurance"];
$DriveLic	=$upData["DriveLic"];
$YueTong	=$upData["YueTong"];
$OilCard	=$upData["OilCard"];

$Enrollment=$Enrollment==""?"":"<div style='color:#F00;'>已上传</div>";
$Insurance=$Insurance==""?"":"<div style='color:#F00;'>已上传</div>";
$DriveLic=$DriveLic==""?"":"<div style='color:#F00;'>已上传</div>";
$YueTong=$YueTong==""?"":"<div style='color:#F00;'>已上传</div>";
$OilCard=$OilCard==""?"":"<div style='color:#F00;'>已上传</div>";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";

$tableWidth=850;
$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <!--<tr>
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
        <td><input name="Maintainer" type="text" id="Maintainer" value="<?php  echo $Maintainer ?>" style="width:380px" maxlength="10"></td>
         </tr>
         
		<tr>
        <td width="101" align="right">车牌号</td>
        <td><input name="CarNo" type="text" id="CarNo" value="<?php  echo $CarNo?>" style="width:380px" maxlength="10"></td>
       </tr>
         
		<tr>
        <td width="101" align="right">CarListNo</td>
        <td><input name="CarListNo" type="text" id="CarListNo" value="<?php  echo $CarListNo?>" style="width:380px" maxlength="10"></td>
       </tr>
		
         <tr>
        <td width="101" align="right">使用人</td>
        <td><input name="User" type="text" id="User" value="<?php  echo $User?>" style="width:380px" maxlength="10"></td>
         </tr>
		
          <tr>
	   <td width="101" align="right">购车时间</td>
        <td><input name="BuyDate" type="text" id="BuyDate" value="<?php  echo $BuyDate?>" onfocus="WdatePicker()" style="width:380px" maxlength="10"></td>
      </tr>
         
		 <tr>
        <td width="101" align="right">保险期限</td>
        <td><input name="InsuranceDate" type="text" id="InsuranceDate" value="<?php  echo $InsuranceDate?>" onfocus="WdatePicker()" style="width:380px" maxlength="10">        </td>
      </tr>
		 
		 <tr>
         <td width="101" align="right">购车4S店</td>
         <td><input name="BuyStore" type="text" id="BuyStore" value="<?php  echo $BuyStore?>" style="width:380px" maxlength="10"></td>
         </tr>
         
         <tr>
         <td width="101" align="right">4S店电话</td>
         <td><input name="StoreNum" type="text" id="StoreNum" value="<?php  echo $StoreNum?>" style="width:380px" maxlength="10"></td>
         </tr>
		 
		   <tr>
	    <td  colspan="2">
        <table width="700" border="0" cellspacing="5" name="picTable" id="picTable">         <tr>
		  <tr>
		  <td height="13" align="right" valign="top" scope="col">行驶证</td>
		  <td scope="col"><input name="DriveLic" type="file" id="DriveLic" value="<?php  echo $DriveLic?>" style="width:380px" DataType="Filter" Accept="jpg" Msg="文件格式不对,限jpg" Row="5" Cel="1"></td>
		  <td width="40"><?php  echo $DriveLic?></td>
	    </tr>
         
		<tr>
		  <td height="13" align="right" valign="top" scope="col">登记证书</td>
		  <td scope="col"><input name="Enrollment" type="file" id="Enrollment" value="<?php  echo $Enrollment?>" style="width:380px" DataType="Filter" Accept="jpg" Msg="文件格式不对,限jpg" Row="5" Cel="1"></td>
		  <td width="40"><?php  echo $Enrollment?></td>
	    </tr>
	    
	    <tr>
		  <td height="13" align="right" valign="top" scope="col">保险单</td>
		  <td scope="col"><input name="Insurance" type="file" id="Insurance" value="<?php  echo $Insurance?>" style="width:380px" DataType="Filter" Accept="jpg" Msg="文件格式不对,限jpg" Row="5" Cel="1"></td>
		  <td width="40"><?php  echo $Insurance?></td>
	    </tr>
         
         <tr>
		  <td height="13" align="right" valign="top" scope="col">粤通卡</td>
		  <td scope="col"><input name="YueTong" type="file" id="YueTong" value="<?php  echo $YueTong?>" style="width:380px" DataType="Filter" Accept="jpg" Msg="文件格式不对,限jpg" Row="5" Cel="1"></td>
		  <td width="40"><?php  echo $YueTong?></td>
	    </tr>
	    
	    <tr>
		  <td height="13" align="right" valign="top" scope="col">加油卡</td>
		  <td scope="col"><input name="OilCard" type="file" id="OilCard" value="<?php  echo $OilCard?>" style="width:380px" DataType="Filter" Accept="jpg" Msg="文件格式不对,限jpg" Row="5" Cel="1"></td>
		  <td width="40"><?php  echo $OilCard?></td>
	    </tr>
		</table>
		</td>
	</tr>    
	
		 <tr>
	    <td width="101" height="40" align="right">购车地址</td>
        <td><textarea name="BuyAddress" style="width:380px" rows="3" id="BuyAddress" value="<?php  echo $BuyAddress?>"></textarea></td>
       </tr>
       <tr>
       <td width="101" height="40" align="right">使用状态</td>
       <td><select name="UserSign" id="UserSign" style="width:380px" dataType="Require"  msg="未选择">
               <option value="1" <?php  echo $UserSelected1?>>公用</option>
               <option value="2" <?php  echo $UserSelected2?>>私用</option>
               <option value="0" <?php  echo $UserSelected0?>>隐藏(不在显示)</option>
             </select>
        </td>
        
     </table>
    </td>
  </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>