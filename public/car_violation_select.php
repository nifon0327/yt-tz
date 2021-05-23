<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 车辆违规记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=800;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="570" border=0 align="center">
              <TBODY>
                <tr>
                 <td align="right">违规时间
                   <input name="Field[]2" type="hidden" id="Field[]2" value="Redate" /></td>
                 <td align="center"><select name="fun[]3" id="fun[]4" style="width: 60px;">
                   <option value="=" 
          selected="selected">=</option>
                   <option value="&gt;">&gt;</option>
                   <option 
          value="&gt;=">&gt;=</option>
                   <option value="&lt;">&lt;</option>
                   <option 
          value="&lt;=">&lt;=</option>
                   <option value="!=">!=</option>
                 </select></td>
                 <td><input name="value[]2" class="textfield" id="value[]2" style="width:180px" onfocus="WdatePicker()" readonly="readonly" />
                   至
                   <input name="DateArray[]" class="textfield" id="DateArray[]" style="width:180px" onfocus="WdatePicker()" readonly="readonly" />
                   <input name="table[]2" type="hidden" id="table[]2" value="A" />
                   <input name="types[]2" type="hidden" id="types[]2" value="isDate" /></td>
               </tr>
				
				<TR>
                  <TD align="right">违规车辆
                      <input name="Field[]" type="hidden" id="Field[]" value="CarId">
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
             $CarSql=mysql_query("SELECT DISTINCT A.CarId,B.CarNo from $DataPublic.car_violation A LEFT JOIN $DataPublic.cardata B ON B.Id=A.CarId ",$link_id);
		     if($CheckRow=mysql_fetch_array($CarSql)){
				do{
			   		$CarId=$CheckRow["CarId"];
					$CarNo=$CheckRow["CarNo"];
			   		echo "<option value='$CarId'>$CarNo</option>";
			   		}while($CheckRow=mysql_fetch_array($CarSql));
				}
		      ?>
            </select>
			
                <input name="table[]" type="hidden" id="table[]" value="V">
                <input name="types[]" type="hidden" id="types[]" value="isStr">
	
               </td>
               </tr>
			   
			   <TR>
                  <TD align="right">违规人
                      <input name="Field[]" type="hidden" id="Field[]" value="Person">
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
         
            $PersonSql=mysql_query("select DISTINCT Person from  $DataPublic.car_violation",$link_id);
		   
		     if($myRow=mysql_fetch_array($PersonSql))
			 {
			   do{
			       $Name = $myRow["Person"];
			       echo "<option value='$Name'>$Name</option>";
				
			     }while($myRow=mysql_fetch_array($PersonSql));
			  }
		   
		       ?>
            </select>
			
                <input name="table[]" type="hidden" id="table[]" value="V">
                <input name="types[]" type="hidden" id="types[]" value="isStr">
	
               </td>
               </tr>
			   <tr>
                   <td align="right">违规费用
                      <input name="Field[]" type="hidden" id="Field[]" value="Charge ">
                   </td>
                  <td align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  
				  </td>
                  <td><INPUT name=value[] class=textfield id="value[]" style="width:380px">
                    <input name="table[]" type="hidden" id="table[]" value="V">
                    <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </td>
                </tr>
			   
			   
			   

			  </TBODY>
	    </TABLE>
	  </td>
	</tr>
</table>

<?php 
//步骤5：
include "../model/subprogram/select_model_b.php";
?>