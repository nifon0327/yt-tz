<?php 
//电信-zxq 2012-08-01
/*
$DataPublic.currencydata
$DataPublic.staffmain
$DataPublic.adminitype
$DataIn.hzqksheet
二合一已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 供应商货款查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
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
                  <TD><div align="right">结付编号
                          <input name="Field[]" type="hidden" id="Field[]" value="Id">
                  </div></TD>
                  <TD width="91"><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value==  selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="482"><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">结付日期
                          <input name="Field[]" type="hidden" id="Field[]" value="PayDate">
                  </div></TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="482"><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
                    至
                      <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isDate">
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">结付总额
                          <input name="Field[]" type="hidden" id="Field[]" value="PayAmount">
                  </div></TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="482"><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">抵付订金
                      <input name="Field[]" type="hidden" id="Field[]" value="djAmount">
                  </div></TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=">">&gt;</OPTION>
                        <OPTION value=">=">&gt;=</OPTION>
                        <OPTION value="<">&lt;</OPTION>
                        <OPTION value="<=">&lt;=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">结付凭证
                          <input name="Field[]" type="hidden" id="Field[]" value="Payee">
                  </div></TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="482"><select name=value[] id="value[]" style="width: 274px;">
                      <option value="" selected>全部</option>
                      <option value="1">有</option>
                      <option value="0">没有</option>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">结付回执
                          <input name="Field[]" type="hidden" id="Field[]" value="Receipt">
                  </div></TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="482"><select name=value[] id="value[]" style="width: 274px;">
                      <option value="" selected>全部</option>
                      <option value="1">有</option>
                      <option value="0">没有</option>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">对 帐 单
                          <input name="Field[]" type="hidden" id="Field[]" value="Checksheet">
                  </div></TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="482"><select name=value[] id="value[]" style="width: 274px;">
                      <option value="" selected>全部</option>
                      <option value="1">有</option>
                      <option value="0">没有</option>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
                </TR>
                <TR>
                  <TD><div align="right">结付备注
                          <input name="Field[]" type="hidden" id="Field[]" value="Remark">
                  </div></TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="482"><INPUT name=value[] class=textfield id="value[]" size=48>
                      <input name="table[]" type="hidden" id="table[]" value="M">
                      <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </TD>
                </TR>
                <TR>
                  <TD colspan="3">&nbsp;</TD>
                </TR>
                <?php 
			}
			?>
                <TR>
                  <TD align="right" valign="top">请款月份
                      <input name="Field[]" type="hidden" id="Field[]" value="Month">
                  </TD>
                  <TD align="center" valign="top">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <option value="=">!=</option>
                    </SELECT>
                  </TD>
                  <TD><input name=value[] class=textfield id="value[]" size=48>
                  <input name="table[]" type="hidden" id="table[]" value="S">
      <input name="types[]" type="hidden" id="types[]" value="isStr">
                  </TD>
                </TR>
                 <TR>
                <TD align="right"><p>采购单号
                        <input name="Field[]" type="hidden" id="Field[]" value="PurchaseID">
                </p></TD>
                <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="GM">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                </TD>
		      </TR>
		      
			  <TR>
                <TD align="right"><p>需求流水号
                        <input name="Field[]" type="hidden" id="Field[]" value="StockId">
                </p></TD>
                <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                </TD>
                <TD><INPUT name=value[] class=textfield id="value[]" size=48>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                </TD>
		      </TR>
				<TR>
                  <TD align="right">采&nbsp;&nbsp;&nbsp;&nbsp;购
                  <input name="Field[]" type="hidden" id="Field[]" value="BuyerId">                  </TD><TD align="center">
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
                      </SELECT>
                  </TD>
                  <TD width="482">
				  <select name=value[] id="value[]" style="width: 274px;">
                    <?php 
					echo "<option value='' selected>全部</option>";
					$buyerSql = mysql_query("SELECT S.BuyerId,M.Name 
					FROM $DataIn.cw1_fkoutsheet S 
					LEFT JOIN $DataPublic.staffmain M ON S.BuyerId=M.Number WHERE 1 GROUP BY S.BuyerId ORDER BY S.BuyerId",$link_id);//and S.Mid>0
					if($buyerRow = mysql_fetch_array($buyerSql)){
						do{
							$thisBuyerId=$buyerRow["BuyerId"];
							$Buyer=$buyerRow["Name"];
							echo "<option value='$thisBuyerId'>$Buyer</option>";
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
                  </p>
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
                    <?php 
					echo "<option value='' selected>全部</option>";
					$providerSql= mysql_query("SELECT S.CompanyId,P.Forshort,P.Letter 
					FROM $DataIn.cw1_fkoutsheet S 
					LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
					WHERE 1  GROUP BY S.CompanyId ORDER BY P.Letter",$link_id);//and S.Mid>0
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
                  <TD align="right"><p>币种
                      <input name="Field[]" type="hidden" id="Field[]" value="Currency">
                  </p>
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <OPTION value==  selected>=</OPTION>
                    </SELECT>                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <?php 
					echo "<option value='' selected>全部</option>";
					$cResult= mysql_query("SELECT C.Id,C.Symbol FROM $DataPublic.currencydata C 
					 WHERE 1 AND C.Estate=1",$link_id);
					if($cRow = mysql_fetch_array($cResult)){
						do{
							$cId=$cRow["Id"];
							$Symbol=$cRow["Symbol"];
							echo"<option value='$cId'>$Symbol</option>";
							}while ($cRow = mysql_fetch_array($cResult));
						}
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR> 
 
                
<TR>
  <TD align="right"><p>配件分类
      <input name="Field[]" type="hidden" id="Field[]" value="TypeId">
  </p></TD>
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
      </SELECT>  </TD>
  <TD><select name=value[] id="value[]" style="width: 274px;">
      <?php 
		echo "<option value='' selected>全部</option>";
		$TypeSql= mysql_query("SELECT A.TypeId,T.Letter,T.TypeName
		FROM $DataIn.cw1_fkoutsheet S 
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId
		WHERE 1 and S.Mid=0 and (S.FactualQty>0 OR S.AddQty>0) 
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
      <input name="table[]" type="hidden" id="table[]" value="D">
      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
  </TD>
</TR>
<TR>
  <TD align="right">配 件 ID
        <input name="Field[]" type="hidden" id="Field[]" value="StuffId">  </TD>
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
      </SELECT>  </TD>
  <TD>      <input name=value[] class=textfield id="value[]" size=48>
    <input name="table[]" type="hidden" id="table[]" value="S">
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
    </SELECT>  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
      <input name="table[]" type="hidden" id="table[]" value="D">
      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
  </TD>
</TR>
<TR>
  <TD align="right">购买单价
        <input name="Field[]" type="hidden" id="Field[]" value="Price">  </TD>
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
      </SELECT>  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
      <input name="table[]" type="hidden" id="table[]" value="S">
      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
  </TD>
</TR>
<TR>
  <TD align="right"><p>订单数量
      <input name="Field[]" type="hidden" id="Field[]" value="OrderQty">
  </p></TD>
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
      </SELECT>  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
      <input name="table[]" type="hidden" id="table[]" value="S">
      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
  </TD>
</TR>
<TR>
  <TD align="right"><p>使用库存
      <input name="Field[]" type="hidden" id="Field[]" value="StockQty">
  </p></TD>
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
      </SELECT>  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
      <input name="table[]" type="hidden" id="table[]" value="S">
      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
</TR>
<TR>
  <TD align="right">需购数量
        <input name="Field[]" type="hidden" id="Field[]" value="FactualQty">  </TD>
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
      </SELECT>  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
      <input name="table[]" type="hidden" id="table[]" value="S">
      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
  </TD>
</TR>
<TR>
  <TD align="right"><p>增购数量
          <input name="Field[]" type="hidden" id="Field[]" value="AddQty">
  </p></TD>
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
      </SELECT>  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size=48>
      <input name="table[]" type="hidden" id="table[]" value="S">
      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
  </TD>
</TR>

 
<TR>
  <TD><div align='right'>出货日期
          <input name='Field[]' type='hidden' id='Field[]' value='Date'>
  </div></TD>
  <TD><div align='center'>
      <SELECT name=fun[] id='fun[]' style='width: 60px;'>
        <OPTION value== selected>=</OPTION>
        <OPTION value='>'>&gt;</OPTION>
        <OPTION value='>='>&gt;=</OPTION>
        <OPTION value='<'>&lt;</OPTION>
        <OPTION value='<='>&lt;=</OPTION>
        <OPTION value=!=>!=</OPTION>
      </SELECT>
  </div></TD>
  <TD width='482'><INPUT name=value[] class=textfield id='value[]' size=18 onfocus='WdatePicker()' readonly>
    至
      <INPUT name=DateArray[] class=textfield id='DateArray[]' size=18 onfocus='WdatePicker()' readonly>
      <input name='table[]' type='hidden' id='table[]' value='H'>
      <input name='types[]' type='hidden' id='types[]' value='isDate'>
  </TD>
</TR>  




<TR>
  <TD align="right"><p>结付状态
          <input name="Field[]" type="hidden" id="Field[]" value="Estate">
  </p></TD>
  <TD align="center">
    <SELECT name=fun[] id="fun[]" style="width: 60px;">
            <OPTION value==  selected>=</OPTION>
 	     </SELECT>  </TD>
  <TD><select name=value[] id="value[]" style="width: 274px;">
      <?php 
      if($fromWebPage==$funFrom."_cw"){		
	    if($Estate==0){
					echo "<option value='0' selected>已结付</option>";
					}
					else
					{
						echo "<option value='' selected>未结付</option>";
					}
		}else{
		        echo "<option value='3' selected>未结付</option>";
			    echo "<option value='0'>已结付</option>";
		}
		?>
    </select> 
      <input name="table[]" type="hidden" id="table[]" value="S">
      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
  </TD>
</TR>

				<?php 
				if($fromWebPage==$funFrom."_cw"){				
				?>
				<?php 
				}?>
              </TBODY>
            </TABLE></td>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/select_model_b.php";
?>