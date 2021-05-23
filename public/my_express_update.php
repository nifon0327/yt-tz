<?php 
/*电信---yang 20120801
$DataPublic.my3_express
$DataPublic.staffmain
$DataPublic.my3_exadd
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新快递资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT E.PayType,E.Contents,E.Pieces,E.Length,E.Width,E.Height,E.cWeight,M.Name AS Shipper,M.Number,A.Name AS Receiver,A.Company AS Company,A.PayerNo,A.Address,A.ZIP,A.Country,A.Tel,A.Mobile,A.Email,E.SendContent ,E.CompanyId,E.expressType
FROM $DataPublic.my3_express E
LEFT JOIN $DataPublic.staffmain M ON M.Number=E.Shipper
LEFT JOIN $DataPublic.my3_exadd A ON A.Id=E.Receiver 
WHERE  E.Id='$Id' LIMIT 1
",$link_id));
$pNumber=$upData["Number"];
$Shipper=$upData["Shipper"];
$PayType=$upData["PayType"];
$TempEstateSTR="PayType".strval($PayType); 
$$TempEstateSTR="selected";	
$Contents=$upData["Contents"];
$Pieces=$upData["Pieces"];
$Length=$upData["Length"];
$Width=$upData["Width"];
$Height=$upData["Height"];
$cWeight=$upData["cWeight"];
$Receiver=$upData["Receiver"];
$Company=$upData["Company"];
$PayerNo=$upData["PayerNo"];
$Address=$upData["Address"];
$ZIP=$upData["ZIP"];
$Country=$upData["Country"];
$Tel=$upData["Tel"];
$Mobile=$upData["Mobile"];
$Email=$upData["Email"];
$SendContent=$upData["SendContent"];
$CompanyId=$upData["CompanyId"];
$expressType=$upData["expressType"];
if($expressType==1)$expressStr1="selected";
else $expressStr2="selected";
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,pNumber,$pNumber,rNumber,";
//步骤5：//需处理
?>
<script  type=text/javascript>
function SearchData(SearchNum,Action){//来源页面，可取记录数，动作（因共用故以参数区别）
	var num=Math.random();  
	switch(Action){
		case 0://查找寄件人
			BackStockId=window.showModalDialog("my_exshipper_s1.php?r="+num+"&Action="+Action+"&SearchNum="+SearchNum,"BackStockId","dialogHeight =700px;dialogWidth=500px;center=yes;scroll=yes");
			if(BackStockId){
				var CL=BackStockId.split("^^");
				document.form1.pNumber.value=CL[0];
				document.form1.ShipperName.value=CL[1];
				}
		break;
		case 1://查找收件人
			BackStockId=window.showModalDialog("my_exreceiver_s1.php?r="+num+"&Action="+Action+"&SearchNum="+SearchNum,"BackStockId","dialogHeight =700px;dialogWidth=900px;center=yes;scroll=yes");
			if(BackStockId){
				var CL=BackStockId.split("^^");
				//$Id."^^".$PayerNo."^^".$Name."^^".$Company."^^".$Country."^^".$ZIP."^^".$Address."^^".$Tel."^^".$Mobile;
				document.form1.rNumber.value=CL[0];
				document.form1.PayerNo.value=CL[1];
				document.form1.Receiver.value=CL[2];
				document.form1.Company.value=CL[3];
				document.form1.Country.value=CL[4];
				document.form1.ZIP.value=CL[5];
				document.form1.Address.value=CL[6].replace("$$","'");
				document.form1.Tel.value=CL[7];
				document.form1.Mobile.value=CL[8];
				}
		break;
		}
	}
</script>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
    	<td width="150" height="25" valign="middle" class='A0010' align="right">寄 件 人：
      </td>
      <td valign="middle" class='A0001'><?php  echo $Shipper?></td>
    </tr>
    <tr>
      <td height="19" valign="middle" class='A0010' align="right">&nbsp;</td>
      <td valign="middle" class='A0001'>&nbsp;            </td>
    </tr>
  <tr>
      <td height="25" valign="middle" class='A0010' align="right">快递公司：</td>
      <td valign="middle" class='A0001'><select name="CompanyId" id="CompanyId" style="width:420px" datatype="Require" msg="未选择">
   <?php 
      $fResult = mysql_query("SELECT CompanyId,Forshort FROM $DataPublic.freightdata WHERE Estate='1' AND Model='1' AND MType=1 ORDER BY Id",$link_id);
		if($fRow = mysql_fetch_array($fResult)){
			if($CompanyId==0){
				echo"<option value=''>请选择</option>";
				}
			do{
				if($CompanyId==$fRow["CompanyId"]){
					echo"<option value='$fRow[CompanyId]' selected>$fRow[Forshort]</option>";
					}
				else{
					echo"<option value='$fRow[CompanyId]'>$fRow[Forshort]</option>";
					}
				} while($fRow = mysql_fetch_array($fResult));
			}
		?>
      </select></td>
    </tr>
  <tr>
      <td height="25" valign="middle" class='A0010' align="right">快递类型：</td>
      <td valign="middle" class='A0001'><select name="expressType" id="expressType" style="width:420px" dataType="Require" msg="未选择">
        <option value="">请选择</option>
        <option value="1" <?php  echo $expressStr1?>>正规</option>
        <option value="2" <?php  echo $expressStr2?>>代理</option>
      </select></td>
    </tr>
    <tr>
      <td height="25" valign="middle" class='A0010' align="right">付款方式：</td>
      <td valign="middle" class='A0001'><select name="PayType" id="PayType" style="width:420px" dataType="Require" msg="未选择">
        <option value="1" <?php  echo $PayType1?>>寄付</option>
        <option value="0" <?php  echo $PayType0?>>到付</option>
      </select></td>
    </tr>
    <tr>
      <td height="25" valign="middle" class='A0010' align="right">付款帐号：</td>
      <td valign="middle" class='A0001'><input name="PayerNo" type="text" id="PayerNo" value="<?php  echo $PayerNo?>" size="78"></td>
    </tr>
    <tr>
    	<td height="25" valign="middle" class='A0010' align="right">收 件 人：</td>
	    <td valign="middle" class='A0001'><input name="Receiver" type="text" id="Receiver" value="<?php  echo $Receiver?>" size="74" dataType="Require" msg="未填写">
        <input type="button" name="Submit" value="..." title="查找收件人"  onclick="SearchData(1,1)"></td>
    </tr>
    <tr>
      <td height="25" valign="middle" class='A0010' align="right">收件公司：</td>
      <td valign="middle" class='A0001'><input name="Company" type="text" id="Company" value="<?php  echo $Company?>" size="78" dataType="Require" msg="未填写"></td>
    </tr>
    <tr>
      <td height="25" valign="middle" class='A0010' align="right">国&nbsp;&nbsp;&nbsp;&nbsp;家：</td>
      <td valign="middle" class='A0001'><input name="Country" type="text" id="Country" value="<?php  echo $Country?>" size="78" dataType="Require" msg="未填写"></td>
    </tr>
    <tr>
      <td height="25" valign="middle" class='A0010' align="right">邮政编码：</td>
      <td valign="middle" class='A0001'><input name="ZIP" type="text" id="ZIP" value="<?php  echo $ZIP?>" size="78"></td>
    </tr>
    <tr>
      <td height="25" valign="middle" class='A0010' align="right">地&nbsp;&nbsp;&nbsp;&nbsp;址：</td>
      <td valign="middle" class='A0001'><input name="Address" type="text" id="Address" value="<?php  echo $Address?>" size="78" dataType="Require" msg="未填写"></td>
    </tr>
    <tr>
      <td height="25" valign="middle" class='A0010' align="right">电话/传真：</td>
      <td valign="middle" class='A0001'><input name="Tel" type="text" id="Tel" size="78" value="<?php  echo $Tel?>" dataType="Require" msg="未填写"></td>
    </tr>
    <tr>
      <td height="25" valign="middle" class='A0010' align="right">手机号码：</td>
      <td valign="middle" class='A0001'><input name="Mobile" type="text" id="Mobile" value="<?php  echo $Mobile?>" size="78"></td>
    </tr>
    <tr>
      <td height="25" valign="middle" class='A0010' align="right">托寄数量：</td>
      <td valign="middle" class='A0001'><input name="Pieces" type="text" id="Pieces" value="<?php  echo $Pieces?>" size="78" dataType="Require" msg="未填写"></td>
    </tr>
    <tr>
      <td height="47" valign="top" class='A0010' align="right">物品说明：</td>
      <td valign="middle" class='A0001'><textarea name="Contents" cols="50" rows="3" id="Contents" datatype="Require" msg="未填写"><?php  echo $Contents?></textarea></td>
    </tr>
	<tr>
      <td height="47" valign="top" class='A0010' align="right">托寄内容：</td>
      <td valign="middle" class='A0001'><textarea name="SendContent" cols="50" rows="3" id="SendContent" datatype="Require" msg="未填写"><?php  echo $SendContent?></textarea></td>
    </tr>
       <tr>
      <td height="25" valign="middle" class='A0010' align="right">外箱尺寸(CM)：</td>
       <td valign="middle" class='A0001'><input name="Length" type="text" id="Length" value="<?php  echo $Length?>" size="17" dataType="Number" msg="未填写">
      &nbsp;*&nbsp;&nbsp;<input name="Width" type="text" id="Width" value="<?php  echo $Width?>" size="17" dataType="Number" msg="未填写">
      &nbsp;*&nbsp;&nbsp;<input name="Height" type="text" id="Height" value="<?php  echo $Height?>" size="18" dataType="Number" msg="未填写"></td>
    </tr>
     <tr>
      <td height="25" valign="middle" class='A0010' align="right">重量(KG)：</td>
      <td valign="middle" class='A0001'><input name="cWeight" type="text" id="cWeight" size="78" value="<?php  echo $cWeight?>"  datatype="Require" msg="未填写"></td>
    </tr>
    <tr>
      <td height="28" valign="top" class='A0010' align="right">说&nbsp;&nbsp;&nbsp;&nbsp;明：</td>
      <td valign="middle" class='A0001'>如果收件人没有在快递通讯录，请填写以上详细资料，系统将自动收录收件人资料，以便下次使用。收录条件：原快递通讯录没有与之相同的收件人名称、公司名称和地址<br>
	  2.托寄内容:说明什么产品寄往何处寄给谁或者什么产品从哪里寄过来给谁</td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>