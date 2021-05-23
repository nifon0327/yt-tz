<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 货运公司资料更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT A.MType,A.CompanyId,A.Forshort,A.Currency,B.Company,B.Tel,B.Fax,B.Area,B.ZIP,B.Address,B.Bank,B.Remark
FROM $DataPublic.freightdata A 
LEFT JOIN $DataIn.companyinfo B ON B.CompanyId=A.CompanyId
WHERE A.Id='$Id'  LIMIT 1",$link_id); 
if($upRow = mysql_fetch_array($upResult)){
	$CompanyId=$upRow["CompanyId"];
	$Forshort=$upRow["Forshort"];
	$Currency=$upRow["Currency"];
	$Company=$upRow["Company"];
	$Tel=$upRow["Tel"];
	$Fax=$upRow["Fax"];
	$Area=$upRow["Area"];
	$ZIP=$upRow["ZIP"];
	$Address=$upRow["Address"];
	$Bank=$upRow["Bank"];
	$Remark=$upRow["Remark"];
	$MType=$upRow["MType"];
	$lmanResult = mysql_query("SELECT * FROM $DataIn.linkmandata WHERE CompanyId=$CompanyId and Defaults=0 and Type='$Type' ORDER BY CompanyId DESC LIMIT 1",$link_id);
	$lmanRow = mysql_fetch_array($lmanResult);
	$LinkId=$lmanRow["Id"];
	$TempSex="SexSTR".strval($lmanRow["Sex"]);$$TempSex="selected";
}


$CheckChargeRow1 = mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.forwardcharge WHERE Type=1 AND CompanyId = '$CompanyId'",$link_id));
$CFSCharge1 = $CheckChargeRow1["CFSCharge"] ==""?0.00:$CheckChargeRow1["CFSCharge"];
$THCCharge1 = $CheckChargeRow1["THCCharge"] ==""?0.00:$CheckChargeRow1["THCCharge"];
$WJCharge1  = $CheckChargeRow1["WJCharge"]  ==""?0.00:$CheckChargeRow1["WJCharge"];
$SXCharge1  = $CheckChargeRow1["SXCharge"]  ==""?0.00:$CheckChargeRow1["SXCharge"];
$ENSCharge1 = $CheckChargeRow1["ENSCharge"] ==""?0.00:$CheckChargeRow1["ENSCharge"];
$BXCharge1  = $CheckChargeRow1["BXCharge"]  ==""?0.00:$CheckChargeRow1["BXCharge"];
$GQCharge1  = $CheckChargeRow1["GQCharge"]  ==""?0.00:$CheckChargeRow1["GQCharge"];
$DFCharge1  = $CheckChargeRow1["DFCharge"]  ==""?0.00:$CheckChargeRow1["DFCharge"];
$TDCharge1  = $CheckChargeRow1["TDCharge"]  ==""?0.00:$CheckChargeRow1["TDCharge"];


$CheckChargeRow2 = mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.forwardcharge WHERE Type=2 AND CompanyId = '$CompanyId'",$link_id));
$CFSCharge2 = $CheckChargeRow2["CFSCharge"] ==""?0.00:$CheckChargeRow2["CFSCharge"];
$THCCharge2 = $CheckChargeRow2["THCCharge"] ==""?0.00:$CheckChargeRow2["THCCharge"];
$WJCharge2  = $CheckChargeRow2["WJCharge"]  ==""?0.00:$CheckChargeRow2["WJCharge"];
$SXCharge2  = $CheckChargeRow2["SXCharge"]  ==""?0.00:$CheckChargeRow2["SXCharge"];
$ENSCharge2 = $CheckChargeRow2["ENSCharge"] ==""?0.00:$CheckChargeRow2["ENSCharge"];
$BXCharge2  = $CheckChargeRow2["BXCharge"]  ==""?0.00:$CheckChargeRow2["BXCharge"];
$GQCharge2  = $CheckChargeRow2["GQCharge"]  ==""?0.00:$CheckChargeRow2["GQCharge"];
$DFCharge2  = $CheckChargeRow2["DFCharge"]  ==""?0.00:$CheckChargeRow2["DFCharge"];
$TDCharge2  = $CheckChargeRow2["TDCharge"]  ==""?0.00:$CheckChargeRow2["TDCharge"];

			
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Orderby,$Orderby,Pagination,$Pagination,Page,$Page,LinkId,$LinkId,CompanyId,$CompanyId,Type,$Type";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
     <table width="700" border="0" align="center" cellspacing="5">
  <tr>
    <td width="150" align="right" scope="col">结付货币</td>
    <td colspan="3" scope="col">
    <?php 
	include "../model/subselect/Currency.php";
	?>
  </td>
  </tr>
    <tr>
  <td align="right">公司分类</td>
  <td colspan="3">             
    <select name="MType" id="MType" style="width:380px" onchange="ShowForwardCharge()" dataType="Require" msg="未选择">
	<option value="" selected>请选择</option>
	<?php 
	$result = mysql_query("SELECT * FROM $DataPublic.freightdatatype WHERE Estate=1 order by Id",$link_id);
	if($myrow = mysql_fetch_array($result)){
		do{
		   $thisId = $myrow["Id"];
		   $thisName = $myrow["Name"];
		   if($MType ==$thisId ){
			   echo"<option value='$thisId' selected>$thisName</option>";
		   }else{
			   echo"<option value='$thisId'>$thisName</option>";
		   }
			
		} while ($myrow = mysql_fetch_array($result));
	}
	?>		
 	</select>
   </td>
  </tr>  
  <tr>
    <td scope="col" align="right">国家地区</td>
    <td colspan="3" scope="col">
<input name="Area" type="text" id="Area" value="<?php  echo $Area?>" style="width:380px;" dataType="LimitB" max="50" min="2" msg="必须在2-50个字节之内">    </td>
  </tr>
  <tr>
    <td align="right" >公司名称</td>
    <td colspan="3"><input name="Company" type="text" id="Company" value="<?php  echo $Company?>" style="width:380px;" dataType="LimitB" max="50" min="2" msg="必须在2-50个字节之内"></td>
  </tr>
  <tr>
    <td align="right">公司简称</td>
    <td colspan="3"><input name="Forshort" type="text" id="Forshort" value="<?php  echo $Forshort?>" style="width:380px;" dataType="LimitB" max="20" min="2" msg="必须在2-20个字节之内"></td>
  </tr>
         
  <tr>
    <td align="right">公司电话</td>
    <td colspan="3"><input name="Tel" type="text" id="Tel" value="<?php  echo $Tel?>" style="width:380px;"></td>
  </tr>
  <tr>
    <td align="right">公司传真</td>
    <td colspan="3"><input name="Fax" type="text" id="Fax" value="<?php  echo $Fax?>" style="width:380px;" require="false"></td>
  </tr>
  <tr>
    <td align="right">网&nbsp;&nbsp;&nbsp;&nbsp;址</td>
    <td colspan="3"><input name="Website" type="text" id="Website" value="<?php  echo $Website?>" style="width:380px;" require="false" dataType="Url" msg="非法的Url"></td>
  </tr>
  <tr>
    <td align="right">邮政编码</td>
    <td colspan="3"><input name="ZIP" type="text" id="ZIP" value="<?php  echo $ZIP?>" style="width:380px;" require="false" dataType="Custom" regexp="^[1-9]\d{5}$" msg="邮政编码不存在"></td>
  </tr>
  <tr>
    <td align="right">通信地址</td>
    <td colspan="3"><input name="Address" type="text" id="Address" value="<?php  echo $Address?>" style="width:380px;" ataType="Limit" max="50" msg="必须在50个字之内"></td>
  </tr>
  <tr>
    <td align="right" valign="top">银行帐户</td>
    <td colspan="3"><textarea name="Bank" style="width:380px;" id="Bank"><?php  echo $Bank?></textarea></td>
  </tr>
  <tr>
    <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
    <td colspan="3"><textarea name="Remark" style="width:380px;" id="Remark"><?php  echo $Remark?></textarea></td>
  </tr>
  <tr>
    <td colspan="4"><div align="center">默认联系人信息</div></td>
	</tr>
          <tr>
            <td align="right">联 系 人</td> <!-- dataType="Limit" max="20" min="2" msg="必须在2-20个字之内"-->
            <td><input name="Linkman" type="text" id="Linkman" style="width:150px;" value="<?php  echo $lmanRow["Name"]?>" ></td>
            <td width="62" align="right">性&nbsp;&nbsp;&nbsp;&nbsp;别</td>
            <td>
              <select name="Sex" id="Sex" style="width:150px">
                <option value="0" <?php  echo $SexSTR0?>>女</option>
                <option value="1" <?php  echo $SexSTR1?>>男</option>
              </select></td>
          </tr>
          <tr>
            <td align="right">职&nbsp;&nbsp;&nbsp;&nbsp;务</td>
            <td width="156"><input name="Headship" type="text" id="Headship" style="width:150px;" value="<?php  echo $lmanRow["Headship"]?>" maxlength="20"></td>
            <td align="right">昵&nbsp;&nbsp;&nbsp;&nbsp;称</td>
            <td width="366"><input name="Nickname" type="text" id="Nickname" style="width:150px;" value="<?php  echo $lmanRow["Nickname"]?>" maxlength="20"></td>
          </tr>
          <tr>
            <td align="right">移动电话</td>
            <td><input name="Mobile" type="text" id="Mobile" style="width:150px;" value="<?php  echo $lmanRow["Mobile"]?>" require="false"></td>
            <td align="right">固定电话</td>
            <td><input name="Tel2" type="text" id="Tel2" style="width:150px;" value="<?php  echo $lmanRow["Tel"]?>" require="false"></td>
          </tr>
          <tr>
            <td align="right">MSN</td>
            <td colspan="3"><input name="MSN" type="text" id="MSN" style="width:380px;" value="<?php  echo $lmanRow["MSN"]?>" require="false" dataType="Email" msg="MSN格式不正确"></td>
          </tr>
          <tr>
            <td align="right">SKYPE</td>
            <td colspan="3"><input name="SKYPE" type="text" id="SKYPE" style="width:380px;" value="<?php  echo $lmanRow["SKYPE"]?>"></td>
          </tr>
          <tr>
            <td align="right">邮件地址</td>
            <td colspan="3"><input name="Email" type="text" id="Email" style="width:380px;" value="<?php  echo $lmanRow["Email"]?>" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>
          <tr>
            <td align="right">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
            <td colspan="3"><textarea name="Remark2" style="width:380px;" id="Remark2"><?php  echo $lmanRow["Remark"]?></textarea></td>
		  </tr>
		  
         <!- ------------------------------------------Forward空运标准收费 -->
          <tr id='ForwardCharge' >
            <td colspan="4"><table cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"> 
	          <tr>
	            <td colspan="2"><div align="center">Forward空运标准收费</div></td>
	          </tr>
	          <tr>
	            <td align="right" width="130" height="25">CFS费</td>
	            <td ><input name="CFSCharge1" type="text" id="CFSCharge1" value="<?php echo $CFSCharge1?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">THC费</td>
	            <td ><input name="THCCharge1" type="text" id="THCCharge1" value="<?php echo $THCCharge1?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">文件费</td>
	            <td ><input name="WJCharge1" type="text" id="WJCharge1" value="<?php echo $WJCharge1?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">手续费</td>
	            <td ><input name="SXCharge1" type="text" id="SXCharge1" value="<?php echo $SXCharge1?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">ENS费</td>
	            <td ><input name="ENSCharge1" type="text" id="ENSCharge1" value="<?php echo $ENSCharge1?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">过桥费</td>
	            <td ><input name="GQCharge1" type="text" id="GQCharge1" value="<?php echo $GQCharge1?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">提单费</td>
	            <td ><input name="TDCharge1" type="text" id="TDCharge1" value="<?php echo $TDCharge1?>" style="width:380px;" ></td>
	          </tr>
	   
	         <!- ------------------------------------------Forward海运标准收费 -->
	          <tr>
	            <td colspan="4"><div align="center">Forward海运标准收费</div></td>
	          </tr>
	         <tr>
	            <td align="right" height="25">CFS费</td>
	            <td ><input name="CFSCharge2" type="text" id="CFSCharge2" value="<?php echo $CFSCharge2?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">文件费</td>
	            <td ><input name="WJCharge2" type="text" id="WJCharge2" value="<?php echo $WJCharge2?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">手续费</td>
	            <td ><input name="SXCharge2" type="text" id="SXCharge2" value="<?php echo $SXCharge2?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">保险费</td>
	            <td ><input name="BXCharge2" type="text" id="BXCharge2" value="<?php echo $BXCharge2?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">ENS费</td>
	            <td ><input name="ENSCharge2" type="text" id="ENSCharge2" value="<?php echo $ENSCharge2?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">电放费</td>
	            <td ><input name="DFCharge2" type="text" id="DFCharge2" value="<?php echo $DFCharge2?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">提单费</td>
	            <td ><input name="TDCharge2" type="text" id="TDCharge2" value="<?php echo $TDCharge2?>" style="width:380px;" ></td>
	          </tr>  
		  </table>
         </td>
       </tr> 
		  
		</table>
</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>

<script>
var tempMType = <?php echo $MType?>;
var sign = showOrHideCharge(tempMType);
function showOrHideCharge(tempMType){
	if(tempMType!=2)document.getElementById("ForwardCharge").style.display = "none";
}
function ShowForwardCharge(){
	
	var  MType = document.getElementById("MType").value;
	if(MType==2){
		
		document.getElementById("ForwardCharge").style.display = "";
	}else{
		document.getElementById("ForwardCharge").style.display = "none";
	}
}
</script>