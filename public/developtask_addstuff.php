<?php 
/*电信---yang 20120801
$DataIn.stufftype
$DataPublic.staffmain
$DataIn.trade_object
二合一已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增配件资料");//需处理
$nowWebPage =$funFrom."_addstuff";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"] = $nowWebPage;
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ItemId,$ItemId,ActionId,94";
$addStuff=mysql_query("SELECT S.Name,D.Developer FROM $DataIn.development D
                       LEFT JOIN $DataPublic.staffmain S ON S.Number=D.Developer
                       WHERE D.Id='$Id'",$link_id);
if($addRow= mysql_fetch_array($addStuff)){
$Name=$addRow["Name"];
$Developer=$addRow["Developer"];
}
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    <td width="300" height="30" align="right" valign="bottom" class="A0010"><LABEL for="Action1">添加新配件</LABEL></td>
    <td valign="bottom"><input name="Action" type="checkbox" id="Action1" value="1" onclick="UpItem(1)"></td>
	<td class="A0001">&nbsp;</td>
   </tr>
		<tr>
            <td height="25" align="right" class="A0010">配件名称</td>
            <td scope="col"><input name="StuffCname" type="text" id="StuffCname" size="40" dataType="LimitB" min="3" max="100"  msg="必须在2-100个字节之内" title="必填项,2-100个字节内" disabled></td>
			<td width="10" class="A0001">&nbsp;</td>
		</tr>
      
        <tr>
        	<td height="25" align="right" class="A0010">配件类型</td>
        	<td scope="col"><select name="TypeId" id="TypeId" style="width:230px" dataType="Require"  msg="未选择分类" disabled>
				<option value='' selected>请选择</option>
			  	<?php 
				//检查登入者，如果
				if($Login_P_Number==10023 || $Login_P_Number==10039){
					if($Login_P_Number==10023){
						$TypeSTR=" AND mainType=3";
						}
					else{
						$TypeSTR=" AND mainType=2";
						}
					}
				else{
					$TypeSTR=" AND mainType<2";
					}
				$result = mysql_query("SELECT TypeId,TypeName,Letter FROM $DataIn.stufftype WHERE Estate='1' $TypeSTR ORDER BY Letter",$link_id);
				while ($StuffType = mysql_fetch_array($result)){
					$Letter=$StuffType["Letter"];
					$TypeId=$StuffType["TypeId"];
					$TypeName=$StuffType["TypeName"];
					echo"<option value='$TypeId'>$Letter-$TypeName</option>";
					}
				?>
				</select>  
			</td>
			<td width="10" class="A0001">&nbsp;</td>
        </tr>
        <tr>
            <td height="25" align="right" class="A0010">参考买价</td>
            <td scope="col"><input name="Price" type="text" id="Price" size="40" dataType="Currency" msg="错误的价格" disabled></td>
			<td width="10" class="A0001">&nbsp;</td>
        </tr>
		 <tr>
            <td height="25" align="right"  class="A0010">对应数量</td>
            <td scope="col"><input name="Relation" type="text" id="Relation" value="1" size="40" disabled></td>
			<td width="10" class="A0001">&nbsp;</td>
        </tr>
		<tr>
            <td height="25" align="right" class="A0010">刀&nbsp;&nbsp;模</td>
            <td scope="col"><input name="Diecut" type="text" id="Diecut" size="40" disabled ></td>
			<td width="10" class="A0001">&nbsp;</td>
        </tr>
		<tr>
            <td height="25" align="right" class="A0010">切割关系</td>
            <td scope="col"><input name="Cutrelation" type="text" id="Cutrelation" size="40" disabled ></td>
			<td width="10" class="A0001">&nbsp;</td>
        </tr>
		<tr>
            <td height="26" align="right" class="A0010">采&nbsp;&nbsp;&nbsp;&nbsp;购</td>
            <td scope="col"><select name="BuyerId" id="BuyerId" style="width:230px;" dataType="Require"  msg="未选择采购" disabled>
			<option value=''>请选择</option>
			<?php 
			$checkStaff ="SELECT P.Number,P.Name FROM $DataPublic.staffmain P,$DataIn.usertable T 
			WHERE T.Number=P.Number AND P.Estate=1 AND (P.BranchId IN(2,3,4) OR P.Number='10039') 
			ORDER BY P.BranchId,P.JobId,P.Number";
			$staffResult = mysql_query($checkStaff); 
			while ( $staffRow = mysql_fetch_array($staffResult)){
				$pNumber=$staffRow["Number"];
				$PName=$staffRow["Name"];					
				echo "<option value='$pNumber'>$PName</option>";
				} 
			?>		 
			</select>
			</td>
			<td width="10" class="A0001">&nbsp;</td>
        </tr>
     
        <tr>
            <td height="25" align="right" class="A0010">供&nbsp; 应商</td>
            <td scope="col"><select name="CompanyId" id="CompanyId" style="width: 230px;" dataType="Require"  msg="未选择供应商" disabled>
			<option value=''>请选择</option>
            <?php 
			//供应商
			$checkSql = "SELECT CompanyId,Forshort,Letter FROM $DataIn.trade_object WHERE (cSign=$Login_cSign OR cSign=0) AND Estate='1' order by Letter";
			$checkResult = mysql_query($checkSql); 
			while ( $checkRow = mysql_fetch_array($checkResult)){
				$CompanyId=$checkRow["CompanyId"];
				$Forshort=$checkRow["Letter"].'-'.$checkRow["Forshort"];
				echo "<option value='$CompanyId'>$Forshort</option>";
				} 
			?>
            </select>
			</td><td width="10" class="A0001">&nbsp;</td>
			</tr>
			<tr>
            <td height="30" align="right" valign="bottom" class="A0010"><LABEL for="Action2">添加旧配件</LABEL></td>
            <td valign="bottom"><input name="Action" type="checkbox" id="Action2" value="2" datatype="Group" min="1" max="1"  msg="必须选择一项目" onclick="UpItem(2)"></td>
			<td width="10" class="A0001">&nbsp;</td>
          </tr>
		  <tr>
          <td height="25" align="right" class="A0010" valign="top">&nbsp;</td>
          <td ><select name="stuffId[]" size="6" id="stuffId" multiple style="width: 230px;"ondblclick="SearchRecord('stuffdata','<?php  echo $funFrom?>',2,6)" datatype="autoList" readonly disabled>
      </select></td><td width="10" class="A0001">&nbsp;</td>   
     </tr>
	<tr>
	 <td height="25" align="right" class="A0010">&nbsp;</td>
	 <td><input type="button"  value="删除选定配件行"  onClick="delstuffRow()" disabled> </td>
	 <td width="10" class="A0001">&nbsp;</td>
	</tr>
	</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function UpItem(Action){
	if(Action==1){
		for(var i=4;i<12;i++){
			if(form1.elements[i].disabled==true)
				form1.elements[i].disabled=false;
			else
				form1.elements[i].disabled=true;
			}
		}
	else{
		for(var i=13;i<15;i++){
			if(form1.elements[i].disabled==true)
				form1.elements[i].disabled=false;
			else
				form1.elements[i].disabled=true;
			}
		}
	}

function delstuffRow(){
   var cList = document.getElementById("stuffId");
   for(var i=0; i<cList.length; i++){
      if(cList.options[i].selected){
       cList.options[i]=null;
	   i=i-1;
	  }
   }
}
function SearchRecord(tSearchPage,fSearchPage,SearchNum,Action){
	var r=Math.random();
	var theType=event.srcElement.getAttribute('type');
	var theName=event.srcElement.getAttribute('name');	
	switch(theType){
		case "select-multiple"://多选列表
			//其它参数：主要是类型限制
			var BackData=window.showModalDialog(tSearchPage+"_s1.php?r="+r+"&tSearchPage="+tSearchPage+"&fSearchPage="+fSearchPage+"&SearchNum="+SearchNum+"&Action="+Action,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
			//拆分BackData
			if(BackData){
				var The_Selectd = window.document.form1.stuffId;
				var BL=BackData.split("``");
				var AddLength=The_Selectd.options.length;
				for(var i=0;i<BL.length;i++){
					var oldNum=0;
					var CL=BL[i].split("^^");
					for (loop=0;loop<AddLength;loop++){
						var oldTemp=The_Selectd.options[loop].value;
						if(CL[0]==oldTemp){
							oldNum=1;
							break;
							}
						}
					if(oldNum==1){
						alert("记录"+CL[1]+"已在列表,跳过继续！");
						}
					else{
						window.document.form1.stuffId.options[document.form1.stuffId.options.length]=new Option(CL[0]+' '+CL[1] ,CL[0]);
						}
					}
				}
			break;
		}//switch(theType)
	}

</script>