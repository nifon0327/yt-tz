<?php 
//EWEN 2013-02-26 OK
include "nobom_config.inc";
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新固定资产折旧信息");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
 $InvoiceFileSTR="";
 $ContractFileSTR="";
 
 if ($From=='m'){
	 $checkResult = mysql_query("SELECT  C.Id,C.Date,C.BarCode,C.rkId,C.GoodsNum,C.Estate,
			 S.BranchId,S.PostingDate,S.Amount,S.DepreciationId,S.Depreciation,S.Salvage,S.Remark,S.Locks,S.Operator,S.InvoiceFile,S.ContractFile,
			 A.GoodsId,A.GoodsName,A.TypeId,F.Name AS TypeName 
			FROM $DataPublic.nonbom7_fixedassets S
			INNER JOIN  $DataIn.nonbom7_code  C  ON S.BarCode=C.BarCode 
			LEFT JOIN     $DataPublic.nonbom4_goodsdata A ON C.GoodsId=A.GoodsId 
			LEFT JOIN     $DataPublic.nonbom2_subtype T  ON T.Id=A.TypeId 
			LEFT JOIN     $DataPublic.acfirsttype F ON F.FirstId=T.FirstId  
			WHERE   S.Id='$Id' LIMIT 1",$link_id);
 }else{
		$checkResult = mysql_query("SELECT  C.Id,C.Date,C.BarCode,C.rkId,C.GoodsNum,C.Estate,
				 S.BranchId,S.PostingDate,S.Amount,S.DepreciationId,S.Depreciation,S.Salvage,S.Remark,S.Locks,S.Operator,S.InvoiceFile,S.ContractFile,
				 A.GoodsId,A.GoodsName,A.TypeId,F.Name AS TypeName 
				FROM $DataIn.nonbom7_code  C 
				INNER JOIN  $DataPublic.nonbom7_fixedassets S ON S.BarCode=C.BarCode 
				LEFT JOIN     $DataPublic.nonbom4_goodsdata A ON C.GoodsId=A.GoodsId 
				LEFT JOIN     $DataPublic.nonbom2_subtype T  ON T.Id=A.TypeId 
				LEFT JOIN     $DataPublic.acfirsttype F ON F.FirstId=T.FirstId  
				WHERE    C.Id='$Id' LIMIT 1",$link_id);
 }
 if($upData = mysql_fetch_array($checkResult)){
        $Id = $upData["Id"];
        $BarCode=$upData["BarCode"];
		$GoodsId=$upData["GoodsId"];
		$GoodsName=$upData["GoodsName"];
		$GoodsNum=$upData["GoodsNum"];
		$rkId=$upData["rkId"];
		$Remark=$upData["Remark"];
		$TypeName=$upData["TypeName"];
		$PostingDate=$upData["PostingDate"];
		$BranchId=$upData["BranchId"];
		$Amount=$upData["Amount"];
		$Estate=$upData["Estate"];
		$Locks=$upData["Locks"];
		$Salvage =$upData["Salvage"];
		$DepreciationId=$upData["DepreciationId"];
		$Depreciation=$upData["Depreciation"];
		$InvoiceFile =  $upData["InvoiceFile"];
		$ContractFile =  $upData["ContractFile"];
		
		if($InvoiceFile!=""){
			$Dir=anmaIn("download/nonbom_cginvoice/",$SinkOrder,$motherSTR);
			$Attached=anmaIn($InvoiceFile,$SinkOrder,$motherSTR);
			$InvoiceFileSTR="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>已上传</span>";
	    }
	    
	    if($ContractFile!=""){
			$Dir2=anmaIn("download/nonbom_contract/",$SinkOrder,$motherSTR);
			$Attached2=anmaIn($ContractFile,$SinkOrder,$motherSTR);
			$ContractFileSTR="<span onClick='OpenOrLoad(\"$Dir2\",\"$Attached2\")' style='CURSOR: pointer;color:#FF6633'>已上传</span>";
	    }

 }
 else{
		$upData =mysql_fetch_array(mysql_query("SELECT   C.Id,C.Date,C.GoodsNum,C.rkId,C.BarCode,C.Operator,C.Estate,C.Locks,C.Picture,
					 A.GoodsId,A.GoodsName,A.DepreciationId,A.Salvage,DP.Depreciation,B.Price,A.TypeId,F.Name AS TypeName 
					FROM $DataIn.nonbom7_code  C 
					LEFT JOIN $DataPublic.nonbom4_goodsdata A ON C.GoodsId=A.GoodsId 
					LEFT JOIN $DataPublic.nonbom2_subtype T  ON T.Id=A.TypeId 
					LEFT JOIN $DataPublic.acfirsttype F ON F.FirstId=T.FirstId  
					LEFT JOIN $DataPublic.nonbom5_goodsstock G ON G.GoodsId=A.GoodsId
					LEFT JOIN $DataPublic.nonbom3_retailermain D ON D.CompanyId=G.CompanyId
					LEFT JOIN $DataPublic.nonbom6_depreciation DP  ON DP.Id=A.DepreciationId  
					LEFT JOIN $DataPublic.nonbom7_insheet  R ON R.Id=C.rkId
					LEFT JOIN $DataPublic.nonbom6_cgsheet B ON B.Id=R.cgId
					WHERE    C.Id='$Id' LIMIT 1",$link_id));
		$BarCode=$upData["BarCode"];
		$GoodsId=$upData["GoodsId"];
		$GoodsName=$upData["GoodsName"];
		$GoodsNum=$upData["GoodsNum"];
		$rkId=$upData["rkId"];
		$Remark=$upData["Remark"];
		$TypeName=$upData["TypeName"];
		$PostingDate=$upData["Date"];
		$Estate=$upData["Estate"];
		$Amount=$upData["Price"];
		$Locks=$upData["Locks"];
		$Salvage =$upData["Salvage"];
		$DepreciationId=$upData["DepreciationId"];
		$Remark = "";
		$BranchId="";
}

$chargeRow= mysql_fetch_array(mysql_query("SELECT Id FROM $DataPublic.nonbom7_depreciationcharge   
		          WHERE BarCode='$BarCode' LIMIT 1",$link_id));
$chargeSign =$chargeRow['Id']>0?1:0;

$RowNum=13;
/*
if($Locks==0){
	$Info="<span class='redB'>记录锁定中.先请主管解锁后更新.</span>";
	$SaveSTR="NO";
}
*/
//步骤4：
$tableWidth=1050;$tableMenuS=600;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,ActionId,$ActionId,Pagination,$Pagination,Page,$Page,BarCode,$BarCode";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="100%" height="250" border="0" align="center" cellspacing="0" id="NoteTable">
		<tr>
			<td align="right" valign="middle" scope="col" height="25">资产名称：</td>
			<td valign="middle" scope="col" class="blueB"><?php echo $GoodsId . '-' . $GoodsName;?><input  id="SIdList" name="SIdList" type="hidden"></td>
		</tr>
        <tr>
		  <td align="right"  height="25">类型：</td>
		  <td ><?php echo $TypeName?></td>
	    </tr>
		<tr>
		  <td align="right"  height="25">使用部门：</td>
		  <td scope="col" >
		  <?php 
             $SelectFrom="";
             include"../model/subselect/BranchId.php";
            ?>
            </td>
	    </tr>
	      <tr>
		 <td align="right"  height="25">资产编号：</td>
		  <td  scope="col"><input name="GoodsNum" type="text" id="GoodsNum" style="width: 380px;"  value="<?php echo $GoodsNum;?>" datatype='Require'  msg="没有填写" /></td>
	    </tr>
	    <input name="rkId" type="hidden" id="rkId" value="<?php echo $rkId;?>"/>
	     <?php if ($rkId==0){  ?>
		<tr>
		  <td align="right">使用情况：</td>
		  <td scope="col" >
		           <select name='Estate' id='Estate'  style='width:380px' dataType='Require' msg='未选择'>
		        <?php
		              $EstateStrs = $APP_CONFIG['NOBOM_FIXEDASSET_ESTATE'];
		              while(list($key,$val)= each($EstateStrs))
		              {
			                 if ($key==$Estate){
				                  echo "<option value='$key' selected>$val</option>"; 
			                 }else{
				                  echo "<option value='$key'>$val</option>";  
			                 } 
		             }
		          ?>
		          </select>
		    </td>
	    </tr>
	   <?php } else{$RowNum--; } ?>
	   
        <tr>
			<td align="right" valign="middle" scope="col" height="25">入帐日期：</td>
			<td valign="middle" scope="col" ><input name='PostingDate' type='text' id='PostingDate' size='12' maxlength='10' value='<?php echo $PostingDate;?>'   onFocus='WdatePicker()' /></td>
		</tr>
		<tr>
			<td align="right" valign="middle" scope="col" height="25">原值金额：</td>
			<td valign="middle" scope="col" ><input name='Amount' type='text' id='Amount' size='12' maxlength='10' value='<?php echo $Amount;?>' dataType=' Price' msg='金额不正确'/></td>
		</tr>
        <tr>
			<td align="right" valign="middle" scope="col" height="25">增加方式：</td>
			<td valign="middle" scope="col" >
				<select name='AddType' id='AddType'  style='width:380px' dataType='Require' msg='未选择'>
		        <?php
		              $AddTypeStrs = $APP_CONFIG['NOBOM_FIXEDASSET_ADDTYPE'];
		              while(list($key,$val)= each($AddTypeStrs))
		              {
			                 if ($key==$AddType){
				                  echo "<option value='$key' selected>$val</option>"; 
			                 }else{
				                  echo "<option value='$key'>$val</option>";  
			                 } 
		             }
		          ?>
		          </select>
			</td>
		</tr>
        <tr>
          <td align="right" height="25">折旧方法：</td>
          <td class="blueB">
	          <select name='DepreciationType' id='DepreciationType'  style='width:380px' dataType='Require' msg='未选择' >
		        <?php
		              $DepreciationTypeStrs = $APP_CONFIG['NOBOM_FIXEDASSET_DEPRECIATIONTYPE'];
		              while(list($key,$val)= each($DepreciationTypeStrs))
		              {
			                 if ($key==$DepreciationType){
				                  echo "<option value='$key' selected>$val</option>"; 
			                 }else{
				                  echo "<option value='$key'>$val</option>";  
			                 } 
		             }
		          ?>
		          </select>
          </td>
        </tr>
        <tr>
          <td align="right" height="25">折旧期数：</td>
          <td >
	          <select name="DepreciationId" id="DepreciationId" style="width: 380px;" dataType='Require' msg='未填写'>     
             <option value='' selected>请选择</option>
			<?php  
	          $mySql="SELECT Id,Depreciation,ListName FROM $DataPublic.nonbom6_depreciation  WHERE Estate=1 order by Id";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $thisDepreciationId=$myrow["Id"];
				 $thisDepreciationName=$myrow["ListName"];
				 if($thisDepreciationId == $DepreciationId){
					 echo "<option value='$thisDepreciationId' selected>$thisDepreciationName</option>"; 
				 }else{
					if ($chargeSign==0) echo "<option value='$thisDepreciationId'>$thisDepreciationName</option>"; 
				 }
				 
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
			</select>
          </td>
        </tr>
        <tr>
          <td height="22" valign="middle" scope="col" align="right" height="25">残值率</td>
          <td valign="middle" scope="col"><input name="Salvage" type="text" id="Salvage" value="<?php echo $Salvage ?>" size='12' maxlength='10'  datatype="Price" msg="没有填写或格式不对"  <?php if ($chargeSign==1) echo "readonly";?>/></td>
        </tr>
        <?php if ($rkId==0){  ?>
        <tr>
            <td height="25" align="right">发票文件</td>
            <td>
			<input name="InvoiceFile" type="file" id="InvoiceFile" size="40" title="可选项,pdf格式"  DataType="Filter" Accept="pdf" Msg="文件格式(限PDF)不对,请重选" Row="11" Cel="1"> <?php echo $InvoiceFileSTR;?>
			</td>
		</tr>
		<td height="25" align="right">采购合同</td>
            <td>
			<input name="ContractFile" type="file" id="ContractFile" size="40" title="可选项,pdf格式"  DataType="Filter" Accept="pdf" Msg="文件格式(限PDF)不对,请重选" Row="12" Cel="1"> <?php echo $ContractFileSTR;?>
			</td>
		</tr>
		 <?php }?>
        <tr>
          <td align="right" valign="top" height="25">更新备注：</td>
          <td><textarea name="Remark" rows="3" id="Remark" style="width: 380px;" dataType='Require' msg='未填写'><?php echo $Remark;?></textarea></td>
        </tr>
	  </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>