<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 报价产品查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,TypeId,$TypeId,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.productdata";
?>	  	
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<TABLE width="600" border=0 align="center">
	  <TR>
        <TD width="127" align="right" >客户
            <input name="Field[]" type="hidden" id="Field[]" value="CompanyId">
        </TD>
        <TD width="98" align="center" ><select name="fun[]" id="fun[]" style="width: 60px;">
            <option value="=" selected>=</option>
            <option value="!=">!=</option>
          </select>
        </TD>
        <TD width="361" ><select name="value[]" id="value[]" size="1" style="width: 274px;">
            <?php  
					echo"<option value='' selected>全部</option>";
					$result = mysql_query("SELECT * FROM $DataIn.trade_object WHERE cSign=$Login_cSign AND Estate=1 order by Id",$link_id);
					if($myrow = mysql_fetch_array($result)){
						do{
							echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
							} while ($myrow = mysql_fetch_array($result));
						}
				  ?>
          </select>
            <input name="table[]" type="hidden" id="table[]" value="P">
            <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
	    </TR>
	  <TR>
        <TD align="right" >产品
          <input name="Field[]" type="hidden" id="Field[]" value="TypeId"></TD>
        <TD align="center" ><SELECT name=fun[] id="fun[]" style="width: 60px;">
            <OPTION value== 
          selected>=</OPTION>
            <OPTION value=!=>!=</OPTION>
          </SELECT>
        </TD>
        <TD ><select name="value[]" id="value[]" style="width: 274px;">
            <?php 
					echo"<option value='' selected>全部</option>";
					$result = mysql_query("SELECT Letter,TypeId,TypeName FROM $DataIn.producttype WHERE Estate='1' order by Letter",$link_id);
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
        <TD align="right" >备注
            <input name="Field[]" type="hidden" id="Field[]" value="Remark">
        </TD>
        <TD align="center" ><SELECT name=fun[] id="fun[]" style="width: 60px;">
            <option value="LIKE" selected>包含</option>
            <OPTION value==>=</OPTION>
            <OPTION 
          value=!=>!=</OPTION>
          </SELECT>
        </TD>
        <TD ><INPUT name=value[] class=textfield id="value[]" size="48">
            <input name="table[]" type="hidden" id="table[]" value="P">
            <input name="types[]" type="hidden" id="types[]"
                value="isStr" />
        </TD>
	    </TR>
         <TBODY>
                <TR>
                  <TD align="right" >更新日期
                    <input name="Field[]" type="hidden" id="Field[]" value="Date">                  </TD>
                  <TD align="center" >
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
                  <TD ><INPUT name=value[] class=textfield id="value[]" size=18 onfocus="WdatePicker()" readonly>
				  至
				  <INPUT name=DateArray[] class=textfield id="DateArray[]" size=18 onfocus="WdatePicker()" readonly>
                  <input name="table[]" type="hidden" id="table[]" value="P">
                  <input name="types[]" type="hidden" id="types[]" value="isDate"></TD>
                </TR>
                <TR>
                  <TD align="right" >可用标记
                    <input name="Field[]" type="hidden" id="Field[]" value="Estate">                  </TD>
                  <TD align="center" >
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>
                  </TD>
                  <TD >                    <select name=value[] id="value[]" style="width: 274px;">
                      <option selected value="">全部</option>
                      <option value="1">可用</option>
                      <option value="0">禁用</option>
                    </select>
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" />
                  </TD>
                </TR>
                <TR>
                  <TD align="right" >操作员
                    <input name="Field[]" type="hidden" id="Field[]" value="Operator">                  </TD>
                  <TD align="center" >
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>
                  </TD>
                  <TD ><select name=value[] id="value[]" style="width: 274px;">
				  	<option value="" selected>全部</option>
                    <?php 
					include "../model/subprogram/select_model_stafflist.php";
					?>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="P">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
                <TR>
                  <TD align="right" >锁定状态
                    <input name="Field[]" type="hidden" id="Field[]" value="Locks">                  </TD>
                  <TD align="center" >
                    <select name="fun[]" id="fun[]" style="width: 60px;">
                      <option value="=" selected>=</option>
                      <option value="!=">!=</option>
                    </select>
                  </TD>
                  <TD ><select name=value[] id="value[]" style="width: 274px;">
                    <option selected  value="">全部</option>
                    <option value="0">锁定</option>
                    <option value="1">未锁定</option>
                  </select>
                    <input name="table[]" type="hidden" id="table[]" value="P">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
              </TBODY>
	    </TABLE>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/select_model_b.php";
?>