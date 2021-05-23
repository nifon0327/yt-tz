<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 报价规则客户查询");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
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
               
                <TR>
                  <TD align="right" >操作员
                    <input name="Field[]" type="hidden" id="Field[]" value="Operator">                 
				 </TD>
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
                
              </TBODY>
	    </TABLE>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/select_model_b.php";
?>