<?php 
//步骤1
include "../model/modelhead.php";
//步骤2：
include "../model/subprogram/s2_model_2.php";
$Parameter.=",Bid,$Bid";
//步骤3：需处理
$CheckTb="$DataIn.stuffdata";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<input name="Action" type="hidden" id="Action" value="<?php  echo $Action?>">
		  <TABLE width="600" border=0 align="center">
              <TBODY>
			  	 <?php 
				 if($uType==""){
				 ?>
				<TR>
                  <TD width="86" align="right">配件分类
                  <input name="Field[]" type="hidden" id="Field[]" value="TypeId"></TD>
                  <TD width="86" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
				  </TD>
                  <TD width="414"><select name=value[] id="value[]" style="width: 274px;">
                    <?php 
					//员工资料表
					$typeSql = "SELECT TypeId,TypeName,Letter FROM $DataIn.stufftype WHERE 1 AND Estate=1 order by Letter";
					$typeResult = mysql_query($typeSql); 
					echo "<option value='' selected>全部</option>";
					while ( $typeMyrow = mysql_fetch_array($typeResult)){
						$typeId=$typeMyrow["TypeId"];
						$typrName=$typeMyrow["TypeName"];
						$typrLetter=$typeMyrow["Letter"];
						echo "<option value='$typeId'>$typrLetter-$typrName</option>";
						} 
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
              <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
				</TD>
                </TR>
				<?php 
				}
				?>
				<TR>
                  <TD width="86" align="right">配&nbsp;件 ID 
                    <input name="Field[]" type="hidden" id="Field[]" value="StuffId"></TD>
                  <TD width="86" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION 
          value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION 
          value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD width="414"><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
                <TR>
                  <TD align="right"><p>配件名称
                    <input name="Field[]" type="hidden" id="Field[]" value="StuffCname">
                  </p>
                  </TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
              <TR>
                  <TD align="right">配件属性
                  <input name="Field[]" type="hidden" id="Field[]" value="Property"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <OPTION value== 
          selected>=</OPTION>
                    <OPTION value=">">&gt;</OPTION>
                    <OPTION 
          value=">=">&gt;=</OPTION>
                    <OPTION value="<">&lt;</OPTION>
                    <OPTION 
          value="<=">&lt;=</OPTION>
                    <OPTION value=!=>!=</OPTION>
                  </SELECT></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
					<option value='' selected>请选择</option>
					<option value='0'>默认</option>
					<option value='1' >代购</option>
					<option value='2' >客供</option>
					<option value='3' >成品</option>
					<option value='4' >参考</option>
					<option value='5' >打印</option>
					<option value='6' >自动</option>
                  </select>
                  <input name="table[]" type="hidden" id="table[]" value="PA">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
 <TR>
                  <TD align="right">送货楼层
                  <input name="Field[]" type="hidden" id="Field[]" value="SendFloor"></TD>
                  <TD align="center"><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
				    <option value="">全部</option>
   		      <?php  
	          $mySql="SELECT Id,Name,Remark FROM $DataIn.base_mposition  
	                  WHERE 1 order by  Remark";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $FloorId=$myrow["Id"];
				 $FloorRemark=$myrow["Remark"];
				 $FloorName=$myrow["Name"];
				 echo "<option value='$FloorId'>$FloorRemark-$FloorName</option>"; 
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
                  </select>
                  <input name="table[]" type="hidden" id="table[]" value="S">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>

                <TR>
                  <TD align="right">规&nbsp;&nbsp;&nbsp;&nbsp;格
                  <input name="Field[]" type="hidden" id="Field[]" value="Spec"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right" valign="top">参考买价
                  <input name="Field[]" type="hidden" id="Field[]" value="Price"></TD>
                  <TD align="center" valign="top"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION 
          value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION 
          value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注
                  <input name="Field[]" type="hidden" id="Field[]" value="Remark"></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <option value="LIKE" selected>包含</option>
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right" valign="top">更新日期
                  <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD>
                  <TD align="center" valign="top"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=">">&gt;</OPTION>
                      <OPTION 
          value=">=">&gt;=</OPTION>
                      <OPTION value="<">&lt;</OPTION>
                      <OPTION 
          value="<=">&lt;=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="S">
  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
                </TR>
                <TR>
                  <TD align="right">操 作 员
                    <input name="Field[]" type="hidden" id="Field[]" value="Operator"></TD>
                  <TD align="center"><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD>
				    <select name=value[] id="value[]" style="width: 274px;">
					<option value="" selected>全部</option>
                    <?php 
					include "../model/subprogram/select_model_stafflist.php";
					?>
                    </select>
				  <input name="table[]" type="hidden" id="table[]" value="S">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum">
				  </TD>
                </TR>
                <TR>
                  <TD align="right">采&nbsp;&nbsp;&nbsp;&nbsp;购
                  <input name="Field[]" type="hidden" id="Field[]" value="BuyerId"></TD>
                  <TD align="center"><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">				  
                    <?php 
					if($fSearchPage=="cg_cgdsheet"){
						$BuyerIdSTR=" and P.Number='$Bid'";
						}
					else{
						echo"<option value=''>全部</option>";
						}
					$checkStaff ="SELECT P.Number,P.Name FROM $DataPublic.staffmain P WHERE P.Estate=1 and (P.BranchId=4 OR  P.JobId=3) $BuyerIdSTR ORDER BY P.Number";
					//echo "SELECT P.Number,P.Name FROM $DataPublic.staffmain P WHERE P.Estate=1 and P.JobId=3 $BuyerIdSTR ORDER BY P.Number";
					$staffResult = mysql_query($checkStaff);
					while ( $staffRow = mysql_fetch_array($staffResult)){
						$pNumber=$staffRow["Number"];
						$PName=$staffRow["Name"];					
						echo "<option value='$pNumber'>$PName</option>";
						} 
					?>		 
                  </select>
                  <input name="table[]" type="hidden" id="table[]" value="B">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
                <TR>
                  <TD align="right">供 应 商
                  <input name="Field[]" type="hidden" id="Field[]" value="CompanyId"></TD>
                  <TD align="center"><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <?php 
					//供应商
					if($fSearchPage=="cg_cgdsheet"){
						$GYS_Sql = "SELECT B.CompanyId,P.Letter,P.Forshort 
						FROM $DataIn.bps B 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId WHERE B.BuyerId='$Bid' GROUP BY B.CompanyId ORDER BY P.Letter";
						}
					else{
						$GYS_Sql = "SELECT CompanyId,Letter,Forshort FROM $DataIn.trade_object WHERE Estate=1 $ProviderIdSTR order by Letter";
						}
					$GYS_Result = mysql_query($GYS_Sql); 
					echo "<option value='' selected>全部</option>";
					while ( $GYS_Myrow = mysql_fetch_array($GYS_Result)){
						$CompanyId=$GYS_Myrow["CompanyId"];
						$Forshort=$GYS_Myrow["Forshort"];
						$Letter=$GYS_Myrow["Letter"];
						$Forshort=$Letter.'-'.$Forshort;		
						if ($myrow["CompanyId"]==$CompanyId){
							echo "<option value='$CompanyId' selected>$Forshort</option>";}
						else{
							echo "<option value='$CompanyId'>$Forshort</option>";}
						}
					?>
                  </select>
                  <input name="table[]" type="hidden" id="table[]" value="B">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
              </TBODY>
      </TABLE>
	</td></tr></table>
<?php 
include "../model/subprogram/s2_model_4.php";
?>
