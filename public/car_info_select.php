<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN
require "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 车辆信息查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=800;
//步骤3：
require "../model/subprogram/select_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center"> <TBODY>
			  <tr>
			    <td align="right">使用标识
			      <input name="Field[]2" type="hidden" id="Field[]2" value="cSign" /></td>
			    <td align="center"><select name="fun[]2" id="fun[]3" style="width: 60px;">
			      <option value="=" 
                  selected="selected">=</option>
			      <option value="!=">!=</option>
			      </select></td>
			    <td><?php 
					$SelectFrom=4;
               		require "../model/subselect/cSign.php";
               	 ?>
			      <input name="table[]2" type="hidden" id="table[]2" value="A" />
			      <input name="types[]2" type="hidden" id="types[]2" value="isNum" /></td>
		      </tr>
                <TR>
                  <TD align="right">车辆类型
                      <input name="Field[]" type="hidden" id="Field[]" value="TypeId">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
                  selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD>
			   	<?php 
               		require "../model/subselect/CarType.php";
               	 ?>
                <input name="table[]" type="hidden" id="table[]" value="A">
                <input name="types[]" type="hidden" id="types[]" value="isNum">
	
               </td>
               </tr>
			 <TR>
                  <TD align="right">车辆简称
                      <input name="Field[]" type="hidden" id="Field[]" value="carListNo ">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
                  selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><select name="value[]" id="value[]" style="width:380px">
                  <option value="" selected>请选择</option>
           <?php 
          $CheckSql=mysql_query("SELECT Id,carListNo  FROM $DataPublic.cardata WHERE Estate=1 AND Locks=0",$link_id);
		   if($CheckRow=mysql_fetch_array($CheckSql)){
			do{
			
			   $Id=$CheckRow["Id"];
			   $Name = $CheckRow["carListNo"];
			   echo "<option value='$Name'>$Name</option>";
				
			   }while($CheckRow=mysql_fetch_array($CheckSql));
			}
		    ?>
                </select>
                <input name="table[]" type="hidden" id="table[]" value="A">
                <input name="types[]" type="hidden" id="types[]"  value="isStr">
			   
               </td>
               </tr>
		  
			   <tr>
                   <td align="right">保险期限
                      <input name="Field[]" type="hidden" id="Field[]" value="InsuranceDate ">
                   </td>
                  <td align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  
				  </td>
                  <td><INPUT name=value[] class=textfield id="value[]" style="width:380px" maxlength="10">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </td>
                </tr>
			   
			    <tr>
                   <td align="right">年检时间
                      <input name="Field[]" type="hidden" id="Field[]" value="Checktime ">
                   </td>
                  <td align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  
				  </td>
                  <td><INPUT name=value[] class=textfield id="value[]" style="width:380px" maxlength="10">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </td>
                </tr>
				
				
				<tr>
                   <td align="right">购车时间
                      <input name="Field[]" type="hidden" id="Field[]" value="BuyDate">
                   </td>
                  <td align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  
				  </td>
                  <td><INPUT name=value[] class=textfield id="value[]" style="width:380px" maxlength="10">
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </td>
                </tr>
			   
			   
			   <tr>
               <td align="right">购车地址
                   <input name="Field[]" type="hidden" id="Field[]" value="BuyAddress">
                 </td>
                 <td align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>			        
				</td>
                 <td>
					<INPUT name=value[] class=textfield id="value[]" style="width:380px">
				    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]" value="isStr" />
                </td>
                </tr>
				
				<tr>
				<td width="115" align="right">保养负责人
                    <input name="Field[]" type="hidden" id="Field[]" value="Maintainer"></td>
                  <td align="center"><select name="fun[]2" id="fun[]2" style="width: 60px;">
                    <option value="LIKE" selected="selected">包含</option>
                    <option value="=">=</option>
                    <option value="!=">!=</option>
                  </select></td>
                 <td><input name="value[]2" class="textfield" id="value[]2" style="width:380px"/>
                   <input name="table[]" type="hidden" id="table[]" value="A">
               <input name="types[]" type="hidden" id="types[]" value="isStr" />
			   
               </td>
               </tr>
			   
			   
			  </TBODY>
	    </TABLE>
	  </td>
	</tr>
</table>

<?php 
//步骤5：
require "../model/subprogram/select_model_b.php";
?>