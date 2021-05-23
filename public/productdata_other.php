<?php 
//步骤1 $DataIn.producttype 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 产品管理-批量更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_other";	
$toWebPage  =$funFrom."_other_up";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$tableWidth=850;$tableMenuS=500;
$CheckFormURL=="thisPage";
$SaveSTR="NO";
include "../model/subprogram/add_model_t.php";
$Parameter="funFrom,$funFrom,From,$From,CompanyId,$CompanyId,TypeId,$TypeId,Pagination,$Pagination,Page,$Page";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
    <table width="800" border="0" align="center" cellspacing="0">
    <tr>
      <td height="23" class="A1011">指定分类：&nbsp;&nbsp;&nbsp;&nbsp;
        <select name="uType" id="uType" style="width:200px" onchange="ClearList('ListId')">
		<?php 
		$ptResult = mysql_query("SELECT Letter,TypeId,TypeName FROM $DataIn.producttype ORDER BY Letter",$link_id);
		echo "<option value='' selected>全部</option>";
		while($ptRow= mysql_fetch_array($ptResult)){
			$Letter=$ptRow["Letter"];
			$TypeId=$ptRow["TypeId"];
			$TypeName=$ptRow["TypeName"];
			echo "<option value='$TypeId'>$Letter-$TypeName</option>";
			}
		?>
        </select></td>
      <td width="529" rowspan="2" align="center" class="A0100">        
          (注意：不指定或分类产品则对全部产品操作)</td>
    </tr>
    <tr>
      <td height="28" class="A1011">指定产品：</td>
      </tr>
    <tr>
      <td rowspan="16" align="center" class="A0111"><select name="ListId[]" size="18" id="ListId" multiple style="width: 300px;" onclick="SearchRecord('productdata','<?php  echo $funFrom?>',2,6)" readonly>
      </select></td>
      <td height="24">
	  1、锁定或解锁全部产品记录：
      <input name="Locks" type="radio" value="0" id="Lock" checked><LABEL for="Lock">全部锁定</LABEL>
	  <input type="radio" name="Locks" value="1" id="unLock"><LABEL for="unLock">全部解锁</LABEL>
	  </td>
    </tr>
    <tr>
      <td height="30" align="right"  class="A0100">          <input name="Submit" type="button"  value="开始执行" onClick="CheckForm(1)">
      </td>
    </tr>
    <tr>
      <td height="33">2、产品名称字符替换：如将名称中的&quot;A&quot;字符替换为&quot;B&quot;字符 </td>
    </tr>
    <tr>
      <td height="33" align="right" class="A0100">将字符A
            <input name="Character_OLD" type="text" id="Character_OLD">
  替换为B
  <input name="Character_NEW" type="text" id="Character_NEW">&nbsp;&nbsp;<input type="button" name="Submit" value="开始替换" onClick="CheckForm(2)">
      </td>
    </tr>
    <tr>
      <td height="18">3、多产品单价更新(需指定分类或产品)</td>
    </tr>
    <tr>
      <td height="9" align="right">新的单价
            <input name="NewPrice" type="text" id="NewPrice">            <input type="button" name="Submit" value="开始更新" onClick="CheckForm(3)">
      </td>
    </tr>
    <tr>
      <td height="4" >4、更改所选产品的分类</td>
    </tr>
    <tr>
      <td height="2" align="right">新的分类
        <select name="NewTypeId" id="NewTypeId" style="width: 150px;" >
		<?php 
		$ptResult = mysql_query("SELECT Letter,TypeId,TypeName FROM $DataIn.producttype WHERE Estate=1 ORDER BY Letter",$link_id);
		echo "<option value='' selected>请选择</option>";
		while($ptRow= mysql_fetch_array($ptResult)){
			$Letter=$ptRow["Letter"];
			$TypeId=$ptRow["TypeId"];
			$TypeName=$ptRow["TypeName"];
			echo "<option value='$TypeId'>$Letter-$TypeName</option>";
			}
		?>
        </select>
        <input type="button" name="Submit" value="开始更新" onClick="CheckForm(4)"></td>
    </tr>
      <tr>
      <td height="4" >5、更改所选产品的客户</td>
    </tr>
    <tr>
      <td height="2" align="right">新的客户
        <select name="NewCompanyId" id="NewCompanyId" style="width: 150px;" ><option value='' selected>请选择</option>
		<?php 
		$result = mysql_query("SELECT * FROM $DataIn.trade_object WHERE cSign IN ($Login_cSign,0) AND Estate=1 AND ObjectSign IN (1,2)  order by Id",$link_id);
					if($myrow = mysql_fetch_array($result)){
						do{
							echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
							} while ($myrow = mysql_fetch_array($result));
		}
		?>
        </select>
        <input type="button" name="Submit" value="开始更新" onClick="CheckForm(7)"></td>
    </tr>

  <tr>
      <td height="4" >6、更改所选产品的产品属性</td>
    </tr>
    <tr>
      <td height="2" align="right">新的产品属性
        <select name="NewbuySign" id="NewbuySign" style="width: 150px;" ><option value='' selected >请选择</option>
			  <option value="1">自购</option>
			  <option value="2">代购</option>
			  <option value="3">客供</option>
        </select>
        <input type="button" name="Submit" value="开始更新" onClick="CheckForm(8)"></td>
    </tr>

    <tr>
      <td height="3" class="A0100">&nbsp;</td>
    </tr>
	<td  height="30" align="left" >7.标准图原件复制.....</td>
	<tr>
	<td height="30" align="right" >将产品名:<input type="text" name="ProductName" id="ProductName" onclick="ViewFileName(this,1)" size="20" />&nbsp;的标准图原件复制给指定产品&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" name="Submit" value="开始复制" onClick="CheckForm(6)">
	<input name='ProductId' type='hidden' id='ProductId'>
	</td>
	</tr>
  </table>	  
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script language = "JavaScript"> 
function ViewFileName(e,Action){
  var r=Math.random();
  var ProductId=document.getElementById('ProductId');
  var BackData=window.showModalDialog("graphicsfile_s1.php?r="+r+"&tSearchPage=graphicsfile&fSearchPage=clientorder&SearchNum=2&Action="+Action,"BackData","dialogHeight:650px;dialogWidth:980px;center=yes;help=0;scroll=yes");
	if(BackData==null || BackData==''){  //专为safari设计的 ,add by zx 2011-05-04
			if(document.getElementById('SafariReturnValue')){
			//alert("return");
			var SafariReturnValue=document.getElementById('SafariReturnValue');
			BackData=SafariReturnValue.value;
			SafariReturnValue.value="";
			}
		}
	if(BackData){
	     var CL=BackData.split("^^");
				e.value=CL[1];
				ProductId.value=CL[0];
	}		
}

function CheckForm(Action){
	var The_Selectd = window.document.form1.ListId;
	for (loop=0;loop<The_Selectd.options.length;loop++){
		The_Selectd.options[loop].selected=true;}
	var Tid=document.getElementById('uType').value;
	switch(Action){
		case 1://锁定记录
			if(The_Selectd.options.length>0 || Tid!=""){
				var Message="已指定了分类或产品，将对这些产品进行 锁定或解锁 操作！是否执行操作？";
				}
			else{
				var Message="没有指定产品或产品分类，将对全部产品进行 锁定或解锁 操作！是否执行操作？";
				}
			var message=confirm(Message);
			if (message==true){
				document.form1.action="productdata_other_up.php?Action="+Action;document.form1.submit();
				}
			else{
				return false;
				}
			break;
			
		case 2:
			var Character_OLD=document.form1.Character_OLD.value;
			var Character_NEW=document.form1.Character_NEW.value;		
			if(Character_OLD==""){
				alert("被替换的字符不能为空！");
				return false;
				}
			else{
				var message1=confirm("注意：产品名称的总长度不可以超过60个字符（即30个中文字符），如果超出此范围系统将做截取！是否继续操作？");
				if (message1==true){
					if(Character_NEW==""){
						var message2=confirm("提醒：新字符为空，即产品名称将 "+Character_OLD+" 的字符！是否继续操作？");
						if(message2==true){
							document.form1.action="productdata_other_up.php?Action="+Action;document.form1.submit();
							}
						else{
							return false;
							}
						}
					else{
						document.form1.action="productdata_other_up.php?Action="+Action;document.form1.submit();
						}
					}
				else{
					return false;
					}
				}
			break;
			
		case 3:
			var NewPriceTemp=document.form1.NewPrice.value;
			if(NewPriceTemp==""){
				alert("价格不能为空！");
				return false;
				}
			else{
				//判断价格是否符合要求
				var ckeckSign=fucCheckNUM(NewPriceTemp,"Price");
				if(ckeckSign==0){
					alert("单价格式不对！");
					return false;
					}
				else{
					if(The_Selectd.options.length>0 || Tid!=""){						
						document.form1.action="productdata_other_up.php?Action="+Action;document.form1.submit();
						}
					else{
						alert("没有指定分类或产品！");
						return false;
						}
					}
				}
		break;
		case 4:
			if(The_Selectd.options.length>0 || Tid!=""){
				var newTid=document.getElementById('NewTypeId').value;
				if(newTid!=""){
					document.form1.action="productdata_other_up.php?Action="+Action;document.form1.submit();}
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
		case 5: //add 2011-05-27
		    var NewQcPicture=document.form1.QcPicture.value;
			if(NewQcPicture==""){
			    alert("请添加图片！");
				return false;
				}
			else{
			     if(The_Selectd.options.length>0 || Tid!=""){
			         //alert(NewQcPicture);
				     var picturetype=NewQcPicture.toLowerCase().substr(NewQcPicture.lastIndexOf(".")); 
				     if(picturetype!=".jpg"){
				           alert("请选择jpg格式的照片上传");
						   return false;
				         }
				     else{
			              document.form1.action="productdata_other_up.php?Action="+Action;document.form1.submit();
		                 }
                  }
				  else{
				    alert("没有指定分类或产品！");
					return false;
				  }
			 } 
		 break;
		 case 6:
		      var ProductId=document.form1.ProductId.value;
			  if(ProductId==""){
			       alert("请选择被复制的产品！");
				   return false;
				   }
			  else{
			      if(The_Selectd.options.length>0 || Tid!=""){						
				document.form1.action="productdata_other_up.php?Action="+Action+"&ProductId"+ProductId;                
				document.form1.submit();
						}
					else{
						alert("没有指定要复制的产品！");
						return false;
						}
			      }
		 
		 break; 
		  case 7:
		      var NewCompanyId=document.form1.NewCompanyId.value;
			  if(NewCompanyId==""){
			       alert("请选择新的客户名称！");
				   return false;
				   }
			  else{
			      if(The_Selectd.options.length>0){						
				document.form1.action="productdata_other_up.php?Action="+Action+"&NewCompanyId"+NewCompanyId;                
				document.form1.submit();
						}
					else{
						alert("没有指定要更新的产品！");
						return false;
						}
			      }
		 
		 break; 
         case 8:
              var NewbuySign=document.form1.NewbuySign.value;
              if(NewbuySign==""){
                   alert("请选择新的产品属性！");
                   return false;
                   }
              else{
                  if(The_Selectd.options.length>0){                     
                document.form1.action="productdata_other_up.php?Action="+Action+"&NewbuySign"+NewbuySign;                
                document.form1.submit();
                        }
                    else{
                        alert("没有指定要更新的产品！");
                        return false;
                        }
                  }
         
         break; 

		}
	}
</script>