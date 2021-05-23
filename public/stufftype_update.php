<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-17
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新配件分类资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT T.mainType,T.TypeName,T.Position,T.ActionId,T.WorkShopId,T.NameRule,T.AQL,T.BlType,T.ForcePicSign,T.PicJobid,T.PicNumber,T.GicJobid,T.GicNumber,T.jhDays,BuyerId,DevelopGroupId,DevelopNumber  
FROM $DataIn.stufftype T
WHERE T.Id='$Id' LIMIT 1",$link_id));
$mainType=$upData["mainType"];
$TypeName=$upData["TypeName"];
$Position=$upData["Position"];
$NameRule=$upData["NameRule"];
$AQL=$upData["AQL"];
$BlType=$upData["BlType"];
$PicJobid=$upData["PicJobid"];
$PicNumber=$upData["PicNumber"];
$GicJobid=$upData["GicJobid"];
$GicNumber=$upData["GicNumber"];
$jhDays=$upData["jhDays"];
$BuyerId=$upData["BuyerId"];
$ActionId=$upData["ActionId"];
$WorkShopId=$upData["WorkShopId"];

$DisabledStr=$mainType==$APP_CONFIG['WORKORDER_ACTION_MAINTYPE']?"":" disabled='disabled' hidden='hidden'";

$DevelopGroupId=$upData["DevelopGroupId"];
$DevelopNumber=$upData["DevelopNumber"];
//echo "PicNumber:$PicNumber";

$ForcePicSign=$upData["ForcePicSign"];
switch($ForcePicSign){
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

$ForcePicOpion="<option value='$ForcePicSign' selected >$ForceStr </option>";

$PGLimit="";
if($Login_P_Number=="10341" || $Login_P_Number=="10009" || $Login_P_Number=="10130"  || $Login_P_Number=="10369" || $Login_P_Number=="10051" ) {
	$PGLimit="OK";
}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="750" height="140" border="0" align="center" cellspacing="5">
        <tr>
          <td align="right" valign="middle" scope="col">分类所属</td>
          <td valign="middle" scope="col"><select name="mainType" id="mainType" style="width:380px" dataType="Require"  msg="未选择分类" onchange="ChangeInfo(this)">
              <?php 
				$CheckResult = mysql_query("SELECT Id,TypeName FROM $DataPublic.stuffmaintype WHERE Estate='1'  ORDER BY Id",$link_id);
				if($CheckRow = mysql_fetch_array($CheckResult)){
					do{
						$mainId=$CheckRow["Id"];
						$mainName=$CheckRow["TypeName"];
						if($mainId==$mainType){
							echo"<option value='$mainId' selected>$mainName</option>";
							}
						else{
							echo"<option value='$mainId'>$mainName</option>";
							}
						}while ($CheckRow = mysql_fetch_array($CheckResult));
					}
				?>
          </select></td>
        </tr>
          <tr>
            <td width="188" valign="middle" scope="col" align="right">配件类型名称</td>
            <td valign="middle" scope="col"><input name="TypeName" type="text" id="TypeName" value="<?php  echo $TypeName?>" style="width:380px" datatype="LimitB" max="30" min="2" msg="没有填写或超出2-30个字节的范围" title="必填项,2-30个字节的范围">
            </td>
          </tr>
          <tr id='ActionTR' <?php  echo $DisabledStr?>>
            <td width="120" height="30" align="right" scope="col">工艺流程</td>
            <td scope="col" ><select name="ActionId" id="ActionId" style="width: 380px;"  dataType="Require"  msg="未选择"<?php  echo $DisabledStr?>>
            <option value=''>请选择</option>
              <?php 
	          $mySql="SELECT ActionId,Name FROM $DataPublic.workorderaction WHERE Estate=1 order by Id";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $unitId=$myrow["ActionId"];
			     $unitName=$myrow["Name"];
			     if ($ActionId==$unitId){
				      echo "<option value='$unitId' selected>$unitName</option>";
			     }
			     else{
				      echo "<option value='$unitId'>$unitName</option>";
			     }
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
            </select></td>
		</tr>

          <tr id='WorkShopTR' <?php  echo $DisabledStr?>>
            <td width="120" height="30" align="right" scope="col">生产单位</td>
            <td scope="col" ><select name="WorkShopId" id="WorkShopId" style="width: 380px;"  dataType="Require"  msg="未选择" <?php  echo $DisabledStr?>>
            <option value=''>请选择</option>
            <option value='0'>不设置</option>
              <?php 
	          $mySql="SELECT Id,Name FROM $DataPublic.workshopdata WHERE Estate=1 order by Id";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $WsId=$myrow["Id"];
			     $WsName=$myrow["Name"];
			     if ($WorkShopId==$WsId){
				      echo "<option value='$WsId' selected>$WsName</option>";
			     }
			     else{
				   echo "<option value='$WsId'>$WsName</option>";
				 }
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
            </select></td>
		</tr>

          <!--
          <tr>
            <td align="right" valign="top">交货周期(天)</td>
            <td><input name="jhDays" type="text" id="jhDays"  value="<php  echo $jhDays?>" style="width:380px" maxlength="6"  dataType="Number" msg="错误的交货周期"></td>
        </tr>
        -->
		      <tr>
		  <td align="right" scope="col">备料分类</td>
		  <td valign="middle" scope="col"><select name="BlType" id="BlType" style="width:380px">
            <option value='' selected>请选择</option>
            <?php 
			  $CheckResult2 = mysql_query("SELECT Id,Name FROM $DataPublic.stuffbltype WHERE Estate='1' and cSign='$Login_cSign' ORDER BY Id",$link_id);
				while ($CheckRow2 = mysql_fetch_array($CheckResult2)){
					$thisId=$CheckRow2["Id"];
					$thisName=$CheckRow2["Name"];
					if($thisId==$BlType){
						echo"<option value='$thisId' selected>$thisName</option>";
						}
					else{
						echo"<option value='$thisId'>$thisName</option>";
						}
					}
				?>
          </select></td>
	    </tr>
	
        <tr>
		  <td align="right" scope="col">AQL标准</td>
		  <td valign="middle" scope="col"><select name="AQL" id="AQL" style="width:380px">
            <option value='' selected>标准列表</option>
            <?php 
				$CheckResult = mysql_query("SELECT AQL FROM $DataIn.qc_levels GROUP BY AQL ORDER BY AQL",$link_id);
				while ($CheckRow = mysql_fetch_array($CheckResult)){
					$Name=$CheckRow["AQL"];
                                        if ($AQL==$Name){
					   echo"<option value='$Name' selected>$Name</option>";
					}else{
                                           echo"<option value='$Name'>$Name</option>"; 
                                        }
                                }
		?>
          </select></td>
	    </tr>
		<tr>
            <td align="right" valign="top">命名规则</td>
            <td><textarea name="NameRule" style="width:380px" rows="4" id="NameRule"><?php  echo $NameRule?></textarea></td>
        </tr>
        
          <tr>
          
            <td align="right">下单需求</td>
            <td>
          	<?php  if ($PGLimit!="") { ?>  
            <select name="ForcePicSign" size="1" id="ForcePicSign" style="width:380px" dataType="Require"  msg="未选择下单需求"  <?php  echo $PGLimit?> >
              <option value="0" <?php  echo $ForcePicStr0?> >无需求</option>
              <option value="1" <?php  echo $ForcePicStr1?> >需要图片</option>
              <option value="2" <?php  echo $ForcePicStr2?> >需要图档</option>
              <option value="3" <?php  echo $ForcePicStr3?> >图片/图档</option>
            </select>
             <?php  }
			else { 
			echo "
            <select name='ForcePicSign' size='1' id='ForcePicSign' style='width:380px;'    >
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
          <tr style='color:#FF0000;'>
                 <td align="right">注意:</td>
                 <td >下面资料更新时，会直接更新配件资料中属该分类的所有配件。</td>
                 </tr>     
           <tr>
            <td align="right">开发负责人</td>

            <td><select name="DevelopId" id="DevelopId" style="width: 380px;"  dataType="Require"  msg="未选择开发负责人">     
             <option value='' selected>--请选择--</option>
             <option value='0|0'>-----</option>
			<?php 
			  $BranchIds=implode(',',$APP_CONFIG['DEVELOPMENT_BRANCHIDS']);
	          $mySql="SELECT M.Number,M.Name,G.Id,G.GroupId,G.GroupName 
							FROM $DataPublic.staffmain M 
							LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
							WHERE  (M.BranchId IN($BranchIds) OR M.GroupId='102')  AND M.Estate='1'  AND M.cSign='$Login_cSign'  ORDER BY M.GroupId";
	  
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $GroupId=$myrow["Id"];
			     $GroupName=$myrow["GroupName"];
				 $Number=$myrow["Number"];
				 $StaffName=$myrow["Name"];
				 $SelectSTR=$DevelopNumber==$Number?" SELECTED ":"";
				   echo "<option value='$GroupId|$Number' $SelectSTR>$GroupName-$StaffName</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>	
			</select>
			</td>
        </tr>
        
        <tr>
            <td align="right">采购</td>
            <td><select name="BuyerId" id="BuyerId" style="width: 380px;"  dataType="Require"  msg="未选择采购">     
             <option value='' selected>--请选择--</option>
             <option value='0'>-----</option>
			<?php 
		         $checkStaff="SELECT G.Id,G.GroupName,M.Number,M.Name as staffname FROM $DataIn.staffgroup  G 
				          LEFT JOIN $DataPublic.staffmain M on M.GroupId=G.GroupId 
		                  WHERE   M.Estate>0 AND M.BranchId IN (4,5) AND M.cSign='$Login_cSign'  order by G.Id";			 
				$staffResult = mysql_query($checkStaff); 
				while ( $staffRow = mysql_fetch_array($staffResult)){
					$pNumber=$staffRow["Number"];
					$PName=$staffRow["staffname"];	
					$GroupName=$staffRow["GroupName"];
					$SelectSTR=$BuyerId==$pNumber?" SELECTED ":"";
					echo "<option value='$pNumber' $SelectSTR>$GroupName-$PName</option>";
					} 
			?>	
			</select>
			</td>
        </tr>   
        <tr>
		  <td align="right" valign="middle" scope="col">送货楼层</td>
		  <td valign="middle" scope="col"><select name="Position" id="Position" style="width:380px">
            <option value='0' selected>位置列表</option>
            <?php 
				$CheckResult = mysql_query("SELECT Id,Name,CheckSign FROM $DataIn.base_mposition WHERE Estate='1' ORDER BY Id",$link_id);
				while ($CheckRow = mysql_fetch_array($CheckResult)){
					$Id=$CheckRow["Id"];
					$Name=$CheckRow["Name"];
					$CheckSign=$CheckRow["CheckSign"];
					switch($CheckSign){
						 case 99: $CheckSign="-----"; break;
						 case 1: $CheckSign="全  检";break;
						 default: $CheckSign="抽  检";break;
					}
					if($Id==$Position){
						echo"<option value='$Id' selected>$Name [$CheckSign]</option>";
						}
					else{
						echo"<option value='$Id'>$Name [$CheckSign]</option>";
						}
					}
				?>
          </select></td>
	    </tr>

        
       <!--
		    <tr>
            <td align="right">图片职责</td>
            <td><select name="PicJobid" id="PicJobid" style="width: 480px;" dataType="Require"  msg="未选择图片上传职位">
			<?php 
	          $mySql="SELECT j.Id,j.GroupName,m.Number,m.Name as staffname FROM $DataIn.staffgroup  j
			          LEFT JOIN $DataPublic.staffmain M on J.GroupId=M.GroupId
	                  WHERE  J.Id in(5,27,34,35) AND M.Estate>0 And M.cSign = '7' order by j.Id,j.GroupName";

	           $result = mysql_query($mySql,$link_id);
	           if($myrow = mysql_fetch_array($result)){
		      do{
		       	$jId=$myrow["Id"];
		       	$jobName=$myrow["GroupName"];
				$Number=$myrow["Number"];
				$staffname=$myrow["staffname"];
				 
			    //if ($jId==$PicJobid){
				if ($Number==$PicNumber){
			   	echo "<option value='$jId|$Number' selected>$jobName-$staffname</option>";
				 }
			    else{
				   echo "<option value='$jId|$Number'>$jobName-$staffname</option>";
				 }
		    	}while ($myrow = mysql_fetch_array($result));
		      }
			  
			  if ($PicJobid==0){
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
            <td align="right">图档职责</td>
            <td><select name="GicJobid" id="GicJobid" style="width: 480px;" dataType="Require"  msg="未选择图档上传职位">
			<?php 
	           $mySql="SELECT j.Id,j.GroupName,m.Number,m.Name as staffname FROM $DataIn.staffgroup  j
			          LEFT JOIN $DataPublic.staffmain M on J.GroupId=M.GroupId
	                  WHERE  J.Id in(5,27,34) AND M.Estate>0 And M.cSign = '7' order by j.Id,j.GroupName";

	           $result = mysql_query($mySql,$link_id);
	           if($myrow = mysql_fetch_array($result)){
		      do{
		       	$jId=$myrow["Id"];
		       	$jobName=$myrow["GroupName"];
				$Number=$myrow["Number"];
				$staffname=$myrow["staffname"];
				 
			    //if ($jId==$PicJobid){
				if ($Number==$GicNumber){
			   	     echo "<option value='$jId|$Number' selected>$jobName-$staffname</option>";
				 }
			    else{
				    echo "<option value='$jId|$Number'>$jobName-$staffname</option>";
				 }
		    	}while ($myrow = mysql_fetch_array($result));
		      }

			  if ($GicJobid==0){
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
        
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function ChangeInfo(e){
    switch(e.value*1){
	    case <?php echo $APP_CONFIG['WORKORDER_ACTION_MAINTYPE'] ?>:
	       setElementHidden("ActionTR",false);
	       setElementHidden("ActionId",false);
	       setElementHidden("WorkShopTR",false);
	       setElementHidden("WorkShopId",false);
	      break;
		default:
	       setElementHidden("ActionTR",true);
	       setElementHidden("ActionId",true);
	       setElementHidden("WorkShopTR",true);
	       setElementHidden("WorkShopId",true);
	      break;
    }
}

function setElementHidden(elementName,hidden){
	if (hidden){
		document.getElementById(elementName).disabled="disabled";
        document.getElementById(elementName).hidden="hidden";
	}
	else{
		document.getElementById(elementName).disabled="";
        document.getElementById(elementName).hidden="";
	}
}

</script>