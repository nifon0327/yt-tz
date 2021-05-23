<?php   
//电信-ZX  2012-08-01
/*
$DataIn.trade_object 
$DataIn.producttype
$DataPublic.productunit
$DataPublic.packingunit
$DataIn.productdata

*/
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新产品资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.newproductdata WHERE Id='$Id' ORDER BY Id LIMIt 1",$link_id));
$upCompanyId=$upData["CompanyId"];
$ProductId=$upData["ProductId"];
$cName=$upData["cName"];
$eCode=$upData["eCode"];
$Description=$upData["Description"];
$TypeId=$upData["TypeId"];
$Price=$upData["Price"];
$Unit=$upData["Unit"];
$TestStandard=$upData["TestStandard"];

$Img_H=$upData["Img_H"];
$Img_L=$upData["Img_L"];

$Remark=$upData["Remark"];
$pRemark=$upData["pRemark"];
$PackingUnit=$upData["PackingUnit"];
$Code=$upData["Code"];
$Moq=$upData["Moq"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ProductId,$ProductId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
       <input id="Moq" name="Moq" type="hidden" value="0" />
       <input id="PackingUnit" name="PackingUnit" type="hidden" value="1" />
       <table width="850" border="0" align="center" cellspacing="4" id="NoteTable">
		<!--
        <tr>
            <td align="right">隶属客户</td>
            <td><select name="CompanyId" id="CompanyId" size="1" style="width: 490px;">
  			< 
			$result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE cSign=$Login_cSign AND Estate=1 ORDER BY Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					if($upCompanyId==$myrow["CompanyId"]){
						echo"<option value='$myrow[CompanyId]' selected>$myrow[Forshort]</option>";
						}
					else{
						echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
						}
					} while ($myrow = mysql_fetch_array($result));
				}
			  ?>			  
      		</select></td>
        </tr>  -->
		<tr>
            <td align="right" scope="col">产品中文名</td>
            <td scope="col">
              <input name="cName" type="text" id="cName" size="91" value="<?php  echo $cName?>" dataType="LimitB" min="3" max="60"  msg="2-60个字节之内" title="必填项,2-60个字节内">
			  </td>
		</tr>
 
        <tr>
            <td align="right">供应商</td>
            <td colspan="2" scope="col">
            <select name="CompanyId" id="CompanyId" style="width: 490px;">
            <?php 
            echo "<option value=''>&nbsp;</option>";
			$checkSql = "SELECT CompanyId,Forshort,Letter FROM $DataIn.trade_object WHERE (cSign>=0) ANDEstate='1' order by Letter";
            $checkResult = mysql_query($checkSql); 
            while ( $checkRow = mysql_fetch_array($checkResult)){
                $theCompanyId=$checkRow["CompanyId"];
                $Forshort=$checkRow["Letter"].'-'.$checkRow["Forshort"];
                if($upCompanyId==$theCompanyId){
                    echo "<option value='$theCompanyId' selected>$Forshort</option>";
                    }
                else{
                    echo "<option value='$theCompanyId'>$Forshort</option>";
                    }
                } 
            ?>
            </select>
            </td>
        </tr>       
        <!--
		<tr>
            <td align="right" scope="col">英文代码<br> Product Code</td>
            <td scope="col">
              <input name="eCode" type="text" id="eCode" size="91" value="<=$eCode?>">  
            </td>
		</tr>
		<tr>
			<td align="right" valign="top" scope="col">英文注释<br>Description</td>
			<td scope="col"><textarea name="Description" cols="58" rows="2" id="Description"><?php  echo $Description?></textarea></td>
		</tr>
        -->
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
    <!--
	<tr>
      <td align="right">最低订购量</td>
      <td><input name="Moq" type="text" id="Moq" size="91" value="<=$Moq?>" dataType="Currency" msg="错误的价格">
      </td>
	  </tr>
     -->
   
    <tr>
		<td align="right">买价</td>
        <td>
		<input name="Price" type="text" id="Price" size="91" value="<?php  echo $Price?>" dataType="Currency" msg="错误的价格">         
		</td>
        
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
    <!--
    <tr>
       <td align="right" valign="top">检验标准图</td>
       <td><input name="TestStandard" type="file" id="TestStandard" size="79" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对" Row="7" Cel="1">
	   
     
     <
	if($TestStandard>0){//$TestStandard="<span onClick='OpenOrLoad(\"$td\",\"$tf\")' style='CURSOR: pointer;' class='yellowB'>$cName</span>";
		$FileName="T".$ProductId.".jpg";
		$tf=anmaIn($FileName,$SinkOrder,$motherSTR);
		$td=anmaIn("download/newproductdata/",$SinkOrder,$motherSTR);			
		$TestTemp="<span onClick='OpenOrLoad(\"$td\",\"$tf\")' style='CURSOR: pointer;' class='yellowB'>预览</span>";
		echo"<input name='oldFile' type='checkbox' id='oldFile' value='1'><LABEL for='oldFile'>删除已传图片</LABEL>$TestTemp";
		}
	  	?>
      </td>  
     </tr>
    
     -->
 	<tr>
       <td align="right" valign="top">高清标准图</td>
       <td><input name="Img_H" type="file" id="Img_H" size="79" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对" Row="7" Cel="1">
     <?php 
	if($Img_H>0){//$TestStandard="<span onClick='OpenOrLoad(\"$td\",\"$tf\")' style='CURSOR: pointer;' class='yellowB'>$cName</span>";
		$FileName="T".$ProductId."_H.jpg";
		$tf=anmaIn($FileName,$SinkOrder,$motherSTR);
		$td=anmaIn("download/newproductdata/",$SinkOrder,$motherSTR);			
		$TestTemp="<span onClick='OpenOrLoad(\"$td\",\"$tf\")' style='CURSOR: pointer;' class='yellowB'>预览</span>";
		echo"<input name='oldFile_H' type='checkbox' id='oldFile_H' value='1'><LABEL for='oldFile'>删除已传图片</LABEL>$TestTemp";
		}
	  	?>
      </td>  
     </tr>    
     <!--
 	<tr>
       <td align="right" valign="top">微缩标准图</td>
       <td><input name="Img_L" type="file" id="Img_L" size="79" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对" Row="7" Cel="1">
     <
	if($Img_L>0){//$TestStandard="<span onClick='OpenOrLoad(\"$td\",\"$tf\")' style='CURSOR: pointer;' class='yellowB'>$cName</span>";
		$FileName="T".$ProductId."_L.jpg";
		$tf=anmaIn($FileName,$SinkOrder,$motherSTR);
		$td=anmaIn("download/newproductdata/",$SinkOrder,$motherSTR);			
		$TestTemp="<span onClick='OpenOrLoad(\"$td\",\"$tf\")' style='CURSOR: pointer;' class='yellowB'>预览</span>";
		echo"<input name='oldFile_L' type='checkbox' id='oldFile_L' value='1'><LABEL for='oldFile'>删除已传图片</LABEL>$TestTemp";
		}
	  	?>
      </td>  
     </tr>    

     

        
     <tr>
        <td align="right" valign="top">包装说明<br>          </td>
            <td><input name="Remark" type="text" id="Remark" size="91" value="<?php  echo $Remark?>"></td>
          </tr>
     <tr>
      -->
       <td align="right" valign="top">产品备注</td>
       <td><textarea name="pRemark" cols="58" rows="2" id="pRemark"><?php  echo $pRemark?></textarea></td>
     </tr>
     <!--
          <tr>
            <td align="right">标签装箱单位</td>
            <td>
              <select name="PackingUnit" id="PackingUnit" style="width: 490px;">
              <
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
        -->  
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>