<?php 
//步骤1$DataIn.电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 配件管理-批量更新");//需处理
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
     <table width="800" height="450" border="0" align="center" cellspacing="0">
    <tr>
      <td height="26" class="A1011">
		<input name="ALType" type="hidden" id="ALType" value="<?php  echo $ALType?>">对属于
		<select name="uType" id="uType" onchange="ClearList('ListId')">
        <?php 
		$result = mysql_query("SELECT * FROM $DataIn.stufftype ORDER BY Letter",$link_id);
		echo "<option value='' selected>所有类型</option>";
		while ($StuffType = mysql_fetch_array($result)){
			$Letter=$StuffType["Letter"];
			$TypeId=$StuffType["TypeId"];
			$TypeName=$StuffType["TypeName"];
			echo "<option value='$TypeId'>$Letter-$TypeName</option>";
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
      <td rowspan="24" class="A0111">
        <select name="ListId[]" size="24" id="ListId" multiple style="width: 300px;" onclick="SearchRecord('stuffdata','<?php  echo $funFrom?>',2,6)" readonly></select>
      </td>
      <td height="26">2、配件名称替换：将配件名称某字符替换为指定的字符，如将名称中的&quot;条码&quot;替换为&quot;标签&quot; </td>
    </tr>
    <tr>
      <td height="23" class="A0100"><div align="right">原字符<input name="Character_OLD" type="text" id="Character_OLD" size="15"> =>>
替换为<input name="Character_NEW" type="text" id="Character_NEW" size="15">
<input type="button" name="Submit" value="开始替换" onClick="CheckForm(2)">
      </div></td>
    </tr>
    <tr>
      <td height="24">3、清除没有使用的配件 ：没有下过采购单且没有设定产品配件关系的配件 </td>
    </tr>
    <tr>
      <td height="23" class="A0100"><div align="right">
        <input type="button" name="Submit" value="开始清除" onClick="CheckForm(3)">
</div></td>
    </tr>
    <tr>
      <td height="23">4、配件取代：将A配件的资料改为B配件后删除A配件（需在左侧列表指定A配件）</td>
    </tr>
    <tr>
      <td height="23" class="A0100"><div align="right">
取代的B配件
    <input name="newStuffId" type="text" id="newStuffId" size="15" onclick="SearchRecord('stuffdata','<?php  echo $funFrom?>',1,6)" readonly>
<input type="button" name="Submit" value="暂停使用" >
<!--<input type="button" name="Submit" value="开始取代" onClick="CheckForm(4)">  -->

      </div></td>
    </tr>
    <tr>
      <td height="24" >5、价格更新：同时更新多个配件（或某分类下的配件）的价格</td>
    </tr>
    <tr>
      <td height="18" class="A0100"><div align="right">新的单价
        <input name="NewPrice" type="text" id="NewPrice" size="15">
        <input type="button" name="Submit" value="开始更新" onClick="CheckForm(5)">
      </div></td>
    </tr>
    <tr>
      <td height="9">6、供应商更新：同时更新多个配件（或某分类下的配件）默认供应商</td>
    </tr>
    <tr>
      <td height="9" class="A0100"><div align="right">新的默认供应商
            <select name="CompanyId" id="CompanyId">
            <?php 
			//供应商
			$GYS_Sql = "SELECT * FROM $DataIn.trade_object WHERE Estate=1 AND (cSign='$Login_cSign' OR cSign=0 ) order by Letter";
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
            <input type="button" name="Submit" value="开始更新" onClick="CheckForm(6)">
      </div></td>
    </tr>
    <tr>
      <td height="9" class="A0100">7、采购更新：同时对多个配件的采购进行更新</td>
    </tr>
    <tr>
      <td height="9" align="right" class="A0100">新的采购员
        <select name="BuyerId" id="BuyerId">
          <?php 
			//供应商
			$GYS_Sql = "SELECT Number,Name FROM $DataPublic.staffmain WHERE Estate=1 AND cSign=$Login_cSign AND  (BranchId=4 OR  JobId=3) order by BranchId,JobId,Number";
			$GYS_Result = mysql_query($GYS_Sql); 
			while ( $GYS_Myrow = mysql_fetch_array($GYS_Result)){
				$Number=$GYS_Myrow["Number"];
				$Name=$GYS_Myrow["Name"];
				echo "<option value='$Number'>$Name</option>";
				} 
			?>
                </select>
        <input type="button" name="Submit" value="开始更新" onClick="CheckForm(7)"></td>
    </tr>
    <tr>
      <td height="9" class="A0100">8、分类更新：同时对多个配件的分类进行更新</td>
    </tr>
    <tr>
      <td height="9" align="right" class="A0100">新的分类
        <select name="NewTypeId" id="NewTypeId">
          <?php 
		$ptResult = mysql_query("SELECT Letter,TypeId,TypeName FROM $DataIn.stufftype WHERE Estate=1 ORDER BY Letter",$link_id);
		echo "<option value='' selected>请选择</option>";
		while($ptRow= mysql_fetch_array($ptResult)){
			$Letter=$ptRow["Letter"];
			$TypeId=$ptRow["TypeId"];
			$TypeName=$ptRow["TypeName"];
			echo "<option value='$TypeId'>$Letter-$TypeName</option>";
			}
		?>
        </select>
        <input type="button" name="Submit" value="开始更新" onClick="CheckForm(8)"></td>
    </tr>
      <tr>
      <td height="9" class="A0100">9、送货楼层更新：同时对多个配件的送货楼层进行更新</td>
    </tr>
    <tr>
      <td height="9" align="right" class="A0100">送货楼层
      <select name="NewSendFloor" id="NewSendFloor">   
       <option value='' selected>请选择</option>
 		<?php  
	          $mySql="SELECT Id,Name,Remark FROM $DataIn.base_mposition  
	                  WHERE Estate=1 order by  Remark";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $FloorId=$myrow["Id"];
				 $FloorRemark=$myrow["Remark"];
				 $FloorName=$myrow["Name"];
				 echo "<option value='$FloorId'>$FloorRemark-$FloorName</option>"; 
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
        </select>
        <input type="button" name="Submit" value="开始更新" onClick="CheckForm(9)"></td>
    </tr>
          <tr>
      <td height="10" class="A0100">10、交货周期更新：同时对多个配件的交货周期进行更新</td>
    </tr>
    <tr>
      <td height="9" align="right" class="A0100">交货周期（天）
       <input name="NewJhDays" type="text" id="NewJhDays" size="15">
      <input type="button" name="Submit" value="开始更新" onClick="CheckForm(10)"></td>
    </tr>
          <tr>
      <td height="10" class="A0100">11、配件属性更新：同时对多个配件的属性进行更新</td>
    </tr>
    <tr>
      <td height="30" align="right" class="A0100"><input name="Property[]" type="checkbox" value="0" >默认&nbsp;&nbsp;&nbsp;&nbsp;
                      <input name="Property[]" type="checkbox" value="1" ><span class="redB">代购</span>&nbsp;&nbsp;&nbsp;&nbsp;
                      <input name="Property[]" type="checkbox" value="2" ><span class="blueB">客供</span>&nbsp;&nbsp;&nbsp;&nbsp;
                      <input name="Property[]" type="checkbox" value="3" ><span class="purpleB">成品</span>&nbsp;&nbsp;&nbsp;&nbsp;
                      <input name="Property[]" type="checkbox" value="4" ><span class="yellowB">参考</span>&nbsp;&nbsp;&nbsp;&nbsp;
                      <input name="Property[]" type="checkbox" value="5" ><span class="orangeB">打印</span>&nbsp;&nbsp;&nbsp;&nbsp;
                      <input name="Property[]" type="checkbox" value="6" ><span class="rmbB">自动</span> &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="Submit" value="开始更新" onClick="CheckForm(11)"></td>
    </tr>
    
    <tr>
      <td height="24" >12、价格更新：同时更新同一个供应商的多个配件（或某分类下的配件）的价格</td>
    </tr>
    <tr>
      <td height="18" class="A0100"><div align="right">新的单价
      <select id="symbol" name="symbol">
      	<option value="+">加减</option>
      	<option value="*">百分比</option>
      </select>
        <input name="NewPriceRate" type="text" id="NewPriceRate" size="15">
        <input type="button" name="Submit" value="开始更新" onClick="CheckForm(12)">
      </div></td>
    </tr>

     <tr>
      <td height="9" class="A0100">13、品捡方式更新：同时对多个配件的品捡方式进行更新</td>
    </tr>
    <tr>
      <td height="9" align="right" class="A0100">品捡方式
      <select name="NewCheckSign" id="NewCheckSign" >
                <?php 
                 $StrSign="CheckSign_" . $CheckSign;
                 $$StrSign="selected";
                 echo " <option value='99' $CheckSign_99>-----</option>
                       <option value='0' $CheckSign_0>抽  检</option>
                       <option value='1' $CheckSign_1>全  检</option>";
                ?> 
               </select>
        <input type="button" name="Submit" value="开始更新" onClick="CheckForm(13)"></td>
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
		case 2://字符替换
			var Character_OLD=document.form1.Character_OLD.value;
			var Character_NEW=document.form1.Character_NEW.value;		
			if(Character_OLD==""){
				alert("被替换的字符不能为空！");
				return false;
				}
			else{
				var message1=confirm("注意：配件名称的总长度不可以超过100个字符（即50个中文字符），如果超出此范围系统将做截取！是否继续操作？");
				if (message1==true){
					if(Character_NEW==""){
						var message2=confirm("提醒：新字符为空，即所有配件名称将取消名称中的"+Character_OLD+"字符！是否继续操作？");
						if(message2==false){
							return false;
							}
						}
					}
				else{
					return false;
					}
				}
		break;
		case 4://配件取代
			//检查是否已经指定配件，没有，则返回
			if(The_Selectd.options.length==1){
				var newStuffId=document.form1.newStuffId.value;	
				if(newStuffId==""){
					alert("没有设置取代的配件！");
					return false;
					}
				}
			else{
				alert("没有指定原配件或原配件多选！该功能只针对一个原配件。");
				return false;
				}
		break;
		case 5://更新价格
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
		case 6://更新供应商
			if(The_Selectd.options.length==0 && Tid==""){
				alert("没有指定配件分类或配件！");
				return false;
			}
		break;
		case 7://更新采购
			if(The_Selectd.options.length==0 && Tid==""){
				alert("没有指定配件分类或配件！");
				return false;
				}
		break;
		case 8:
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
		case 9://更新配件送货楼层
		   if(The_Selectd.options.length==0 && Tid==""){
				alert("没有指定配件分类或配件！");
				return false;
				}
			else{
				var sendFloorTemp=document.getElementById('NewSendFloor').value;
				if(sendFloorTemp==""){
					alert("没有指定送货楼层！");
					return false;
				}
			}
		break;
		case 10://更新交货周期
			if(The_Selectd.options.length==0 && Tid==""){
				alert("没有指定配件分类或配件！");
				return false;
				}
			else{
				//交货周期检查
				var JhDaysTemp=document.form1.NewJhDays.value;
				if(JhDaysTemp==""){
					alert("没有设置新的交货周期！");
					return false;
					}
				else{
					var ckeckJhDays=fucCheckNUM(JhDaysTemp,"");
					if(ckeckJhDays==0){
						alert("交货周期格式不对！");
						document.form1.NewJhDays.onfocus();
						return false;
						}
					}
				}
		break;
		case 11://更新配件送货楼层
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
		
		case 13://更新配件品捡方式
		   if(The_Selectd.options.length==0 && Tid==""){
				alert("没有指定配件分类或配件！");
				return false;
				}
			else{
				var checkSignTemp=document.getElementById('NewCheckSign').value;
				if(checkSignTemp==""){
					alert("没有指定品捡方式！");
					return false;
				}
			}

	}
	
	var message=confirm("确定进行操作吗？");
	if (message==true){
		for (loop=0;loop<The_Selectd.options.length;loop++){
		The_Selectd.options[loop].selected=true;}
		document.form1.action="stuffdata_other_up.php?Action="+Action;document.form1.submit();
		}
	else{
		return false;
		}
	}
</script>
