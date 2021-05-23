<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cg1_stockmain
$DataPublic.staffmain
$DataIn.cg1_stockmain
$DataIn.trade_object
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
include "../model/subprogram/s2_model_2.php";
//步骤3：需处理
$Parameter.=",Bid,$Bid,Jid,$Jid,Kid,$Kid,Action,$Action";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
	  <td class="A0011">
			  <input name="Action" type="hidden" id="Action" value="<?php  echo $Action?>">
              <input name="Sid" type="hidden" id="Sid" value="<?php  echo $Sid?>">	
          <input name="Bid" type="hidden" id="Bid" value="<?php  echo $Bid?>">		
        <TABLE width="572" border=0 align="center">
            <TBODY>
              <TR>
                <TD width="104" align="right">配 件 ID
                        <input name="Field[]" type="hidden" id="Field[]" value="StuffId">
                </TD>
                <TD width="89" align="center">
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
                    </SELECT>                </TD>
                <TD width="365"><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="G">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
              </TR>
              <TR>
                <TD align="right"><p>配件名称
                        <input name="Field[]" type="hidden" id="Field[]" value="StuffCname">
                </p></TD>
                <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="D">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                </TD>
              </TR>
              <TR>
                <TD align="right">需求单流水号
                        <input name="Field[]" type="hidden" id="Field[]" value="StockId">
                </TD>
                <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="G">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                </TD>
              </TR>
              <TR>
                <TD align="right">订单数量
                        <input name="Field[]" type="hidden" id="Field[]" value="OrderQty">
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
                    </SELECT>                </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="G">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
              </TR>
              <TR>
                <TD><div align="right"> </div></TD>
                <TD><div align="center"> </div></TD>
                <TD>&nbsp;</TD>
              </TR>
              <TR>
                <TD align="right">供 应 商
                        <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">                </TD>
                <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <option value="!=">!=</option>
                    </SELECT>                </TD>
                <TD><select name=value[] id="value[]" style="width: 274px;">
                    <?php 
					$result = mysql_query("SELECT G.CompanyId,P.Forshort,P.Letter 
					FROM $DataIn.cg1_stockmain G,$DataIn.trade_object P 
					WHERE G.CompanyId=P.CompanyId GROUP BY G.CompanyId ORDER BY P.Letter",$link_id);
					echo "<option value='' selected>全部</option>";
					while ($myrow = mysql_fetch_array($result)){
						$Provider=$myrow["CompanyId"];
						$Forshort=$myrow["Forshort"];
						$Letter=$myrow["Letter"];
						$Forshort=$Letter.'-'.$Forshort;	
						echo"<option value='$Provider'>$Forshort</option>";
						}
					
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="G">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
              </TR>
              <TR>
                <TD align="right">采&nbsp;&nbsp;&nbsp;&nbsp;购
                  <input name="Field[]" type="hidden" id="Field[]" value="BuyerId">                </TD>
                <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== 
          selected>=</OPTION>
                      <OPTION value=!=>!=</OPTION>
                    </SELECT>                </TD>
                <TD><select name=value[] id="value[]" style="width: 274px;">
                    <?php 
				$PD_Sql = "SELECT G.BuyerId,A.Name FROM $DataIn.cg1_stockmain G LEFT JOIN $DataPublic.staffmain A ON G.BuyerId=A.Number GROUP BY G.BuyerId ORDER BY G.BuyerId";
				$PD_Result = mysql_query($PD_Sql); 
				echo "<option value='' selected>全部</option>";
				while($PD_Myrow = mysql_fetch_array($PD_Result)){
					$PD_BuyerId=$PD_Myrow["BuyerId"];
					$PD_StuffCname=$PD_Myrow["Name"];					
						echo "<option value='$PD_BuyerId'>$PD_StuffCname</option>";
					} 				
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="G">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                </TD>
              </TR>
              <TR>
                <TD><div align="right"> </div></TD>
                <TD><div align="center"> </div></TD>
                <TD>&nbsp;</TD>
              </TR>
              <TR>
                <TD align="right">产 品 ID
                        <input name="Field[]" type="hidden" id="Field[]" value="ProductId">                </TD>
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
                    </SELECT>                </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]" value="isNum">
                </TD>
              </TR>
              <TR>
                <TD align="right">产品名称
                        <input name="Field[]" type="hidden" id="Field[]" value="cName">                </TD>
                <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                </TD>
              </TR>
              <TR>
                <TD align="right">订 单 PO
                        <input name="Field[]" type="hidden" id="Field[]" value="OrderPO">                </TD>
                <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="M">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                </TD>
              </TR>
            </TBODY>
          </TABLE></td>
	</tr>
</table>
<?php 
//步骤4：
include "../model/subprogram/s2_model_4.php";
?>