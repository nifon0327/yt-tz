<?php 
//步骤1 $DataIn.producttype 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 产品分类管理的其它功能操作");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_other";	
$toWebPage  =$funFrom."_other_up";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/other_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Orderby,$Orderby,Pagination,$Pagination,Page,$Page";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
      <td height="28" class="A1011">1、首字母重整：分类名称的拼音首字母重新整理，用于排序及快速查找</td>
      </tr>
    <tr>
		<td height="24" class="A0111" align="right"><input name="Submit" type="button"  value="开始字母重整" onClick="CheckForm(1)"></td>
    </tr>
    <tr>
      <td height="30" class="A0011">2、产品的类型转换：将属于分类A的全部产品，改为属于分类B</td>
	</tr>
    <tr>
      <td height="33" class="A0111" align="right">
	  <?php 
		$result = mysql_query("SELECT * FROM $DataIn.producttype order by Letter",$link_id);
		$SelectStr="产品分类列表~";
		while ($myrow = mysql_fetch_array($result)){
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];					
			$SelectValue=$TypeName."~".$myrow["TypeId"]."|".$myrow["TypeName"];
			if ($SelectValue!=""){
				$SelectStr=$SelectStr."~".$SelectValue;}
				} 
			$Default_Str="产品分类列表";
  			echo "产品原属&nbsp;<select name='oldTypeId' id='oldTypeId'>
				<SCRIPT type=text/javascript>
		  	OutputSelects(\"$SelectStr\",\"$Default_Str\",2);
			</script></select>";
			echo"&nbsp;转属于&nbsp;";
			 echo "<select name='newTypeId' id='newTypeId'>
			<SCRIPT type=text/javascript>
		  	OutputSelects(\"$SelectStr\",\"$Default_Str\",2);
			</script></select>";

		?>
		&nbsp;&nbsp;<input type="button" name="Submit" value="开始分类转换" onClick="CheckForm(2)">
		</td>
    </tr>
    <tr>
      <td height="33" class="A0011">3、清除无用分类：把没有使用到的分类删除</td>
      </tr>
    <tr>
      <td height="36" class="A0111" align="right"><input type="button" name="Submit" value="开始清除分类" onClick="CheckForm(3)"></td>
    </tr>
  </table>	 
 <?php 
//步骤5：
include "../model/subprogram/other_model_b.php";
?>
<script language = "JavaScript"> 
function CheckForm(Action){
	if(Action==2){//需提醒
		var oldTypeId=	document.getElementById('oldTypeId').value;
		var newTypeId=	document.getElementById('newTypeId').value;
		if(oldTypeId=="" || newTypeId=="" || oldTypeId==newTypeId){
			alert("没选分类或转换前后的分类名称一样！");
			return false;
			}
		else{
			var message=confirm("分类转换过程不可恢复，请确定是否进行转换？");
   			if (message==true){
				//检查是否选好分类：1、分类不能为空 2、转换前后分类名不能一样
				document.form1.action="producttype_other_up.php?Action="+Action;document.form1.submit();
				}
			else{
				return false;
				}
			}
		}
	else{
		var message=confirm("确定进行操作吗？");
   		if (message==true){
			document.form1.action="producttype_other_up.php?Action="+Action;document.form1.submit();}
		else{
			return false;
			}
		}	
	}
</script>
