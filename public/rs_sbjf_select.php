<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.staffmain
$DataPublic.branchdata
$DataPublic.jobdata
$DataIn.sbpaysheet
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 社保缴费记录查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,chooseMonth,$chooseMonth,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.sbpaysheet";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">
              <TBODY>
				<?php 
				//如果是来自于财务查询
				if($fromWebPage==$funFrom."_cw" && $Estate==0){
				?>
				<TR>	
                  	<TD align="right">结付编号
                  	  <input name="Field[]" type="hidden" id="Field[]" value="Id"></TD><TD align="center"><div align="center">
               		  	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value==  selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                   	  </SELECT>
						 </div>
           		    </TD>
                	<TD width="354">
						<INPUT name=value[] class=textfield id="value[]" size="48">
                      	<input name="table[]" type="hidden" id="table[]" value="M">
                      	<input name="types[]" type="hidden" id="types[]" value="isNum">
					</TD>
                </TR>
				<TR>
                  	<TD align="right">结付日期
                  	  <input name="Field[]" type="hidden" id="Field[]" value="PayDate"></TD><TD align="center"><div align="center">
               		  	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      	</SELECT>
					  	</div>
					</TD>
                	<TD width="354">
						<INPUT name=value[] class=textfield id="value[]" size="18" onfocus="WdatePicker()" readonly> 至
					  	<INPUT name=DateArray[] class=textfield id="DateArray[]" size="19" onfocus="WdatePicker()" readonly>
					  	<input name="table[]" type="hidden" id="table[]" value="M">
					  	<input name="types[]" type="hidden" id="types[]" value="isDate">
  					</TD>
                </TR>
				<TR>
                  	<TD align="right">结付金额
                  	    <input name="Field[]" type="hidden" id="Field[]" value="PayAmount"></TD><TD align="center"><div align="center">
               		  	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      	</SELECT>
           		    	</div>
					</TD>
                	<TD width="354">
						<INPUT name=value[] class=textfield id="value[]" size="48">
                      	<input name="table[]" type="hidden" id="table[]" value="M">
                      	<input name="types[]" type="hidden" id="types[]" value="isNum">
					</TD>
                </TR>
				<TR>
                  	<TD align="right">结付凭证
                  	  <input name="Field[]" type="hidden" id="Field[]" value="Payee"></TD><TD align="center"><div align="center">
               		 	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      	</SELECT>
           		    	</div>
					</TD>
                	<TD width="354">
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
                  	  <input name="Field[]" type="hidden" id="Field[]" value="Receipt"></TD><TD align="center"><div align="center">
               		 	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      	</SELECT>
           		   		</div>
					</TD>
                	<TD width="354">
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
                  	  <input name="Field[]" type="hidden" id="Field[]" value="Checksheet"></TD><TD align="center"><div align="center">
               		  	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                     	</SELECT>
           		    	</div>
					</TD>
                	<TD width="354">
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
                  	  <input name="Field[]" type="hidden" id="Field[]" value="Remark"></TD><TD align="center"><div align="center">
               		  	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      	</SELECT>
           		    	</div>
					</TD>
                	<TD width="354">
						<INPUT name=value[] class=textfield id="value[]" size="48">
                      	<input name="table[]" type="hidden" id="table[]" value="M">
                      	<input name="types[]" type="hidden" id="types[]" value="isStr">
					</TD>
                </TR>
				<TR>
      				<TD align="right">结付操作
      				  <input name="Field[]" type="hidden" id="Field[]" value="Operator"></TD><TD align="center"><div align="center">
						<select name="fun[]" id="fun[]" style="width: 60px;">
						<option value="=" selected>=</option>
						<option value="!=">!=</option>
						</select>
					  	</div>
					</TD>
      				<TD><select name=value[] id="value[]" style="width: 274px;">
          				<option value="" selected>全部</option>
          				<?php 
						$PD_Sql = "SELECT Number,Name FROM $DataPublic.staffmain WHERE Number IN (SELECT Operator FROM $DataIn.socialsecurity group by Operator ORDER BY Id)order by Id";
						$PD_Result = mysql_query($PD_Sql); 
						while ( $PD_Myrow = mysql_fetch_array($PD_Result)){
							$Number=$PD_Myrow["Number"];
							$Name=$PD_Myrow["Name"];					
							echo "<option value='$Number'>$Name</option>";
							} 
						?>
       					</select>
          				<input name="table[]" type="hidden" id="table[]" value="M">
         				<input name="types[]" type="hidden" id="types[]" value="isNum">
     				</TD>
    			</TR>
    			<TR>
      			  <TD align="right">操作日期
      			    <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD><TD align="center"><div align="center">
          			<select name="fun[]" id="fun[]" style="width: 60px;">
            		<option value="=">=</option>
          			</select>
      				</div>
				</TD>
     			<TD><INPUT name=value[] class=textfield id="value[]" size="18" onfocus="WdatePicker()" readonly> 至
          			<INPUT name=DateArray[] class=textfield id="DateArray[]" size="19" onfocus="WdatePicker()" readonly>
          			<input name="table[]" type="hidden" id="table[]" value="M">
          			<input name="types[]" type="hidden" id="types[]" value="isDate">
				</TD>
    		</TR>
			<TR><TD colspan="3">&nbsp;</TD></TR>
			<?php 
			  }
			 ?>
			<TR>
            	<TD align="right">缴费月份
           	    <input name="Field[]" type="hidden" id="Field[]" value="Month"></TD>
                <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <OPTION value== selected>=</OPTION>
                    <OPTION value=">">&gt;</OPTION>
                    <OPTION value=">=">&gt;=</OPTION>
                    <OPTION value="<">&lt;</OPTION>
                    <OPTION value="<=">&lt;=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                  	</SELECT>
				</TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size="18" maxlength="7"> 至
					<INPUT name=MonthArray[] class=textfield id="MonthArray[]" size="19" maxlength="7">
					<input name="table[]" type="hidden" id="table[]" value="S">  
					<input name="types[]" type="hidden" id="types[]" value="isMonth">
				</TD>
            </TR>
            
				<TR>
                  	<TD align="right">保费分类
                  	  <input name="Field[]" type="hidden" id="Field[]" value="TypeId"></TD><TD align="center"><div align="center">
               		 	<SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      	</SELECT>
           		   		</div>
					</TD>
                	<TD width="354">
						<select name=value[] id="value[]" style="width: 274px;">
                      	<option value="" selected>全部</option>
                        <option value='1' $TypeId1>社保</option>
                        <option value='2' $TypeId2>公积金</option>
                        <option value='3' $TypeId3>意外险</option>
                    	</select>
                      	<input name="table[]" type="hidden" id="table[]" value="S">
                      	<input name="types[]" type="hidden" id="types[]" value="isNum">
					</TD>
                </TR>
                            
			<TR>
				<TD align="right">个人缴费金额
			    <input name="Field[]" type="hidden" id="Field[]" value="mAmount"></TD>
				<TD align="center"><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                  	</select>
				</TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum">
				</TD>
            </TR>
			<TR>
				<TD align="right">公司缴费金额
			    <input name="Field[]" type="hidden" id="Field[]" value="cAmount"></TD>
				<TD align="center"><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                  	</select>
				</TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum">
				</TD>
            </TR>
			<TR>
            	<TD align="right">结付标记
           	    <input name="Field[]" type="hidden" id="Field[]" value="Estate"></TD>
				<TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                	<OPTION value== selected>=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                    </SELECT>
				</TD>
				<TD><select name=value[] id="value[]" style="width: 274px;">
					<?php 
					if($fromWebPage==$funFrom."_cw"){
						if($Estate==0){
							echo"<option value='0'>已结付</option>";
							$EstatePass=" WHERE socialsecurity.Estate=0";
							}
						else{
							echo"<option value='3'>未结付</option>";
							$EstatePass=" WHERE socialsecurity.Estate=3";
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
				<TD align="right">更新日期
			    <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD>
				<TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                	<OPTION value== selected>=</OPTION>
                    <OPTION value=">">&gt;</OPTION>
                    <OPTION value=">=">&gt;=</OPTION>
                    <OPTION value="<">&lt;</OPTION>
                    <OPTION value="<=">&lt;=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                    </SELECT>
                 </TD>
                 <TD><INPUT name=value[] class=textfield id="value[]" size="18" onfocus="WdatePicker()" readonly> 至
					 <INPUT name=DateArray[] class=textfield id="DateArray[]" size="19" onfocus="WdatePicker()" readonly>
					 <input name="table[]" type="hidden" id="table[]" value="S">
					 <input name="types[]" type="hidden" id="types[]" value="isDate">
				</TD>
			</TR>
            <TR>
				<TD align="right">操 作 员
			    <input name="Field[]" type="hidden" id="Field[]" value="Operator"></TD>
				<TD align="center"><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                	</select>
				</TD>
                <TD><select name=value[] id="value[]" style="width: 274px;">
					<option value="" selected>全部</option>
                    <?php 
					include "../model/subprogram/select_model_stafflist.php";
					?>
				  	</select>
                  	<input name="table[]" type="hidden" id="table[]" value="S">                  
                  	<input name="types[]" type="hidden" id="types[]" value="isNum">
				</TD>
			</TR>
			<TR>
				<TD align="right">锁定状态
			    <input name="Field[]" type="hidden" id="Field[]" value="Locks"></TD>
				<TD align="center">
					<select name="fun[]" id="fun[]" style="width: 60px;">
					<option value="=">=</option>
					</select>
				</TD>
				<TD>
					<select name=value[] id="value[]" style="width: 274px;">
					<option selected  value="">全部</option>
					<option value="0">锁定</option>
					<option value="1">未锁定</option>
					</select>
					<input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum">
				</TD>
			</TR>
			<TR><TD colspan="3">&nbsp;</TD></TR>
			<TR>
            	<TD width="120" align="right">入职日期
           	    <input name="Field[]" type="hidden" id="Field[]" value="ComeIn"></TD>
				<TD width="84" align="center">
					<SELECT name=fun[] id="fun[]" style="width: 60px;">
					<OPTION value== selected>=</OPTION>
                    <OPTION value=">">&gt;</OPTION>
                    <OPTION value=">=">&gt;=</OPTION>
                    <OPTION value="<">&lt;</OPTION>
                    <OPTION value="<=">&lt;=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                    </SELECT>
				</TD>
				<TD width="354">
					<INPUT name=value[] class=textfield id="value[]" size="18" onfocus="WdatePicker()" readonly> 至
				 	<INPUT name=DateArray[] class=textfield id="DateArray[]" size="19" onfocus="WdatePicker()" readonly>
				  	<input name="table[]" type="hidden" id="table[]" value="P">
				  	<input name="types[]" type="hidden" id="types[]" value="isDate">
				</TD>
			</TR>
			<TR>
				<TD width="120" align="right">介 绍 人
			    <input name="Field[]" type="hidden" id="Field[]" value="Introducer"></TD>
				<TD width="84" align="center">
					<SELECT name=fun[] id="fun[]" style="width: 60px;">
					<OPTION value== selected>=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                    </SELECT>
				</TD>
				<TD width="354">
					<select name=value[] id="value[]" style="width: 274px;">
                      <option value="" selected>全部</option>
                      <?php 
			 $Result1 = mysql_query("SELECT M.Number,M.Name FROM $DataPublic.staffmain M,$DataPublic.staffmain I WHERE I.Introducer=M.Number AND M.Estate='1' GROUP BY I.Introducer ORDER BY M.BranchId,M.JobId,M.Number",$link_id);
			 if($myRow1 = mysql_fetch_array($Result1)){
				do{
					echo" <option value='$myRow1[Number]'>$myRow1[Name]</option>";
					}while($myRow1 = mysql_fetch_array($Result1));
				}
			 ?>
                    </select>
                  <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]" value="isNum">
				</TD>
			</TR>
			<TR>
				<TD width="120" align="right">部&nbsp;&nbsp;&nbsp;&nbsp;门				  <input name="Field[]" type="hidden" id="Field[]" value="BranchId"></TD>
				<TD width="84" align="center">
					<SELECT name=fun[] id="fun[]" style="width: 60px;">
					<OPTION value== selected>=</OPTION>
					<OPTION value=">">&gt;</OPTION>
                    <OPTION value=">=">&gt;=</OPTION>
                    <OPTION value="<">&lt;</OPTION>
                    <OPTION value="<=">&lt;=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                    </SELECT>
				</TD>
				<TD width="354">
					<select name=value[] id="value[]" style="width: 274px;">
                  	<option value="" selected>全部</option>
                   	<?php 
					$B_Result=mysql_query("SELECT * FROM $DataPublic.branchdata WHERE Estate=1 order by Id",$link_id);
					if($B_Row = mysql_fetch_array($B_Result)) {
						do{
							$B_Id=$B_Row["Id"];
							$B_Name=$B_Row["Name"];
							echo "<option value='$B_Id'>$B_Name</option>";
							}while ($B_Row = mysql_fetch_array($B_Result));
						}
					?>			  
                 	</select>
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]" value="isNum">
				</TD>
			</TR>
			<TR>
				<TD width="120" align="right">职&nbsp;&nbsp;&nbsp;&nbsp;位				  <input name="Field[]" type="hidden" id="Field[]" value="JobId"></TD>
				<TD width="84" align="center">
					<SELECT name=fun[] id="fun[]" style="width: 60px;">
					<OPTION value== selected>=</OPTION>
                    <OPTION value=">">&gt;</OPTION>
                    <OPTION value=">=">&gt;=</OPTION>
	                <OPTION value="<">&lt;</OPTION>
                    <OPTION value="<=">&lt;=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                    </SELECT>
				</TD>
				<TD width="354">
					<select name=value[] id="value[]" style="width: 274px;">
                   	<option value="" selected>全部</option>
                   	<?php 
					$J_Result=mysql_query("SELECT * FROM $DataPublic.jobdata WHERE Estate=1 order by Id",$link_id);
					if($J_Row = mysql_fetch_array($J_Result)) {
						do{
							$J_Id=$J_Row["Id"];
							$J_Name=$J_Row["Name"];
							echo "<option value='$J_Id'>$J_Name</option>";
							}while ($J_Row = mysql_fetch_array($J_Result));
						}
					?>			  
                  	</select>
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]" value="isNum">
				</TD>
			</TR>
			<TR>
				<TD width="120" align="right">员工 I D
			    <input name="Field[]" type="hidden" id="Field[]" value="Number"></TD>
				<TD width="84" align="center">
					<SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <OPTION value== selected>=</OPTION>
                    <OPTION value=">">&gt;</OPTION>
                    <OPTION value=">=">&gt;=</OPTION>
	                <OPTION value="<">&lt;</OPTION>
                    <OPTION value="<=">&lt;=</OPTION>
                    <OPTION value=!=>!=</OPTION>
	                </SELECT>
    			</TD>
                <TD width="354">
					<INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum">
				</TD>
			</TR>
			<TR>
				<TD align="right">员工姓名
			    <input name="Field[]" type="hidden" id="Field[]" value="Name"></TD>
				<TD align="center">
					<SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                    </SELECT>
                 </TD>
                 <TD>
				  	<INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]" value="isStr">
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