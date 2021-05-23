<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cg1_stocksheet
$DataIn.stuffdata
$DataIn.stufftype
$DataIn.cg1_stocksheet
$DataPublic.staffmain
$DataIn.cg1_stocksheet
$DataIn.trade_object
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 请款查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td class="A0011">
			<TABLE width="572" border=0 align="center">

                <TR>
                  <TD align="right">申购人
                          <input name="Field[]" type="hidden" id="Field[]" value="BuyerId">                  </TD>
                  <TD align="center">
                      <select name="fun[]" id="fun[]" style="width: 60px;">
                        <option value="=" selected>=</option>
                        <option value="!=">!=</option>
                      </select>
                  </TD>
                  <TD><select name=value[] id="value[]" style="width:380px">
				  	<option value="" selected>全部</option>
                    <?php 
					$checkResult = mysql_query("SELECT G.BuyerId,F.Name 
						FROM $DataIn.nonbom11_qksheet B
						LEFT JOIN $DataIn.nonbom6_cgmain G ON G.Id=B.cgMId
						LEFT JOIN $DataPublic.staffmain F ON F.Number=G.BuyerId
						WHERE 1 GROUP BY G.BuyerId ORDER BY F.Name",$link_id);
				
					if($checkRow = mysql_fetch_array($checkResult)) {
						do{			
							$Temp_BuyerId=$checkRow["BuyerId"];
							$Temp_Name=$checkRow["Name"];
							echo"<option value='$Temp_BuyerId'>$Temp_Name</option>";					
							}while($checkRow = mysql_fetch_array($checkResult));
						echo"</select>";
						}
					?>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="G">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
            
               <TR>
                  <TD align="right">供应商
                      <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">                  </TD>
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
                  <TD><select name=value[] id="value[]" style="width:380px">
                      <option value="" selected>全部</option>
						<?php 
						$checkSql = "SELECT C.CompanyId,C.Forshort, C.letter
		FROM $DataIn.nonbom11_qksheet B
		LEFT JOIN $DataIn.nonbom6_cgmain G ON G.Id=B.cgMId
		LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=B.CompanyId
		WHERE 1  GROUP BY B.CompanyId ORDER BY C.letter";
						$checkResult = mysql_query($checkSql); 
						while($checkRow = mysql_fetch_array($checkResult)){
							$CompanyId=$checkRow["CompanyId"];
							$Forshort=$checkRow["Forshort"];
							$letter=$checkRow["letter"];
							$Forshort="$letter-".$Forshort."";
							echo "<option value='$CompanyId'>$Forshort</option>";
							}
						?>		 
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="C">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>

                <TR>
                  <TD align="right">记录状态
                    <input name="Field[]" type="hidden" id="Field[]" value="Estate">                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </TD>
                  <TD><select name=value[] id="value[]" style="width:380px">
						<option selected  value="">全部</option>
							<option value="1">未处理</option>
							<option value="2">请款中</option>
							<option value="3">请款通过</option>
							<option value="0">已结付</option>							
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="B">
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