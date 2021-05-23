<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ch10_samplemail
$DataIn.ch10_mailaddress
$DataIn.trade_object
$DataPublic.freightdata
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 客户样品寄送资料查询");			//需处理
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
                  <?php 
				//如果是来自于财务查询
				if($fromWebPage==$funFrom."_cw" && $Estate==0){
				?>
                  <TD><div align="right">结付编号
                          <input name="Field[]" type="hidden" id="Field[]" value="Id">
                  </div></TD>
                  <TD><div align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value==  selected>=</OPTION>
                        <OPTION value=!=>!=</OPTION>
                      </SELECT>
                  </div></TD>
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size="48">
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
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size="18" onfocus="WdatePicker()" readonly>
                    至
                      <INPUT name=DateArray[] class=textfield id="DateArray[]" size="18" onfocus="WdatePicker()" readonly>
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
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size="48">
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
                  <TD width="432"><select name=value[] id="value[]" style="width: 274px;">
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
                  <TD width="432"><select name=value[] id="value[]" style="width: 274px;">
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
                  <TD width="432"><INPUT name=value[] class=textfield id="value[]" size="48">
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
                <TD align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户
                  <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">
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
                <TD><select name=value[] id="value[]" style="width: 274px;">
                    <option value="" selected>请选择</option>
                    <?php 
					$LinkMan_Result= mysql_query("SELECT A.CompanyId,C.Forshort FROM $DataIn.ch10_samplemail M					
					LEFT JOIN $DataIn.ch10_mailaddress A ON A.Id=M.LinkMan
					LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId
					GROUP BY A.CompanyId
					ORDER BY C.OrderBy DESC",$link_id);
					if($LinkManRow = mysql_fetch_array($LinkMan_Result)){
						do{
							echo"<option value='$LinkManRow[CompanyId]'>$LinkManRow[Forshort]</option>";
							} while($LinkManRow = mysql_fetch_array($LinkMan_Result));
						}
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="A">
                    <input name="types[]" type="hidden" id="types[]" value="isNum">
                </TD>
		      </TR>
              <TBODY>
				<TR>
                  <TD width="102" align="right">收 件 人
                      <input name="Field[]" type="hidden" id="Field[]" value="LinkMan">
                  </TD>
                  <TD width="95" align="center">
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
                  <TD width="432"><select name=value[] id="value[]" style="width: 274px;">
					<option value="" selected>请选择</option>
					<?php 
					$LinkMan_Result= mysql_query("SELECT A.Id,A.LinkMan,C.Forshort 
					FROM $DataIn.ch10_samplemail M					
					LEFT JOIN $DataIn.ch10_mailaddress A ON A.Id=M.LinkMan
					LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId
					GROUP BY M.LinkMan
					ORDER BY C.OrderBy DESC,A.Id",$link_id);
					if($LinkManRow = mysql_fetch_array($LinkMan_Result)){
						do{
							echo"<option value='$LinkManRow[Id]'>$LinkManRow[Forshort] - $LinkManRow[LinkMan]</option>";
							} while($LinkManRow = mysql_fetch_array($LinkMan_Result));
						}
					?>
                  </select>                    <input name="table[]" type="hidden" id="table[]" value="S">
      <input name="types[]" type="hidden" id="types[]" value="isNum">
                  </TD>
			    </TR>
                <TR>
                  <TD align="right"><p>快递公司
                      <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">
                  </p>
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
		   			<option value="" selected>请选择</option>
					<?php 
					$forward_result = mysql_query("SELECT M.CompanyId,F.Forshort 
					FROM $DataIn.ch10_samplemail M
					LEFT JOIN $DataPublic.freightdata F ON F.CompanyId=M.CompanyId
					GROUP BY M.CompanyId ORDER BY M.CompanyId",$link_id);
					if($forward_myrow = mysql_fetch_array($forward_result)){
						do{
							echo"<option value='$forward_myrow[CompanyId]'>$forward_myrow[Forshort]</option>";
							} while ($forward_myrow = mysql_fetch_array($forward_result));
						}
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right"><p>寄件日期
                      <input name="Field[]" type="hidden" id="Field[]" value="SendDate">
                  </p></TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD>                      <INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
至
  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
  <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isDate" />
                  </TD>
                </TR>			
<TR>
                  <TD align="right"><p>提单号码
                      <input name="Field[]" type="hidden" id="Field[]" value="ExpressNO">
                  </p>
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
</TD>
                </TR>
<TR>
  <TD align="right">件&nbsp;&nbsp;&nbsp;&nbsp;数
      <input name="Field[]" type="hidden" id="Field[]" value="Pieces">
  </TD>
  <TD align="center">
    <SELECT name=fun[] id="fun[]" style="width: 60px;">
        <OPTION value== selected>=</OPTION>
        <OPTION 
          value=!=>!=</OPTION>
    </SELECT>  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
      <input name="table[]" type="hidden" id="table[]" value="S">
      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
  </TD>
</TR>
<TR>
  <TD align="right">重&nbsp;&nbsp;&nbsp;&nbsp;量
      <input name="Field[]" type="hidden" id="Field[]" value="Weight">
  </TD>
  <TD align="center">
    <SELECT name=fun[] id="fun[]" style="width: 60px;">
        <OPTION value== selected>=</OPTION>
        <OPTION 
          value=!=>!=</OPTION>
    </SELECT>  </TD>
  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
      <input name="table[]" type="hidden" id="table[]" value="S">
      <input name="types[]" type="hidden" id="types[]" value="isNum" />
  </TD>
</TR>					               
<TR>
            <TD align="right">单&nbsp;&nbsp;&nbsp;&nbsp;价
              <input name="Field[]" type="hidden" id="Field[]" value="Price">
            </TD>
            <TD align="center">
              <SELECT name=fun[] id="fun[]" style="width: 60px;">
                <OPTION value== selected>=</OPTION>
                <OPTION 
          value=!=>!=</OPTION>
              </SELECT>            </TD>
            <TD><INPUT name=value[] class=textfield id="value[]" size="48">
              <input name="table[]" type="hidden" id="table[]" value="S">
              <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">费&nbsp;&nbsp;&nbsp;&nbsp;用
                    <input name="Field[]" type="hidden" id="Field[]" value="Amount">
                  </TD>
                  <TD align="center">
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>                  </TD>
                  <TD>
				  <INPUT name=value[] class=textfield id="value[]" size="48">
                  <input name="table[]" type="hidden" id="table[]" value="S">                  
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">付款方式
                      <input name="Field[]" type="hidden" id="Field[]" value="PayType">
                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                      </SELECT>                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
					<option value="" selected>请选择</option>
					<option value="1">CASH 现付</option>
					<option value="2">A/C 月结</option>
					<option value="3">PP 预付</option>
					<option value="4">CC 到付</option>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">服务类型
                      <input name="Field[]" type="hidden" id="Field[]" value="ServiceType">
                  </TD>
                  <TD align="center">
                      <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <OPTION value== selected>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                      </SELECT>                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
					<option value="" selected>请选择</option>
					<option value="1">PARCEL 包裹</option>
					<option value="2">DOCUMENT 文件</option>
					<option value="3">OTHERS 其它</option>
                    </select>
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]" value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">物品描述
                      <input name="Field[]" type="hidden" id="Field[]" value="Description">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">数&nbsp;&nbsp;&nbsp;&nbsp;量
                    <input name="Field[]" type="hidden" id="Field[]" value="Qty">
                  </TD>
                  <TD align="center">
                      <select name="fun[]" id="fun[]" style="width: 60px;">
                        <option value="=" selected>=</option>
                        <option value="!=">!=</option>
                      </select>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注
                    <input name="Field[]" type="hidden" id="Field[]" value="Remark">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                        <option value="LIKE" selected>包含</option>
                        <OPTION value==>=</OPTION>
                        <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                      <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right">经 手 人
                      <input name="Field[]" type="hidden" id="Field[]" value="HandledBy">
                  </TD>
                  <TD align="center">
				    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>			        </TD>
                  <TD>
					  <select name=value[] id="value[]" style="width: 274px;">
					  <?php 
						$result = mysql_query("SELECT U.HandledBy,M.Name FROM $DataIn.ch10_samplemail U,$DataPublic.staffmain M  WHERE M.Number>10001 AND M.Number=U.HandledBy and M.Estate='1' GROUP BY U.HandledBy ORDER BY M.BranchId,M.JobId,M.Number",$link_id);
						if($myrow = mysql_fetch_array($result)){
							echo "<option value=''>请选择</option>";
							do{
								$Number=$myrow["Number"];
								$Name=$myrow["Name"];
								echo "<option value='$Number'>$Name</option>";
								}while ($myrow = mysql_fetch_array($result));
							} 
					  ?>
					  </select>
					  <input name="table[]" type="hidden" id="table[]" value="S">
                      <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
</TD>
                </TR>
                <TR>
                  <TD align="right">签收日期
                      <input name="Field[]" type="hidden" id="Field[]" value="ReceiveDate">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <option value="LIKE" selected>包含</option>
                      <OPTION value==>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isStr" /></TD>
                </TR>
                <TR>
                  <TD align="right">结付状态
                      <input name="Field[]" type="hidden" id="Field[]" value="Estate">
                  </TD>
                  <TD align="center">
                    <SELECT name=fun[] id="fun[]" style="width: 60px;">
                      <OPTION value== selected>=</OPTION>
                      <OPTION 
          value=!=>!=</OPTION>
                    </SELECT>                  </TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
                    <option selected  value="">全部</option>
                    <option value="1">未处理</option>
                    <option value="2">请款中</option>
                    <option value="3">等待结付</option>
                    <option value="0">已结付</option>
                                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
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