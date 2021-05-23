<?php 
/*
$DataPublic.net_cpdata
$DataPublic.staffmain
二合一已更新
电信-joseph
*/
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
      <script type='text/javascript' src='../model/inputSuggest1.0c.js'></script>";
//步骤2：
ChangeWtitle("$SubCompany 更新设备资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤3：//需处理

$myResult =mysql_query("SELECT D.*,T.MainTypeID  FROM $DataPublic.fixed_assetsdata D 
                                        LEFT JOIN $DataPublic.oa2_fixedsubtype T ON T.Id=D.TypeId 
					WHERE D.Id='$Id' ",$link_id);  //那个公司收录的只能在那个公司更改
if($upData = mysql_fetch_array($myResult)){
	$theId=$upData["Id"];
	$CpName=$upData["CpName"];
	$TypeId=$upData["TypeId"];
	$thBranchId=$upData["BranchId"];
	$Buyer=$upData["Buyer"];
	$Qty=$upData["Qty"];
	$price=$upData["price"];
	$Model=$upData["Model"];
	$SSNumber=$upData["SSNumber"];
	$BuyDate=$upData["BuyDate"];
	$Warranty=$upData["Warranty"];
	$MTCycle=$upData["MTCycle"];
	$ServiceLife=$upData["ServiceLife"];
	$Retiredate=$upData["Retiredate"];
	$Remark=$upData["Remark"];
	$Attached=$upData["Attached"];
	$theCompanyId=$upData["CompanyId"];
	$theAttached=$upData["Attached"];
	$theEstate=$upData["Estate"];
	$BuyerName=$upData["Buyer"];
	//$BuyerName=$upData["BuyerName"];
	$mainTypeId=$upData["MainTypeID"];
}
else 
{
	echo "无更新权限！";
	return false;
	
}
// echo "mainTypeId:" . $mainTypeId;
//echo "Buyer: $Buyer <br>";
//$User=$upData["User"];
//echo "theCompanyId:$theCompanyId";
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
?>
<script  type="text/javascript">
/*
function SearchData(SearchNum,Action){//来源页面，可取记录数，动作（因共用故以参数区别）
	var num=Math.random();  
	switch(Action){
		case 0://
			BackStockId=window.showModalDialog("fixed_assets_s1.php?r="+num+"&Action="+Action+"&SearchNum="+SearchNum,"BackStockId","dialogHeight =700px;dialogWidth=500px;center=yes;scroll=yes");
			if(BackStockId){
				var CL=BackStockId.split("^^");
				document.form1.User.value=CL[0];
				document.form1.UserName.value=CL[1];
				}
		break;
		}
	}

*/
/*
function SearchData(SearchNum,Action){//来源页面，可取记录数，动作（因共用故以参数区别）
	var num=Math.random();  
	switch(Action){
		case 0://
			BackStockId=window.showModalDialog("fixed_assets_s1.php?r="+num+"&Action="+Action+"&SearchNum="+SearchNum,"BackStockId","dialogHeight =700px;dialogWidth=500px;center=yes;scroll=yes");
			if(BackStockId){
				var CL=BackStockId.split("^^");
				document.form1.User.value=CL[0];
				document.form1.UserName.value=CL[1];
				}
		break;

		case 1://
			BackStockId=window.showModalDialog("fixed_assets_s1.php?r="+num+"&Action="+Action+"&SearchNum="+SearchNum,"BackStockId","dialogHeight =700px;dialogWidth=500px;center=yes;scroll=yes");
			if(BackStockId){
				var CL=BackStockId.split("^^");
				document.form1.Buyer.value=CL[0];
				document.form1.BuyerName.value=CL[1];
				}
		break;
		case 2://
			BackStockId=window.showModalDialog("fixed_assets_s1.php?r="+num+"&Action="+Action+"&SearchNum="+SearchNum,"BackStockId","dialogHeight =700px;dialogWidth=500px;center=yes;scroll=yes");
			if(BackStockId){
				var CL=BackStockId.split("^^");
				document.form1.maintainer.value=CL[0];
				document.form1.MTName.value=CL[1];
				}
		break;		
	
	}
}
*/
function ResetDateS(){
	 document.getElementById("Retiredate").value="0000-00-00";
}
</script>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
      
			 <tr>
			    <td align="right" scope="col">购买人</td>                
				<td><input name="BuyerName" type="text" id="BuyerName" size="53" value="<?php  echo $BuyerName?>" dataType="Require"  msg="未填写">
             <!--   <input type="button" name="Submit" value="..." title="购买人" onclick="SearchData(1,1)">-->
                <input name='Buyer' type='hidden' id='Buyer' value="<?php  echo $Buyer?>">
                </td>
 
		     </tr>
                   
			  <tr>
				<td width="150" align="right" scope="col">资产编号</td>
				<td valign="middle" scope="col"><input name="CpName" type="text" id="CpName" size="53" maxlength="20" value="<?php  echo $CpName?>" dataType="Require"  msg="未填写" ></td>
			  </tr>
                            <tr>
			    <td width="150" align="right" scope="col">设备主分类</td>
			    <td valign="middle" scope="col"><select name="mainTypeId" id="mainTypeId" style="width: 300px;" onchange="mTypeChange(this)">
                  <?php            
				$checkSql=mysql_query("SELECT Id,Name FROM $DataPublic.oa1_fixedmaintype WHERE 1 AND Estate=1 ORDER BY Letter",$link_id);
				if($checkRow=mysql_fetch_array($checkSql)){
					echo"<option value='' selected>请选择</option>";
					do{
						$Id=$checkRow["Id"];
						$Name=$checkRow["Name"];
                                                
						if($mainTypeId==$Id){
							echo"<option value='$Id' selected>$Name</option>";
							}
						else{
							echo"<option value='$Id'>$Name</option>";
							}
						}while($checkRow=mysql_fetch_array($checkSql));
					}
				?>
                      </select></td>
	                </tr>
			  <tr>
			    <td width="150" align="right" scope="col">设备分类</td>
			    <td valign="middle" scope="col"><select name="TypeId" id="TypeId" style="width: 300px;"  dataType="Require"  msg="未选择">
                  <?php 
				$checkSql=mysql_query("SELECT Id,Name FROM $DataPublic.oa2_fixedsubtype WHERE 1 AND Estate=1 ORDER BY Id",$link_id);
				if($checkRow=mysql_fetch_array($checkSql)){					
					do{
						$Id=$checkRow["Id"];
						$Name=$checkRow["Name"];
						if($TypeId==$Id){
							echo"<option value='$Id' selected>$Name</option>";
							}
						else{
							echo"<option value='$Id'>$Name</option>";
							}						
						
						}while($checkRow=mysql_fetch_array($checkSql));
					}
				?>
             </select></td>
	    </tr>
			<tr>
                <td align="right" scope="col">使用部门</td>
                <td valign="middle" scope="col">
             	<select name="BranchId" id="BranchId" style="width:300px" dataType="Require"  msg="未选择部门">
				<?php 
				$B_Result=mysql_query("SELECT Id,Name FROM $DataPublic.branchdata WHERE Estate=1 order by Id",$link_id);
				if($B_Row = mysql_fetch_array($B_Result)) {
				do{
					$B_Id=$B_Row["Id"];
					$B_Name=$B_Row["Name"];
					
					if($thBranchId==$B_Id){
						echo "<option value='$B_Id' selected>$B_Name</option>";
						}
					else{
						echo "<option value='$B_Id'>$B_Name</option>";
						}					
					}while ($B_Row = mysql_fetch_array($B_Result));
				}
				?>			  
            	</select>
             </td>             
	    	</tr>
            
			 <tr>
             <!--
			    <td align="right" scope="col">购买人</td>                
				<td>
              
                <input name='UserName' type='text' id='UserName' onclick="SearchRecord('staff','<=$funFrom?>',1,10)"  value="<=$UserName?>"  readonly="readonly" />
                <input name='User' type='hidden' id='User' value="<=$User?>">  -->
               <?php 
			   /*
			   <select name="User" id="User" style="width: 300px;" dataType="Require"  msg="未选择">
				$CheckSql = mysql_query("SELECT Number,Name FROM $DataPublic.staffmain WHERE Estate=1 ORDER BY BranchId,JobId,Number",$link_id);
				if($CheckRow=mysql_fetch_array($CheckSql)){
					do{
						$Number=$CheckRow["Number"];
						$Name=$CheckRow["Name"];
						if($User==$Number){
							echo "<option value='$Number' selected>$Name</option>";
							}
						else{
							echo "<option value='$Number'>$Name</option>";
							}
						}while($CheckRow=mysql_fetch_array($CheckSql));
					}
					</select> 
					*/
					
				?>
                <!--              
                </td>
 
		     </tr>
              -->        
			  <tr>
                <td align="right" scope="col">数量</td>
                <td valign="middle" scope="col"><input name="Qty" type="text" id="Qty" value="<?php  echo $Qty?>" size="53" dataType="Number"  msg="格式不对" readonly="readonly"></td>
	    	</tr>
            
			 <tr>
                <td align="right" scope="col">价格</td>
                <td valign="middle" scope="col"><input name="price" type="text" id="price"  value="<?php  echo $price?>" size="53" dataType="Currency"  msg="格式不对"></td>
	    	</tr>
                        
			  <tr>
			    <td align="right" scope="col">名称-型号</td>
			    <td valign="middle" scope="col"><input name="Model" type="text" id="Model" value="<?php  echo $Model?>" size="53" dataType="Require"  msg="未填写"></td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">服务编号</td>
			    <td valign="middle" scope="col"><input name="SSNumber" type="text" id="SSNumber" value="<?php  echo $SSNumber?>" size="53" dataType="Require"  msg="未填写"></td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">购买日期</td>
<td valign="middle" scope="col"><input name="BuyDate" type="text" id="BuyDate" value="<?php  echo $BuyDate?>" size="53" onfocus="new WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly></td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">保修期</td>
			    <td valign="middle" scope="col"><input name="Warranty" type="text" id="Warranty" value="<?php  echo $Warranty?>" size="53" dataType="Number"  msg="格式不对"></td>
		      </tr>
              
			<tr>
                <td align="right" scope="col">维护周期</td>
                <td valign="middle" scope="col"><input name="MTCycle" type="text" id="MTCycle" value="<?php  echo $MTCycle?>" size="40" dataType="Number"  msg="格式不对"  >按天来计算</td>
	    	</tr>              
              
 			  <tr>
			    <td align="right" scope="col">使用年限</td>
			    <td valign="middle" scope="col"><input name="ServiceLife" type="text" id="ServiceLife"  value="<?php  echo $ServiceLife?>" size="53" dataType="Currency"  msg="格式不对"></td>
		      </tr>             
			  <tr>
			    <td align="right" scope="col">报废日期</td>
			    <td valign="middle" scope="col"><input name="Retiredate" type="text" id="Retiredate" value="<?php  echo $Retiredate?>" size="45" onfocus="new WdatePicker()"   readonly>		<input type="button" name="ResetDate" id="ResetDate" value="重置" onclick="ResetDateS();" />
                </td>
		      </tr>


			  <tr>
			    <td align="right" scope="col">销售商</td>
                              <input type="hidden"  name="oldCompanyId" id="oldCompanyId" value='<?php  echo $theCompanyId?>'/>   
			    <td valign="middle" scope="col"><select name="CompanyId" id="CompanyId" style="width: 300px;" onchange="selChange(this)" dataType="Require"  msg="未选择">
				<?php 
                                if($theCompanyId==-1){  
                                     echo "<option value='-1' selected>--</option>";
                                }
				$checkSql=mysql_query("SELECT CompanyId,Forshort FROM $DataPublic.dealerdata WHERE 1 ORDER BY Forshort",$link_id);
				if($checkRow=mysql_fetch_array($checkSql)){
					do{
						$CompanyId=$checkRow["CompanyId"];
						$Forshort=$checkRow["Forshort"];
						if($theCompanyId==$CompanyId){
							echo"<option value='$CompanyId' selected>$Forshort</option>";
							}
						else{
							echo"<option value='$CompanyId'>$Forshort</option>";
							}						
						
						}while($checkRow=mysql_fetch_array($checkSql));
					} 
                                 if($theCompanyId==-1){  
                                    
                                     echo "</select>&nbsp;<input name='ShowCompany' type='checkbox' id='ShowCompany'  onclick='ShowCompanyS(\"Show_Company\")' checked/>直接输入公司"; 
                                 }else{
                                     echo "</select>&nbsp;<input name='ShowCompany' type='checkbox' id='ShowCompany'  onclick='ShowCompanyS(\"Show_Company\")' />直接输入公司"; 
        
				}
				?>
		        </td>
	    </tr>
        
        <?php 
	 if($theCompanyId==-1)
	   {	 
	     $CompanyRow = mysql_fetch_array(mysql_query("SELECT Company,Name,Tel,Fax,Address,Remark   FROM $DataPublic.company_assets WHERE Mid=$theId ",$link_id));
	     //echo "SELECT Company,Name,Tel,Fax,Address,Remark   FROM $DataPublic.company_assets WHERE Mid=$theId ";
	     $NewCompany=$CompanyRow["Company"];
             $NewName=$CompanyRow["Name"];
	     $NewTel=$CompanyRow["Tel"];
             $NewFax=$CompanyRow["Fax"];
             $NewAddress=$CompanyRow["Address"];
             $NewRemark=$CompanyRow["Remark"];
			 
		echo "
                  <tr> <td  colspan=2>
                    <table width='760' border='0' align='center' cellspacing='5'  id='Show_Company'> 
			  <tr    >
                  <td  width='150' align='right' scope='col'>公司名称</td>
                  <td valign='middle' scope='col'><input name='NewCompany' type='text' id='NewCompany'  value='$NewCompany' size='53' ></td>        
                </tr>
                 <tr   >
                  <td align='right' scope='col'>联系人</td>
                  <td valign='middle' scope='col'><input name='NewName' type='text' id='NewName' value='$NewName' size='53' ></td>        
                </tr>
                 <tr  >
                  <td align='right' scope='col'>联系电话</td>
                  <td valign='middle' scope='col'><input name='NewTel' type='text' id='NewTel'  value='$NewTel'  size='53' ></td>        
                </tr>    
                 <tr>
                    <td align='right' scope='col'>通信地址</td>
                    <td valign='middle' scope='col' ><input name='NewAddress' type='text' require='false' id='NewAddress' value='$NewAddress'  size='60'  dataType='Limit' max='100' msg='必须在150个字之内'></td>
                  </tr>                         
                 <tr  >
                  <td align='right' scope='col'>其它资料</td>
                  <td valign='middle' scope='col'><textarea name='NewRemark' cols='53' rows='6' id='NewRemark'>$NewRemark</textarea></td>      
                </tr>
		</table></tr>";
	     }
             else{
                echo "<tr> <td  colspan=2>
                    <table width='760' border='0' align='center' cellspacing='5' style='display:none' id='Show_Company'>           
                <tr    >
                  <td  width='150' align='right' scope='col'>公司名称</td>
                  <td valign='middle' scope='col'><input name='NewCompany' type='text' id='NewCompany' size='53' ></td>        
                </tr>
                 <tr   >
                  <td align='right' scope='col'>联系人</td>
                  <td valign='middle' scope='col'><input name='NewName' type='text' id='NewName' size='53' ></td>        
                </tr>
                 <tr  >
                  <td align='right' scope='col'>联系电话</td>
                  <td valign='middle' scope='col'><input name='NewTel' type='text' id='NewTel' size='53' ></td>        
                </tr>    
                 <tr>
                    <td align='right' scope='col'>通信地址</td>
                    <td valign='middle' scope='col' ><input name='NewAddress' type='text' require='false' id='NewAddress' size='60' dataType='Limit' max='100' msg='必须在150个字之内'></td>
                  </tr>                         
                 <tr  >
                  <td align='right' scope='col'>其它资料</td>
                  <td valign='middle' scope='col'><textarea name='NewRemark' cols='53' rows='6' id='NewRemark'></textarea></td>      
                </tr>
 	  </table></tr>";
             }
             
            $AttachedResult = mysql_query("SELECT Id,FileName,Type  FROM $DataPublic.fixed_file WHERE Mid=$theId order by Type",$link_id); 
             while ($AttachedRow=mysql_fetch_array($AttachedResult)){
                 $Attached="Attached" . $AttachedRow["Type"];
                 $FileName=$AttachedRow["FileName"];
                 $$Attached="<a href='../download/fixedFile/$FileName' target='_blank'>预览</a>";
             }
	 ?>
       
		 <tr>
                      <td  colspan=2>
                       <table id="NoteTable"  width="760" border="0" align="center" cellspacing="5"> 
   
		 <tr>
			    <td  width="150" align="right" scope="col">物品照片</td>
			    <td valign="middle" scope="col">
                 <input name="Attached1" type="file" id="Attached1" size="41" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="0" Cel="1"><?php  echo $Attached1?>
			     </td>
	         </tr>
                  <tr>
			    <td  width="150" align="right" scope="col">说明书</td>
			    <td valign="middle" scope="col">
			      <input name="Attached2" type="file" id="Attached2" size="41" dataType="Filter" msg="非法的文件格式" accept="pdf,jpg" Row="1" Cel="1"><?php  echo $Attached2?> </td>
	          </tr>
                  <tr>
			  <td  width="150" align="right" scope="col">保修卡</td>
			  <td valign="middle" scope="col">
			  <input name="Attached3" type="file" id="Attached3" size="41" dataType="Filter" msg="非法的文件格式" accept="htm,html,pdf,jpg" Row="2" Cel="1"><?php  echo $Attached3?> </td>
	          </tr>
              <tr>
			  <td  width="150" align="right" scope="col">操作规程</td>
			  <td valign="middle" scope="col">
			  <input name="Attached4" type="file" id="Attached4" size="41" dataType="Filter" msg="非法的文件格式" accept="pdf" Row="3" Cel="1"><?php  echo $Attached4?></td>
	          </tr>
                            
	  </table>
       </td>
			  <tr>
			    <td align="right" valign="top" scope="col">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
			    <td valign="middle" scope="col"><textarea name="Remark" cols="53" rows="6" id="Remark"><?php  echo $Remark;?></textarea></td>
	    </tr>
        
			  <tr>
			    <td align="right" scope="col">状态</td>
			    <td valign="middle" scope="col"><select name="Estate" id="Estate" style="width: 300px;" dataType="Require"  msg="未选择">
				<?php 
				$TempEstateSTR="Estate".strval($theEstate); 
				$$TempEstateSTR="selected";
				echo"<option value='0' $Estate0>报废</option>";
				echo"<option value='1' $Estate1>使用中 </option>";
				echo"<option value='3' $Estate3>闲置</option>";
				?>
		        </select></td>
	    </tr>
                
	  </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
$checkSql=mysql_query("SELECT Id,MainTypeID,Name FROM $DataPublic.oa2_fixedsubtype WHERE 1 AND Estate=1 ORDER BY MainTypeID,Letter",$link_id);
while($checkRow=mysql_fetch_array($checkSql)){
    $sMid=$checkRow["MainTypeID"];
    $sId=$checkRow["Id"];
    $sType=$checkRow["Name"];
    $subType[]=array($sMid,$sId,$sType);
}


$StaffSql = mysql_query("SELECT M.Id,M.Number,M.Name, B.Name AS Branch
	FROM $DataPublic.staffmain M 
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId	
	WHERE 1 AND M.Estate='1' ORDER BY M.BranchId,M.JobId,M.ComeIn,M.Number",$link_id);

	while ($StaffRow = mysql_fetch_array($StaffSql)){
		$sNumber=$StaffRow["Number"];
                $sName=$StaffRow["Name"];
		$sBranch=$StaffRow["Branch"];

                $subName[]=array($sNumber,$sName,$sBranch);
	}
           
?>

<script language="JavaScript" type="text/JavaScript">
<!-- 
 window.onload = function(){
                var subName=<?php  echo json_encode($subName);?>;
                
		var sinaSuggest = new InputSuggest({
			input: document.getElementById('BuyerName'),
			poseinput: document.getElementById('Buyer'),
			data: subName,
			width: 290
		});
				
}

var subTypeName=<?php echo json_encode($subType);?>;
 //当状态改变的时候执行的函数   
 function userhandle()   
 {
	 
	 var title=document.getElementById('UserName').title;
	 var user=document.getElementById('User').value;
	 if(user!=title){
	      document.getElementById('User').value=title;
	 }


	
 }   
 //firefox下检测状态改变只能用oninput,且需要用addEventListener来注册事件。   
 if(/msie/i.test(navigator.userAgent))    //ie浏览器   
 {
	 document.getElementById('UserName').onpropertychange=userhandle;   
 }   
 else   
 {//非ie浏览器，比如Firefox   
	document.getElementById('UserName').addEventListener("input",userhandle,false);   
 }   


function ShowCompanyS(RowTemp){
	//var e=eval(RowTemp);
	var e=document.getElementById("Show_Company");
	//e.style.display=(e.style.display=="none")?"":"none";
         var checkForm=document.getElementById("ShowCompany");
	var CompanyId=document.getElementById("CompanyId");
	if(checkForm.checked)  { 
               e.style.display="";
		e.myProperty=true;
                if (CompanyId.options[0].value!=-1){
                    CompanyId.options[0] = new Option('--',-1);
                }
		CompanyId.value=-1;
	}
	else{
                e.style.display="none";
	   	e.myProperty=false;
                if (CompanyId.options[0].value==-1) {
                        CompanyId.options[0]=null;
                  }
                 CompanyId.options[0] = new Option('请选择', '');;
	    }
			
}

function selChange(e){
    var checkForm=document.getElementById("ShowCompany");
    if (e.value==-1){
        checkForm.checked=true;
        ShowCompanyS('');
        }
    else{
         checkForm.checked=false;
         ShowCompanyS('');
    }
}

function mTypeChange(e){
    var sLen=subTypeName.length;
    if (sLen>0){
       Main_SelectChanged('TypeId',subTypeName,e.value); 
    }
}

function Main_SelectChanged(selectObj,OptionList,selIndex){
   if (typeof selectObj != 'object')
     {
       selectObj = document.getElementById(selectObj);
     }
     
    // 清空选项
    var slen = selectObj.options.length;
 
    for (var i=0; i < slen; i++)
    {
        // 移除当前选项
        selectObj.options[0] = null;
    }
    
    var len = OptionList.length;
    
    selectObj.options[0] = new Option('请选择', '');
    
    var n=1;
    
    for (var i=0; i < len; i++)
    {
        if (OptionList[i][0]==selIndex)
        {
           selectObj.options[n] = new Option(OptionList[i][2],OptionList[i][1]);
           n++;
        }
    }
}
    
</script>