<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-20 
//加入血型字段 EWEN 2012-10-29
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新员工资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION['nowWebPage']; 
//步骤3：//需处理
$mainRow=mysql_fetch_array(mysql_query("SELECT M.Id,M.WorkAdd,M.Number,M.IdNum,M.Name,M.Nickname,M.Grade,M.KqSign,M.BranchId,M.GroupId,M.GroupEmail,M.OffStaffSign,
M.JobId,M.Mail,M.AppleID,M.ExtNo,M.ComeIn,M.ContractSDate,M.ContractEDate,M.AttendanceFloor,M.Introducer,M.Estate,M.Locks,M.Date,M.Operator,M.FormalSign,S.Sex,S.BloodGroup,S.Nation,S.Rpr,S.Education,S.Married,
S.Birthday,S.Photo,S.IdcardPhoto,S.Idcard,S.Address,S.Postalcode,S.Tel,S.Mobile,S.Dh,S.Bank,S.Note,S.InFile,S.HealthPhoto,S.vocationHPhoto,M.cSign,I.Name as StaffName,DU.Description AS Jobduties,S.ClothesSize
FROM $DataPublic.staffmain M
LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
LEFT JOIN  $DataPublic.staffmain I ON I.Number=M.Introducer
 LEFT JOIN $DataPublic.staff_jobduties  DU ON DU.Number=M.Number
WHERE M.Id='$Id' LIMIT 1",$link_id));
$Type=$mainRow["Type"];
$TempType="TypeSTR".strval($Type);
$$TempType="selected";
$WorkAdd=$mainRow["WorkAdd"];
$floorAdd = $mainRow["AttendanceFloor"];
$Number=$mainRow["Number"];
$IdNum=$mainRow["IdNum"];

$StaffName=$mainRow["StaffName"];

$Introducer=$mainRow["Introducer"];
$Name=$mainRow["Name"];
$Nickname=$mainRow["Nickname"];
$Sex=$mainRow["Sex"];
$Nation=$mainRow["Nation"];
$Education=$mainRow["Education"];
$Married=$mainRow["Married"];
$Rpr=$mainRow["Rpr"];
$Birthday=$mainRow["Birthday"];
$Tel=$mainRow["Tel"];
$Postalcode=$mainRow["Postalcode"];
$Address=$mainRow["Address"];
$Idcard=$mainRow["Idcard"];
$IdcardPhoto=$mainRow["IdcardPhoto"];
$HealthPhoto=$mainRow["HealthPhoto"];
$vocationHPhoto=$mainRow["vocationHPhoto"];
$Photo=$mainRow["Photo"];
$Mobile=$mainRow["Mobile"];
$Dh=$mainRow["Dh"];
$ExtNo=$mainRow["ExtNo"];
$ComeIn=$mainRow["ComeIn"];
$Jobduties=$mainRow["Jobduties"];
$ClothesSize=$mainRow["ClothesSize"];
$ContractSDate=$mainRow["ContractSDate"];
$ContractEDate=$mainRow["ContractEDate"];

$OffStaffSign=$mainRow["OffStaffSign"];
$Mail=$mainRow["Mail"];
$GroupEmail=$mainRow["GroupEmail"];
$AppleID=$mainRow["AppleID"];
$Weixin=$mainRow["Weixin"];
$LinkedIn=$mainRow["LinkedIn"];

$Bank=$mainRow["Bank"];
$Note=$mainRow["Note"];
$InFile=$mainRow["InFile"];
$GroupId=$mainRow["GroupId"];
$BloodGroup=$mainRow["BloodGroup"];
$FormalSign=$mainRow["FormalSign"];
if($FormalSign==1){$selected1="selected";$selected2="";}
else {$selected2="selected";$selected1="";}
$cSign=$mainRow["cSign"];
// include "../model/subprogram/read_datain.php";
 
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../admin/subprogram/add_model_t.php";
 
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td height="16" scope="col">基本信息</td>
            <td scope="col">(部门、职位、等级、考勤状态请使用相应的调动功能)</td>
          </tr>

          <tr>
            <td align="right" scope="col">介 绍 人</td>
            <td scope="col"><input name="StaffName" type="text" id="StaffName" style="width:380px" value="<?php echo $StaffName?>"  onclick="SearchData('<?php  echo $funFrom?>',1,-1,1)" readonly="readonly" /> 
		                                 <input name="Introducer" id="Introducer"  type='hidden' value="<?php echo $Introducer?>"/> </td>
			</td>
          </tr>
          <tr>
            <td align="right" scope="col">姓&nbsp;&nbsp;&nbsp;&nbsp;名</td>
            <td scope="col"><input name="Name" type="text" id="Name" value="<?php  echo $Name?>" style="width:380px;" maxlength="8" dataType="Chinese" msg="只允许中文">
            </td>
          </tr>
<tr>
            <td align="right" scope="col">昵&nbsp;&nbsp;&nbsp;&nbsp;称</td>
            <td scope="col"><input name="Nickname" type="text" id="Nickname" value="<?php  echo $Nickname?>" style="width:380px;">
          </td>
        </tr>         
         <tr>
            <td align="right" scope="col">性 &nbsp;&nbsp;&nbsp;别</td>
            <td scope="col">
			<select name="Sex" id="Sex" style="width:380px;">
			<?php 
			if($Sex==1){
				echo"<option value='1' selected>男</option>";
				echo"<option value='0'>女</option>";
				}
			else{
				echo"<option value='1'>男</option>";
				echo"<option value='0' selected>女</option>";
				}
			?>
            </select></td>
          </tr>
          <tr>
            <td align="right" scope="col">民&nbsp;&nbsp;&nbsp;&nbsp;族</td>
            <td scope="col">
			<select id=Nation size=1 name=Nation style="width:380px;">
			 <?php 
			 $nResult = mysql_query("SELECT Id,Name FROM $DataPublic.nationdata WHERE Estate=1 order by Id",$link_id);
			 if($nRow = mysql_fetch_array($nResult)){
				do{
					if($nRow["Id"]==$Nation){
						echo"<option value='$nRow[Id]' selected>$nRow[Name]</option>";
						}
					else{
						echo"<option value='$nRow[Id]'>$nRow[Name]</option>";
						}
					}while ($nRow = mysql_fetch_array($nResult));
				}
			  ?>
            </select>
			</td>
          </tr>
          <tr>
            <td align="right" scope="col">户口原地</td>
            <td scope="col">
			<select name="Rpr" size="1" class=select id=Rpr style="width:380px;">
			 <?php  
			 $rResult = mysql_query("SELECT Id,Name FROM $DataPublic.rprdata WHERE Estate=1 order by Id",$link_id);
			 if($rRow = mysql_fetch_array($rResult)){
				do{
					if($Rpr==$rRow["Id"]){
						echo"<option value='$rRow[Id]' selected>$rRow[Name]</option>";
						}
					else{
						echo"<option value='$rRow[Id]'>$rRow[Name]</option>";
						}
					}while ($rRow = mysql_fetch_array($rResult));
				}
			  ?>
		    </select>
			</td>
          </tr>
          <tr>
            <td align="right">教育程度</td>
            <td>
			<select name="Education" size="1" id="select" style="width:380px;">
			<?php  
			 $eResult = mysql_query("SELECT Id,Name FROM $DataPublic.education WHERE Estate=1 order by Id",$link_id);
			 if($eRow = mysql_fetch_array($eResult)){
				do{
					if($Education==$eRow["Id"]){
						echo"<option value='$eRow[Id]' selected>$eRow[Name]</option>";
						}
					else{
						echo"<option value='$eRow[Id]'>$eRow[Name]</option>";
						}
					}while ($eRow = mysql_fetch_array($eResult));
				}
			  ?>
              </select>
            </td>
          </tr>
          <tr>
            <td align="right">婚姻状况</td>
            <td><select name="Married" size="1" id="Married" style="width:380px;">
			<?php 
			if($Married==0){				
				echo"<option value='1'>未婚</option>";
				echo"<option value='0' selected>已婚</option>";
				}
			else{
				echo"<option value='1' selected>未婚</option>";
				echo"<option value='0'>已婚</option>";
				}
			?>
            </select></td>
          </tr>
          <tr>
            <td width="112" align="right">出生日期</td>
            <td><input name="Birthday" type="text" id="Birthday" value="<?php  echo $Birthday?>" style="width:380px;" maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="生日日期不正确" readonly></td>
          </tr>
          <tr>
            <td align="right">紧急联系人-电话</td>
            <!--
            <td><input name="Tel" type="text" id="Tel" value="<php  echo $Tel?>" style="width:380px;" maxlength="14" require="false" dataType="Phone" msg="电话号码不正确"></td>
            -->
            <td><input name="Tel" type="text" id="Tel" value="<?php  echo $Tel?>" style="width:380px;" maxlength="40" ></td>
            
          </tr>
          <tr>
            <td align="right">邮政编码 </td>
            <td><input name="Postalcode" type="text" id="Postalcode" value="<?php  echo $Postalcode?>" style="width:380px;" maxlength="6" require="false" dataType="Zip" msg="邮政编码不存在"></td>
          </tr>
          <tr>
            <td align="right">家庭地址</td>
            <td><input name="Address" type="text" id="Address" value="<?php  echo $Address?>" style="width:380px;" maxlength="100"></td>
          </tr>
          <tr>
            <td align="right">身份证号</td>
            <td><input name="Idcard" type="text" id="Idcard" value="<?php  echo $Idcard?>" style="width:380px;" maxlength="18">
            </td>
          </tr>
          <tr>
            <td align="right">身 份 证</td>
            <td>
			<input name="IdcardPhoto" type="file" id="IdcardPhoto" style="width:380px;" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="13" Cel="1">
			</td>
          </tr>
		 <?php 
		if($IdcardPhoto==1){
			echo"<tr><td>&nbsp;</td><td>
			<input type='checkbox' name='oldIphoto' id='oldIphoto' value='1'><LABEL for='oldIphoto'>删除已传身份证扫描档</LABEL></td></tr>";
			$RowNext=15;
			}
		else{
			$RowNext=14;
			}
		?>
          <tr>
            <td align="right">照 &nbsp;&nbsp;&nbsp;片</td>
            <td><input name="Photo" type="file" id="Photo" style="width:380px;" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="<?php  echo $RowNext?>" Cel="1"></td></tr>
			<?php 
			if($Photo==1){
				echo"<tr><td>&nbsp;</td><td>
				<input type='checkbox' name='oldPhoto' id='oldPhoto' value='1'><LABEL for='oldPhoto'>删除已传照片</LABEL></a></td></tr>";
				$RowNext=$RowNext+2;
				}
			else{
				$RowNext++;
				}
			?>
			 <tr>
			 <tr>
            <td align="right">照片PNG</td>
            <td><input name="PngPhoto" type="file" id="PngPhoto" style="width:380px;" dataType="Filter" msg="非法的文件格式" accept="png" Row="13" Cel="1"></td></tr>
			 <tr>
            <td align="right">健康体检</td>
            <td>
			<input name="HealthPhoto" type="file" id="HealthPhoto" style="width:380px;" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="13" Cel="1">
			</td>
          </tr>
		  <?php 
			if($HealthPhoto==1){
				echo"<tr><td>&nbsp;</td><td>
				<input type='checkbox' name='oldHPhoto' id='oldHPhoto' value='1'><LABEL for='oldHPhoto'>删除已传照健康体检证</LABEL></a></td></tr>";
				$RowNext=$RowNext+2;
				}
			else{
				$RowNext++;
				}
			?>
			<tr>
            
			 <tr>
            <td align="right">职业体检</td>
            <td>
			<input name="vocationHPhoto" type="file" id="vocationHPhoto" style="width:380px;" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="13" Cel="1">
			</td>
          </tr>
		  <?php 
			if($vocationHPhoto==1){
				echo"<tr><td>&nbsp;</td><td>
				<input type='checkbox' name='oldVPhoto' id='oldVPhoto' value='1'><LABEL for='oldVPhoto'>删除已传职业体检证</LABEL></a></td></tr>";
				$RowNext=$RowNext+2;
				}
			else{
				$RowNext++;
				}
			?>
			<tr>            
            
            <tr>
		    <td align="right">血型</td>
		    <td><select name="BloodGroup" size="1" id="BloodGroup" style="width:380px" datatype="Require"  msg="未选择婚姻状况">
		      <option value="">--请选择--</option>
              <?php 
			  if($BloodGroup==0){
		      	echo"<option value='0' selected>未设置</option>";
			  	}
			  else{
				echo"<option value='0'>未设置</option>";
				}
              $checkBGSql=mysql_query("SELECT Id,Name FROM $DataPublic.bloodgroup_type WHERE Estate=1 ORDER BY Id",$link_id);
			  if($checkBGRow=mysql_fetch_array($checkBGSql)){
				  do{
					  if($BloodGroup==$checkBGRow["Id"]){
						  echo"<option value='$checkBGRow[Id]' selected>$checkBGRow[Name]</option>";
						  }
					  else{
					  	echo"<option value='$checkBGRow[Id]'>$checkBGRow[Name]</option>";
					  	}
					  }while($checkBGRow=mysql_fetch_array($checkBGSql));
				  }
			  ?>
		      </select></td>
	      </tr>
            <td align="right">入职档案</td>
            <td><input name="InFile" type="file" id="InFile" style="width:380px;" dataType="Filter" msg="非法的文件格式" accept="pdf" Row="<?php  echo $RowNext?>" Cel="1"></td></tr>
			<?php 
			if($InFile==1){
				echo"<tr><td>&nbsp;</td><td>
				<input type='checkbox' name='oldInFile' id='oldInFile' value='1'><LABEL for='oldInFile'>删除已传档案</LABEL></a></td></tr>";
				}
			?>
          <tr>
            <td height="22" colspan="2"><div align="left">公司信息</div></td>
          </tr>
		   <tr>
		  <td align="right">员工类别</td>
		  <td><select name="FormalSign" id="FormalSign" style="width:380px;" dataType="Require"  msg="未选择">
			 <option value="1" <?php  echo $selected1?>>正式工</option>
			 <option value="2" <?php  echo $selected2?>>试用期</option>
			 </select></td>
		  </tr>
		   <tr>
		     <td align="right">工作地点</td>
		     <td><?php 
             include "../model/subselect/WorkAdd.php";
			 ?></td>
        </tr>
        
        <tr>
        	<td align="right">考勤楼层</td>
        	<td>
	        	<?php
		        	include "../model/subselect/FloorAdd.php";
	        	?>
        	</td>
        </tr>
          <tr>
            <td align="right">部门小组</td>
            <td>
              <?php 
			     include"../model/subselect/GroupIdType.php";
			?>
            </select></td>
          </tr>
          <tr>
            <td align="right">ID 卡 号</td>
            <td><input name="IdNum" type="text" id="IdNum" value="<?php  echo $IdNum?>" style="width:380px;" maxlength="10"></td>
          </tr>
          <tr>
            <td align="right">移动电话</td>
            <td><input name="Mobile" type="text" id="Mobile" value="<?php  echo $Mobile?>" style="width:380px;" maxlength="16"></td>
          </tr>
          <tr>
            <td align="right">短&nbsp;&nbsp;&nbsp;&nbsp;号</td>
            <td><input name="Dh" type="text" id="Dh" value="<?php  echo $Dh?>" style="width:380px;"></td>
          </tr>
          <tr>
            <td align="right">分 机 号</td>
            <td><input name="ExtNo" type="text" id="ExtNo" value="<?php  echo $ExtNo?>" style="width:380px;"></td>
          </tr>      
          <tr>
          <td align="right">微信</td>
            <td><input name="Weixin" type="text" id="Weixin" style="width:380px"   value="<?php  echo $Weixin?>" ></td>
          </tr>
          <tr>
          <td align="right">LinkedIn</td>
            <td><input name="LinkedIn" type="text" id="LinkedIn" style="width:380px"  value="<?php  echo $LinkedIn?>"  maxlength="50" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>
              
          <tr>
            <td align="right">入职时间</td>
            <td><input name="ComeIn" type="text" id="ComeIn" value="<?php  echo $ComeIn?>" style="width:380px;" maxlength="10"  dataType="Date" format="ymd" msg="入职日期不正确" ></td>
          </tr>
          
          <tr>
            <td align="right">合同起始日期</td>
            <td><input name="ContractSDate" type="text" id="ContractSDate" value="<?php  echo $ContractSDate;?>" style="width:380px"  maxlength="10"  msg="合同起始日期不正确" >
            </td>
          </tr>
          
          <tr>
            <td align="right">合同结束日期</td>
            <td><input name="ContractEDate" type="text" id="ContractEDate" value="<?php   echo $ContractEDate;?>" style="width:380px"  maxlength="10"   msg="合同结束日期不正确" >
            </td>
          </tr>
           <tr>
            <td align="right">公司邮箱</td>
            <td><input name="GroupEmail" type="text" id="GroupEmail" value="<?php  echo $GroupEmail?>" style="width:380px;" maxlength="50" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>
         
          <tr>
            <td align="right">电子邮件</td>
            <td><input name="Mail" type="text" id="Mail" value="<?php  echo $Mail?>" style="width:380px;" maxlength="50" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>
          
          <tr>
            <td align="right">AppleID</td>
            <td><input name="AppleID" type="text" id="AppleID" value="<?php  echo $AppleID?>" style="width:380px;" maxlength="50" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>          

          <tr>
            <td align="right" valign="top">工衣尺寸</td>
            <td><textarea name="ClothesSize" style="width:380px" rows="3" id="ClothesSize"><?php  echo $ClothesSize?></textarea></td>
          </tr>
          
          <tr>
            <td>其它信息</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td align="right" valign="top">工作职责</td>
            <td><textarea name="Jobduties" id="Jobduties" style="width:380px;" rows="3" id="Note"><?php  echo $Jobduties?></textarea></td>
          </tr>
          <tr>
            <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Note" style="width:380px;" rows="3" id="Note"><?php  echo $Note?></textarea></td>
          </tr>
          <tr>
            <?php 
	            $checkSTR=$OffStaffSign==1?" checked ":"";
            ?>
            <td colspan="2"><input name='OffStaffSign' type='checkbox' id='OffStaffSign' value='1' <?php echo $checkSTR;?>><LABEL for='Locks'>是否属编外人员</LABEL></td>
          </tr>
          
   </table>
</td></tr></table>
<?php 
//步骤5：
include "../admin/subprogram/add_model_b.php";
//加载快速选择员工列表功能
$staffName_InputID="StaffName";  //显示员工姓名
$staffNumber_InputID="Introducer"; //获取员工Number
include  "../model/subprogram/staffname_input.php";
?>

<script language="JavaScript" type="text/JavaScript">

function SearchData(fSearchPage,SearchNum,Action,TimeId){//来源页面，可取记录数，动作（因共用故以参数区别）
	var num=Math.random();  
			var tSearchPage="../public/staff";
			BackData=window.showModalDialog(tSearchPage+"_s1.php?r="+num+"&Action="+Action+"&tSearchPage="+tSearchPage+"&fSearchPage="+fSearchPage+"&SearchNum="+SearchNum,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
			if(!BackData){  
		            if(document.getElementById('SafariReturnValue')){
			        var SafariReturnValue=document.getElementById('SafariReturnValue');
			        BackData=SafariReturnValue.value;
			        SafariReturnValue.value="";
			        }
		      }	
			  //alert (TimeId);
			  TimeId=TimeId*1;
                if(BackData){
                         switch(TimeId){
                              case 1: 
							    //alert (BackData);
								var FieldArray=BackData.split("^^");//分拆记录中的字段
							  	document.getElementById("StaffName").value=FieldArray[1];
								document.getElementById("Introducer").value=FieldArray[0];
								
							  	break;
                              case 2: 
							  	document.getElementById("StaffName").value=BackData;
							 	 break;
                               }
                   }
	}

</script>
