<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-15
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增公司基本资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5">
         <tr>
            <td width="89" scope="col" align="right">所属公司</td>
            <td scope="col">
              <?php 
              include "../model/subselect/cSign.php";
			  ?>
			</td></tr>
		 <tr>
		   	<td width="89" align="right" scope="col">类别</td>
		   	<td scope="col">
              <select name="Type" id="Type" style="width:380px" dataType="Require"  msg="未选择">
                <option value="" selected>请选择</option>
                <option value="S">简体中文</option>
                <option value="C">繁体中文</option>
                <option value="E">英文</option>
              </select></td>
	      </tr> 
          <tr>
            <td align="right" >公司全称</td>
            <td><input name="Company" type="text" id="Company" style="width:380px" dataType="LimitB" max="100" min="2" msg="必须在2-100个字节之内"></td>
          </tr>
          <tr>
            <td align="right">公司简称</td>
            <td><input name="Forshort" type="text" id="Forshort" style="width:380px" dataType="LimitB" max="20" min="2" msg="必须在2-20个字节之内"></td>
          </tr>
          <tr>
            <td align="right">公司电话</td>
            <td><input name="Tel" type="text" id="Tel" style="width:380px"></td>
          </tr>
          <tr>
            <td align="right">公司传真</td>
            <td><input name="Fax" type="text" id="Fax" style="width:380px" require="false"></td>
          </tr>
          <tr>
            <td align="right">网&nbsp;&nbsp;&nbsp;&nbsp;址</td>
            <td><input name="WebSite" type="text" id="WebSite" style="width:380px"></td>
          </tr>
          <tr>
            <td align="right">邮政编码</td>
            <td><input name="ZIP" type="text" id="ZIP" style="width:380px" require="false" dataType="Custom" regexp="^[1-9]\d{5}$" msg="邮政编码不存在"></td>
          </tr>
          <tr>
            <td align="right">通信地址</td>
            <td><input name="Address" type="text" require="false" id="Address" style="width:380px" ataType="Limit" max="100" msg="必须在100个字之内"></td>
          </tr>
          <tr>
            <td align="right">联 系 人</td>
            <td><input name="LinkMan" type="text" id="LinkMan" style="width:380px" dataType="Limit" max="20" min="2" msg="必须在2-20个字之内"></td>
          </tr>
          <tr>
            <td align="right">移动电话</td>
            <td><input name="Mobile" type="text" id="Mobile" style="width:380px" require="false"></td>
          </tr>
          <tr>
            <td align="right">邮件地址</td>
            <td><input name="Email" type="text" id="Email" style="width:380px" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>
        </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>