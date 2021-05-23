<?php 
//电信-EWEN
//更新到public by zx 2012-08-03
//代码、数据共享-EWEN 2012-08-14
include "../model/modelhead.php";
include "../model/livesearch/modellivesearch.php";//用于在文本框输入文字显示与之相关的内
//步骤2：
ChangeWtitle("$SubCompany 新增会计科目分类");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">
    
		<tr>
            <td height="42" scope="col" align="right" >科目代码</td>
            <td scope="col"><input name="FirstId" type="text" id="FirstId" value="" style="width:380px;" dataType="LimitB" msg="不合要求"
            onkeyup="showResult(this.value,'FirstId','acfirsttype',1,'')" onblur="LoseFocus()" autocomplete="off" ></td>
         </tr>
        
 		<tr>
            <td height="31" scope="col" align="right">科目名称</td>
            <td scope="col"><input name="Name" type="text" id="Name" style="width:380px;" maxlength="20" title="必选项,在20个汉字内." dataType="LimitB" min="1" max="40" msg="没有填写或超出许可范围">
        	</td>
        </tr> 
        
 	   <tr>
	    <td height="31" scope="col" align="right">类别</td>
           <td scope="col"><select name="TypeId" id="TypeId" style="width: 380px;"  dataType="Require"  msg="未选择">
              <?php 
	          $mySql="SELECT Id,Name FROM $DataPublic.actype WHERE Estate=1 order by Name";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $unitId=$myrow["Id"];
			     $unitName=$myrow["Name"];
				   echo "<option value='$unitId'>$unitName</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
            </select>
          </td>
	    </tr>
        
  	   <tr>
	    <td height="31" scope="col" align="right">方向</td>
           <td scope="col"><select name="OISignId" id="OISignId" style="width: 380px;"  dataType="Require"  msg="未选择">
              <?php 
	          $mySql="SELECT Id,Name FROM $DataPublic.acOISign WHERE Estate=1 order by Id";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $unitId=$myrow["Id"];
			     $unitName=$myrow["Name"];
				   echo "<option value='$unitId'>$unitName</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
            </select>
          </td>
	    </tr>   
        
        
  	   <tr>
	    <td height="31" scope="col" align="right">外币核算</td>
           <td scope="col"><select name="CalCurrencyId" id="CalCurrencyId" style="width: 380px;"  dataType="Require"  msg="未选择">
              <?php 
	          $mySql="SELECT Id,Name FROM $DataPublic.acCalCurrency WHERE Estate=1 order by Id";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $unitId=$myrow["Id"];
			     $unitName=$myrow["Name"];
				   echo "<option value='$unitId'>$unitName</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
            </select>
          </td>
	    </tr>    
        
        
  	   <tr>
	    <td height="31" scope="col" align="right">期末调汇</td>
           <td scope="col"><select name="EndTermSignId" id="EndTermSignId" style="width: 380px;"  dataType="Require"  msg="未选择">
              <?php 
	          $mySql="SELECT Id,Name FROM $DataPublic.acEndTermSign WHERE Estate=1 order by Id";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $unitId=$myrow["Id"];
			     $unitName=$myrow["Name"];
				   echo "<option value='$unitId'>$unitName</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
            </select>
          </td>
	    </tr> 
                         

  	   <tr>
	    <td height="31" scope="col" align="right">辅助核算</td>
           <td scope="col"><select name="AssistCalId" id="AssistCalId" style="width: 380px;"  dataType="Require"  msg="未选择">
              <?php 
	          $mySql="SELECT Id,Name FROM $DataPublic.acAssistCal WHERE Estate=1 order by Id";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $unitId=$myrow["Id"];
			     $unitName=$myrow["Name"];
				   echo "<option value='$unitId'>$unitName</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
            </select>
          </td>
	    </tr>
               
		<tr>
            <td valign="top" align="right">备 &nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" style="width:380px;" rows="3" id="Remark"></textarea></td>
          </tr>
          <tr>
            <td valign="top" align="right">&nbsp;</td>
            <td><input name='ExpenseSign' type='checkbox' id='ExpenseSign' value='1'>是否为“费用报销”选项
	            &nbsp;&nbsp;&nbsp;&nbsp;
	            <input name='IntangibleSign' type='checkbox' id='IntangibleSign' value='1'>是否为“无形资产”选项
            </td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>