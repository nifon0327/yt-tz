<?php
//步骤1
include "../model/modelhead.php";
//步骤2：
$nowWebPage ="bom_data_add";
$toWebPage  = "bom_data_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,proId,$proId";

$type = $_GET["type"];
if (!$type) {
    $type = $_POST["type"];
}

if ($type) {
    if ($type == 2) {
        $fromWebPage = "bom_loss_read";
    } else {
        $fromWebPage = "bom_mould_read";
    }
} else {
    //功能菜单 入口
    $isBack="N";
}

ChangeWtitle("$SubCompany 数据导入");

//步骤3：
$tableWidth=800;$tableMenuS=500;
//include "../model/subprogram/add_model_t.php";

$d=anmaIn("bom/phpExcelReader/",$SinkOrder,$motherSTR);
$f=anmaIn("bom_sample.xls",$SinkOrder,$motherSTR);
$sampleFile="<a href=\"../admin/openorload.php?d=$d&f=$f&Type=&Action=6\"target=\"download\">下载模板<img src='../images/down.gif' style='vertical-align: bottom;margin-left: 5px;' title='样板EXCEL' width='18' height='18'></a>";

echo " <input type='hidden' name='type' id='type' value='$type' />";
//步骤4：需处理
?>

<style type="text/css">
.input_radio2{
	vertical-align: top;
	margin-top: -1.5px;
}
.select1{
    min-width: 100px;
	height: 25px;
	margin-right: 25px;
	border: 1px solid lightgray;
}
.table_td{
	height: 50px;
	border-bottom: 1px solid lightgray;
}
.table_td2{
	border-bottom: 1px solid lightgray;
}
.input_file1{
	width:300px;
	height: 20px;
}
</style>
    <body ><form name="form1" id="checkFrom" enctype="multipart/form-data" action="" method="post" >
    <div class="div-select div-mcmain" style='width:<?php echo $tableWidth ?>'>

<table border="0" width="<?php echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#F2F3F5" id='NoteTable'>
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="0">
		<tr>
		  <td scope="col" colspan="2"  class="table_td td1" style="font-weight: bold;text-align: center">客户项目:
		  	<select name='tradeChoose' id='tradeChoose' class="select1">
			<?php
			//项目数据检索
			$mySql="SELECT a.Id, a.Forshort, b.TradeNo FROM $DataIn.trade_object a
			INNER JOIN $DataIn.trade_info b on a.Id = b.TradeId 
            INNER JOIN $DataIn.bom_object c on a.Id = c.TradeId and c.Estate in (0,3,4)
            where a.ObjectSign = 2 order by a.Date";
			
			$myResult = mysql_query($mySql, $link_id);
			if($myResult  && $myRow = mysql_fetch_array($myResult)){
			    do{
			        $Id = $myRow["Id"];
			        $Forshort = $myRow["Forshort"];
			        //$TradeNo = $myRow["TradeNo"];
			        echo "<option value='$Id' ", $Id == $proId?"selected":"", ">$Forshort</option>";
			    }while ($myRow = mysql_fetch_array($myResult));
			}
			?>
			</select>	
		  </td>
        </tr>
          <tr>
		  <td scope="col" class="table_td" style="font-weight: bold;" align="center">损耗数据行数:
		  	<input type='text' name='cmptLossNumber' id='cmptLossNumber' autocomplete='off' value='0' dataType="Number" msg="未填写或格式不对"/>
		  </td>
            <td scope="col" class="table_td" style="font-weight: bold;padding-left: 25px;">模具数据行数:
                <input type='text' name='cmptModuleNumber' id='cmptModuleNumber' autocomplete='off' value='0' dataType="Number" msg="未填写或格式不对"/>
            </td>
		</tr>
      	  <tr>
      		<td height="50" scope="col" class="table_td td1" align="right">
      			&nbsp;&nbsp;文件信息(EXCEL)&nbsp;&nbsp;
      			<input name="ExcelFile" type="file" id="ExcelFile" class="input_file1" style="width:200px" datatype="Filter" msg="非法的文件格式" accept="xls,XLS,xlsx" row="1" cel="1" />
      		</td>

      		<td height="50" align="center" scope="col" class="table_td td1">
    		  <input type="checkbox" name="lossChk" id="lossChk" class="input_radio2" value="2" <?php if ($type==null || $type == 2) echo "checked" ?> /><LABEL for="lossChk">损耗</LABEL>
    		  &nbsp;&nbsp;&nbsp;&nbsp;
    		  <input type="checkbox" name="mouldChk" id="mouldChk" class="input_radio2" value="3" <?php if ($type==null || $type == 3) echo "checked" ?> /><LABEL for="mouldChk">模具</LABEL>
    		</td>
      	  </tr>
      	  <tr>
              <td height="50"  scope="col"  ></td>
      		<td height="50" align="center" scope="col"  ><?php echo $sampleFile?></td>
		</tr>
      </table>
</td></tr></table>
        <table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0"  bgcolor="#F2F3F5">
            <tr>
                <td id="menuB1" width="<?php  echo $tableMenuS?>">&nbsp;</td>
                <td width="150" id="menuT2" align="center" class=''>
                    <table border="0" align="center" cellspacing="0">
                        <tr>
                            <td class="readlink" height="50px">
                                <nobr>
                                    <?php
                                    echo $SaveFun;
                                    echo $CustomFun;//自定义功能
                                    if($CheckFormURL=="thisPage"){
                                        if($SaveSTR!="NO"){
                                            if(isset($ValidatorUd)&&$ValidatorUd){
                                                $ClickFucUd = 'if(Validator.Validate(document.getElementById(document.form1.id),3,"", 3)){CheckForm();}';
                                            }else{
                                                $ClickFucUd = 'CheckForm()';
                                            }
                                            echo"<span onClick='$ClickFucUd' class='btn-confirm'>保存</span>&nbsp;　&nbsp;";
                                        }
                                        if ($ResetSTR!="NO"){
                                            echo"<span onClick='javascript:ReOpen(\"$nowWebPage\");' class='btn-confirm'>重置</span>";
                                        }
                                    }
                                    else{
                                        if($SaveSTR!="NO"){
                                            $ErrorInfoModel=$ErrorInfoModel==""?3:$ErrorInfoModel;
                                            echo"<span id='buttonSaveBtn' onClick='Validator.Validate(document.getElementById(document.form1.id),$ErrorInfoModel,\"$toWebPage\")' class='btn-confirm'>保存</span>&nbsp;　&nbsp;";
                                        }
                                        if($ResetSTR!="NO"){
                                            echo"<span onClick='javascript:ReOpen(\"$nowWebPage\");' class='btn-confirm'>重置</span>";
                                        }
                                    }
                                    if($isBack!="N"){
                                        echo"&nbsp;<span onClick='javascript:ReOpen(\"$fromWebPage\");' class='btn-confirm'>返回</span>";
                                    }
                                    ?>
                                </nobr>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <?php
            $SearchRows="";
            if($Parameter!=""){
                PassParameter($Parameter);
            }
            ?>

</table>
    </div>
    </form></body>
<?php
//步骤5：
//include "../model/subprogram/add_model_b.php";

echo"<iframe name=\"download\" style=\"display:none\"></iframe>";
?>