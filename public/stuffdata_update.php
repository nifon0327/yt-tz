<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新配件资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT S.StuffId,S.StuffCname,S.TypeId,S.Picture,S.Pjobid,S.PicNumber as PNumber,S.Jobid,S.GicNumber,S.StuffEname,
S.GcheckNumber,S.Spec,S.Weight,S.NoTaxPrice,S.Price,S.Remark,S.SendFloor,S.SeatId,
S.CheckSign,s.ForcePicSpe,S.Unit,S.jhDays,S.Estate,S.DevelopState,S.BoxPcs,	S.PriceDetermined,							   B.BuyerId,B.CompanyId,A.PicJobid,A.GicJobid,A.ForcePicSign,S.Estate,
F.GroupName as PJobname,G.GroupName as GJobname,M.Name as PicstaffName,
M2.Name as GicstaffName,N.Name AS Buyer,A.mainType 
FROM $DataIn.stuffdata S 
LEFT JOIN $DataIn.stufftype A ON A.TypeId=S.TypeId 
LEFT JOIN $DataIn.bps B ON S.StuffId=B.StuffId
LEFT JOIN $DataIn.staffgroup F ON F.Id=A.PicJobid
LEFT JOIN $DataIn.staffgroup G ON G.Id=A.GicJobid
LEFT JOIN $DataIn.staffmain M ON M.Number=A.PicNumber
LEFT JOIN $DataIn.staffmain M2 ON M2.Number=A.GicNumber
LEFT JOIN $DataIn.staffmain N ON N.Number=B.BuyerId 
WHERE S.Id='$Id' LIMIT 1",$link_id));
				
$oldSeatId = $upData["SeatId"];//库位编号
$StuffId=$upData["StuffId"];
$StuffCname=$upData["StuffCname"];
$StuffEname=$upData["StuffEname"];
$TypeId=$upData["TypeId"];
$Picture=$upData["Picture"];
$Spec=$upData["Spec"];
$Weight=$upData["Weight"];
$NoTaxPrice=$upData["NoTaxPrice"];
$Price=$upData["Price"];
$oldFile=$Picture==0?"":$StuffId.".jpg";
$Remark=$upData["Remark"];
$Unit=$upData["Unit"];
$BuyerId=$upData["BuyerId"];
$mainType=$upData["mainType"];
$PriceDetermined=$upData["PriceDetermined"];
if($PriceDetermined==1)$PriceDeterminedCheck = "checked";

$CompanyId=$upData["CompanyId"];

$CheckTaxRow  = mysql_fetch_array(mysql_query("SELECT InvoiceTax 
FROM $DataIn.providersheet WHERE CompanyId = '$CompanyId'",$link_id));
$taxRate = $CheckTaxRow["InvoiceTax"];

$newNoTaxPrice =  sprintf("%.4f", $Price /(1+$taxRate/100));
if($newNoTaxPrice!=$NoTaxPrice)$NoTaxPrice=$newNoTaxPrice;
   
$SendFloor=$upData["SendFloor"];
$CheckSign=$upData["CheckSign"];
$ForcePicSpe=$upData["ForcePicSpe"];
$jhDays=$upData["jhDays"];
$ForcePicSign=$upData["ForcePicSign"];
$BoxPcs=$upData["BoxPcs"];
$Buyer=$upData["Buyer"];
if($ForcePicSpe>=0){  //-1表示用stufftype用的，否则用它指定
	switch($ForcePicSpe){
		case 0: 
			$ForcePicStr0="selected";
			$ForceStr="无需求";
		break;
		case 1: 
			$ForcePicStr1="selected";
			$ForceStr="需要图片";
		break;
		case 2: 
			$ForcePicStr2="selected";
			$ForceStr="需要图档";
		break;
		case 3: 
			$ForcePicStr3="selected";
			$ForceStr="图片/图档";
		break;
		case 4: 
			$ForcePicStr4="selected";
			$ForceStr="强行锁定";
		break;	
		
	}	
}

	
		
switch($ForcePicSign){
	case 0: 
		$ForcePicSign="无需求（系统默认）";
	break;
	case 1: 
		$ForcePicSign="需要图片（系统默认）";
	break;
	case 2: 
		$ForcePicSign="需要图档（系统默认）";
	break;
	case 3: 
		$ForcePicSign="图片/图档（系统默认）";
	break;
	case 4: 
		$ForcePicSign="强行锁定（系统默认）";
	break;	
}	

if($ForcePicSpe>=0) {
	$ForcePicOpion="<option value='$ForcePicSpe' selected >$ForceStr </option>";
}
else {
	$ForcePicOpion="<option value='-1' selected >$ForcePicSign </option>";
}

$Pjobid=$upData["Pjobid"];
$PNumber=$upData["PNumber"];

$PicJobid=$upData["PicJobid"];
$PicstaffName=$upData["PicstaffName"];
$PJobname=$upData["PJobname"]==""?"不需传图片（系统默认）":$upData["PJobname"]."-$PicstaffName （系统默认）";


if($Pjobid>=0){
	$PicJobid=$Pjobid;
}
else {
	
	$PicStr="selected";
	
}

$Jobid=$upData["Jobid"];
$GicNumber=$upData["GicNumber"];
$GicstaffName=$upData["GicstaffName"];
$GicJobid=$upData["GicJobid"];
$GJobname=$upData["GJobname"]==""?"不需传图档":$upData["GJobname"] ."-$GicstaffName （系统默认）";;
//$GJobname="$GJobname"."（系统默认）";
if($Jobid>=0){
	$GicJobid=$Jobid;
}
else {
	$GicStr="selected";
	
}

$GcheckNumber=$upData["GcheckNumber"];
if ($GcheckNumber==-1) {
	$GcheckStr="selected";
}


$Estate=$upData["Estate"];
$DevelopState=$upData["DevelopState"];
$DevelopSel="DevelopState" .$DevelopState;
$$DevelopSel="selected";

$CnameLimit="";

if ($Estate>1) {
	$PriceLimit="";
}
else {
	$PriceLimit=($Login_BranchId==$APP_CONFIG['PROCUREMENT_BRANCHID'] || in_array($Login_BranchId, $APP_CONFIG['DEVELOPMENT_BRANCHIDS']) || in_array($Login_GroupId,$APP_CONFIG['IT_DEVELOP_GROUPID']))?"":"readonly";//采购
}

$Estate=$upData["Estate"];
$PGLimit="";

$NumberArray=explode(',',  $APP_CONFIG['UPDATE_STUFFDATA_NUMBER']);
if(in_array($Login_BranchId, $APP_CONFIG['DEVELOPMENT_BRANCHIDS']) || in_array($Login_P_Number,$NumberArray)) {
	
 	$PGLimit="OK";
	$CnameLimit="";
	$PriceLimit="";
}
if ($Estate==0 ||  $Estate==2) {
	$PGLimit="OK";
}
$comSign =0 ;
$PropertyResult=mysql_query("SELECT Property FROM $DataIn.stuffproperty WHERE StuffId=$StuffId",$link_id);
while($PropertyRow=mysql_fetch_array($PropertyResult)){
      $Property=$PropertyRow["Property"];
      $ProStr="Property".$Property;
      $$ProStr="checked";
     if($Property==10){
           $comSign=1;
        }
 }
 


//步骤4：
$tableWidth=850;$tableMenuS=500;
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,StuffId,$StuffId,StuffType,$StuffType";
//步骤5：//需处理
if($comSign==1){
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<input id="PackingUnit" name="PackingUnit" type="hidden" value="1" />
	<table width="800" border="0" align="center" cellspacing="5">
	   <tr><td width="103" align="right" scope="col">&nbsp;</td> <td >
	   <span class="redB">配件属性为子配件，只能更改名称</span></td></tr>
		<tr>
            <td width="103" align="right" scope="col">配件名称</td>
            <td scope="col"><input name="StuffCname" type="text" id="StuffCname" value="<?php  echo $StuffCname?>"  style='width:580px;'  dataType="LimitB" min="3" max="100"  msg="必须在2-100个字节之内" title="必填项,2-100个字节内" <?php  echo $CnameLimit?>>
                *只限业务修改
            </td><input name="oldStuffCname" type="hidden" id="oldStuffCname" value="<?php  echo $StuffCname?>" /><input name="comSubSign" type="hidden" id="comSubSign" value="<?php  echo $comSign?>" />
          </tr>
         <tr>
            <td align="right">主产品重</td>
            <td><input name="Weight" type="text" id="Weight" size="53" dataType="Currency" value="<?php  echo $Weight?>" msg="错误的重量">(单位:克[g])</td> <input name="oldWeight" type="hidden" id="oldWeight" value="<?php  echo $Weight?>" />
        </tr>
        </table>
</td></tr></table>

<?php

}
else{
	

?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<input id="PackingUnit" name="PackingUnit" type="hidden" value="1" />
	<table width="800" border="0" align="center" cellspacing="5">
		<tr>
            <td width="103" align="right" scope="col">配件名称</td>
            <td scope="col"><input name="StuffCname" type="text" id="StuffCname" value="<?php  echo $StuffCname?>"  style='width:580px;'  dataType="LimitB" min="3" max="100"  msg="必须在2-100个字节之内" title="必填项,2-100个字节内" <?php  echo $CnameLimit?>>
                *只限业务修改
            </td><input name="oldStuffCname" type="hidden" id="oldStuffCname" value="<?php  echo $StuffCname?>" />
            <input name="comSubSign" type="hidden" id="comSubSign" value="<?php  echo $comSign?>" />
            <input  name="mainType" type="hidden" id="mainType" value="<?php echo $mainType?>"/>
          </tr>

          <tr>
            <td align="right">配件类型</td>
            <td><select name="TypeId" id="TypeId" style="width:480px"><!--onchange="getType(1)"-->
            
			  <?php 
				$result = mysql_query("SELECT TypeId,TypeName,Letter FROM $DataIn.stufftype WHERE Estate='1' OR TypeId='$theTypeId' order by Letter",$link_id);
				while ($StuffType = mysql_fetch_array($result)){
					$theTypeId=$StuffType["TypeId"];
					$TypeName=$StuffType["Letter"]."-".$StuffType["TypeName"];
					if($TypeId==$theTypeId){
						echo "<option value='$theTypeId' selected>$TypeName</option>";
						}
					else{
						echo "<option value='$theTypeId'>$TypeName</option>";
						}
					}
			 	?>
            </select><input name="oldTypeId" type="hidden" id="oldTypeId" value="<?php  echo $TypeId?>" />
              </td>
          </tr>
           <tr>
            <td align="right">配件规格</td>
            <td><input name="Spec" type="text" id="Spec" value="<?php  echo $Spec?>" size="53">(配件为外箱时必填)</td>
            <input name="oldSpec" type="hidden" id="oldSpec" value="<?php  echo $Spec?>" />
          </tr>
        <tr>
            <td align="right">主产品重</td>
            <td><input name="Weight" type="text" id="Weight" size="53" dataType="Currency" value="<?php  echo $Weight?>" msg="错误的重量">
              (单位:克[g],配件为外箱时必填)</td> <input name="oldWeight" type="hidden" id="oldWeight" value="<?php  echo $Weight?>" />
        </tr>
        
          <tr>
            <td align="right">含税价</td>
            <td><input name="Price" type="text" id="Price" value="<?php  echo $Price?>" size="53" dataType="Currency"  msg="错误的价格" <?php  echo $PriceLimit?> >*只限采购/开发修改 <input type="hidden" id='PriceDetermined' name="PriceDetermined" value="0"><input type="checkbox" onclick="checkPriceDetermined(this)" <?php echo $PriceDeterminedCheck ?>><span class="redB">(价格待定)</span></td>
             <input name="oldPrice" type="hidden" id="oldPrice" value="<?php  echo $Price?>" />
          </tr>     
        
       <!-- <tr>
            <td align="right">加税点</td>
            <td><input name="taxRate" type="text" id="taxRate" style='width:480px;'  value="<?php echo $taxRate?>" readonly></td>
        </tr>
        
          
        <tr>
            <td align="right">参考价</td>
            <td><input name="NoTaxPrice" type="text" id="NoTaxPrice"  style='width:480px;'  dataType="Currency" msg="错误的价格"  value="<?php echo $NoTaxPrice?>" onblur="changePrice(3)"></td>
        </tr> -->
          
          
		  <tr>
          <td align="right">单&nbsp;&nbsp;&nbsp;&nbsp;位</td>
          <td><select name="Unit" id="Unit" style="width: 480px;"  dataType="Require"  msg="未选择">
              <?php 
	          $mySql="SELECT Id,Name FROM $DataPublic.stuffunit WHERE Estate=1 order by Name";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $unitId=$myrow["Id"];
			     $unitName=$myrow["Name"];
				 if($Unit==$unitId){
				 	echo "<option value='$unitId' selected>$unitName</option>";
					}
				 else{
				   echo "<option value='$unitId'>$unitName</option>";
				   }
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
            </select></td><input name="oldUnit" type="hidden" id="oldUnit" value="<?php  echo $Unit?>" />
          </tr>
        <tr>
            <td align="right">箱/pcs</td>
            <td><input name="BoxPcs" type="text" id="BoxPcs"  style='width:480px;'  value="<?php echo $BoxPcs?>"></td>
        </tr><input name="oldBoxPcs" type="hidden" id="oldBoxPcs" value="<?php  echo $BoxPcs?>" />
           <tr>
            <td align="right" height="30">配件属性</td>
            <td>
            <?php 
               $x=0;
			    $checkResult = mysql_query("SELECT * FROM $DataIn.stuffpropertytype  WHERE Estate=1 AND Id<>10 ORDER BY Id",$link_id);
                while($checkRow = mysql_fetch_array($checkResult)){
                    $PropertyId=$checkRow["Id"];
                    $TypeName=$checkRow["TypeName"];
                    $TypeColor=$checkRow["TypeColor"];
                    $ProStr="Property". $PropertyId;
                    $CheckedValue=$$ProStr;      
                    if ($x>0 && $x%10==0) echo "<br>"; 
                    echo "<input name='Property[]' type='checkbox' value='$PropertyId'  $CheckedValue  onclick='CheckProperty(this)'><span style='color:$TypeColor;'>$TypeName</span>&nbsp;&nbsp;&nbsp;&nbsp;";
                    $x++;
                    }
			?>
          </td>
        </tr>
        <tr>
            <td align="right">英文代码</td>
            <td><input name="StuffEname" type="text" id="StuffEname" style='width:480px;' value="<?php echo $StuffEname?>"></td>
            <input name="oldStuffEname" type="hidden" id="oldStuffEname" value="<?php  echo $StuffEname?>" />
        </tr>

        <tr>
            <td align="right">开发状态</td>
            <td><select name="DevelopState" size="1" id="DevelopState" style="width:480px" >
              <option value="0"  <?php  echo $DevelopState0?>>否</option>
              <option value="1" <?php  echo $DevelopState1?>>是</option>
            </select>
            </td>
          </tr> 
          
           <tr>
            <td align="right">下单需求</td>
            <td>
            <?php  if ($PGLimit!="") { ?>
            <select name="ForcePicSpe" size="1" id="ForcePicSpe" style="width:480px;"   >
              <option value="-1"  ><?php  echo $ForcePicSign?> </option>
              <option value="0" <?php  echo $ForcePicStr0?> >无图需求</option>
              <option value="1" <?php  echo $ForcePicStr1?> >需要图片</option>
              <option value="2" <?php  echo $ForcePicStr2?> >需要图档</option>
              <option value="3" <?php  echo $ForcePicStr3?> >图片/图档</option>
            </select>
            <?php  }
			else { 
			echo "
            <select name='ForcePicSpe' size='1' id='ForcePicSpe' style='width:480px;'    >
              $ForcePicOpion
            </select>  ";          
            
				
			
            }
			?>
            
            </td>
            <!--
              <option value="3" <=$ForcePicStr3?> >图片/图档</option>
              <option value="4" <=$ForcePicStr4?> >强行锁定</option>
              -->
          </tr>   
          <!--        
 		    <tr>
            <td align="right">图片上传</td>
            <td><select name="Pjobid" id="Pjobid" style="width: 480px;" dataType="Require"  msg="未选择图片上传职位">
            <option value="-1|0"  <?php  echo $PicStr?> ><?php  echo $PJobname ?>  </option>
			<?php 
	          /*
$mySql="SELECT j.Id,j.Name,m.Number,m.Name as staffname FROM $DataPublic.jobdata  j
			          LEFT JOIN $DataPublic.staffmain M on J.Id=M.JobId
	                  WHERE  J.Id in(3,4,6,7,32)  AND M.Estate>0 order by j.Id,j.Name";
*/
				$mySql="SELECT j.Id,j.GroupName,m.Number,m.Name as staffname FROM $DataIn.staffgroup  j
			          LEFT JOIN $DataPublic.staffmain M on J.GroupId=M.GroupId
	                  WHERE  J.Id in(5,27,34,35) AND M.Estate>0 And M.cSign = '7' order by j.Id,j.GroupName";
	           $result = mysql_query($mySql,$link_id);
			   $first==0;
	           if($myrow = mysql_fetch_array($result)){
		      do{
		       	$jId=$myrow["Id"];
		       	$jobName=$myrow["GroupName"];
				$Number=$myrow["Number"];
				$staffname=$myrow["staffname"];				
				
			   // if ($jId==$PicJobid && $PicStr==""){
			  if ($jId==$PicJobid && $PNumber==0 && $PicStr==""){
				 if ($first==0){
				 	echo "<option value='$jId|$Number' selected>$jobName-(未指定人)</option>";  
					echo "<option value='$jId|$Number' >$jobName-$staffname</option>"; 
					$first=1; 
				 }
				 else {
					
					echo "<option value='$jId|$Number' >$jobName-$staffname</option>";  
				 }
				 
			  }
			  else {
				  if ($Number==$PNumber && $PicStr==""){   
					echo "<option value='$jId|$Number' selected>$jobName-$staffname</option>";
					 }
					else{
					   echo "<option value='$jId|$Number'>$jobName-$staffname</option>";
					 }
			    }
		    	}while ($myrow = mysql_fetch_array($result));
		      }
			  if ($PicJobid==0  && $PicStr=="" ){
				  echo " <option value='0|0' selected>不需传图片</option>";
			  }
			  else{
				  echo " <option value='0|0'>不需传图片</option>"; 
			  }
		    ?>
           
			</select>
			</td>
       	   </tr>         
          
          
		    <tr>
            <td align="right">图档上传</td>
            <td><select name="Jobid" id="jobId" style="width: 480px;" dataType="Require"  msg="未选择图档上传职位">
            <option value="-1|0"  <?php  echo $GicStr?> ><?php  echo $GJobname ?> </option>
			<?php 
	          /* $mySql="SELECT Id,Name FROM $DataPublic.jobdata  
	                WHERE Estate=1 AND Id in(3,4,6,7,32) order by Id,Name";  
	                */
	             $mySql="SELECT j.Id,j.GroupName,m.Number,m.Name as staffname FROM $DataIn.staffgroup  j
			          LEFT JOIN $DataPublic.staffmain M on J.GroupId=M.GroupId
	                  WHERE  J.Id in(5,27,34) AND M.Estate>0 And M.cSign = '7' order by j.Id,j.GroupName";
	           $result = mysql_query($mySql,$link_id);
	          $first==0;
	           if($myrow = mysql_fetch_array($result)){
		      do{
		       	$jId=$myrow["Id"];
		       	$jobName=$myrow["GroupName"];
				$Number=$myrow["Number"];
				$staffname=$myrow["staffname"];				
				
			   // if ($jId==$PicJobid && $PicStr==""){
			  if ($jId==$GicJobid && $GicNumber==0 && $GicStr==""){
				 if ($first==0){
				 	echo "<option value='$jId|$Number' selected>$jobName-(未指定人)</option>";  
					echo "<option value='$jId|$Number' >$jobName-$staffname</option>"; 
					$first=1; 
				 }
				 else {
					echo "<option value='$jId|$Number' >$jobName-$staffname</option>";  
				 }
			  }
			  else {
				  if ($Number==$GicNumber && $GicStr==""){   
					echo "<option value='$jId|$Number' selected>$jobName-$staffname</option>";
					 }
					else{
					   echo "<option value='$jId|$Number'>$jobName-$staffname</option>";
					 }
			    }
		    	}while ($myrow = mysql_fetch_array($result));
		      }
		      if ($GicJobid==0 && $GicStr==""){
				  echo " <option value='0|0' selected>不需传图档</option>";
			  }
			  else{
				  echo " <option value='0|0'>不需传图档</option>"; 
			  }
		    ?>
           
			</select>
			</td>
        </tr>
        -->
         <tr>
            <td align="right">图档审核</td>
            <td><select name="GcheckNumber" id="GcheckNumber" style="width: 480px;"  dataType="Require"  msg="未选择图档审核人">     
              <option value="-1|-1" <?php  echo $GcheckStr?> >（系统默认） </option>	 
			<?php 
	          $mySql="SELECT j.Id,j.Name,m.Number,m.Name as staffname FROM $DataPublic.jobdata  j
			          LEFT JOIN $DataPublic.staffmain M on J.Id=M.JobId
	                  WHERE  J.Id in(3,4,6,7,32) AND M.Estate>0 order by j.Id,j.Name";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $jobId=$myrow["Id"];
			     $jobName=$myrow["Name"];
				 $Number=$myrow["Number"];
				 $staffname=$myrow["staffname"];
				 if ($GcheckNumber==$Number){
					 echo "<option value='$jobId|$Number' selected >$jobName-$staffname</option>";
				 }
				 else {
					echo "<option value='$jobId|$Number'>$jobName-$staffname</option>"; 
				 }
			   }while ($myrow = mysql_fetch_array($result));
		    }
			
			if ($GcheckNumber==0) {
				echo "<option value='0|0' selected>不需图档审核</option>";
			}
			else {
				echo "<option value='0|0'>不需图档审核</option>";
			}
			?>	
           
			</select>
			</td>
        </tr>
         <tr>
            <td align="right">开发负责人</td>
            <td colspan="2" scope="col" name="PJobname" id="PJobname" ><?php echo $PJobname;?></td>
          </tr>

                 
          <tr>
            <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="65" rows="4" id="Remark"><?php  echo $Remark?></textarea></td>
          </tr>
          <tr>
            <td align="right">设定关系</td>
            <td colspan="2" align="center" scope="col">&nbsp;</td>
          </tr>
                  
          <tr>
            <td align="right">采 &nbsp;购</td>
            <td colspan="2" scope="col">
			<select name="BuyerId" id="BuyerId" style="width: 480px;">
			<?php
            $checkStaff="SELECT M.Number,M.Name as staffname FROM  
			          $DataIn.staffmain M 
	                  WHERE   M.Estate>0 AND M.BranchId IN (". $APP_CONFIG['STUFF_BUYER_BRANCHID'] .") 
	                  UNION ALL  SELECT  '0' AS Number,'-----' AS  Name  ";//or $BuyerId=M.Number
            $staffResult = mysql_query($checkStaff);
            while ($staffRow = mysql_fetch_array($staffResult)){
                $pNumber=$staffRow["Number"];
                $PName=$staffRow["staffname"];
                /*$GroupName=$staffRow["GroupName"]; */
                if ($BuyerId==$pNumber){
                    echo "<option value='$pNumber' selected>$PName</option>";
                }
                else{
                    echo "<option value='$pNumber'>$PName</option>";
                }
            }
            ?>
			
            </select>
      
            <input name="oldBuyerId" type="hidden" id="oldBuyerId" value="<?php  echo $BuyerId?>" />
		    </td>
          </tr>
         
          <tr>
            <td align="right">供应商</td>
            <td colspan="2" scope="col">
			<select name="CompanyId" id="CompanyId" style="width: 480px;" onchange="changePrice(2)">
            <?php 
			$checkSql = " SELECT  * FROM (
                SELECT CompanyId,Forshort,Letter FROM $DataIn.trade_object 
                 WHERE Estate='1' AND (cSign=$Login_cSign or cSign=0) AND  ObjectSign IN (1,3) 
               UNION ALL 
                 SELECT  '0' AS CompanyId,'----'  AS Forshort,'' AS Letter
            ) A   order by Letter";
			
			$checkResult = mysql_query($checkSql); 
			while ( $checkRow = mysql_fetch_array($checkResult)){
				$theCompanyId=$checkRow["CompanyId"];
				$Forshort=$checkRow["Letter"].'-'.$checkRow["Forshort"];
				if($CompanyId==$theCompanyId){
					echo "<option value='$theCompanyId' selected>$Forshort</option>";
					}
				else{
                        echo "<option value='$theCompanyId'>$Forshort</option>";
					  }
				} 
			?>
            </select>
            <input name="oldCompanyId" type="hidden" id="oldCompanyId" value="<?php  echo $CompanyId?>" />
			</td>
          </tr>
 
     
       </select>
		</td> <input name="oldCheckSign" type="hidden" id="oldCheckSign" value="<?php  echo $CheckSign?>" />
    	    </tr>

        <tr>
            <td align="right">库位编号</td>
            <td><select name="SeatId" id="SeatId" style="width: 480px;"  dataType="Require"  msg="未选择默认库位编号" >
                    <option value='' selected>请选择</option>
                    <?php
                    $mySql="SELECT SeatId,WareHouse,ZoneName FROM wms_seat  order by SeatId";
                    $result = mysql_query($mySql,$link_id);
                    if($myrow = mysql_fetch_array($result)){
                        do{
                            $SeatId=$myrow["SeatId"];
                            $WareHouse=$myrow["WareHouse"];
                            $ZoneName=$myrow["ZoneName"];
                            //$CheckSign=$myrow["CheckSign"];

                            if ($SeatId==$oldSeatId){
                                echo "<option value='$SeatId' selected>$WareHouse - $ZoneName - $SeatId</option>";
                            }
                            else{
                                echo "<option value='$SeatId'>$WareHouse - $ZoneName - $SeatId</option>";
                            }
                        }while ($myrow = mysql_fetch_array($result));
                    }
                    ?>
                </select>
            </td><input name="oldSeatId" type="hidden" id="oldSeatId" value="<?php  echo $oldSeatId?>" />
        </tr>

            <tr>
            <td align="right">送货楼层</td>
            <td><select name="SendFloor" id="SendFloor" style="width: 480px;"  dataType="Require"  msg="未选择默认送货楼层" onchange="getCheckSign(this)">     
             <option value='' selected>请选择</option>
			<?php  
	          $mySql="SELECT Id,Name,Remark,CheckSign FROM $DataIn.base_mposition  
	                  WHERE Estate=1 order by  Remark";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $FloorId=$myrow["Id"];
				 $FloorRemark=$myrow["Remark"];
				 $FloorName=$myrow["Name"];
				 //$CheckSign=$myrow["CheckSign"];
				 
				 if ($FloorId==$SendFloor){
				   echo "<option value='$FloorId' selected>$FloorName</option>";
				 }
				 else{
				   echo "<option value='$FloorId'>$FloorName</option>"; 
				 }
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
			</select>
			</td><input name="oldSendFloor" type="hidden" id="oldSendFloor" value="<?php  echo $SendFloor?>" />
        </tr> 
        
           <tr>
            <td align="right">品检方式</td>
            <td><select name="CheckSign" id="CheckSign" style="width: 480px;" dataType="Require"  msg="未选择品检要求">
                <?php 
                 $StrSign="CheckSign_" . $CheckSign;
                 $$StrSign="selected";
                 echo " <option value='99' $CheckSign_99>-----</option>
                       <option value='0' $CheckSign_0>抽  检</option>
                       <option value='1' $CheckSign_1>全  检</option>";
                ?> 
               </select></td>
            <tr>
            
            <td align="right" valign="top">修改原因</td>
            <td><textarea name="Reason" cols="65" rows="3" id="Reason"></textarea></td>
          </tr>
          
        <tr>
          <td>&nbsp;</td>
          <td><div class="redB">
            注意：<br>
            1、外箱配件规格的写法：长*宽*高CM+其它规格说明,尺寸一定要按格式写在前面,这影响出货装箱设置<br>
		  </div></td>
        </tr>
        </table>
</td></tr></table>
<?php 
}
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script language="JavaScript" type="text/JavaScript">
var comSubSign = document.getElementById("comSubSign").value;
if(comSubSign==0)getType(0);

function getType(sign){
	 var TypeId=document.getElementById("TypeId").value;
     var PropertyObj=  document.getElementsByName("Property[]");
    if(TypeId==9124){
        for(var k=0;k<PropertyObj.length;k++){
                  if(k==3)PropertyObj[k].checked=true;
                  else {
                          PropertyObj[k].checked=false;  PropertyObj[k].disabled=true;
                        }
              }
            document.getElementById("Price").value=0.00;
        }
   
   if (sign==1){
		     var url="stuffdata_type_ajax.php?TypeId="+TypeId+"&do="+Math.random();
			 var ajax=InitAjax();
		　	 ajax.open("GET",url,true);
		     ajax.onreadystatechange =function(){
		     if(ajax.readyState==4){//&& ajax.status ==200
					var BackData=ajax.responseText;
					//alert(ajax.responseText);
					if(BackData!=""){
					      var dataArray=BackData.split("|");
					      document.getElementById("Buyer").innerHTML=dataArray[0];
					      document.getElementById("PJobname").innerHTML=dataArray[1];
					     }
				 }	
			   }
			 ajax.send(null);
	}
     
}

function getCheckSign(e){
	var FloorId=e.value;
	if (FloorId>0){
		var url="stuffdata_checksign.php?FloorId="+FloorId+"&do="+Math.random();
	 var ajax=InitAjax();
　	 ajax.open("GET",url,true);
     ajax.onreadystatechange =function(){
     if(ajax.readyState==4){//&& ajax.status ==200
			var BackData=ajax.responseText;
			//alert(ajax.responseText);
			if(BackData!=""){
			      var  CheckSign=document.getElementById("CheckSign");
			      var index=getSOptionValueIndex(BackData,CheckSign);
			      CheckSign.selectedIndex=index;
			}
		 }	
	   }
	 ajax.send(null);
	}
}

function  getSOptionValueIndex(Value,e){
	
	for(i=0;i<e.length;i++){
		//alert ("d:"+e.options[i].value);
		if(e.options[i].value==Value){
			return i;
		}
	}
	return -1;
}
 
function CheckForm()
{
     var sign=0;
     if(comSubSign==0){
	     if (document.getElementById("oldStuffCname").value!=document.getElementById("StuffCname").value){
		     sign++;
	     }
	     
	     if (document.getElementById("oldPrice").value*1!=document.getElementById("Price").value*1){
		     sign++;
	     }
	     
	      if (document.getElementById("oldCompanyId").value!=document.getElementById("CompanyId").value){
		     sign++;
	     }

         if (document.getElementById("oldSeatId").value!=document.getElementById("SeatId").value){
             sign++;
         }
	  }
     
     if (sign>0){
           var Reason=document.getElementById("Reason").value;
           Reason=Reason.replace(/(^\s*)|(\s*$)/g, "");
            if (Reason==""){
	             alert("请填写修改原因！");return false;
	        }
     }

     
	 Validator.Validate(document.getElementById(document.form1.id),3,"stuffdata_updated");
}


function  CheckProperty(e){
      var PropertyObj=  document.getElementsByName("Property[]");
      var CheckSign=0;
      for(var k=0;k<PropertyObj.length;k++){
                 if(PropertyObj[k].value==2 && PropertyObj[k].checked  ){
                           CheckSign=1;break;
                       }
              }
              
         if(CheckSign==1){
              if(e.value!=2){
                   for(var k=0;k<PropertyObj.length;k++){
                           if(k!=1 && k!=2 && e.value!=2 && k!=7){
                                    if(PropertyObj[k].checked) PropertyObj[1].checked=false;
                                  }
                              }
                       }
                 else{
                   for(var k=0;k<PropertyObj.length;k++){
                           if(k!=1 && k!=2 && k!=7){
                                    if(PropertyObj[k].checked) PropertyObj[k].checked=false;
                                  }
                              }
                       }
            }
  }
  
  
function changePrice(Action){
	
	if(Action==1){    
	    var Price  = parseFloat(document.getElementById("Price").value);
		var taxRate = parseFloat(document.getElementById("taxRate").value); 
		var newTaxRate = (1 + taxRate/100);
		var NoTaxPrice = Price /newTaxRate;
		
		document.getElementById("NoTaxPrice").value = NoTaxPrice.toFixed(4);
	}else if (Action==3){
		
		var NoTaxPrice  = parseFloat(document.getElementById("NoTaxPrice").value);
		var taxRate = parseFloat(document.getElementById("taxRate").value); 
		var newTaxRate = (1 + taxRate/100);
		var Price = NoTaxPrice *newTaxRate;
		
		document.getElementById("Price").value = Price.toFixed(4);
	}
	else{
		    var Price  = parseFloat(document.getElementById("Price").value);
		    var NoTaxPrice  = parseFloat(document.getElementById("NoTaxPrice").value);    
		    var CompanyId = document.getElementById("CompanyId").value;
			var url="stuffdata_getTax_ajax.php?CompanyId="+CompanyId;
			var ajax=InitAjax();
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
			　　if(ajax.readyState==4 && ajax.status ==200){
			　　　	 
			        var taxRate = parseFloat(ajax.responseText);
  
				        document.getElementById("taxRate").value = taxRate;
				        var newTaxRate = (1 + taxRate/100);
				        if(Price >0){
					        var newNoTaxPrice = Price /newTaxRate;
			                document.getElementById("NoTaxPrice").value = newNoTaxPrice.toFixed(4);
				        }else{
					        
					        var newPrice = newNoTaxPrice * newTaxRate;
			                document.getElementById("Price").value = newPrice.toFixed(4);
				        }      
				  }
			  }
		　	ajax.send(null);	
	  }
}

function  checkPriceDetermined(e){
    var oldPrice = document.getElementById("Price").value;
    if(e.checked){
	    document.getElementById("PriceDetermined").value=1;
	    document.getElementById("Price").value = "0.0000";
    }else{
	    document.getElementById("PriceDetermined").value =0 ;
	    document.getElementById("Price").value = oldPrice;
    }
}
            
</script>