<?php   
//步骤1电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 通货记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page,ReQtyType,$ReQtyType";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";

echo"<input name='ReEstate' type='hidden' id='ReEstate' value='$Estate&ReQtyType=$ReQtyType'>";   //为了传递明细或统计，借用了EState  add by zx 2012-10-10

//步骤4：需处理
$CheckTb="$DataPublic.branchdata";
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
                <TR>
                  <TD width="105" align="right"><p>Product Code
                      <input name="Field[]" type="hidden" id="Field[]" value="eCode">
                  </p>
                  </TD>
                  <TD width="87" align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD width="366"><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="R">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
                <tr>
                  <td align="right"><p>退货月份
                      <input name="Field[]2" type="hidden" id="Field[]2" value="ReturnMonth" />
                  </p></td>
                  <td align="center"><select name="fun[]2" id="fun[]2" style="width: 60px;">
                    <option value="LIKE" selected="selected">包含</option>
                    <option value="=">=</option>
                    <option 
          value="!=">!=</option>
                  </select></td>
                  <td><input name="value[]2" class="textfield" id="value[]2" size="48" />
                    <input name="table[]2" type="hidden" id="table[]2" value="R" />
                    <input name="types[]2" type="hidden" id="types[]2"
                value="isStr" /></td>
                </tr>
                <TR>
                  <TD align="right">退货数量
                    <input name="Field[]" type="hidden" id="Field[]" value="Qty">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION 
          value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION 
          value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="R">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">单价
                    <input name="Field[]" type="hidden" id="Field[]" value="Price">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION 
          value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION 
          value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="R">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>	
                
                
               <TR>
                  <TD align="right" >产品分类
                    <input name="Field[]" type="hidden" id="Field[]" value="TypeId"></TD>
                  <TD align="center" >
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD ><select name="value[]" id="value[]" style="width: 274px;">
                    <?php   
					echo"<option value='' selected>全部</option>";
					$result = mysql_query("SELECT P.TypeId,T.TypeName,T.Letter,C.Color 
	FROM (select Distinct eCode from $DataIn.product_returned )  R 
	LEFT JOIN $DataIn.productdata P ON P.eCode=R.eCode
	LEFT JOIN $DataIn.ProductType T ON T.TypeId=P.TypeId
	LEFT JOIN $DataIn.productmaintype C ON C.Id=T.mainType
	WHERE T.Estate=1 GROUP BY P.TypeId ORDER BY T.mainType DESC,T.Letter",$link_id);
					while ($myrow = mysql_fetch_array($result)){
						$Letter=$myrow["Letter"];
						$TypeId=$myrow["TypeId"];
						$TypeName=$myrow["TypeName"];
						echo "<option value='$TypeId'>$Letter-$TypeName</option>";
						} 
					?>
					</select>
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
                                
                			               
                <TR>
                  <TD align="right">可用状态
                    <input name="Field[]" type="hidden" id="Field[]" value="Estate">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION 
          value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION 
          value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <option selected  value="">全部</option>
                    <option value="1">可用</option>
                    <option value="0">不可用</option>
                                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="R">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                
                <TR>
                  <TD align="right">更新日期
                    <input name="Field[]" type="hidden" id="Field[]" value="Date">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== 
          selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION 
          value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION 
          value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="R">
  <input name="types[]" type="hidden" id="types[]" value="isDate">
</TD>
                </TR>
                <TR>
                  <TD align="right">操 作 员
                    <input name="Field[]" type="hidden" id="Field[]" value="Operator">
                  </TD>
                  <TD align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>                  </TD>
                  <TD>
				  <select name=value[] id="value[]" style="width: 274px;">
				  	<option value="" selected>全部</option>
                    <?php   
					include "../model/subprogram/select_model_stafflist.php";
					?>
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="R">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">锁定状态
                    <input name="Field[]" type="hidden" id="Field[]" value="Locks">
                  </TD>
                  <TD align="center">
				    <select name="fun[]" id="fun[]" style="width: 60px;">
			          <option value="=">=</option>
		            </select>			        </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width: 274px;">
						<option selected  value="">全部</option>
						<option value="0">锁定</option>
						<option value="1">未锁定</option>
					  </select>
					  <input name="table[]" type="hidden" id="table[]" value="R">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                
                <!---
                
 				<TR>
                  	<TD align="right">选择&nbsp;&nbsp;&nbsp;&nbsp;择                  	  <input name="Field[]" type="hidden" id="Field[]" value="Estate"></TD>
                  	<TD align="center">
                    	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <OPTION value== selected>=</OPTION>
                          <OPTION value=!=>!=</OPTION>
               		  </SELECT>
               		</TD>
                  	<TD>
						<select name=value[] id="value[]" style="width: 274px;">
					 	<

							if($Estate==0){
								echo"<option value='0'>明细</option>";
								$EstatePass=" WHERE cwdyfsheet.Estate=0";
								}
							else{
								echo"<option value='3'>总计</option>";
								$EstatePass=" WHERE cwdyfsheet.Estate=3";
								}

						?>
						</select>
                    	<input name="table[]" type="hidden" id="table[]" value="P">
                    	<input name="types[]" type="hidden" id="types[]" value="isNum">
					</TD>
                </TR>               
                -->
                
              </TBODY>
	    </TABLE>
		</td>
	</tr>
    
</table>
<?php   
//步骤5：
include "../model/subprogram/select_model_b.php";
?>