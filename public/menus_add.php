<?php 
//电信-EWEN
//代码、数据库共享，加标识-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增功能模块资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";//需处理
//步骤3：
$MaxResult =mysql_fetch_array(mysql_query("SELECT MAX(ModuleId) AS maxModuleId FROM $DataIn.ac_menus",$link_id));
$maxModuleId=$MaxResult["maxModuleId"]+1;
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5">
		<tr>
            <td width="98" scope="col" align="right">菜单标识</td>
            <td width="633" scope="col">
              <?php 
			  $SharingShow="Y";//显示共享
              include "../model/subselect/cSign.php";
			  ?>
			</td></tr>
          <tr>
            <td width="98" scope="col" align="right">菜单类型</td>
            <td  scope="col">
                 <select name="level"  id="level" style="width:380px" dataType="Require"  msg="未选择菜单类型"  onchange="changeMenu(this)">
              <option value="" >请选择</option>
              <option value="1" >一级菜单</option>
              <option value="2" >二级菜单</option>
            </select>
			</td>
           </tr> 
          <tr style="display:none;" id="showParentMenu"></tr> 
		  <tr>
				  <td scope="col" align="right">菜单ID</td>
		          <td scope="col"><input name="ModuleId" type="text" id="ModuleId" style="width:380px" value="<?php echo $maxModuleId?>" dataType="Require"  msg="未输入菜单ID"> 
	              </td>
		  </tr>

				<tr>
            <td scope="col" align="right">菜单名称</td>
            <td scope="col"><input name="name" type="text" id="name" style="width:380px" title="可输入1-20个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="20" Min="1" Msg="没有填写或字符超出20字节">
            </td>
				</tr>
				<tr>
				  <td scope="col" align="right">action</td>
		          <td scope="col"><input name="action" type="text" id="action" style="width:380px" title="可输入不超过100个字节的参数(每1英文字母占1个字节)" Max="100" Msg="字符超出100字节"> 
	              </td>
		  </tr>
				<tr>
				  <td scope="col" align="right">回呼</td>
		          <td scope="col"><input name="callback" type="text" id="callback" style="width:380px" title="可输入不超过100个字节的参数(每1英文字母占1个字节)" Max="100" Msg="字符超出100字节"> 
	              </td>
		  </tr>
				<tr>
				  <td scope="col" align="right">标章</td>
		          <td scope="col"><input name="badges" type="text" id="badges" style="width:380px" title="可输入不超过100个字节的参数(每1英文字母占1个字节)" Max="100" Msg="字符超出100字节"> 
	              </td>
		  </tr>
				<tr>
				  <td scope="col" align="right">图标类型</td>
		          <td scope="col"><input name="icon_type" type="text" id="icon_type" style="width:380px" value="0"> 
	              </td>
		  </tr>

				<tr>
				  <td scope="col" align="right">绝对位置</td>
		          <td scope="col"><input name="abs" type="text" id="abs" style="width:120px" value="0"> 
	              </td>
		  </tr>
				<tr>
				  <td scope="col" align="right">位置(横向)</td>
		          <td scope="col"><input name="row" type="text" id="row" style="width:120px" value="0"> 
	              </td>
		  </tr>
				<tr>
				  <td scope="col" align="right">位置(纵向)</td>
		          <td scope="col"><input name="col" type="text" id="col" style="width:120px"  value="0"> 
	              </td>
		  </tr>
				<tr>
				  <td scope="col" align="right">排序号码</td>
				  <td scope="col"><input name="order" type="text" id="order" style="width:380px" value="1" readonly></td>
		  </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function  changeMenu(e){
    if(e.value==2){
               		var	myurl="meuns_ajax.php?ActionId=1";
					var ajax=InitAjax(); 
					ajax.open("GET",myurl,true);
					ajax.onreadystatechange =function(){
					if(ajax.readyState==4){// && ajax.status ==200
                             document.getElementById("showParentMenu").style.display="";
                              document.getElementById("showParentMenu").innerHTML=ajax.responseText;
						}
                   }
					ajax.send(null); 	
          }
   else{
               		var	myurl="meuns_ajax.php?ActionId=2";
					var ajax=InitAjax(); 
					ajax.open("GET",myurl,true);
					ajax.onreadystatechange =function(){
					if(ajax.readyState==4){// && ajax.status ==200
                             document.getElementById("showParentMenu").style.display="";
                              document.getElementById("showParentMenu").innerHTML=ajax.responseText;
						}
                   }
					ajax.send(null); 	
        }
}
</script>