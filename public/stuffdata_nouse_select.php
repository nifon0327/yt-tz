<?php 
/*$DataIn.电信---yang 20120801
$DataIn.stufftype
$DataIn.stuffdata
二合一已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 查询条件");			//需处理
$nowWebPage =$funFrom."_select";	
$toWebPage  ="temptb_model";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,StuffType,$StuffType,Pagination,$Pagination,Page,$Page";
$tableMenuS=500;
$tableWidth=850;
//步骤3：
include "../model/subprogram/select_model_t.php";
//步骤4：需处理
$CheckTb="$DataIn.stuffdata";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
			  <TABLE width="600" border=0 align="center">
              <TBODY>
                <TR>
                  <TD width="89">配件ID 
                    <input name="Field[]" type="hidden" id="Field[]" value="StuffId"></TD>
                  <TD width="126"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD width="316"><INPUT name=value[] class=textfield id="value[]" size="48">
                    <input name="table[]" type="hidden" id="table[]" value="S">
                    <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
                <TR>
                  <TD><p>配件名称
                    <input name="Field[]" type="hidden" id="Field[]" value="StuffCname">
                  </p>
                  </TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD>配件分类
                  <input name="Field[]" type="hidden" id="Field[]" value="TypeId"></TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                    <?php 
					$stSql = "SELECT TypeId,TypeName,Letter FROM $DataIn.stufftype WHERE Estate=1 order by Letter";
					$stResult = mysql_query($stSql); 
					echo "<option value='' selected>全部</option>";
					while ( $stRows = mysql_fetch_array($stResult)){
						$TypeId=$stRows["TypeId"];
						$Letter=$stRows["Letter"];
						$TypeName=$Letter."-".$stRows["TypeName"];					
						echo "<option value='$TypeId'>$TypeName</option>";
						} 
					?>
                  </select>
                  <input name="table[]" type="hidden" id="table[]" value="S">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
                <TR>
                  <TD>规格
                  <input name="Field[]" type="hidden" id="Field[]" value="Spec"></TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD valign="top">参考买价
                  <input name="Field[]" type="hidden" id="Field[]" value="Price"></TD>
                  <TD valign="top"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD>图片上传
                  <input name="Field[]" type="hidden" id="Field[]" value="Jobid"></TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
                    <OPTION value==>=</OPTION>
                    <OPTION 
          value=!=>!=</OPTION>
                  </SELECT>
                  </TD>
				   <TD>
				    <select name=value[] id="value[]" style="width: 274px;">
                    <option value=''> 全部</option>
			<?php 
	          $mySql="SELECT Id,Name FROM $DataPublic.jobdata  
	                  WHERE Estate=1 AND Id in(3,4,6,7) order by Id,Name";
	           $result = mysql_query($mySql,$link_id);
               if($myrow = mysql_fetch_array($result)){
	   	          do{
			        $jobId=$myrow["Id"];
			         $jobName=$myrow["Name"];
				    echo "<option value='$jobId'>$jobName</option>";
			      }while ($myrow = mysql_fetch_array($result));
		       }
			?>		 
				  </select>
                  <input name="table[]" type="hidden" id="table[]" value="S">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" />                 
                  </TD>
                  
                  
                <TR>
                  <TD>备注
                  <input name="Field[]" type="hidden" id="Field[]" value="Remark"></TD>
                  <TD><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                  <TD valign="top">更新日期
                  <input name="Field[]" type="hidden" id="Field[]" value="Date"></TD>
                  <TD valign="top"><SELECT name=fun[] id="fun[]" style="width: 60px;">
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
                    <input name="types[]" type="hidden" id="types[]"
                value="isDate" />
</TD>
                </TR>
                <TR>
                  <TD>操作员
                  <input name="Field[]" type="hidden" id="Field[]" value="Operator"></TD>
                  <TD><select name="fun[]" id="fun[]" style="width: 60px;">
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
                value="isNum" />
				  </TD>
                </TR>
                <TR>
                  <TD>采&nbsp;&nbsp;&nbsp;&nbsp;购
                  <input name="Field[]" type="hidden" id="Field[]" value="BuyerId"></TD>
                  <TD><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
				  <option value="">全部</option>
                    <?php 
					$checkStaffSql =mysql_query("SELECT P.Number,P.Name FROM $DataPublic.staffmain P,$DataIn.bps B WHERE P.Number=B.BuyerId GROUP BY B.BuyerId ORDER BY P.Number",$link_id);
					if($checkStaffRow = mysql_fetch_array($checkStaffSql)){
						do{
							$pNumber=$checkStaffRow["Number"];
							$PName=$checkStaffRow["Name"];
							echo "<option value='$pNumber'>$PName</option>";
							}while ($checkStaffRow = mysql_fetch_array($checkStaffSql));
						} 
					?>		 
					</select>
                  </select>
                  <input name="table[]" type="hidden" id="table[]" value="B">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
                <TR>
                  <TD>供 应 商
                  <input name="Field[]" type="hidden" id="Field[]" value="CompanyId"></TD>
                  <TD><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
				    <option value="">全部</option>
                    <?php 
					$checkSql = "SELECT P.CompanyId,P.Forshort,P.Letter FROM $DataIn.trade_object P,$DataIn.bps B WHERE B.CompanyId=P.CompanyId GROUP BY B.CompanyId ORDER BY P.Letter";
					$checkResult = mysql_query($checkSql); 
					while ( $checkRow = mysql_fetch_array($checkResult)){
						$CompanyId=$checkRow["CompanyId"];
						$Forshort=$checkRow["Letter"].'-'.$checkRow["Forshort"];
						echo "<option value='$CompanyId'>$Forshort</option>";
						} 
					?>
                  </select>
                  <input name="table[]" type="hidden" id="table[]" value="B">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
                 <TR>
                  <TD>品检要求
                  <input name="Field[]" type="hidden" id="Field[]" value="CheckSign"></TD>
                  <TD><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
		             <option value="">全  部</option>
                             <option value='0'>抽  检</option>
                             <option value='1'>全  检</option>
                  </select>
                  <input name="table[]" type="hidden" id="table[]" value="S">
                  <input name="types[]" type="hidden" id="types[]"
                value="isNum" /></TD>
                </TR>
                 <TR>
                  <TD>送货楼层
                  <input name="Field[]" type="hidden" id="Field[]" value="SendFloor"></TD>
                  <TD><select name="fun[]" id="fun[]" style="width: 60px;">
                    <option value="=" selected>=</option>
                    <option value="!=">!=</option>
                  </select></TD>
                  <TD><select name=value[] id="value[]" style="width: 274px;">
				    <option value="">全部</option>
   		      <?php  
	          $mySql="SELECT Id,Name,Remark FROM $DataIn.base_mposition  
	                  WHERE Estate=1 order by  Remark";
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
              </TBODY>
	    </TABLE>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/select_model_b.php";
?>