<?php 
//电信---yang 20120801

include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新产品资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.productdata WHERE Id='$Id' ORDER BY Id LIMIt 1",$link_id));
$upCompanyId=$upData["CompanyId"];
$ProductId=$upData["ProductId"];
$cName=$upData["cName"];
$eCode=$upData["eCode"];
$Description=$upData["Description"];
$TypeId=$upData["TypeId"];
$Price=$upData["Price"];
$Unit=$upData["Unit"];
$TestStandard=$upData["TestStandard"];
//$Img_H=$upData["Img_H"];  
$Remark=$upData["Remark"];
$pRemark=$upData["pRemark"];
$bjRemark=$upData["bjRemark"];
$LoadQty=$upData["LoadQty"];
$PackingUnit=$upData["PackingUnit"];
$Code=$upData["Code"];
$Moq=$upData["Moq"];
$Weight=$upData["Weight"];
$MisWeight=$upData["MisWeight"];
$MainWeight=$upData["MainWeight"];
$MaterialQ=$upData["MaterialQ"];
$UseWay=$upData["UseWay"];
$taxtypeId=$upData["taxtypeId"];
$taxtypeId=$taxtypeId==0?1:$taxtypeId; 
$dzSign=$upData["dzSign"];
if($dzSign==1){$selected1="selected"; $selected0="";}
else{$selected1=""; $selected0="selected";}
$buySign=$upData["buySign"];
$InspectionSign=$upData["InspectionSign"];
$InspectionStr = "Inspection".$InspectionSign;
$$InspectionStr="selected";

$ClientProxyRow = mysql_fetch_array(mysql_query("SELECT cId FROM $DataIn.yw7_clientproduct WHERE ProductId ='$ProductId'",$link_id));
$ClientProxy = $ClientProxyRow["cId"];

if($upCompanyId === '1004' || $upCompanyId === '1059' || $upCompanyId === '100024' || $CompanyId == '2668'){
    $hasProductParameterSql = "Select * From $DataIn.productprintparameter Where productId = '$ProductId' and Estate = 1 Order by Id Limit 1";
    $hasProductParameterResult = mysql_query($hasProductParameterSql);
    $hasProductParameterRow = mysql_fetch_assoc($hasProductParameterResult);
    if($hasProductParameterRow){
        $lotto = $hasProductParameterRow["Lotto"];
        $itf = $hasProductParameterRow["itf"];
    }

    if($lotto != ''){
        $lottoColor = "class='redB'";
    }
    else{
        if($CompanyId == '100024'){
            $lotto = "ART01";
        }
        else if($CompanyId == '2668'){
            $lotto = "LOP01";
        }
        else{
            $lotto = "ASH01";
        }
    }

    if($itf != ''){
         $itfColor = "class='redB'";
    }else{
         $itf = "4";
    }
}


$prateValue=1;
$prateText="汇率";
$prateStrArr=explode("}*",$bjRemark);
if(count($prateStrArr)==2){ // 6.1(汇率) 
	
	$tmpprateStr1=$prateStrArr[1];
	$tmpprateStr2=explode("(",$tmpprateStr1);
	if(count($tmpprateStr2)>=2){
		$prateTextStr=explode(")",$tmpprateStr2[1]);
		$prateText=$prateTextStr[0];
	}
	
	$prateValue=trim($tmpprateStr2[0]); //取得数字
	if(is_numeric($prateValue)==false){
		$prateValue=1;
	}
	
}

//echo "Prate:$prateValue:$prateText <br>";

$x1StrArr=explode("{",$prateStrArr[0]); //去掉左边{
$x1Str=$x1StrArr[0];
if(count($x1StrArr)>=2){  //只取右边
	$x1Str=$x1StrArr[1];
}

$pcsValue=1;
$pcsText="Pcs";
$pcsStrArr=explode("]*",$x1Str); //去掉左边{
if(count($pcsStrArr)==2){ // 6.1(汇率) 
	
	$tmppcsStr1=$pcsStrArr[1];
	$tmppcsStr2=explode("(",$tmppcsStr1);
	if(count($tmppcsStr2)>=2){
		$pcsTextStr=explode(")",$tmppcsStr2[1]);
		$pcsText=$pcsTextStr[0];
	}
	
	$pcsValue=trim($tmppcsStr2[0]); //取得数字
	if(is_numeric($pcsValue)==false){
		$pcsValue=1;
	}
	
}
//echo "Pcs:$pcsValue:$pcsText <br>";

$x1StrArr=explode("[",$pcsStrArr[0]); //去掉左边{
$x1Str=$x1StrArr[0];
if(count($x1StrArr)>=2){  //只取右边
	$x1Str=$x1StrArr[1];
}

//echo  "$x1Str <br> ";

$pValueStrArr=explode(")+",$x1Str);
$pValueLen=count($pValueStrArr);
for($pi=1; $pi<=6; $pi++){
	$tmppV="pValue"."$pi";
	$$tmppV="";
	$tmppT="pText"."$pi";
	$$tmppT="";	
}
for($pi=1; $pi<=$pValueLen; $pi++){
	
	$tmppV="pValue"."$pi";
	$$tmppV=0;
	$tmppT="pText"."$pi";
	$$tmppT="";
	
	$tmppValueStr1=$pValueStrArr[$pi-1];
	$tmppValueStr2=explode("(",$tmppValueStr1);
	if(count($tmppValueStr2)>=2){
		$pTextStr=explode(")",$tmppValueStr2[1]);
		$$tmppT=$pTextStr[0];
	}
	
	$$tmppV=trim($tmppValueStr2[0]); //取得数字
	if(is_numeric($$tmppV)==false){
		$$tmppV=0;
	}
	
}


//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ProductId,$ProductId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
       <table width="820" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td align="right" width="180px">隶属客户</td>
            <td><select name="CompanyId" id="CompanyId" size="1" style="width: 380px;">
  			<?php  
			$result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE 1 AND Estate=1 AND  ObjectSign IN (1,2)  ORDER BY Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					if($upCompanyId==$myrow["CompanyId"]){
                          $Forshort=$myrow["Forshort"];
						echo"<option value='$myrow[CompanyId]' selected>$myrow[Forshort]</option>";
						}
					else{
						echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
						}
					} while ($myrow = mysql_fetch_array($result));
				}
			  ?>			  
      		</select></td>
        </tr>

		<tr>
            <td align="right" scope="col">产品中文名</td>
            <td scope="col">
              <input name="cName" type="text" id="cName" style="width: 380px;" value="<?php  echo $cName?>" dataType="LimitB" min="3" max="60"  msg="2-60个字节之内" title="必填项,2-60个字节内">
			  </td>
		</tr>
		<tr>
            <td align="right" scope="col">英文代码<br> Product Code</td>
            <td scope="col">
              <input name="eCode" type="text" id="eCode" style="width: 380px;" value="<?php  echo $eCode?>">  
            </td>
		</tr>
		<tr>
			<td align="right" valign="top" scope="col">英文注释<br>Description</td>
			<td scope="col"><textarea name="Description" style="width: 380px;" rows="2" id="Description"><?php  echo $Description?></textarea></td>
		</tr>
			
		  <tr>
            <td align="right">产品属性</td>
            <td>
              <select name="buySign" id="buySign"  style="width: 380px;" dataType="Require" msg="未选择分类">
               <?php    
				echo"<option value='' selected>请选择</option>";
				$result = mysql_query("SELECT Id,Name FROM $DataIn.product_property WHERE 1 AND  Estate=1 ORDER BY Id",$link_id);
				if($myrow = mysql_fetch_array($result)){
					do{
					   $thisId = $myrow["Id"];
					   $thisName = $myrow["Name"];
					   if($buySign==$thisId){
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
    	<td width="123" align="right">成品类别</td>
        <td>
		<select name="TypeId" id="TypeId" style="width: 380px;">
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
      <td align="right">最低订购量</td>
      <td><input name="Moq" type="text" id="Moq" style="width: 380px;" value="<?php  echo $Moq?>" dataType="Currency" msg="错误的价格">
      </td>
	  </tr>
    <tr>
		<td align="right">参考售价</td>
        <td>
		<input name="Price" type="text" id="Price" style="width: 380px;" value="<?php  echo $Price?>" dataType="Currency" msg="错误的价格"> 
		</td>
	</tr>
    <tr>
      <td align="right">成品重(kg)</td>
      <td><input name="Weight" type="text" id="Weight" style="width: 380px;" value="<?php  echo $Weight?>" dataType="Currency" require="false" msg="错误的重量"></td>
    </tr>
    <tr>
      <td align="right">误差值(±g)</td>
      <td><input name="MisWeight" type="text" id="MisWeight" style="width: 380px;" value="<?php  echo $MisWeight?>" dataType="Currency" require="false" msg="错误的重量"></td>
    </tr>    
	 <tr>
      <td align="right">单品体积(m³)</td>
      <td><input name="MainWeight" type="text" id="MainWeight" style="width: 380px;" value="<?php  echo $MainWeight?>" dataType="Currency" require="false" msg="错误的重量"></td>
    </tr>
     <tr>
		    <td align="right">装框数量</td>
		    <td><input name="LoadQty" type="text" id="LoadQty" value="<?php  echo $LoadQty?>" style="width: 380px;" datatype="Number" msg="错误的数量" /></td>
	      </tr>
	      
    <tr>
      <td align="right">单 位</td>
      <td><select name="Unit" id="Unit" style="width: 380px;">
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
		  <tr><td align="right">材 质</td>
            <td><select name="MaterialQ" id="MaterialQ"  style="width: 380px;" >
               <?php    
				echo"<option value='0' selected>请选择</option>";
				$MaterialQResult = mysql_query("SELECT Id,Name FROM $DataIn.productmq WHERE 1 AND  Estate=1 ORDER BY Id",$link_id);
				if($MaterialQRow = mysql_fetch_array($MaterialQResult)){
					do{
					   $thisMaterialQId = $MaterialQRow["Id"];
					   $thisMaterialQName = $MaterialQRow["Name"];
					   if($MaterialQ ==$thisMaterialQId){
						   echo"<option value='$thisMaterialQId' selected>$thisMaterialQName</option>";
					   }else{
						   echo"<option value='$thisMaterialQId'>$thisMaterialQName</option>";
					   }	
				    } while ($MaterialQRow = mysql_fetch_array($MaterialQResult));
			     }
				?>
              </select></td>
		  </tr>
          <tr>
            <td align="right">用 途</td>
            <td>
              <select name="UseWay" id="UseWay"  style="width: 380px;" >
               <?php    
				echo"<option value='0' selected>请选择</option>";
				$UseWayResult = mysql_query("SELECT Id,Name FROM $DataIn.productuseway WHERE 1 AND  Estate=1 ORDER BY Id",$link_id);
				if($UseWayRow = mysql_fetch_array($UseWayResult)){
					do{
					   $thisUseWayId = $UseWayRow["Id"];
					   $thisUseWayName = $UseWayRow["Name"];
					   if($UseWay ==$thisUseWayId){
						   echo"<option value='$thisUseWayId' selected>$thisUseWayName</option>";
					   }else{
						   echo"<option value='$thisUseWayId'>$thisUseWayName</option>";
					   }
					} while ($UseWayRow = mysql_fetch_array($UseWayResult));
				}
				?>
              </select></td></tr>
    	    <tr>
            <td align="right">电子类</td>
            <td><select name="dzSign" id="dzSign" onchange="showTable()" style="width:380px;" dataType="Require" msg="未选择分类">
			  <option value="1" <?php  echo $selected1?>>是</option>
			  <option value="0" <?php  echo $selected0?>>否</option></select>
			</td>
			</tr>
		   <?php  
		    if($dzSign==1){
		     ?>
		     <tr style="display:'';" id="pictureTable"><td>&nbsp;</td><td >
		     <?php 
		       }
		     else{
		     ?>
		     <tr style="display:none;" id="pictureTable"><td>&nbsp;</td><td >
		     <?php  }?>
			<table border="1"  cellspacing="5" id="uploadTable">
			<tr>
			<td align="center"><input type="hidden" value=""><a href="#" onclick="AddRow()" title="新增认证文档">新增</a></td>
			<td align="right">删除</td>
			<td align="center">序号</td>
			<td align="center"><span style="color:red; font-size:14px; font-weight:bold">认证图</span>上传(限pdf图片,可同时上传多个图片)</td>
			</tr>
	<?php 
	//检查是否有旧文件,如果有则列出
	$checkImgSql=mysql_query("SELECT Picture,Remark FROM $DataIn.product_certification WHERE ProductId='$ProductId' ORDER BY Picture",$link_id);
	if($checkImgRow=mysql_fetch_array($checkImgSql)){
		$i=1;
		do{
			$ImgName=$checkImgRow["Picture"];
			$ImgRemark=$checkImgRow["Remark"];
			$Item="<a href='../download/productcer/$ImgName' target='_black'><div class='redB'>$i</div></a>";
			echo"
			<tr>
				<td align='right'>&nbsp;</td>
				<td align='center' height='30'><input name='OldImg[]' type='hidden' id='OldImg[]' value='$ImgName'><a href='#' onclick='deleteImg(\"$ImgName\",this.parentNode.parentNode.rowIndex)' title='删除原图片: $ImgName'>×</a></td>
				<td align='center'>$Item</td>
				<td><input name='Picture[]' type='file' id='Picture[]' size='80' DataType='Filter' Accept='pdf' Row='$i' Cel='3'>图档备注<input name='rzRemark[]' type='text' id='rzRemark[]' size='25'  value='$ImgRemark'></td>
			</tr>";
			$i++;
			}while ($checkImgRow=mysql_fetch_array($checkImgSql));
		}
		else{
	?>
			<tr>
			<td align="right">&nbsp;</td>
			<td align="center"><input name="OldImg[]" type="hidden" id="OldImg[]"><a href="#" onclick='deleteRow(this.parentNode.parentNode.rowIndex)'>×</a></td>
			<td align="center">1</td>
			<td><input name="Picture[]" type="file" id="Picture[]" DataType="Filter" Accept="pdf" Msg="格式不对,请重选" Row="1" Cel="5">图档备注<input name="rzRemark[]" type="text" id="rzRemark[]" size="25"></td>
			</tr>
	<?php 
		}
	?>
			</table></td></tr>
			
       <td align="right">报关方式</td>
      <td><select name="taxtypeId" id="taxtypeId" style="width: 380px;">
        <?php 
			   $ptResult = mysql_query("SELECT * FROM $DataIn.taxtype WHERE Estate=1 ORDER BY Id",$link_id);
				while ($ptRow = mysql_fetch_array($ptResult)){
					$ptId=$ptRow["Id"];
					$ptName=$ptRow["Name"];
					if($ptId==$taxtypeId){
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
            <td align="right">装箱单位</td>
            <td>
              <select name="PackingUnit" id="PackingUnit" style="width: 380px;">
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
			  	</select></td> </tr>
          <tr>
            <td align="right">品牌授权书</td>
            <td>
              <select name="ClientProxy" id="ClientProxy" style="width: 380px;" >
                <option value="" selected>请选择</option>
              <?php 
			   $ProxyResult = mysql_query("SELECT * 
			   FROM $DataIn.yw7_clientproxy WHERE Estate=1 AND CompanyId ='$CompanyId' order by Id",$link_id);
				while ($ProxyRow = mysql_fetch_array($ProxyResult)){
					$ProxyId=$ProxyRow["Id"];
					$ProxyCaption=$ProxyRow["Caption"];
					if($ClientProxy ==$ProxyId){
						echo"<option value='$ProxyId' selected>$ProxyCaption</option>";
					}else{
						echo"<option value='$ProxyId'>$ProxyCaption</option>";
					}
					
				} 
				?>
              </select></td></tr>			  	
     <tr>
       <td align="right" valign="top">检验标准图</td>
       <td><input name="TestStandard" type="file" id="TestStandard" style="width: 380px;" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对" Row="7" Cel="1">
		 </td>
     </tr>
     <?php 
	if($TestStandard>0){$TestStandard="<span onClick='OpenOrLoad(\"$td\",\"$tf\")' style='CURSOR: pointer;' class='yellowB'>$cName</span>";
		$FileName="T".$ProductId.".jpg";
		$tf=anmaIn($FileName,$SinkOrder,$motherSTR);
		$td=anmaIn("download/teststandard/",$SinkOrder,$motherSTR);			
		$TestTemp="<span onClick='OpenOrLoad(\"$td\",\"$tf\")' style='CURSOR: pointer;' class='yellowB'>预览</span>";
		echo"<tr><td>&nbsp;</td><td><input name='oldFile' type='checkbox' id='oldFile' value='1'><LABEL for='oldFile'>删除已传图片</LABEL>$TestTemp</td></tr>";
		}
	  	?>
	  	  <tr>
            <td align="right">客户验货</td>
            <td><select name="InspectionSign" id="InspectionSign"  style="width: 120px;" dataType="Require" msg="未选择分类">
			  <option value="1"  <?php  echo $Inspection1?>>是</option>
			  <option value="0"  <?php  echo $Inspection0?>>否</option></select><span> &nbsp; &nbsp;*车间生成完成后，需客户验货合格后至待出</span>
			</td>
			</tr>
     <tr>
        <td align="right" valign="top">包装说明<br>          </td>
            <td><input name="Remark" type="text" id="Remark" style="width: 380px;" value="<?php  echo $Remark?>"></td>
          </tr>
	 <tr>
            <td align="right">外箱标签条码</td>
            <td><input name="Code" type="text" id="Code" style="width: 380px;" value="<?php  echo $Code?>" title="注:条码的英文注释与条码数字之间用&quot;|&quot;隔开,英文注释中需换行的地方输入&quot;&lt;br&gt;&quot;"></td>
          </tr>
     <tr>
       <td align="right" valign="top">产品备注</td>
       <td><textarea name="pRemark" style="width: 380px;" rows="2" id="pRemark"><?php  echo $pRemark?></textarea></td></tr>

         
          
    <!-- <tr>
       <td align="right" valign="top">标准图（H）</td>
       <td><input name="Img_H" type="file" id="Img_H" style="width: 380px;"  title="可选项,zip格式" DataType="Filter" Accept="zip" Msg="文件格式不对" Row="7" Cel="1"></td>
     </tr>
     <?php 
	/*if($Img_H>0){
		$I_FilePath="download/teststandard/";
		$I_Field="T".$ProductId."_".'H'.".zip";
		//echo "$Field";
		$I_Field=anmaIn($I_Field,$SinkOrder,$motherSTR);
		$I_td=anmaIn("$I_FilePath",$SinkOrder,$motherSTR);
		$HTestTemp="<a href=\"../admin/openorload.php?d=$I_td&f=$I_Field&Type=&Action=6\" target=\"download\">下载</a>";	
		echo"<tr><td>&nbsp;</td><td><input name='HoldFile' type='checkbox' id='HoldFile' value='1'><LABEL for='HoldFile'>删除已传RAR</LABEL>$HTestTemp</td></tr>";
		}*/
	  	?>
     <tr>    -->     
     
 
           <tr>
            <td align="right" valign="top">报价计算</td>
            <td>
                <table  border="0">
                  <tr>
                	<td><input name="pValue1" type="text" id="pValue1" style="width: 40px;" title="成本1数字"  value="<?php  echo $pValue1?>"/></td>
                    <td><input name="pText1"  type="text" id="pText1"  style="width: 160px;" title="成本1描述"  value="<?php  echo $pText1?>"/></td>
                    <td width="" rowspan="6">金额/描述<br /><span style="color:#F00" >不要用单引(')及双引(")</span></td>
                  </tr>
                  <tr>
                	<td><input name="pValue2" type="text" id="pValue2" style="width: 40px;" title="成本2数字" value="<?php  echo $pValue2?>"/></td>
                    <td><input name="pText2"  type="text" id="pText2"  style="width: 160px;" title="成本2描述" value="<?php  echo $pText2?>"/></td>
                  </tr>
                  <tr>
                	<td><input name="pValue3" type="text" id="pValue3" style="width: 40px;" title="成本3数字" value="<?php  echo $pValue3?>"/></td>
                    <td><input name="pText3"  type="text" id="pText3"  style="width: 160px;" title="成本3描述" value="<?php  echo $pText3?>" /></td>
                  </tr>
                  
                  <tr>
                	<td><input name="pValue4" type="text" id="pValue4" style="width: 40px;" title="成本4数字" value="<?php  echo $pValue4?>"/>	</td>
                    <td><input name="pText4"  type="text" id="pText4"  style="width: 160px;" title="成本4描述" value="<?php  echo $pText4?>" /></td>
                  </tr>
                  <tr>
                	<td> <input name="pValue5" type="text" id="pValue5" style="width: 40px;" title="成本5数字" value="<?php  echo $pValue5?>"/></td>
                    <td><input name="pText5"  type="text" id="pText5"  style="width: 160px;" title="成本5描述" value="<?php  echo $pText5?>"/></td>
                  </tr>
                  <tr>
                	<td><input name="pValue6" type="text" id="pValue6" style="width: 40px;" title="成本6数字" value="<?php  echo $pValue6?>"/></td>
                    <td><input name="pText6"  type="text" id="pText6"  style="width: 160px;" title="成本6描述" value="<?php  echo $pText6?>"/></td>
                  </tr>
                  <!--
                  <tr height="2" style="font-size:1px; line-height:1px; margin:0px; background-color:#666">
                    <td colspan="3">&nbsp;</td>
                  </tr> 
                  
                   <tr>
                    <td><input name="pcsValue" type="text" id="pcsValue" style="width: 40px;" title="无多个组合填1" value="1"/></td>
                    <td><input name="pcsText"  type="text" id="pcsText"  style="width: 80px;" title="Pcs"  value="Pcs"/></td>
                    <td>多少Pcs组合价</td>
                  </tr>
                  -->
                  <tr height="2" style="font-size:1px; line-height:1px; margin:0px;">
                    <td colspan="2" style="background-color:#CCC;">&nbsp;</td>
                    <td colspan="1">&nbsp;</td>
                  </tr> 
                    
                  <tr>
                    <td><input name="prateValue" type="text" id="prateValue" style="width: 40px;" title="无汇率转换填1" value="<?php  echo $prateValue?>" /></td>
                    <td><input name="prateText"  type="text" id="prateText"  style="width: 160px;" title="汇率描述" value="<?php  echo $prateText?>"/></td>
                    <td>汇率</td>
                  </tr>
                   
                   <tr>
                    <td colspan="3"> &nbsp;
                    <input type="button" value="生成报价规则" onclick="createBj()" /> &nbsp;&nbsp;
                    <input type="button" onclick="clearBj()" value="清除" /> </td>
                  </tr>     
                                
                </table>              
            </td>
          </tr>      
          
          <tr>
            <td align="right">报价规则</td>
            <td><textarea name="bjRemark" style="width: 380px;" id="bjRemark" readonly="readonly" ><?php  echo $bjRemark?>
            </textarea></td>
          </tr>
<?php
    if($upCompanyId === '1004' || $upCompanyId === '1059' || $upCompanyId === '100024'){
           echo "<tr>
            <td align='right'>lotto码</td>
            <td><input $lottoColor type='text' name='lotto' style='width: 380px;' id='lotto' value='$lotto' readonly>
            </input></td>
          </tr>
           <tr>
            <td align='right'>itf码</td>
            <td><input $itfColor type='text' name='itf' style='width: 380px;' id='itf' value='$itf' readonly>
            </input></td>
          </tr>";
    }
?>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>

function trim(str) {
  return str.replace(/(^\s+)|(\s+$)/g, "");
}


function createBj(){
	var Price=document.getElementById("Price").value;
	if(Price=="" ||  (fucCheckNUM(Price,"Price")==0)){ //必填报价规则
		alert("价格不能为空或价格无效!!");
		return false;
	}
	
	var sumPrice=0.00;
	var bjStr="";
	for(var i=1;i<=6;i++){
		var tmpValue=document.getElementById("pValue"+i).value;
		var tmpStr=document.getElementById("pText"+i).value;
		if((fucCheckNUM(tmpValue,"Price")>=0) && trim(tmpStr)!="" ){
			if(tmpValue>=0){  //大于零的才计算
				tmpValue=(tmpValue*1000)/1000
				sumPrice=(sumPrice*1000+tmpValue*1000)/1000;
				//var tmpStr=document.getElementById("pText"+i).value;
				if(bjStr==""){
					bjStr=tmpValue+"("+tmpStr+")";
				}
				else{
					bjStr=bjStr+"+"+tmpValue+"("+tmpStr+")";
				}
			}
		}
		else{
			if(trim(tmpValue)!=""){
				alert("无效的金额!");
				return false;
			}
		}
	} //end for 
	//alert (sumPrice);
	/*
	var tmpValue=document.getElementById("pcsValue").value;
	if(fucCheckNUM(tmpValue,"Price")>0){
		if(tmpValue>0 && tmpValue!=1){
			var tmpStr=document.getElementById("pcsText").value;
			if(tmpStr==""){
				tmpStr="Pcs";
			}			
			sumPrice=(tmpValue*1000*sumPrice)/1000;
			bjStr="["+bjStr+"]*"+tmpValue+"("+tmpStr+")";
		}
	}
	else{
		alert("无效的Pcs数值!");
		return false;
	}
	*/
	//alert (sumPrice);
	var tmpValue=document.getElementById("prateValue").value;
	if(fucCheckNUM(tmpValue,"Price")>0){
		if(tmpValue>0 && tmpValue!=1){
			var tmpStr=document.getElementById("prateText").value;
			if(tmpStr==""){
				tmpStr="汇率";
			}
			sumPrice=(tmpValue*1000*sumPrice)/1000;
			bjStr="{"+bjStr+"}*"+tmpValue+"("+tmpStr+")";
		}
	}
	else{
		alert("无效的汇率数值!");
		return false;
	}
	
	if((sumPrice*1)!=(Price*1)){
		alert("报价规则的价格("+sumPrice+")与实际价格不符("+Price+")!")
		return false;		
	}
	document.getElementById("bjRemark").value=bjStr;
	
}

function clearBj(){
	document.getElementById("bjRemark").value="";
}



//删除指定行
function deleteRow(rowIndex){
	uploadTable.deleteRow(rowIndex);
	ShowSequence(uploadTable);
	}
function deleteImg(Img,rowIndex){
	var message=confirm("确定要删除原图片 "+Img+" 吗?");
	if (message==true){
	var	myurl="productdata_delcer.php?ImgName="+Img;	
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					  if(BackData=="Y"){
					      ReOpen("productdata_update");
					     }
					}
				}
			ajax.send(null); 
		}
	}  
function ShowSequence(TableTemp){
	//原档个数
	var oldNum=document.getElementsByName("OldImg[]").length;
	for(i=1;i<TableTemp.rows.length;i++){ 
		var j=i-1;
		if(j<oldNum){
			var ImgLink=document.getElementsByName("OldImg[]")[j].value;
			TableTemp.rows[i].cells[1].innerHTML="<a href='../download/productcer/"+ImgLink+"' target='_black'><div class='redB'>"+i+"</div></a>";
			}
		else{
			TableTemp.rows[i].cells[1].innerHTML=i;//如果原序号带连接、带CSS的处理是？
			}
		document.getElementsByName("Picture[]")[j].Row=i;
		}
	}   
function AddRow(){
	oTR=uploadTable.insertRow(uploadTable.rows.length);
	tmpNum=oTR.rowIndex;
	//第一列:序号
	oTD=oTR.insertCell(0);
	oTD.innerHTML="";
	oTD.align="center";
	
	//第二列:操作
	oTD=oTR.insertCell(1);
	oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>";
	oTD.onmousedown=function(){
		window.event.cancelBubble=true;
		};
	oTD.align="center";
	oTD.height="25";
				
	//第三列:序号
	oTD=oTR.insertCell(2);
	oTD.innerHTML=""+tmpNum+"";
	oTD.align="center";
				
	//四、说明
	oTD=oTR.insertCell(3);
	oTD.innerHTML="<input name='Picture[]' type='file' id='Picture[]' DataType='Filter' Accept='pdf' Msg='格式不对,请重选' Row='1' Cel='5'>图档备注<input name='rzRemark[]' type='text' id='rzRemark[]' size='25' DataType='Require' Msg='请填写图档备注'>";
}

function showTable(){
  var dzSign=document.getElementById("dzSign").value;
  if(dzSign==1)document.getElementById("pictureTable").style.display="";
  else document.getElementById("pictureTable").style.display="none";
}
</script>