<?php   
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 数量关系设定");//需处理
//步骤3：
$tableWidth=350;$tableMenuS=200;
$CustomFun="<span onClick='javascript:CheckForm();' $onClickCSS>确定</span> ";//自定义功能
$SaveSTR="NO";
$isBack="N";
$Parameter="Q,Q";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr><td height="30" valign="middle" class='A0011'>&nbsp;&nbsp;请输入产品与配件的对应数量: </td></tr>
	<tr><td class='A0011' align="center"><input name="Qty" type="text" id="Qty" value="1" size="10"></td></tr>
	<tr><td class='A0011' height="40">&nbsp;&nbsp;默认数量关系为1，直接关闭窗口则取消加入配件需求单.</td></tr>
</table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function CheckForm(){ 
	var Qty= document.form1.Qty.value; 
	if(window.opener){
		if (window.opener.document.getElementById("SafariReturnQty")) {  //专为safari设计 zx 2011-05-04
			window.opener.document.getElementById("SafariReturnQty").value = Qty;
			//alert ("Here");
		}
	}
	window.returnValue=Qty; 
	window.close(); 
	} 
</script> 