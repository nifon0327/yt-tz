<?php 
//步骤1 $DataIn.cwdyfsheet 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 开发费用查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.cwdyfsheet";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="692" border=0 align="center">
              <TBODY>
				<TR>
				<?php 
				//如果是来自于财务查询
				if($fromWebPage==$funFrom."_cw" && $Estate==0){
				?>				
                  	<TD align="right">结付编号
                  	  <input name="Field[]" type="hidden" id="Field[]" value="Id"></TD><TD width="93" align="center">
               		  	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <OPTION value==  selected>=</OPTION>
                          <OPTION value=!=>!=</OPTION>
               	        </SELECT>
						 </TD>
                	<TD width="460">
						<INPUT name=value[] class=textfield id="value[]" size="48">
                      	<input name="table[]" type="hidden" id="table[]" value="M">
                      	<input name="types[]" type="hidden" id="types[]" value="isNum">
					</TD>
                </TR>
				<TR>
                  	<TD align="right">结付日期
                  	  <input name="Field[]" type="hidden" id="Field[]" value="PayDate"></TD><TD align="center">
               		  	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <OPTION value== selected>=</OPTION>
                          <OPTION value=">">&gt;</OPTION>
                          <OPTION value=">=">&gt;=</OPTION>
                          <OPTION value="<">&lt;</OPTION>
                          <OPTION value="<=">&lt;=</OPTION>
                          <OPTION value=!=>!=</OPTION>
                   	    </SELECT>
					  	</TD>
                	<TD width="460">
						<INPUT name=value[] class=textfield id="value[]" size="18" onfocus="WdatePicker()" readonly>
						至
					  	<INPUT name=DateArray[] class=textfield id="DateArray[]" size="18" onfocus="WdatePicker()" readonly>
					  	<input name="table[]" type="hidden" id="table[]" value="M">
					  	<input name="types[]" type="hidden" id="types[]" value="isDate">
  					</TD>
                </TR>
				<TR>
                  	<TD align="right">结付总额
                  	  <input name="Field[]" type="hidden" id="Field[]" value="PayAmount"></TD><TD align="center">
               		  	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <OPTION value== selected>=</OPTION>
                          <OPTION value=">">&gt;</OPTION>
                          <OPTION value=">=">&gt;=</OPTION>
                          <OPTION value="<">&lt;</OPTION>
                          <OPTION value="<=">&lt;=</OPTION>
                          <OPTION value=!=>!=</OPTION>
                   	    </SELECT>
           		    	</TD>
                	<TD width="460">
						<INPUT name=value[] class=textfield id="value[]" size="48">
                      	<input name="table[]" type="hidden" id="table[]" value="M">
                      	<input name="types[]" type="hidden" id="types[]" value="isNum">
					</TD>
                </TR>
				<TR>
                  	<TD align="right">结付凭证
                  	  <input name="Field[]" type="hidden" id="Field[]" value="Payee"></TD><TD align="center">
               		 	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <OPTION value== selected>=</OPTION>
                          <OPTION value=!=>!=</OPTION>
                   	    </SELECT>
           		    	</TD>
                	<TD width="460">
						<select name=value[] id="value[]" style="width: 274px;">
                      	<option value="" selected>全部</option>
                     	<option value="1">有</option>
                      	<option value="0">没有</option>
						</select>
                      	<input name="table[]" type="hidden" id="table[]" value="M">
                      	<input name="types[]" type="hidden" id="types[]" value="isNum">
					</TD>
                </TR>
				<TR>
                  	<TD align="right">结付回执
                  	  <input name="Field[]" type="hidden" id="Field[]" value="Receipt"></TD><TD align="center">
               		 	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <OPTION value== selected>=</OPTION>
                          <OPTION value=!=>!=</OPTION>
                   	    </SELECT>
           		   		</TD>
                	<TD width="460">
						<select name=value[] id="value[]" style="width: 274px;">
                      	<option value="" selected>全部</option>
                      	<option value="1">有</option>
                      	<option value="0">没有</option>
                    	</select>
                      	<input name="table[]" type="hidden" id="table[]" value="M">
                      	<input name="types[]" type="hidden" id="types[]" value="isNum">
					</TD>
                </TR>
				<TR>
                  	<TD align="right">对 帐 单
                  	  <input name="Field[]" type="hidden" id="Field[]" value="Checksheet"></TD><TD align="center">
               		  	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <OPTION value== selected>=</OPTION>
                          <OPTION value=!=>!=</OPTION>
                   	  </SELECT>
           		    	</TD>
                	<TD width="460">
						<select name=value[] id="value[]" style="width: 274px;">
					  	<option value="" selected>全部</option>
					  	<option value="1">有</option>
					  	<option value="0">没有</option>
                        </select>
                	  	<input name="table[]" type="hidden" id="table[]" value="M">
                      	<input name="types[]" type="hidden" id="types[]" value="isNum">
					</TD>
                </TR>
				<TR>
                  	<TD align="right">结付备注
                  	  <input name="Field[]" type="hidden" id="Field[]" value="Remark"></TD><TD align="center">
               		  	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <option value="LIKE" selected>包含</option>
                          <OPTION value==>=</OPTION>
                          <OPTION value=!=>!=</OPTION>
                   	    </SELECT>
           		    	</TD>
                	<TD width="460">
						<INPUT name=value[] class=textfield id="value[]" size="48">
                      	<input name="table[]" type="hidden" id="table[]" value="M">
                      	<input name="types[]" type="hidden" id="types[]" value="isStr">
					</TD>
                </TR>
				<TR><TD colspan="3">&nbsp;</TD></TR>
			<?php 
			}
			?>
				<TR>
					<TD width="125" align="right">供 应 商
				    <input name="Field[]" type="hidden" id="Field[]" value="Provider"></TD>
                  	<TD align="center">
				   		<SELECT name=fun[] id="fun[]" style="width: 60px;">
                      	  <option value="LIKE" selected>包含</option>
                      	  <OPTION value==>=</OPTION>
                     	  <OPTION value=!=>!=</OPTION>
                  	  </SELECT>
               		</TD>
                  	<TD width="460">
						<INPUT name=value[] class=textfield id="value[]" size="48">                  
						<input name="table[]" type="hidden" id="table[]" value="S">
						<input name="types[]" type="hidden" id="types[]" value="isStr">
					</TD>
                </TR>
				<TR>
					<TD width="125" align="right">项 目 ID
				    <input name="Field[]" type="hidden" id="Field[]" value="ItemId"></TD>
                  	<TD align="center">
                    	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <OPTION value== selected>=</OPTION>
                          <OPTION value=!=>!=</OPTION>
                  	  </SELECT>
               		</TD>
                  	<TD width="460">
				  		<INPUT name=value[] class=textfield id="value[]" size="48">
                    	<input name="table[]" type="hidden" id="table[]" value="S">
                    	<input name="types[]" type="hidden" id="types[]" value="isNum">
                 	</TD>
                </TR>
				<TR>
					<TD align="right">费用金额
                    	<input name="Field[]" type="hidden" id="Field[]" value="Amount">
       				</TD>
                  	<TD align="center">
                    	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                      	  <OPTION value== selected>=</OPTION>
                      	  <OPTION value=">">&gt;</OPTION>
                      	  <OPTION value=">=">&gt;=</OPTION>
                      	  <OPTION value="<">&lt;</OPTION>
                      	  <OPTION value="<=">&lt;=</OPTION>
                      	  <OPTION value=!=>!=</OPTION>
                  	  </SELECT>
               		</TD>
                  	<TD>
						<INPUT name=value[] class=textfield id="value[]" size="48">
						<input name="table[]" type="hidden" id="table[]" value="S">                  
						<input name="types[]" type="hidden" id="types[]" value="isNum">
					</TD>
                </TR>
				<TR>
                	<TD width="125" align="right">费用说明
               	    <input name="Field[]" type="hidden" id="Field[]" value="Description"></TD>
                  	<TD align="center">
                    	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                      	  <option value="LIKE" selected>包含</option>
                      	  <OPTION value==>=</OPTION>
                      	  <OPTION value=!=>!=</OPTION>
                  	  </SELECT>
               		</TD>
                  	<TD width="460">
						<INPUT name=value[] class=textfield id="value[]" size="48">
                    	<input name="table[]" type="hidden" id="table[]" value="S">
                    	<input name="types[]" type="hidden" id="types[]" value="isStr">
					</TD>
                </TR>
                <TR>
                  	<TD align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注                  	  <input name="Field[]" type="hidden" id="Field[]" value="Remark"></TD>
                  	<TD align="center">
                    	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <option value="LIKE" selected>包含</option>
                          <OPTION value==>=</OPTION>
                          <OPTION value=!=>!=</OPTION>
                  	  </SELECT>
               		</TD>
                  	<TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    	<input name="table[]" type="hidden" id="table[]" value="S">
                    	<input name="types[]" type="hidden" id="types[]" value="isStr">
					</TD>
                </TR>
				<TR>
                  	<TD align="right">状&nbsp;&nbsp;&nbsp;&nbsp;态                  	  <input name="Field[]" type="hidden" id="Field[]" value="Estate"></TD>
                  	<TD align="center">
                    	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <OPTION value== selected>=</OPTION>
                          <OPTION value=!=>!=</OPTION>
               		  </SELECT>
               		</TD>
                  	<TD>
						<select name=value[] id="value[]" style="width: 274px;">
					 	<?php 
						if($fromWebPage==$funFrom."_cw"){
							if($Estate==0){
								echo"<option value='0'>已结付</option>";
								$EstatePass=" WHERE cwdyfsheet.Estate=0";
								}
							else{
								echo"<option value='3'>未结付</option>";
								$EstatePass=" WHERE cwdyfsheet.Estate=3";
								}
							}
						else{
							$EstatePass="";
							echo"<option selected  value=''>全部</option>
							<option value='1'>未处理</option>
							<option value='2'>请款中</option>
							<option value='3'>请款通过</option>
							<option value='0'>已结付</option>";
							}
						?>
						</select>
                    	<input name="table[]" type="hidden" id="table[]" value="S">
                    	<input name="types[]" type="hidden" id="types[]" value="isNum">
					</TD>
                </TR>
				<TR>
                	<TD align="right" valign="top">请款日期
               	    <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD>
                  	<TD align="center" valign="top">
                   	  <SELECT name=fun[] id="fun[]" style="width: 60px;">
                   	    <OPTION value== selected>=</OPTION>
                   	    <OPTION value=">">&gt;</OPTION>
                   	    <OPTION value=">=">&gt;=</OPTION>
                   	    <OPTION value="<">&lt;</OPTION>
                   	    <OPTION value="<=">&lt;=</OPTION>
                   	    <OPTION value=!=>!=</OPTION>
                  	  </SELECT>
           		    </TD>
                  	<TD>
                    	<INPUT name=value[] class=textfield id="value[]" size="18" onfocus="WdatePicker()" readonly>
						至
						<INPUT name=DateArray[] class=textfield id="DateArray[]" size="18" onfocus="WdatePicker()" readonly>
						<input name="table[]" type="hidden" id="table[]" value="S">
						<input name="types[]" type="hidden" id="types[]" value="isDate">
					</TD>
                </TR> 
				<TR>                 
                  	<TD align="right">请 款 人
               	    <input name="Field[]" type="hidden" id="Field[]" value="Operator"></TD>
                 	 <TD align="center">
                    	<select name="fun[]" id="fun[]" style="width: 60px;">
                      	  <option value="=" selected>=</option>
                      	  <option value="!=">!=</option>
                  	  </select>
               		</TD>
                 	<TD>
				  	<select name=value[] id="value[]" style="width: 274px;">
				  	<option value="" selected>全部</option>
                    <?php 
					include "../model/subprogram/select_model_stafflist.php";
					?>
				  	</select>
                  	<input name="table[]" type="hidden" id="table[]" value="S">                  
                 	 <input name="types[]" type="hidden" id="types[]" value="isNum">
					</TD>
                </TR>
              </TBODY>
	    </TABLE>
		</td>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/select_model_b.php";
?>