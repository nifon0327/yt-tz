<?php
//步骤1
include "../model/modelhead.php";
//步骤2：
$nowWebPage ="ch_shippinglist_cell_enter";
$toWebPage  = "ch_shippinglist_cell_excel";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";

ChangeWtitle("$SubCompany 出货单元导入");

$fromWebPage="ch_shippinglist_cell";

//步骤3：
$tableWidth=400;
$tableMenuS=300;
include "../model/subprogram/add_model_t.php";

//步骤4：需处理
?>

    <style type="text/css">
        .input_file1{
            width:300px;
            height: 20px;
        }
    </style>
    <table border="0" width="<?php echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id='NoteTable'>
        <tr><td class="A0011">
                <table width="360" border="0" align="center" cellspacing="0">
                    <tr>
                        <td height="40" scope="col" colspan=2>
                            &nbsp;&nbsp;文件信息(EXCEL)&nbsp;&nbsp;
                            <input name="ExcelFile" type="file" id="ExcelFile" class="input_file1" style="width:200px" datatype="Filter" msg="非法的文件格式" accept="xls,XLS,xlsx" row="1" cel="1" />
                        </td>
                    </tr>

                </table>
            </td></tr></table>
<?php
//步骤5：
include "../model/subprogram/add_model_b.php";

echo"<iframe name=\"download\" style=\"display:none\"></iframe>";
?>