<?php 
//代码、数据共享-EWEN 2012-09-18
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新菜单");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT  *   FROM  $DataIn.ac_menus   WHERE id=$Id",$link_id));
$parent_id=$upData["parent_id"];
$menuname=$upData["name"];
$action=$upData["action"];
$icon=$upData["icon"];
$row=$upData["row"];
$col=$upData["col"];
$icon_type=$upData["icon_type"];
$badges=$upData["badges"];
$abs=$upData["abs"];
$typeid=$upData["typeid"];
$callback=$upData["callback"];
$order=$upData["order"];
$ModuleId=$upData["ModuleId"];
$abs=$upData["abs"];
$level=$upData["level"];
$cSign=$upData["csign"];
$levelname = $level ==1?"一级菜单":"二级菜单";

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,level,$level";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="600" border="0" align="center" cellspacing="5">
		<tr>
            <td scope="col" align="right">菜单标识</td>
            <td width="460" scope="col"><?php 
			  $SharingShow="Y";//显示共享
              include "../model/subselect/cSign.php";
			  ?>
			</td>
		</tr>

	  <tr>
				  <td scope="col" align="right">菜单ID</td>
		          <td scope="col"><input name="ModuleId" type="text" id="ModuleId" style="width:380px" value="<?php echo $ModuleId?>" dataType="Require"  msg="未输入菜单ID"> 
	              </td>
		  </tr>

		<tr>
            <td scope="col" align="right">菜单名称</td>
            <td scope="col"><input name="name" type="text" id="name" style="width:380px;" maxlength="30" value="<?php  echo $menuname?>" title="可输入1-20个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="20" Min="1" Msg="没有填写或字符超出20字节">
			</td>
		</tr>
		<tr>
            <td scope="col" align="right">菜单类型</td>  <td scope="col"><?php  echo $levelname?></td>
		</tr>
<?php
 if($level==1){
?>
		<tr>
            <td scope="col" align="right">一级菜单类型</td>  <td scope="col">
          <select name="typeid" id="typeid"  style="width:380px" dataType="Require"  msg="未选择一级菜单类型">
           <option value='' >请选择</option>
            <?php
			 $mysql = "SELECT   id,name FROM ac_menutypes order by id ASC ";
			 $myResult = mysql_query($mysql);
			 while($myRow = mysql_fetch_array($myResult)){
			      $id = $myRow["id"];
			      $name = $myRow["name"];
                  if($typeid==$id) echo"<option value='$id' selected>$name</option>";
                  else  echo"<option value='$id' >$name</option>";
			}
           ?>
          </select></td>
		</tr>
<?php
}else{
?>
		<tr>
            <td scope="col" align="right">一级菜单</td>  <td scope="col">
            <select name="parent_id" id="parent_id"  style="width:380px" dataType="Require"  msg="未选择一级菜单">
         <option value='' >请选择</option>
            <?php
			 $mysql = "SELECT   M.id,M.name,T.name AS TypeName FROM ac_menus  M 
			LEFT JOIN ac_menutypes  T ON T.id = M.typeid
			 WHERE parent_id=0";
			 $myResult = mysql_query($mysql);
			 while($myRow = mysql_fetch_array($myResult)){
			      $id = $myRow["id"];
			      $name = $myRow["name"];
			      $TypeName = $myRow["TypeName"];
                 if($parent_id==$id) echo"<option value='$id' selected>$TypeName-$name</option>";
               else  echo"<option value='$id' >$TypeName-$name</option>";
			}
           ?>
          </select>
          </td>
		</tr>
<?php
}
?>
		<tr>
				  <td scope="col" align="right">action</td>
		          <td scope="col"><input name="action" type="text" id="action" style="width:380px" value="<?php  echo $action?>"  title="可输入不超过100个字节的参数(每1英文字母占1个字节)" Max="100" Msg="字符超出100字节"> 
	              </td>
		  </tr>
				<tr>
				  <td scope="col" align="right">回呼</td>
		          <td scope="col"><input name="callback" type="text" id="callback" style="width:380px" value="<?php  echo $callback?>" title="可输入不超过100个字节的参数(每1英文字母占1个字节)" Max="100" Msg="字符超出100字节"> 
	              </td>
		  </tr>
				<tr>
				  <td scope="col" align="right">标章</td>
		          <td scope="col"><input name="badges" type="text" id="badges" style="width:380px" value="<?php  echo $badges?>" title="可输入不超过100个字节的参数(每1英文字母占1个字节)" Max="100" Msg="字符超出100字节"> 
	              </td>
		  </tr>

				<tr>
				  <td scope="col" align="right">图标类型</td>
		          <td scope="col"><input name="icon_type" type="text" id="icon_type" style="width:380px" value="<?php  echo $icon_type?>"> 
	              </td>
		  </tr>

				<tr>
				  <td scope="col" align="right">绝对位置</td>
		          <td scope="col"><input name="abs" type="text" id="abs" style="width:120px" value="<?php  echo $abs?>"> 
	              </td>
		  </tr>

				<tr>
				  <td scope="col" align="right">位置(横向)</td>
		          <td scope="col"><input name="row" type="text" id="row" style="width:120px" value="<?php  echo $row?>" > 
	              </td>
		  </tr>
				<tr>
				  <td scope="col" align="right">位置(纵向)</td>
		          <td scope="col"><input name="col" type="text" id="col" style="width:120px"  value="<?php  echo $col?>" > 
	              </td>
		  </tr>
				<tr>
				  <td scope="col" align="right">排序号码</td>
				  <td scope="col"><input name="order" type="text" id="order" style="width:380px" value="<?php  echo $order?>"  readonly></td>
		  </tr>

        </table>
	</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>