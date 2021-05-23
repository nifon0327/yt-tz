
<?php 
//代码 branchdata by zx 2012-08-13
//步骤1 二合一已更新
//电信-joseph
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
      <script type='text/javascript' src='../model/inputSuggest1.0c.js'></script>";
//步骤2：
ChangeWtitle("$SubCompany 新增固定资产资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：

$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";

//步骤4：需处理
$cSign=$_SESSION["Login_cSign"];
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
}*/
</script>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
			 <tr>
			    <td align="right" scope="col">购买人</td>                
				<td><input name="BuyerName" type="text" id="BuyerName" size="53"  dataType="Require"  msg="未填写" >
              <!--  <input type="button" name="Submit" value="..." title="购买人" onclick="SearchData(1,1)">-->
                <input name='Buyer' type='hidden' id='Buyer' >
                </td>
 
		     </tr>
             
 			  <tr>
			    <td align="right" scope="col">购买日期</td>
			    <td valign="middle" scope="col"><input name="BuyDate" type="text" id="BuyDate" size="53" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly>			</td>
		      </tr>            
                   
			  <!--<tr>
				<td width="150" align="right" scope="col">资产编号</td>
				<td valign="middle" scope="col"><input name="CpName" type="text" id="CpName" size="53" maxlength="20" dataType="Require"  msg="未填写" autocomplete="off">
               <?php 
				//关系到lookup.js, readmodel_3.php if ($FromSearch=="FromSearch") { 
				/*$searchtable="fixed_assetsdata|M|CpName|0|0"; 
				echo "	
				  <input name='searchtable' type='hidden' id='searchtable' value='$searchtable'>
				  <input name='searchfile' type='hidden' id='searchfile' value='$searchfile'>
				  <input name='FromSearch' type='hidden' id='FromSearch' value='$FromSearch'>
				  <div    name='querydiv' id='querydiv' style='width:100px'> </div>
				  ";  //$searchfile 默认为 Quicksearch_ajax.php, 可自行设定 
				echo "
				 <script language='javascript' type='text/javascript'>
					window.oninit=InitQueryCode('CpName','querydiv'); 
					//clearDiv();
				 </script> ";  //*/
				?> 
                
                
                <input name='CpNamer' type='hidden' id='CpNamer' >
                </td>
			  </tr>-->
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
						echo"<option value='$Id'>$Name</option>";
						}while($checkRow=mysql_fetch_array($checkSql));
					}
				?>
                      </select></td>
	                </tr>
			<tr>
			  <tr>
			    <td width="150" align="right" scope="col">设备分类</td>
			    <td valign="middle" scope="col"><select name="TypeId" id="TypeId" style="width: 300px;" dataType="Require"  msg="未选择">
                            <option value='' selected>请选择</option>
                           <?php 
				/*$checkSql=mysql_query("SELECT Id,Name FROM $DataPublic.oa2_fixedsubtype WHERE 1 AND Estate=1 ORDER BY Letter",$link_id);
				if($checkRow=mysql_fetch_array($checkSql)){
					echo"<option value='' selected>请选择</option>";
					do{
						$Id=$checkRow["Id"];
						$Name=$checkRow["Name"];
						echo"<option value='$Id'>$Name</option>";
						}while($checkRow=mysql_fetch_array($checkSql));
					}*/
				?>
             </select></td>
	    </tr>
			<tr>
                <td align="right" scope="col">使用部门</td>
                <td valign="middle" scope="col">
             	<select name="BranchId" id="BranchId" style="width:300px"  dataType="Require"  msg="未选择部门">
			 	<option value="" selected>--请选择--</option>
				<?php 
				//固定资产可以不加选择部门
				$B_Result=mysql_query("SELECT Id,Name FROM $DataPublic.branchdata 
									   WHERE Estate=1  order by Id",$link_id);
				if($B_Row = mysql_fetch_array($B_Result)) {
				do{
					$B_Id=$B_Row["Id"];
					$B_Name=$B_Row["Name"];
					echo "<option value='$B_Id'>$B_Name</option>";
					}while ($B_Row = mysql_fetch_array($B_Result));
				}
				?>			  
            	</select>
             </td>             
	    	</tr>
            
			 <tr>
			    <td align="right" scope="col">领用人</td>                
				<td><input name="UserName" type="text" id="UserName" size="53"  dataType="Require"   msg="未填写" >
              <!--  <input type="button" name="Submit" value="..." title="领用人" onclick="SearchData(1,0)">-->
                <input name='User' type='hidden' id='User' >
                </td>
 
		     </tr>
           
			<tr>
			    <td align="right" scope="col">领用日期</td>
			    <td valign="middle" scope="col"><input name="UserDate" type="text" id="UserDate" size="53" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly>			</td>
		      </tr>             
                      
			  <tr>
                <td align="right" scope="col">数量</td>
                <td valign="middle" scope="col"><input name="Qty" type="text" id="Qty" size="53" dataType="Number"  msg="格式不对" value="1" readonly="readonly"></td>
	    	</tr>
            
			 <tr>
                <td align="right" scope="col">价格</td>
                <td valign="middle" scope="col"><input name="price" type="text" id="price" size="53" dataType="Currency"  msg="格式不对"></td>
	    	</tr>
                        
			  <tr>
			    <td align="right" scope="col">名称-型号</td>
			    <td valign="middle" scope="col"><input name="Model" type="text" id="Model" size="53" dataType="Require"  msg="未填写"></td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">服务编号</td>
			    <td valign="middle" scope="col"><input name="SSNumber" type="text" id="SSNumber" size="53" ></td>
		      </tr>

			  <tr>
			    <td align="right" scope="col">保修期</td>
			    <td valign="middle" scope="col"><input name="Warranty" type="text" id="Warranty" size="53" dataType="Number"  msg="格式不对"></td>
		      </tr>
 			 <tr>
                <td align="right" scope="col">维护周期</td>
                <td valign="middle" scope="col"><input name="MTCycle" type="text" id="MTCycle" size="40" dataType="Number"  msg="格式不对" value="" >按天来计算</td>
	    	</tr>
                         
 			  <tr>
			    <td align="right" scope="col">使用年限</td>
			    <td valign="middle" scope="col"><input name="ServiceLife" type="text" id="ServiceLife" size="53" dataType="Number"  msg="格式不对"></td>
		      </tr>  
 
			 <tr>
			    <td align="right" scope="col">维护人</td>                
				<td><input name="MTName" type="text" id="MTName" size="53"  dataType="Require"   msg="未填写" >
               <!-- <input type="button" name="Submit" value="..." title="维护人" onclick="SearchData(1,2)">-->
                <input name='maintainer' type='hidden' id='maintainer' >
                </td>
 
		     </tr>
                                     
			  <tr>
			    <td align="right" scope="col">报废日期</td>
			    <td valign="middle" scope="col"><input name="Retiredate" type="text" id="Retiredate" size="53"   value="0000-00-00" readonly>			</td>
		      </tr>


			  <tr>
			    <td align="right" scope="col">销售商</td>
			    <td valign="middle" scope="col"><select name="CompanyId" id="CompanyId" style="width: 300px;" dataType="Require"  msg="未选择">
				<?php 
				$checkSql=mysql_query("SELECT CompanyId,Forshort FROM $DataPublic.dealerdata WHERE 1 ORDER BY Forshort",$link_id);
				if($checkRow=mysql_fetch_array($checkSql)){
					echo"<option value='' selected>请选择</option>";
					do{
						$CompanyId=$checkRow["CompanyId"];
						$Forshort=$checkRow["Forshort"];
						echo"<option value='$CompanyId'>$Forshort</option>";
						}while($checkRow=mysql_fetch_array($checkSql));
					}
				?>
                <option value='-1'>--</option>
		        </select>
                &nbsp;
                <input name="ShowCompany" type="checkbox" id="ShowCompany"  onclick='ShowCompanyS("Show_Company")' />直接输入公司
                </td>            
	    </tr>
        
 	  </table>
</td></tr></table>         
 <table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style='display:none' id="Show_Company">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">           
     
            
                <tr    >
                  <td  width="150" align="right" scope="col">公司名称</td>
                  <td valign="middle" scope="col"><input name="NewCompany" type="text" id="NewCompany" size="53" ></td>        
                </tr>
                 <tr   >
                  <td align="right" scope="col">联系人</td>
                  <td valign="middle" scope="col"><input name="NewName" type="text" id="NewName" size="53" ></td>        
                </tr>
                 <tr  >
                  <td align="right" scope="col">联系电话</td>
                  <td valign="middle" scope="col"><input name="NewTel" type="text" id="NewTel" size="53" ></td>        
                </tr>    
                 <tr>
                    <td align="right" scope="col">通信地址</td>
                    <td valign="middle" scope="col" ><input name="NewAddress" type="text" require="false" id="NewAddress" size="91" dataType="Limit" max="100" msg="必须在150个字之内"></td>
                  </tr>                         
                 <tr  >
                  <td align="right" scope="col">其它资料</td>
                  <td valign="middle" scope="col"><textarea name="NewRemark" cols="53" rows="6" id="NewRemark"></textarea></td>      
                </tr>
            

        
 	  </table>
</td></tr></table>         
 <table  border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table id="NoteTable"  width="760" border="0" align="center" cellspacing="5"> 
   
		 <tr>
			    <td  width="150" align="right" scope="col">物品照片</td>
			    <td valign="middle" scope="col">
			      <input name="Attached1" type="file" id="Attached1" size="41" dataType="Filter" msg="非法的文件格式" accept="jpg" Row="0" Cel="1"></td>
	         </tr>
                  <tr>
			    <td  width="150" align="right" scope="col">说明书</td>
			    <td valign="middle" scope="col">
			      <input name="Attached2" type="file" id="Attached2" size="41" dataType="Filter" msg="非法的文件格式" accept="pdf,jpg" Row="1" Cel="1"></td>
	          </tr>
                  <tr>
			    <td  width="150" align="right" scope="col">保修卡</td>
			    <td valign="middle" scope="col">
			      <input name="Attached3" type="file" id="Attached3" size="41" dataType="Filter" msg="非法的文件格式" accept="htm,html,pdf,jpg" Row="2" Cel="1"></td>
	          </tr>
<tr>
			    <td  width="150" align="right" scope="col">操作规程</td>
			    <td valign="middle" scope="col">
			      <input name="Attached4" type="file" id="Attached4" size="41" title="可选项pdf格式" dataType="Filter" msg="非法的文件格式" accept="pdf" Row="1" Cel="1"></td>
	          </tr>              
                  
			  <tr>
			    <td align="right" valign="top" scope="col">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
			    <td valign="middle" scope="col"><textarea name="Remark" cols="53" rows="6" id="Remark"></textarea></td>
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
	WHERE 1 AND M.Estate='1'  ORDER BY M.BranchId,M.JobId,M.ComeIn,M.Number",$link_id);

	while ($StaffRow = mysql_fetch_array($StaffSql)){
		$sNumber=$StaffRow["Number"];
                $sName=$StaffRow["Name"];
		$sBranch=$StaffRow["Branch"];

                $subName[]=array($sNumber,$sName,$sBranch);
	};

/*
$fixedCPSql = mysql_query("SELECT M.Id,M.CpName,M.buyer 
	FROM $DataPublic.fixed_assetsdata M 
	WHERE 1 AND M.Estate='1' ORDER BY M.CpName DESC",$link_id);


	while ($fixedRow = mysql_fetch_array($fixedCPSql)){
		$fixedCpName=$fixedRow["CpName"];
		$fixedCpId=$fixedRow["Id"];
		$fixedbuyer=$fixedRow["buyer"];
        $subfixedCpName[]=array($fixedCpId,$fixedCpName,'');
		//print_r $subfixedCpName;
	}	
 */          
?>

<script type="text/javascript">
 window.onload = function(){
                var subName=<?php  echo json_encode($subName);?>;
				/*
				var subfixedCpName=<echo json_encode($subfixedCpName);?>;
                */
		var sinaSuggest = new InputSuggest({
			input: document.getElementById('BuyerName'),
			poseinput: document.getElementById('Buyer'),
			data: subName,
			width: 290
		});
                
                var sinaSuggest1 = new InputSuggest({
			input: document.getElementById('UserName'),
			poseinput: document.getElementById('User'),
			data: subName,
			width: 290
		});
                
                var sinaSuggest2 = new InputSuggest({
			input: document.getElementById('MTName'),
			poseinput: document.getElementById('maintainer'),
			data: subName,
			width: 290
		});
				
				
				
	}
var subTypeName=<?php echo json_encode($subType);?>;
function setUser(){
   var s=document.getElementById('BranchId');
   document.getElementById('UserName').value=s[s.selectedIndex].text+'-';
}

function mTypeChange(e){
    var sLen=subTypeName.length;
    if (sLen>0){
       Main_SelectChanged('TypeId',subTypeName,e.value); 
    }
}

function ShowCompanyS(RowTemp){
	//var e=eval(RowTemp);
	var e=document.getElementById("Show_Company");
	e.style.display=(e.style.display=="none")?"":"none";
    var checkForm=document.getElementById("ShowCompany");
	var CompanyId=document.getElementById("CompanyId");
	if(checkForm.checked)  { 
		e.myProperty=true;
		CompanyId.value=-1;
	}
	else{
	   	e.myProperty=false;
		CompanyId.options[0].selected = true; 
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