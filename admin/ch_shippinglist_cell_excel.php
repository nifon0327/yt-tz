<?php
//步骤1
include "../model/modelhead.php";
//步骤2：
$fromWebPage = "ch_shippinglist_cell_enter";
$Log_Item = "出货单元导入";
$nowWebPage = "ch_shippinglist_cell_excel"; //$funFrom."_save"
$_SESSION["nowWebPage"] = $nowWebPage;
$ALType = "fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion = "保存";
$TitleSTR = $SubCompany . " " . $Log_Item . $Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime = date("Y-m-d H:i:s");
$Operator = $Login_P_Number;
$OperationResult = "Y";
$Date = date("Y-m-d");
//$Month="2011-04";
$x = 0;
?>
<body onkeydown="unUseKey()" oncontextmenu="event.returnValue=false" onhelp="return false;">
<table border="0" width="470" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">

    <tr>
        <td height="20" valign="top">
            <p>操作日志：</p>
        </td>
    </tr>
    <tr>
        <td valign="top" marquee class="A1111">
            <div style="height:160;overflow-y:auto">
                <?php
                echo $_SESSION['Login_Name'] . " 于 " . $DateTime . " 进行 $Log_Item - $Log_Funtion 的操作，操作结果如下：<br>";
                if (empty($_FILES['ExcelFile']['tmp_name'])) {
                    //没有选择构件资料
                    echo "<p style='color: red'>请选择导入资料！</p>";
//    include "../model/logpage.php";
                    return;
                }

                //步骤3：需处理
                //读取导入的XLS文件，核对后写入数据表
                $FilePath = "phpExcelReader/cellExcel/";
                if (!file_exists($FilePath)) {
                    dir_mkdir($FilePath);
                }
                $tmpFile = $_FILES['ExcelFile']['tmp_name'];
                $imgname = $_FILES["ExcelFile"]["name"]; //获取上传的文件名称
                $filetype = pathinfo($imgname, PATHINFO_EXTENSION);//获取后缀
                if ($filetype == "xls" || $filetype == "XLS") {
                    if ($tmpFile != "") {
                        $str_time = time();
                        $tmpXmlFile = $Login_P_Number . "_" . $str_time . "." . $filetype;
                        $PreFileName = $FilePath . $tmpXmlFile;
                        $uploadInfo = move_uploaded_file($tmpFile, $PreFileName);
                        chmod($PreFileName, 0777);
                        //echo iconv("UTF-8","gb2312",$PreFileName);
                        if ($uploadInfo != "") {
                            require_once "phpExcelReader/Excel/reader.php";
                            $data = new Spreadsheet_Excel_Reader();
                            $data->setOutputEncoding('utf-8');
                            $data->read($PreFileName);
                            error_reporting(E_ALL ^ E_NOTICE);

                            $rs = $data->getExcelResult();

                            $RowNum = $data->sheets[0]['numRows'];
                            $ColNum = $data->sheets[0]['numCols'];

                            $x = 0;
                            for ($i = 2; $i <= $RowNum; $i++) {

                                if ($rs[$i][5] != NULL || $rs[$i][5] != "") {
                                    //序号
                                    $Id = trim($rs[$i][1]);
                                    //客户名称
                                    $CompanyName = trim($rs[$i][2]);
                                    //楼栋
//                                    $Build = trim($rs[$i][3]);
                                    //楼层
//                                    $Floor = trim($rs[$i][4]);
                                    //类型
                                    $TypeName = trim($rs[$i][3]);
                                    //构件名称
                                    $GName = trim($rs[$i][4]);
                                    //单元
                                    $cell = trim($rs[$i][5]);

                                    $cName = '-' . $GName . '-';

                                    $CompanySql = "SELECT CompanyId FROM $DataIn.trade_object WHERE Forshort = '$CompanyName' ";
                                    //echo $updateSql;

                                    $CompanyRecode = mysql_query($CompanySql);
                                    if ($myrow = mysql_fetch_array($CompanyRecode)) {
                                        $CompanyId = $myrow['CompanyId'];
                                        $updateSql = "update $DataIn.productdata set cell = '$cell' where CompanyId = '$CompanyId' AND cName LIKE '%$cName%'";
                                        //echo $updateSql;

                                        $InRecode = @mysql_query($updateSql);
                                        if ($InRecode) {
                                            $x++;
                                        }
                                    }
                                }
                            }
                            echo "<p style='color: green'> $x 条记录更新成功！</p>";
                        }
                    } else {
                        echo "<p style='color: red'>加载Excel文件失败！</p>";
                    }
                }else{
                    echo "<p style='color: red'>不支持当前Excel格式！</p>";
                }

                //步骤4：
                //include "../model/logpage.php";
                ?>
