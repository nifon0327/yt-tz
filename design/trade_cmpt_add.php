<?php
defined('IN_COMMON') || include '../basic/common.php';

//步骤1
include "../model/modelhead.php";
//步骤2：
$nowWebPage ="trade_cmpt_add";
$toWebPage  = "trade_cmpt_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,type,$type,proId,$proId";

if ($type) {
    if ($type == 1) {
        $fromWebPage = "trade_drawing_read";
    } else if ($type == 2) {
        $fromWebPage = "trade_steel_read";
    } else {
        $fromWebPage = "trade_embedded_read";
    }
} else {
    //功能菜单 入口
    $isBack="N";
}

ChangeWtitle("$SubCompany 数据导入");

//步骤3：
$tableWidth=850;$tableMenuS=500;
//include "../model/subprogram/add_model_t.php";
?>
    <body ><form name="form1" id="checkFrom" enctype="multipart/form-data" action="" method="post" >
        <div class="div-select div-mcmain" style='width:<?php echo $tableWidth ?>'>
        <?php

        $d=anmaIn("design/phpExcelReader/",$SinkOrder,$motherSTR);
        $f=anmaIn("trade_sample.xls",$SinkOrder,$motherSTR);
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
                height: 40px;
                border-bottom: 1px solid lightgray;
            }
            .table_td2{
                border-bottom: 1px solid lightgray;
            }
            .input_file1{
                width:300px;
                height: 20px;
            }
            td{
                height: 40px;
            }
            .select2{
                width: auto;
                padding: 0 1%;
                margin: 0;
            }

            .sel option{
                text-align:center;
            }
        </style>
            <table border="0" width="<?php echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#F2F3F5" id='NoteTable'>
                <tr><td class="A0011">
                        <table width="760" border="0" align="center" cellspacing="0">
                            <tr align="center">
                                <td scope="col" colspan="2" class="table_td" >客户项目:
                                    <select name='tradeChoose' id='tradeChoose' class="select1">
                                    <?php
                                    //项目数据检索
                                    $mySql="SELECT a.Id, a.Forshort, b.TradeNo FROM $DataIn.trade_object a
			INNER JOIN $DataIn.trade_info b on a.Id = b.TradeId where a.ObjectSign = 2 
            and b.Estate in (0, 3, 5, 8, 9) order by a.Date";

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
                            <td scope="col" class="table_td2">
                                <table>
                                    <tr>
                                        <td colspan="2" >
                                            项目栋号：
                                            <input type="text" name="buildId" id="buildId" autocomplete="off" placeholder="请输入楼栋编号" value="" dataType="LimitB" max="20" min="1" msg="未填写或格式不对">
                                        </td>
                                    </tr><tr>
                                        <td colspan="2" >
                                            构件类型：
                                            <select name='TypeName' id='TypeName' class="select2"  dataType="Require" msg="未选择构件类型">
                                                <option value='' selected>请选择构件类型</option>";
                                                <?php
                                                //项目数据检索
                                                $TypeSql="SELECT DISTINCT TypeId,TypeName FROM $DataIn.producttype  order by TypeId";

                                                $TypeResult = mysql_query($TypeSql, $link_id);
                                                if($TypeResult  && $TypeRow = mysql_fetch_array($TypeResult)){
                                                    do{
                                                        $TypeId = $TypeRow["TypeId"];
                                                        $TypeName = $TypeRow["TypeName"];
                                                        echo "<option value='$TypeName' >$TypeName</option>";
                                                    }while ($TypeRow = mysql_fetch_array($TypeResult));
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr><tr>
                                        <td colspan="2" >
                                            数　　量：
                                            <input type='text' name='cmptNumber' id='cmptNumber' autocomplete='off' value='' dataType="Number" msg="未填写或格式不对"/>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td  align="right" scope="col">构件资料(EXCEL)</td>
                                        <td scope="col"><input name="ExcelFile" type="file" id="ExcelFile" class="input_file1" style="width:200px" datatype="Filter" msg="非法的文件格式" accept="xls,XLS,xlsx" row="1" cel="1" /></td>
                                    </tr>
<!--                                    <tr>-->
<!--                                        <td  align="center" scope="col" colspan=2>-->
<!--                                            <input type="checkbox" name="drawingChk" id="drawingChk" class="input_radio2" value="1" --><?php //if ($type==null || $type == 1) echo "checked" ?><!-- /><LABEL for="drawingChk">图纸</LABEL>&nbsp;&nbsp;-->
<!--                                            <input type="checkbox" name="steelChk" id="steelChk" class="input_radio2" value="2" --><?php //if ($type==null || $type == 2) echo "checked" ?><!-- /><LABEL for="steelChk">钢筋</LABEL>&nbsp;&nbsp;-->
<!--                                            <input type="checkbox" name="embeddedChk" id="embeddedChk" class="input_radio2" value="3" --><?php //if ($type==null || $type == 3) echo "checked" ?><!-- /><LABEL for="embeddedChk">预埋件</LABEL>-->
<!--                                        </td>-->
<!--                                    </tr>-->
                                    <tr>
                                        <td  align="right" scope="col" colspan=2><?php echo $sampleFile?></td>
                                    </tr>
                                </table>
                            </td>
                            <td scope="col" class="table_td2" style="padding-left: 25px;border-left: 1px solid lightgray;" class="table_td2" colspan="3">
                                <table>
                                    <tr>
                                        <td  align="right" scope="col">成品图纸</td>
                                        <td scope="col"><input name="PordFile[]" type="file" id="PordFile" class="input_file1" row="1" cel="1" multiple="multiple"/></td>
                                    </tr>
                                    <tr>
                                        <td  align="right" scope="col">模具图纸</td>
                                        <td scope="col"><input name="MouldFile[]" type="file" id="MouldFile" class="input_file1" row="1" cel="1" multiple="multiple"/></td>
                                    </tr>
                                    <tr>
                                        <td  align="right" scope="col">钢筋图纸</td>
                                        <td scope="col"><input name="SteelFile[]" type="file" id="SteelFile" class="input_file1" row="1" cel="1" multiple="multiple"/></td>
                                    </tr>
                                    <tr>
                                        <td  align="right" scope="col">预埋件图纸</td>
                                        <td scope="col"><input name="EmbeddedFile[]" type="file" id="EmbeddedFile" class="input_file1" row="1" cel="1" multiple="multiple"/></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td></tr></table>
            <table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#F2F3F5">
                <tr>
                    <td  id="menuB1" width="<?php  echo $tableMenuS?>">&nbsp;</td>
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
                                                echo"<span onClick='CheckForm()' class='btn-confirm'>保存</span>&nbsp;";
                                            }
                                            if ($ResetSTR!="NO"){
                                                echo"<span onClick='javascript:ReOpen(\"$nowWebPage\");' class='btn-confirm'>重置</span>";
                                            }
                                        }
                                        else{
                                            if($SaveSTR!="NO"){
                                                $ErrorInfoModel=$ErrorInfoModel==""?3:$ErrorInfoModel;
                                                echo"<span id='buttonSaveBtn' onClick='SaveCmpt(\"$toWebPage\")' class='btn-confirm'>保存</span>&nbsp;";
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
    </form>
    </body>

    <script>

        function SaveCmpt (toWebPage) {
            var ExcelFile = jQuery("#ExcelFile").val();
            var PordFile = jQuery("#PordFile").val();
            var MouldFile = jQuery("#MouldFile").val();
            var SteelFile = jQuery("#SteelFile").val();
            var EmbeddedFile = jQuery("#EmbeddedFile").val();
            var cmptNumber = jQuery("#cmptNumber").val();
            var buildId = jQuery("#buildId").val();
            var TypeId = jQuery("#TypeId").val();

            if (ExcelFile == "" && cmptNumber == "" && buildId == "" && TypeId == "") {
                if (PordFile == "" && MouldFile == "" && SteelFile == "" && EmbeddedFile == "") {
                    alert("请选择导入资料");
                } else {
                    document.form1.action="trade_cmpt_save.php";
                    document.form1.submit();
                }
            } else {
                // if(jQuery('#drawingChk').is(':checked') || jQuery('#steelChk').is(':checked') || jQuery('#embeddedChk').is(':checked')) {
                    Validator.Validate(document.getElementById(document.form1.id),3,"trade_cmpt_save");
                // } else {
                //     alert("请选择导入构件资料类型");
                // }
            }
        }

    </script>
    </html>
<?php
//步骤5：
//include "../model/subprogram/add_model_b.php";

echo"<iframe name=\"download\" style=\"display:none\"></iframe>";
?>