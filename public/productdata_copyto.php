<?php 
/*电信---yang 20120801
$DataIn.trade_object 
$DataIn.producttype
$DataPublic.productunit
$DataPublic.packingunit
$DataIn.productdata

*/
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 复制产品资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_copyto";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT P.CompanyId,P.ProductId,P.cName,P.eCode,P.Description,P.TypeId,P.Price,P.Unit,P.Remark,P.pRemark,P.TestStandard,P.PackingUnit,P.Code,C.Forshort ,P.bjRemark,P.LoadQty,P.MainWeight,P.Weight
FROM $DataIn.productdata P,$DataIn.trade_object C WHERE P.Id='$Id' AND P.CompanyId=C.CompanyId ORDER BY P.Id LIMIt 1",$link_id));
$upCompanyId=$upData["CompanyId"];
$ProductId=$upData["ProductId"];
$cName=$upData["cName"];
$eCode=$upData["eCode"];
$Description=$upData["Description"];
$TypeId=$upData["TypeId"];
$Price=$upData["Price"];
$Unit=$upData["Unit"];
$Remark=$upData["Remark"];
$pRemark=$upData["pRemark"];
$PackingUnit=$upData["PackingUnit"];
$Code=$upData["Code"];
$Forshort=$upData["Forshort"];
$TestStandard=$upData["TestStandard"];
$bjRemark=$upData["bjRemark"];
$LoadQty=$upData["LoadQty"];
$MainWeight=$upData["MainWeight"];
$Weight=$upData["Weight"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ProductId,$ProductId,ActionId,58,TestStandard,$TestStandard";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
       <table width="820" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td align="right">隶属客户</td>
            <td><?php  echo $Forshort?></td>
        </tr>
		<tr>
            <td align="right" scope="col">产品中文名</td>
            <td scope="col">
              <input name="cName" type="text" id="cName" size="91" value="<?php  echo $cName?>" dataType="LimitB" min="3" max="60"  msg="2-60个字节之内" title="必填项,2-60个字节内">
			  </td>
		</tr>
		<tr>
            <td align="right" scope="col">英文代码<br> Product Code</td>
            <td scope="col">
              <input name="eCode" type="text" id="eCode" size="91" value="<?php  echo $eCode?>">  
            </td>
		</tr>
		<tr>
			<td align="right" valign="top" scope="col">英文注释<br>Description</td>
			<td scope="col"><textarea name="Description" cols="58" rows="2" id="Description"><?php  echo $Description?></textarea></td>
		</tr>
	<tr>
    	<td width="123" align="right">成品类别</td>
        <td>
		<select name="TypeId" id="TypeId" style="width: 490px;">
		<?php 
		$result = mysql_query("SELECT Letter,TypeId,TypeName FROM $DataIn.producttype WHERE Estate=1 order by Letter",$link_id);
		while ($myrow = mysql_fetch_array($result)){
			$Letter=$myrow["Letter"];
			$TypeName=$myrow["TypeName"];
			if($TypeId==$myrow["TypeId"]){
				echo "<option value='$myrow[TypeId]' selected>$Letter-$TypeName</option>";
				}
			else{
				echo"<option value='$myrow[TypeId]'>$Letter-$TypeName</option>";
				}
			} 
		?>
        </select>
		</td>
	</tr>
    <tr>
		<td align="right">参考售价</td>
        <td>
		<input name="Price" type="text" id="Price" size="91" value="<?php  echo $Price?>" dataType="Currency" msg="错误的价格"> 
		</td>
	</tr>
  <tr>
            <td align="right">成品重(g)</td>
            <td><input name="Weight" type="text" id="Weight" style="width: 380px;" value="<?php  echo $Weight?>" dataType="Currency" msg="错误的重量"></td>
          </tr>
		  <tr>
            <td align="right">单品重(g)</td>
            <td><input name="MainWeight" type="text" id="MainWeight" style="width: 380px;" value="<?php  echo $MainWeight?>" dataType="Currency" msg="错误的重量"></td>
          </tr>
		  <tr>
		    <td align="right">装框数量</td>
		    <td><input name="LoadQty" type="text" id="LoadQty" style="width: 380px;" value="0" datatype="Number" msg="错误的数量" /></td>
	      </tr>
    <tr>
      <td align="right">单 位</td>
      <td><select name="Unit" id="Unit" style="width: 490px;">
        <?php 
			   $ptResult = mysql_query("SELECT * FROM $DataPublic.productunit WHERE Estate=1 order by Id",$link_id);
				while ($ptRow = mysql_fetch_array($ptResult)){
					$ptId=$ptRow["Id"];
					$ptName=$ptRow["Name"];
					if($ptId==$Unit){
						echo "<option value='$ptId' selected>$ptName</option>";
						}
					else{
						echo"<option value='$ptId'>$ptName</option>";
						}
					} 
				?>
      </select></td>
    </tr>
     <tr>
        <td align="right" valign="top">包装说明<br>          </td>
            <td><input name="Remark" type="text" id="Remark" size="91" value="<?php  echo $Remark?>"></td>
          </tr>
     <tr>
       <td align="right" valign="top">产品备注</td>
       <td><textarea name="pRemark" cols="58" rows="2" id="pRemark"><?php  echo $pRemark?></textarea></td>
     </tr>
          <tr>
            <td align="right">标签装箱单位</td>
            <td>
              <select name="PackingUnit" id="PackingUnit" style="width: 490px;">
              <?php 
			   $puResult = mysql_query("SELECT * FROM $DataPublic.packingunit  WHERE Estate=1 order by Id",$link_id);
				while ($puRow = mysql_fetch_array($puResult)){
					$puId=$puRow["Id"];
					$puName=$puRow["Name"];
					if($puId==$PackingUnit){
						echo "<option value='$puId' selected>$puName</option>";
						}
					else{
						echo"<option value='$puId'>$puName</option>";
						}
					} 
				?>
			  	</select>
            </td>
          </tr>
          <tr>
            <td align="right">外箱标签条码</td>
            <td><input name="Code" type="text" id="Code" size="91" value="<?php  echo $Code?>" title="注:条码的英文注释与条码数字之间用&quot;|&quot;隔开,英文注释中需换行的地方输入&quot;&lt;br&gt;&quot;"></td>
          </tr>
         <tr>
            <td align="right" valign="top">报价规则</td>
            <td><textarea name="bjRemark" style="width: 380px;" id="bjRemark"><?php  echo $bjRemark?></textarea></td>
          </tr>
          <tr>
            <td align="right">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td align="right">复制至</td>
            <td>              <input name="CopyTo[]" type="checkbox" id="CopyTo1" value="7">
            七楼 
              <select name="CompanyId7" id="CompanyId7" size="1" style="width: 435px;">
  			<?php  
			$result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE cSign=7 AND Estate=1 AND CompanyId!=$upCompanyId ORDER BY Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
					} while ($myrow = mysql_fetch_array($result));
				}
			  ?>			  
      		</select></td>
          </tr>
          <tr>
            <td align="right">&nbsp;</td>
            <td><input name="CopyTo[]" type="checkbox" id="CopyTo2" value="5" dataType="Group" min="1" msg="必须选择1个"> 
              五楼
                <select name="CompanyId5" id="CompanyId5" size="1" style="width: 435px;">
  			<?php  
			$result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE cSign=5 AND Estate=1 AND CompanyId!=$upCompanyId ORDER BY Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
					} while ($myrow = mysql_fetch_array($result));
				}
			  ?>			  
      		</select></td>
          </tr>
          <tr>
            <td align="right">&nbsp;</td>
            <td><div class="redB">注意：此复制动作，将新建一产品ID，产品资料为本页内容，同时复制BOM表和标准图，<br>但因中文名唯一，所以中文名需做更改才能成功保存。不同的地方都要进行修改。</div></td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>