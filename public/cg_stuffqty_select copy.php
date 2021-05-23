<?php 
//电信-zxq 2012-08-01
/*
$DataIn.stuffdata
$DataIn.cg1_stocksheet
$DataIn.stufftype
$DataPublic.staffmain
$DataIn.trade_object
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 待购需求单查询");			//需处理
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
              <TBODY>
                <TR>
                  <TD width="89" align="right">采&nbsp;&nbsp;&nbsp;&nbsp;购
                      <input name="Field[]" type="hidden" id="Field[]" value="BuyerId">
                  </TD>
                  <TD width="84" align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="385"><select name=value[] id="value[]" style="width: 274px;">
                      <?php 
					echo "<option value='' selected>全部</option>";
					$buyerSql = mysql_query("SELECT S.BuyerId,M.Name 
					FROM $DataIn.cg1_stocksheet S 
					LEFT JOIN $DataPublic.staffmain M ON S.BuyerId=M.Number 
					WHERE S.Mid>0 GROUP BY S.BuyerId ORDER BY S.BuyerId",$link_id);
					if($buyerRow = mysql_fetch_array($buyerSql)){
						do{
							$thisBuyerId=$buyerRow["BuyerId"];
							$Buyer=$buyerRow["Name"];
							echo "<option value='$thisBuyerId'>$thisBuyerId $Buyer</option>";
							}while ($buyerRow = mysql_fetch_array($buyerSql));
						} 
					?>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right"><p>供 应 商
                          <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">
                  </p></TD>
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
                    </SELECT>
                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                      <?php 
					echo "<option value='' selected>全部</option>";
					$providerSql= mysql_query("SELECT S.CompanyId,P.Forshort,P.Letter 
					FROM $DataIn.cg1_stocksheet S 
					LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
					WHERE 1 AND S.Mid>0  GROUP BY S.CompanyId ORDER BY P.Letter",$link_id);
					if($providerRow = mysql_fetch_array($providerSql)){
						do{
							$Letter=$providerRow["Letter"];
							$Forshort=$providerRow["Forshort"];
							$Forshort=$Letter.'-'.$Forshort;
							$thisCompanyId=$providerRow["CompanyId"];
							echo"<option value='$thisCompanyId'>$Forshort</option>";
							}while ($providerRow = mysql_fetch_array($providerSql));
						}
					?>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right"><p>配件分类
                          <input name="Field[]" type="hidden" id="Field[]" value="TypeId">
                  </p></TD>
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
                    </SELECT>
                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                      <?php 
		echo "<option value='' selected>全部</option>";
		$TypeSql= mysql_query("SELECT A.TypeId,T.Letter,T.TypeName
		FROM $DataIn.cg1_stocksheet S 
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId
		WHERE 1 and S.Mid>0
		GROUP BY A.TypeId ORDER BY T.Letter",$link_id);
		if($TypeRow = mysql_fetch_array($TypeSql)){
			do{
				$TypeId=$TypeRow["TypeId"];
				$TypeName=$TypeRow["Letter"]."-".$TypeRow["TypeName"];
				echo"<option value='$TypeId'>$TypeName</option>";
				}while ($TypeRow = mysql_fetch_array($TypeSql));
			}
		?>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="A">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">配件ID
                      <input name="Field[]" type="hidden" id="Field[]" value="StuffId">
                  </TD>
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
                    </SELECT>
                  </TD>
                  <TD><input name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right"><p>配件名称
                          <input name="Field[]" type="hidden" id="Field[]" value="StuffCname">
                  </p></TD>
                  <TD align="center"><SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>
                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="A">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
              </TBODY>
            </TABLE></td>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/select_model_b.php";
?>