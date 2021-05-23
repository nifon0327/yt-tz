<?php
//步骤1
include "../model/modelhead.php";
//步骤2：
$fromWebPage = "yw_order_price_add";
$Log_Item="订单价格数据";
$nowWebPage="yw_order_price_save"; //$funFrom."_save"
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
//$Month="2011-04";
$x=0;

if(empty($_FILES['ExcelFile']['tmp_name'])) {
    //没有选择构件资料
    echo "请选择导入资料";
    include "../model/logpage.php";
    return;
    //echo "<script >alert('请选择构件资料(EXCEL)');</script></html>";
}

//步骤3：需处理
//读取导入的XLS文件，核对后写入数据表
$FilePath="phpExcelReader/expressfile/";
if(!file_exists($FilePath)){
    dir_mkdir($FilePath);
}
$tmpFile=$_FILES['ExcelFile']['tmp_name'];
$imgname = $_FILES["ExcelFile"]["name"]; //获取上传的文件名称
$filetype = pathinfo($imgname, PATHINFO_EXTENSION);//获取后缀

if ($tmpFile!=""){
    $str_time=time();
    $tmpXmlFile=$Login_P_Number . "_" . $str_time .".".$filetype;
    $PreFileName=$FilePath .$tmpXmlFile;
    $uploadInfo=move_uploaded_file($tmpFile,$PreFileName);
    chmod($PreFileName,0777);
    //echo iconv("UTF-8","gb2312",$PreFileName);
    if($uploadInfo!=""){
        require_once "phpExcelReader/Excel/reader.php";
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('utf-8');
        $data->read($PreFileName);
        error_reporting(E_ALL ^ E_NOTICE);
        //echo $data->sheets[0]['cells'][2][3];
        // print_r($rs);

        $rs = $data -> getExcelResult();

        $RowNum=$data->sheets[0]['numRows'];
        $ColNum=$data->sheets[0]['numCols'];

        $x = 0;
        for($i=2;$i<=$RowNum;$i++){

            if($rs[$i][1]!=NULL || $rs[$i][1]!=""){
                //订单编号
                $OrdreId = trim($rs[$i][1]);
                //客户名称
                $CompanyName = trim($rs[$i][2]);
                //产品编号
                $ProductId = trim($rs[$i][3]);
                //产品名称
                $ProductName = trim($rs[$i][4]);
                //价格
                $Price = trim($rs[$i][5]);

                $updateSql="update $DataIn.yw1_ordersheet
                        set price = '$Price'
                    where POrderId = '$OrdreId' ";
                //echo $updateSql;

                $InRecode=@mysql_query($updateSql);
                if($InRecode){
                    $x++;
                }
            }
        }
        $Log.="$x 条记录更新yw1_ordersheet表";
    }
}
else{
    echo "加载EXCEL文件失败！";
}

//步骤4：
include "../model/logpage.php";
?>
