<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-17
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增配件分类");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="750" height="140" border="0" align="center" cellspacing="5">
	  <tr>
        <td align="right" valign="middle" scope="col">分类所属</td>
        <td valign="middle" scope="col"><select name="mainType" id="mainType" style="width:380px" dataType="Require"  msg="未选择分类" onchange="ChangeInfo(this)">
            <option value="" selected>请选择</option>
            <?php 
				$CheckResult = mysql_query("SELECT Id,TypeName FROM $DataPublic.stuffmaintype WHERE Estate='1' ORDER BY Id",$link_id);
				while ($CheckRow = mysql_fetch_array($CheckResult)){
					$Id=$CheckRow["Id"];
					$TypeName=$CheckRow["TypeName"];
					echo"<option value='$Id'>$TypeName</option>";
					}
				?>
           </select></td>
	    </tr>
		<tr>
			<td width="137" valign="middle" scope="col" align="right">分类名称</td>
			<td valign="middle" scope="col"><input name="TypeName" type="text" id="TypeName" style="width:380px" maxlength="30" datatype="LimitB" max="30" min="2" msg="没有填写或超出2-30个字节的范围" title="必填项,2-30个字节的范围">
			</td>
		</tr>
		<tr id='ActionTR' disabled="disabled" hidden="hidden">
            <td width="120" height="30" align="right" scope="col">工艺流程</td>
            <td scope="col" ><select name="ActionId" id="ActionId" style="width: 380px;"  dataType="Require"  msg="未选择" disabled="disabled" hidden="hidden">
            <option value=''>请选择</option>
              <?php 
	          $mySql="SELECT ActionId,Name FROM $DataPublic.workorderaction WHERE Estate=1 order by Id";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $unitId=$myrow["ActionId"];
			     $unitName=$myrow["Name"];
				   echo "<option value='$unitId'>$unitName</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
            </select></td>
		</tr>
		<tr id='WorkShopTR' disabled="disabled" hidden="hidden">
            <td width="120" height="30" align="right" scope="col">生产单位</td>
            <td scope="col" ><select name="WorkShopId" id="WorkShopId" style="width: 380px;"  dataType="Require"  msg="未选择" disabled="disabled" hidden="hidden">
            <option value=''>请选择</option>
             <option value='0'>不设置</option>
              <?php 
	          $mySql="SELECT Id,Name FROM $DataPublic.workshopdata WHERE Estate=1 order by Id";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $WsId=$myrow["Id"];
			     $WsName=$myrow["Name"];
			     
				 echo "<option value='$WsId'>$WsName</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
            </select></td>
		</tr>

        <!--
		<tr>
            <td align="right" valign="top">交货周期(天)</td>
            <td><input name="jhDays" type="text" id="jhDays" style="width:380px" maxlength="6"  dataType="Number" msg="错误的交货周期"></td>
        </tr>
        -->
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
					echo"<option value='$Id'>$Name  [$CheckSign]</option>";
					}
				?>
          </select></td>
	    </tr>
	 
            <tr>
		  <td align="right"  scope="col">AQL标准</td>
		  <td valign="middle" scope="col"><select name="AQL" id="AQL" style="width:380px">
            <option value='' selected>标准列表</option>
            <?php 
				$CheckResult = mysql_query("SELECT AQL FROM $DataIn.qc_levels GROUP BY AQL ORDER BY AQL",$link_id);
				while ($CheckRow = mysql_fetch_array($CheckResult)){
					$Name=$CheckRow["AQL"];
					echo"<option value='$Name'>$Name</option>";
					}
				?>
          </select></td>
	    </tr>
		 <tr>
            <td align="right" valign="top">命名规则</td>
            <td><textarea name="NameRule" style="width:380px" rows="4" id="NameRule"></textarea></td>
        </tr>
        
          <tr>
            <td align="right">下单需求</td>
            <td><select name="ForcePicSign" size="1" id="ForcePicSign" style="width:380px" dataType="Require"  msg="未选择下单需求">
              <option value="">--请选择--</option>
              <option value="0">无需求</option>
              <option value="1">需要图片</option>
              <option value="2">需要图档</option>
			  <option value="3">图片/图档</option>
            </select>
            </td>
            <!--
              <option value="3">图片/图档</option>
              <option value="4">强行锁定</option> 
            -->
          </tr> 
         
           <tr>
            <td align="right">开发负责人</td>

            <td><select name="DevelopId" id="DevelopId" style="width: 380px;"  dataType="Require"  msg="未选择开发负责人">     
             <option value='' selected>--请选择--</option>
             <option value='0'>-----</option>
			<?php 
	          $mySql="SELECT M.Number,M.Name,G.Id,G.GroupId,G.GroupName 
							FROM $DataPublic.staffmain M 
							LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
							WHERE  (M.BranchId='5' OR M.GroupId='102')  AND M.Estate='1'  AND M.cSign='$Login_cSign'  ORDER BY M.GroupId";
	  
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $GroupId=$myrow["Id"];
			     $GroupName=$myrow["GroupName"];
				 $Number=$myrow["Number"];
				 $StaffName=$myrow["Name"];
				 
				   echo "<option value='$GroupId|$Number'>$GroupName-$StaffName</option>";
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
					echo "<option value='$pNumber'>$GroupName-$PName</option>";
					} 
			?>	
			</select>
			</td>
        </tr>
        
       <!--
         <tr>
            <td align="right">图片职责</td>

            <td><select name="PicJobid" id="PicJobid" style="width: 380px;"  dataType="Require"  msg="未选择图片上传职位">     
             <option value='' selected>--请选择--</option>
			<?php 
	          $mySql="SELECT j.Id,j.GroupName,m.Number,m.Name as staffname FROM $DataIn.staffgroup  j
			          LEFT JOIN $DataPublic.staffmain M on J.GroupId=M.GroupId
	                  WHERE  J.Id in(5,27,34,35) AND M.Estate>0 And M.cSign = '7' order by j.Id,j.GroupName";
	  
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $jobId=$myrow["Id"];
			     $jobName=$myrow["GroupName"];
				 $Number=$myrow["Number"];
				 $staffname=$myrow["staffname"];
				 
				   echo "<option value='$jobId|$Number'>$jobName-$staffname</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>	
            <option value='0|0'>不需传图片</option>	 
			</select>
			</td>
        </tr>
        
        
        
         <tr>
            <td align="right">图档职责</td>
            <td><select name="GicJobid" id="GicJobid" style="width: 380px;"  dataType="Require"  msg="未选择图档上传职位">     
             <option value='' selected>--请选择--</option>
			<?php 
	          $mySql="SELECT j.Id,j.GroupName,m.Number,m.Name as staffname FROM $DataIn.staffgroup  j
			          LEFT JOIN $DataPublic.staffmain M on J.GroupId=M.GroupId
	                  WHERE  J.Id in(5,27,34) AND M.Estate>0 And M.cSign = '7' order by j.Id,j.GroupName";
	  
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $jobId=$myrow["Id"];
			     $jobName=$myrow["GroupName"];
				 $Number=$myrow["Number"];
				 $staffname=$myrow["staffname"];
				 
				   echo "<option value='$jobId|$Number'>$jobName-$staffname</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }			
		    ?>	
            <option value='0|0'>不需传图档</option>	 
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

