<?php 
//电信-zxq 2012-08-01
/*
$DataIn.trade_object
$DataPublic.sys2_labelmodel
$DataPublic.sys2_invoicemodel
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增出货文档设置记录");//需处理
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
	<tr>
    	<td colspan="2" class='A0011'><input name="Id" type="hidden" id="Id" value=""><input name="AttachedName" type="hidden" id="AttachedName" value="<?php  echo $BulletinRows["Attached"]?>"></td>
	</tr>
    <tr>
    	<td width="150" height="30" valign="middle" class='A0010'><p align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户：<br> 
      </td>
	    <td valign="middle" class='A0001'>
			<select name="CompanyId" id="CompanyId" style="width: 455px;" dataType="Require"  msg="未选择客户">
			  <option value="" selected>请选择</option>
			<?php 
			$checkSql = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE 1 AND ObjectSign IN (1,2) AND Estate=1 ORDER BY OrderBy DESC",$link_id);
			while($checkRow = mysql_fetch_array($checkSql)){
				$CompanyId=$checkRow["CompanyId"];
				$Forshort=$checkRow["Forshort"];					
				echo "<option value='$CompanyId'>$Forshort</option>";
				} 
			?>		 
	      </select></td>
    </tr>
    <tr>
      <td width="150" height="30" valign="middle" class='A0010'><div align="right">模板标题：</div></td>
      <td valign="middle" class='A0001'><input name="Title" type="text" id="Title"  size="84" dataType="LimitB" msg="必须在2~50个字符之间" placeholder="模板标题" min="3" max="50"></td>
    </tr>
    <tr>
      <td width="150" height="30" valign="middle" class='A0010'><div align="right">项目名称：</div></td>
      <td valign="middle" class='A0001'><input name="Company" type="text" id="Company"  size="84" dataType="LimitB" msg="必须在2~50个字符之间" placeholder="项目名称" min="3" max="50"></td>
    </tr>
    <tr>
      <td height="30" valign="middle" class='A0010'><p align="right">标签模板：<br>
      </td>
      <td valign="middle" class='A0001'><select name="LabelModel" id="LabelModel" style="width: 455px;" dataType="Require"  msg="未选择标签模板">
        <option value="" selected>请选择</option>
          <?php 
			$checkSql = "SELECT Id,Name FROM $DataPublic.sys2_labelmodel WHERE Estate='1'";
			$checkResult = mysql_query($checkSql); 
			while( $checkRow = mysql_fetch_array($checkResult)){
				$Id=$checkRow["Id"];
				$Name=$checkRow["Name"];					
				echo "<option value='$Id'>$Name</option>";
				} 
			?>
        </select></td>
    </tr>
    <tr>
    	<td height="30" align="right" valign="top" class='A0010'>出货目的地：</td>
	    <td valign="middle" class='A0001'><textarea name="EndPlace" cols="54" id="EndPlace" datatype="LimitB" msg="必须在2~100个字符之间" min="3" max="100" placeholder="客户名称"></textarea></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="middle" class='A0010'>出货始发地：</td>
      <td valign="middle" class='A0001'><input name="StartPlace" type="text" id="StartPlace"  size="84" dataType="LimitB" msg="必须在2~100个字符之间" placeholder="生产单位" min="3" max="100"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="middle" class='A0010'>联系人：</td>
      <td valign="middle" class='A0001'><input name="Contact" type="text" id="Contact"  size="84" dataType="LimitB" msg="必须在2~30个字符之间" placeholder="联系人" min="3" max="30"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="middle" class='A0010'>联系电话：</td>
      <td valign="middle" class='A0001'><input name="TEL" type="text" id="TEL"  size="84" dataType="LimitB" msg="必须在5~20个字符之间" placeholder="联系电话" min="5" max="20"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="middle" class='A0010'>Invoice模板：</td>
      <td valign="middle" class='A0001'><select name="InvoiceModel" id="InvoiceModel" style="width: 455px;" dataType="Require"  msg="未选择Ivoice模板">
        <option value="" selected>请选择</option>
        <?php 
			$checkSql = "SELECT Id,Name FROM $DataPublic.sys2_invoicemodel WHERE Estate='1'";
			$checkResult = mysql_query($checkSql); 
			while( $checkRow = mysql_fetch_array($checkResult)){
				$Id=$checkRow["Id"];
				$Name=$checkRow["Name"];					
				echo "<option value='$Id'>$Name</option>";
				} 
			?>
         </select></td>
    </tr>
    
    <tr>
      <td height="30" align="right" valign="middle" class='A0010'>ShipToFrom(Pdf)：<br><span style='color:#358fc1;'>Sold To</span>&nbsp;&nbsp;&nbsp;</td>
      <td valign="middle" class='A0001'><input name="SoldFrom" type="text" id="SoldFrom" size="84"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="middle" class='A0010'>FromFaxNo(Pdf)：</td>
      <td valign="middle" class='A0001'><input name="FromFaxNo" type="text" id="FromFaxNo" size="84"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="middle" class='A0010'>FromAddress(Pdf)：<br><span style='color:#358fc1;'>SoldToAddress</span>&nbsp;&nbsp;&nbsp;</td>
      <td valign="middle" class='A0001'><input name="FromAddress" type="text" id="FromAddress" size="84"></td>
    </tr>    
    
    <tr>
      <td height="30" align="right" valign="middle" class='A0010'>ShipTo(Pdf)：</td>
      <td valign="middle" class='A0001'><input name="SoldTo" type="text" id="SoldTo" size="84"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="middle" class='A0010'>FaxNo(Pdf)：</td>
      <td valign="middle" class='A0001'><input name="FaxNo" type="text" id="FaxNo" size="84"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="middle" class='A0010'>Address(Pdf)：</td>
      <td valign="middle" class='A0001'><input name="Address" type="text" id="Address" size="84"></td>
    </tr>

    <tr>
      <td height="30" align="right" valign="middle" class='A0010'>货运信息：</td>
      <td valign="middle" class='A0001'><input name="Wise" type="text" id="Wise" size="84"></td>
    </tr>

    
 <tr>
    	<td width="150" height="30" valign="middle" class='A0010'><p align="right">默认PI模板：<br> 
      </td>
	    <td valign="middle" class='A0001'>
			<select name="PISign" id="PISign" style="width: 100px;" dataType="Require"  msg="默认PI模板">
			  <option value=0 selected>否</option>
	  		  <option value=1 >是</option>
	      </select></td>
    </tr>   
    
    <tr>
      <td valign="top" class='A0010' align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注：</td>
      <td valign="middle" class='A0001'><p><br>
        1、如果客户对Invoice或外箱标签格式有特殊需求，则需要管理员先加入该特殊需求格式，然后再进行本页操作。</p>
      <p>2、Invoice标签头的客户资料如果不填写，则与客户资料一致，如有特别要求，则需填写。</p></td>
    </tr>
    
     <tr>
      <td valign="top" class='A0010' align="right"> 指定转外发公司资料：</td>
      <td valign="middle" class='A0001'><input name="OutCheck" id="OutCheck" type="checkbox" onclick="OutCheckClick(this)" value="9" /> 
                                          如高通公司等 <br />！需要使用它们指定公司的资料生成PI、Invice PackList等</td>
    </tr>
    
</table>



<!------------  转外发服务的公司名称， 地址， 银行账号，。。。。。。。。---------------------------- -->


<table border="0" id="TransOutTable" name="TransOutTable" width="<?php  echo $tableWidth?>"  cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="DISPLAY:none" >

    <tr>
      <td width="150" height="30" valign="middle" class='A0010'><div align="right">转发公司名称：</div></td>
      <td valign="middle" class='A0001'><input name="OutCompanyName" type="text" id="OutCompanyName"  size="84" ></td>
    </tr>


    <tr>
    	<td height="30" align="right" valign="top" class='A0010'>转发公司币别：</td>
	    <td valign="middle" class='A0001'><select name="OutCurrency" id="OutCurrency" style="width:400px" >
              <option value="" selected>请选择</option>
              <?php 
			$PayModeResult = mysql_query("SELECT Id,Name FROM $DataIn.currencydata WHERE Estate=1 order by Id",$link_id);
			if($PayModeRow = mysql_fetch_array($PayModeResult)){
				$i=1;
				do{
					$Id=$PayModeRow["Id"];
					$Name=$PayModeRow["Name"];
					echo"<option value='$Id'>$i $Name</option>";
					$i++;
					}while ($PayModeRow = mysql_fetch_array($PayModeResult));
				}
			?>
            </select>
		    
	    </td>
    </tr>
    
    
    <tr>
    	<td height="30" align="right" valign="top" class='A0010'>转发公司地址：</td>
	    <td valign="middle" class='A0001'><textarea name="OutAddress" cols="54" id="OutAddress" ></textarea></td>
    </tr>
 
     <tr>
      <td height="30" align="right" valign="middle" class='A0010'>转发公司电话：</td>
      <td valign="middle" class='A0001'><input name="OutTel" type="text" id="OutTel"  size="84" ></td>
    </tr 

     <tr>
      <td height="30" align="right" valign="middle" class='A0010'>转发公司传真：</td>
      <td valign="middle" class='A0001'><input name="OutFax" type="text" id="OutFax"  size="84" ></td>
    </tr 

     <tr>
      <td height="30" align="right" valign="middle" class='A0010'>转发公司网址：</td>
      <td valign="middle" class='A0001'><input name="OutURL" type="text" id="OutURL"  size="84" ></td>
    </tr  
    
    <tr>
      <td height="30" align="right" valign="middle" class='A0010'>转发公司联络人：</td>
      <td valign="middle" class='A0001'><input name="OutRequistion" type="text" id="OutRequistion"  size="84" ></td>
    </tr>
    
    <tr>
      <td height="30" align="right" valign="middle" class='A0010'>转发公司联络人电话：</td>
      <td valign="middle" class='A0001'><input name="OutReqTel" type="text" id="OutReqTel"  size="84" ></td>
    </tr>    

    <tr>
      <td width="150" height="30" valign="middle" class='A0010'><div align="right">Beneficiary：</div></td>
      <td valign="middle" class='A0001'><input name="OutBeneficiary" type="text" id="OutBeneficiary"  size="84" ></td>
    </tr>

    <tr>
    	<td height="30" align="right" valign="top" class='A0010'>Beneficiary Code：</td>
	    <td valign="middle" class='A0001'><input name="OutBeneficiaryCode" type="text" id="OutBeneficiaryCode"  size="84" ></td>
    </tr>
    
    <tr>
      <td height="30" align="right" valign="middle" class='A0010'>SWIFT Address：</td>
      <td valign="middle" class='A0001'><input name="OutSWIFTAddress" type="text" id="OutSWIFTAddress"  size="84" ></td>
    </tr>

    <tr>
      <td width="150" height="30" valign="middle" class='A0010'><div align="right">Account Name：</div></td>
      <td valign="middle" class='A0001'><input name="OutAccountName" type="text" id="OutAccountName"  size="84" ></td>
    </tr>

    <tr>
    	<td height="30" align="right" valign="top" class='A0010'>Account Number：</td>
	    <td valign="middle" class='A0001'><input name="OutAccountNumber" type="text" id="OutAccountNumber"  size="84" ></td>
    </tr>
    
    <tr>
      <td height="30" align="right" valign="middle" class='A0010'>Bank Address：</td>
      <td valign="middle" class='A0001'><input name="OutBankAddress" type="text" id="OutBankAddress"  size="84" ></td>
    </tr>

    <tr>
    	<td height="30" align="right" valign="top" class='A0010'>其它备注：</td>
	    <td valign="middle" class='A0001'><textarea name="OutRemark" cols="54" id="OutRemark" ></textarea></td>
    </tr>
 
</table>


<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script language="JavaScript" type="text/JavaScript">
	function OutCheckClick(element){
		var TransOutTable=document.getElementById("TransOutTable");
		if(element.checked==true){
			TransOutTable.style.display="";
		}
		else{
			TransOutTable.style.display="none";	
		}
	}
</script>