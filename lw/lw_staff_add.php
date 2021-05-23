<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 新增劳务员工资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
            <td align="right">身份证号</td>
            <td><input name="Idcard" type="text" id="Idcard" style='width:380px' maxlength="20" onblur="checkIdCard(this);" oninput="idCardonChange();"  onchange="idCardonChange();" dataType="IdCard"  msg="身份证号码不正确"> &nbsp; 
                    <span name="checkInfo" id="checkInfo" style="color:#FF0000;display:none;">*身份证号已存在!</span>
             </td>
          </tr>

		<tr>
            <td align="right" scope="col">姓&nbsp;&nbsp;&nbsp;&nbsp;名</td>
            <td scope="col"><input name="Name" type="text" id="Name" style="width:380px" maxlength="8" dataType="Chinese" msg="只允许中文"> </td>
          </tr>
		<tr>
		  <td colspan="2" scope="col">基本信息</td>
		  </tr>
		<tr>
		  <td align="right" scope="col">性 &nbsp;&nbsp;&nbsp;别</td>
		  <td scope="col">
		  <select name="Sex" id="Sex" style="width:380px" dataType="Require"  msg="未选择性别" >
		    <option value="" selected>--请选择--</option>
		    <option value="1">男 </option>
		    <option value="0">女 </option>
          </select>
		    </td>
		  </tr>
				<tr>
            <td height="23" align="right" scope="col">民&nbsp;&nbsp;&nbsp;&nbsp;族</td>
            <td scope="col">
			<select class=selet id=Nation style="width:380px" name=Nation dataType="Require"  msg="未选择民族">
			 <option value="" selected>--请选择--</option>
              <?php 
			 $Result2 = mysql_query("SELECT Id,Name FROM $DataPublic.nationdata WHERE Estate=1 order by Id",$link_id);
			 if($myRow2 = mysql_fetch_array($Result2)){
				do{
					echo" <option value='$myRow2[Id]'>$myRow2[Name]</option>";
					}while($myRow2 = mysql_fetch_array($Result2));
				}
			 ?>
            </select>
			</td>
          </tr>
				<tr>
				  <td align="right" scope="col">籍&nbsp;&nbsp;贯</td>
		          <td scope="col"><select name="Rpr" id="select2" style="width:380px" dataType="Require"  msg="未选择籍贯">
		            <option value="" selected>--请选择--</option>
					  <?php 
					 $Result3 = mysql_query("SELECT Id,Name FROM $DataPublic.rprdata WHERE Estate=1 order by Id",$link_id);
					 if($myRow3 = mysql_fetch_array($Result3)){
						do{
							echo" <option value='$myRow3[Id]'>$myRow3[Name]</option>";
							}while($myRow3 = mysql_fetch_array($Result3));
						}
					 ?>
                  </select>
		          </td>
          </tr>

          <tr>
            <td align="right">婚姻状况</td>
            <td><select name="Married" id="Married" style="width:380px" dataType="Require"  msg="未选择婚姻状况">
              <option value="">--请选择--</option>
              <option value="1">未 婚</option>
              <option value="0">已 婚</option>
            </select>
            </td>
          </tr>          
          <tr>
            <td width="113" align="right">出生日期</td>
            <td><input name="Birthday" type="text" id="Birthday" style="width:380px" maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="生日日期不正确" readonly>
            </td>
          </tr>
          <tr>
            <td align="right">照 &nbsp;&nbsp;&nbsp;片</td>
            <td><input name="Photo" type="file" id="Photo" style="width:380px" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="9" Cel="1"></td>
          </tr>
          <tr>
            <td align="right">身 份 证</td>
            <td><input name="IdcardPhoto" type="file" id="IdcardPhoto" style="width:380px" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="10" Cel="1"></td>
          </tr>
		  <tr>
            <td align="right">健康体检</td>
            <td><input name="HealthPhoto" type="file" id="HealthPhoto" style="width:380px" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="10" Cel="1"></td>
          </tr>
          
          <tr>
            <td align="right">家庭地址</td>
            <td><textarea name="Address" type="text" id="Address" style="width:380px" rows="3"  ></textarea></td>
          </tr>
        
          <tr>
            <td colspan="2"><div align="left">公司信息</div></td>
          </tr>
     	  <tr>
			  <td align="right" scope="col">劳务公司</td>
	          <td scope="col"><select name="CompanyId" id="CompanyId" style="width:380px" dataType="Require"  msg="未选择籍贯">
	            <option value="" selected>--请选择--</option>
				  <?php 
				 $Result4 = mysql_query("SELECT CompanyId,Forshort FROM $DataPublic.lw_company WHERE Estate=1 order by Id",$link_id);
				 if($myRow4 = mysql_fetch_array($Result4)){
					do{
						echo" <option value='$myRow4[CompanyId]'>$myRow4[Forshort]</option>";
						}while($myRow4 = mysql_fetch_array($Result4));
					}
				 ?>
              </select>
	          </td>
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
            <td align="right">部&nbsp;&nbsp;&nbsp;&nbsp;门</td>
            <td>
			<?php 
             $SelectFrom="";
             include"../model/subselect/BranchId.php";
            ?>
            </select>              
            </td>
          </tr>
          <tr>
            <td align="right">小&nbsp;&nbsp;&nbsp;&nbsp;组</td>
            <td><?php 
             $SelectFrom="";
             include"../model/subselect/GroupIdType.php";
            ?>
            </td>
          </tr>
          <tr>
            <td align="right">职&nbsp;&nbsp;&nbsp;&nbsp;位</td>
            <td>
			<?php 
             $SelectFrom="";
             include"../model/subselect/JobIdType.php";
            ?>          
            </td>
          </tr>
          
          <tr>
            <td align="right">考勤状态</td>
            <td><select name="KqSign"  id="KqSign" style="width:380px" dataType="Require"  msg="未选择婚姻状况">
              <option value="">--请选择--</option>
              <option value="1">考勤</option>
              <option value="2">考勤参考</option>
              <option value="3">无须考勤</option>
            </select>
            </td>
          </tr>     
          
          <tr>
            <td><div align="right">ID卡号</div></td>
            <td><input name="IdNum" type="text" id="IdNum" style="width:380px"  maxlength="10"></td>
          </tr>
          <tr>
            <td align="right">移动电话</td>
            <td><input name="Mobile" type="text" id="Mobile" style="width:380px"  maxlength="16"></td>
          </tr>

          <tr>
            <td align="right">电子邮件</td>
            <td><input name="eMail" type="text" id="eMail" style="width:380px"  maxlength="50" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>
          <tr>
          <td align="right">微信</td>
            <td><input name="Weixin" type="text" id="Weixin" style="width:380px"  maxlength="50" require="false"></td>
          </tr>
          <tr>          
          <tr>
            <td align="right">入职日期</td>
            <td><input name="ComeIn" type="text" id="ComeIn" value="<?php  echo date("Y-m-d");?>" style="width:380px"  maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="入职日期不正确" readonly>
            </td>
          </tr>

          <tr>
            <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" style="width:380px" rows="4" id="Remark"></textarea></td>
          </tr>
        </table>
</td></tr></table>
<input name="Number" type="hidden" id="Number" value="<?php  echo $Number?>">
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>

<script language="JavaScript" type="text/JavaScript">
function idCardonChange()
{
   document.getElementById("checkInfo").style.display="none";
}

function checkIdCard(e){
     var idcard=e.value;
     if (idcard.length>=10 && idcard.length<=18){
	     document.getElementById("checkInfo").style.display="none";
	      url="lw_staff_info_ajax.php?IdCard="+idcard+"&Action=IdCard";
		     var ajax=InitAjax(); 
		     ajax.open("GET",url,true);
		     ajax.onreadystatechange =function(){
				 if(ajax.readyState==4){
					     if (ajax.responseText=="Y"){
						      document.getElementById("checkInfo").style.display="";
					     }
				   }  
		      }  
		     ajax.send(null);
    }
}


</script>