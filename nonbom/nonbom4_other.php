<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 非BOM配件管理-批量更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_other";	
$toWebPage  =$funFrom."_other_up";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/other_model_t.php";
$Parameter="funFrom,$funFrom,From,$From,Orderby,$Orderby,Pagination,$Pagination,Page,$Page,StuffType,$StuffType";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
     <table width="800" height="350" border="0" align="center" cellspacing="0">
    <tr>
      <td height="26" class="A1011">
		<input name="ALType" type="hidden" id="ALType" value="<?php  echo $ALType?>">对属于
		<select name="uType" id="uType" onchange="ClearList('ListId')">
        <?php 
		$result = mysql_query("SELECT * FROM $DataPublic.nonbom2_subtype",$link_id);
		echo "<option value='' selected>所有类型</option>";
		while ($StuffType = mysql_fetch_array($result)){
			$TypeId=$StuffType["Id"];
			$TypeName=$StuffType["TypeName"];
			echo "<option value='$TypeId'>$TypeName</option>";
			}
		?>
        </select>的配件</td>
    <td height="23" class="A1000">
		1、锁定或解锁全部记录：<input name="Locks" type="radio" value="0" checked id="Locks1"><LABEL for="Locks1">记录锁定</LABEL>&nbsp;&nbsp;<input type="radio" id="Locks2" name="Locks" value="1"><LABEL for="Locks2">记录解锁</LABEL>
	</td>
    </tr>
    <tr>
      <td height="21" class="A0111"><div align="center">或下述指定的配件进行操作</div></td>
    <td width="598" class="A0100"><div align="right">
      <input name="Submit" type="button"  value="开始更新" onClick="CheckForm(1)">
    </div></td>
    </tr>
    <tr>
      <td rowspan="8" class="A0111">
        <select name="ListId[]" size="20" id="ListId" multiple style="width: 300px;" onclick="SearchRecord('nonbom4','<?php  echo $funFrom?>',2,6)" readonly></select>
      </td>
      <td height="30" >2、价格更新：同时更新多个配件（或某分类下的配件）的价格</td>
    </tr>
    <tr>
      <td height="30" class="A0100"><div align="right">新的单价
        <input name="NewPrice" type="text" id="NewPrice" size="15">
        <input type="button" name="Submit" value="开始更新" onClick="CheckForm(2)">
      </div></td>
    </tr>

    <tr>
      <td height="30">3、供应商更新：同时更新多个配件（或某分类下的配件）默认供应商</td>
    </tr>
    <tr>
      <td height="30" class="A0100"><div align="right">新的默认供应商
            <select name="CompanyId" id="CompanyId">
            <?php 
			//供应商
			$GYS_Sql = "SELECT * FROM $DataPublic.nonbom3_retailermain  WHERE Estate=1 AND (cSign='$Login_cSign' OR cSign=0 ) order by Letter";
			$GYS_Result = mysql_query($GYS_Sql); 
			while ( $GYS_Myrow = mysql_fetch_array($GYS_Result)){
				$CompanyId=$GYS_Myrow["CompanyId"];
				$Forshort=$GYS_Myrow["Forshort"];
				$Letter=$GYS_Myrow["Letter"];
				$Forshort=$Letter.'-'.$Forshort;		
				if ($myrow["CompanyId"]==$CompanyId){
					echo "<option value='$CompanyId' selected>$Forshort</option>";}
				else{
					echo "<option value='$CompanyId'>$Forshort</option>";}
				} 
			?>
            </select>
            <input type="button" name="Submit" value="开始更新" onClick="CheckForm(3)">
      </div></td>
    </tr>
       <tr>
      <td height="30" >4、分类更新：同时对多个配件的分类进行更新</td>
    </tr>
    <tr>
      <td height="30" align="right" class="A0100">新的分类
        <select name="NewTypeId" id="NewTypeId">
          <?php 
		$ptResult = mysql_query("SELECT Id,TypeName FROM $DataPublic.nonbom2_subtype WHERE Estate=1",$link_id);
		echo "<option value='' selected>请选择</option>";
		while($ptRow= mysql_fetch_array($ptResult)){
			$TypeId=$ptRow["Id"];
			$TypeName=$ptRow["TypeName"];
			echo "<option value='$TypeId'>$TypeName</option>";
			}
		?>
        </select>
        <input type="button" name="Submit" value="开始更新" onClick="CheckForm(4)"></td>
    </tr>
            <tr>
      <td height="30" >5、配件属性更新：同时对多个配件的属性进行更新</td>
    </tr>
    <tr>
      <td height="30" align="right" class="A0100">            <?php 
               $x=0;
			    $checkResult = mysql_query("SELECT * FROM $DataPublic.nonbom4_propertytype  WHERE Estate=1 ORDER BY Id",$link_id);
                while($checkRow = mysql_fetch_array($checkResult)){
                    $PropertyId=$checkRow["Id"];
                    $TypeName=$checkRow["TypeName"];
                    $TypeColor=$checkRow["TypeColor"];
                    if ($x>0 && $x%8==0) echo "<br>"; 
                    echo "<input name='Property[]' type='checkbox' value='$PropertyId'><span style='color:$TypeColor;'>$TypeName</span>&nbsp;&nbsp;&nbsp;&nbsp;";
                    $x++;
                    }
			?><input type="button" name="Submit" value="开始更新" onClick="CheckForm(5)"></td>
    </tr>
  </table>	
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/other_model_b.php";
?>
<script language = "JavaScript"> 
function CheckForm(Action){
	var The_Selectd = window.document.form1.ListId;
	var Tid=document.getElementById('uType').value;
	switch(Action){
			case 2://更新价格
			if(The_Selectd.options.length==0 && Tid==""){
				alert("没有指定配件分类或配件！");
				return false;
				}
			else{
				//价格检查
				var PriceTemp=document.form1.NewPrice.value;
				if(PriceTemp==""){
					alert("没有设置新的单价！");
					return false;
					}
				else{
					var ckeckPrice=fucCheckNUM(PriceTemp,"Price");
					if(ckeckPrice==0){
						alert("单价格式不对！");
						document.form1.NewPrice.select();
						return false;
						}
					}
				}
		break;
		case 3://更新供应商
			if(The_Selectd.options.length==0 && Tid==""){
				alert("没有指定配件分类或配件！");
				return false;
				}
		break;

		case 4:
			if(The_Selectd.options.length>0 || Tid!=""){
				var newTid=document.getElementById('NewTypeId').value;
				if(newTid!=""){
					document.form1.action="productdata_other_up.php?Action="+Action;document.form1.submit();
					}
				else{
					alert("没有指定新的分类！");
					return false;
					}
				}
			else{
				alert("没有指定足够的条件！");
				return false;
				}
		break;
			case 5:
		   if(The_Selectd.options.length==0 && Tid==""){
				alert("没有指定配件分类或配件！");
				return false;
				}
			else{
                var Property=document.getElementsByName("Property[]");
                 var objarray=Property.length;
                var tempk=0;
                 for (i=0;i<objarray;i++){
                 if(Property[i].checked == true){
                            tempk++;
                       }
                 }
				if(tempk==0){
					alert("没有指定配件属性");
					return false;
				}
			}
		break;

	}
	
	var message=confirm("确定进行操作吗？");
	if (message==true){
		for (loop=0;loop<The_Selectd.options.length;loop++){
		  The_Selectd.options[loop].selected=true;}
		   document.form1.action="nonbom4_other_up.php?Action="+Action;document.form1.submit();
		}
	else{
		return false;
		}
	}
</script>
