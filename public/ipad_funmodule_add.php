<?php 
//代码、数据库共享-zx
//电信-ZX
//代码共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增ipad功能模块资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";//需处理
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5">
        
          <tr>
   			 <td width="98" align="right" >所属公司: </td>
   			 <td >
				<?php 
     			 //选择公司名称
       		  $SharingShow="Y";
			  $cSign=$_SESSION["Login_cSign"] ;
              include "../model/subselect/cSign.php";
   			  ?>
		   </td>
 		 </tr>
        
		<tr>
            <td width="98" scope="col" align="right">位&nbsp;&nbsp;&nbsp;&nbsp;置</td>
            <td width="633" scope="col">
              <select name="Place" id="Place" style="width:380px" dataType="Require" msg="未选择">
			  	<option value="" selected>请选择</option>
                <option value="1">ipad主项目</option>
                <option value="2">ipad子项目</option>
              </select>
			</td></tr> 
				<tr>
            <td scope="col" align="right">功能名称</td>
            <td scope="col"><input name="ModuleName" type="text" id="ModuleName" style="width:380px" title="可输入1-20个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="20" Min="1" Msg="没有填写或字符超出20字节">
            </td>
				</tr>
				<tr>
				  <td scope="col" align="right">连接参数</td>
		          <td scope="col"><input name="Parameter" type="text" id="Parameter" style="width:380px" title="可输入不超过100个字节的参数(每1英文字母占1个字节)" Max="100" Msg="字符超出100字节"> 
	              </td>
		  </tr>
				<tr>
				  <td scope="col" align="right">排序号码</td>
				  <td scope="col"><input name="OrderId" type="text" id="OrderId" style="width:380px" value="1" readonly></td>
		  </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>