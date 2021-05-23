<?php 
//代码 jobdata by zx 2012-08-13
//代码 branchdata by zx 2012-08-13
/*$DataIn.电信---yang 20120801
$DataPublic.jobdata
$DataPublic.branchdata
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 薪资查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page,$tempFrom";
$tableMenuS=500;
$tableWidth=850;
if($From=='m' || $tempFrom=='m')
{
	$TableAs='M';
}
else
{
	$TableAs='P';
}

//步骤3：
include "../model/subprogram/select_model_t.php";

//步骤4：需处理
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
                  	  <input name="Field[]" type="hidden" id="Field[]" value="Id"></TD><TD width="96" align="center">
               		  	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <OPTION value==  selected>=</OPTION>
                          <OPTION value=!=>!=</OPTION>
               	        </SELECT>
						 </TD>
                	<TD width="457">
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
                	<TD width="457">
						<INPUT name=value[] class=textfield id="value[]" size="18" onfocus="WdatePicker()" readonly> 至
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
                	<TD width="457">
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
                	<TD width="457">
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
                	<TD width="457">
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
                	<TD width="457">
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
					<TD width="125" align="right">薪资月份
			          <input name="Field[]" type="hidden" id="Field[]" value="Month"></TD>
                  	<TD align="center">
				   		<SELECT name=fun[] id="fun[]" style="width: 60px;">
                      	  <option value="LIKE" selected>包含</option>
                      	  <OPTION value==>=</OPTION>
                     	  <OPTION value=!=>!=</OPTION>
                  	  </SELECT>
               		</TD>
                  	<TD width="457">
						<INPUT name=value[] class=textfield id="value[]" size="18" maxlength="7">
						至
                        <INPUT name=MonthArray[] class=textfield id="MonthArray[]" size="18" maxlength="7">
                        <input name="table[]" type="hidden" id="table[]" value="S">
                        <input name="types[]" type="hidden" id="types[]" value="isMonth">
</TD>
                </TR>
				<TR>
                  <TD align="right">结付状态
                          <input name="Field[]" type="hidden" id="Field[]" value="Estate">                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
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
                  <TD align="right">员工ID
                          <input name="Field[]" type="hidden" id="Field[]" value="Number">                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>                  </TD>
                  <TD><input name=value[] class=textfield id="value[]" size="48">
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
			    </TR>
				<TR>
                  <TD align="right" valign="top">员工姓名
                          <input name="Field[]" type="hidden" id="Field[]" value="Name">                  </TD>
                  <TD align="center" valign="top">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>                  </TD>
                  <TD><input name=value[] class=textfield id="value[]" size="48">
                      <input name="table[]" type="hidden" id="table[]" value="<?php  echo $TableAs?>">
                      <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </TD>
			    </TR>
				<TR>
					<TD width="125" align="right">部&nbsp;&nbsp;&nbsp;&nbsp;门
                      <input name="Field[]" type="hidden" id="Field[]" value="BranchId"></TD>
                  	<TD align="center">
                    	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                          <OPTION value== selected>=</OPTION>
                          <OPTION value=!=>!=</OPTION>
                  	  </SELECT>
               		</TD>
                  	<TD width="457">
				  		<select name=value[] id="value[]" style="width: 274px;">
                          <option value="" selected>全部</option>
                          <?php 
					$B_Result=mysql_query("SELECT * FROM $DataPublic.branchdata 
										   WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0 )  order by Id",$link_id);
					if($B_Row = mysql_fetch_array($B_Result)) {
						do{
							$B_Id=$B_Row["Id"];
							$B_Name=$B_Row["Name"];
							echo "<option value='$B_Id'>$B_Name</option>";
							}while ($B_Row = mysql_fetch_array($B_Result));
						}
					?>
                        </select>
                   	  <input name="table[]" type="hidden" id="table[]" value="S">
                    	<input name="types[]" type="hidden" id="types[]" value="isNum">
                 	</TD>
                </TR>
				<TR>
					<TD align="right">职&nbsp;&nbsp;&nbsp;&nbsp;位
                      <input name="Field[]" type="hidden" id="Field[]" value="JobId">
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
						<select name=value[] id="value[]" style="width: 274px;">
                          <option value="" selected>全部</option>
                          <?php 
					$J_Result=mysql_query("SELECT * FROM $DataPublic.jobdata 
										   WHERE Estate=1  AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);
					if($J_Row = mysql_fetch_array($J_Result)) {
						do{
							$J_Id=$J_Row["Id"];
							$J_Name=$J_Row["Name"];
							echo "<option value='$J_Id'>$J_Name</option>";
							}while ($J_Row = mysql_fetch_array($J_Result));
						}
					?>
                        </select>
					  <input name="table[]" type="hidden" id="table[]" value="S">                  
						<input name="types[]" type="hidden" id="types[]" value="isNum">
					</TD>
                </TR>
				<TR>
                	<TD width="125" align="right">员工等级
               	        <input name="Field[]" type="hidden" id="Field[]" value="Grade"></TD>
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
                  	<TD width="457">
						<INPUT name=value[] class=textfield id="value[]" size="48">
                    	<input name="table[]" type="hidden" id="table[]" value="S">
                    	<input name="types[]" type="hidden" id="types[]" value="isStr">
					</TD>
                </TR>
                <TR>
                  	<TD align="right">考勤状态
               	    <input name="Field[]" type="hidden" id="Field[]" value="KqSign"></TD>
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
					<select name=value[] id="value[]" style="width: 274px;">
					  <option value=""selected>全部</option>
					  <option value="1">考勤有效</option>
					  <option value="2">考勤参考</option>
					  <option value="3">无须考勤</option>
                    </select>
                  	  <input name="table[]" type="hidden" id="table[]" value="S">
                    	<input name="types[]" type="hidden" id="types[]" value="isNum">
					</TD>
                </TR> 
				<TR>                 
                  	<TD align="right">底薪
               	        <input name="Field[]" type="hidden" id="Field[]" value="Dx"></TD>
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
				  <TD align="right">加班费
				      <input name="Field[]" type="hidden" id="Field[]" value="Jbf">				  </TD>
				  <TD align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>			      </TD>
				  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum"></TD>
			    </TR>
				<TR>
				  <TD align="right">工龄津贴
				    <input name="Field[]" type="hidden" id="Field[]" value="Gljt">				  </TD>
				  <TD align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>			      </TD>
				  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum"></TD>
			    </TR>
				<TR>
				  <TD align="right">岗位津贴
				    <input name="Field[]" type="hidden" id="Field[]" value="Gwjt">				  </TD>
				  <TD align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>			      </TD>
				  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum"></TD>
			    </TR>
				<TR>
				  <TD align="right">奖&nbsp;&nbsp;&nbsp;&nbsp;金
                    <input name="Field[]" type="hidden" id="Field[]" value="Jj">				  </TD>
				  <TD align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>			      </TD>
				  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum"></TD>
			    </TR>
				<TR>
				  <TD align="right">生活补助
				    <input name="Field[]" type="hidden" id="Field[]" value="Shbz">				  </TD>
				  <TD align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>			      </TD>
				  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum"></TD>
			    </TR>
				<TR>
				  <TD align="right">住宿补助
				    <input name="Field[]" type="hidden" id="Field[]" value="Zsbz">				  </TD>
				  <TD align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>			      </TD>
				  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum"></TD>
			    </TR>
				<TR>
				  <TD align="right">夜宵补助
				    <input name="Field[]" type="hidden" id="Field[]" value="Yxbz">				  </TD>
				  <TD align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>			      </TD>
				  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum"></TD>
			    </TR>
				<TR>
				  <TD align="right">考勤扣款
				    <input name="Field[]" type="hidden" id="Field[]" value="Operator">				  </TD>
				  <TD align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>			      </TD>
				  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum"></TD>
			    </TR>
				<TR>
				  <TD align="right">借&nbsp;&nbsp;&nbsp;&nbsp;支
                    <input name="Field[]" type="hidden" id="Field[]" value="Jz">				  </TD>
				  <TD align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>			      </TD>
				  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum"></TD>
			    </TR>
				<TR>
				  <TD align="right">社&nbsp;&nbsp;&nbsp;&nbsp;保
                    <input name="Field[]" type="hidden" id="Field[]" value="Sb">				  </TD>
				  <TD align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>			      </TD>
				  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum"></TD>
			    </TR>
				<TR>
				  <TD align="right">其它扣款
				    <input name="Field[]" type="hidden" id="Field[]" value="Otherkk">				  </TD>
				  <TD align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>			      </TD>
				  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum"></TD>
			    </TR>
				<TR>
				  <TD align="right">实&nbsp;&nbsp;&nbsp;&nbsp;付
                    <input name="Field[]" type="hidden" id="Field[]" value="Amount">				  </TD>
				  <TD align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>			      </TD>
				  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum"></TD>
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