<?php
include "../model/modelhead.php";

$fromWebPage = "report_dely_add";
$Log_Item="导入报表数据";
$nowWebPage="report_dely_save";
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
$Log='';//日志

if(empty($_FILES['ExcelFile']['tmp_name'])) {
    //没有选取文件
    $Log .= "<div class=redB>请选择导入资料</div>";
    include "../model/logpage.php";
    die();
}

//读取导入的XLS文件，核对后写入数据表
$FilePath="phpExcelReader/expressfile/";//存储文件夹
if(!file_exists($FilePath)){
    dir_mkdir($FilePath);//若无，新建
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
    if($uploadInfo!=""){
        require_once "phpExcelReader/Excel/reader.php";
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('utf-8');
        $data->read($PreFileName);
        error_reporting(E_ALL ^ E_NOTICE);

        $rs = $data -> getExcelResult();
        
        $RowNum=$data->sheets[0]['numRows'];
        $ColNum=$data->sheets[0]['numCols'];
        
        $x = 0;
        $LogFlag = '';
        for($i=2;$i<=$RowNum;$i++){
            
            if($rs[$i][1]!=NULL || $rs[$i][1]!=""){

                //项目名称
                $TradeName = trim($rs[$i][1]);
                //构件类别
                $CmptType = trim($rs[$i][2]);
                //构件总层数
                $CmptFloors = trim($rs[$i][3]);
                //总方量（m³）
                $TotalCube = trim($rs[$i][4]);
                //完成方量
                $FinishedCube = trim($rs[$i][5]);
                //发货方量
                $DeliveredCube = trim($rs[$i][6]);
                //生产层数
                $BuildFloors = trim($rs[$i][7]);
                //发货层数
                $DeliveredFloors = trim($rs[$i][8]);
                //创建者
                $Ceator = $Operator;
                //创建时间
                $Created = $DateTime;
                //修改者
                $Modifier = $Operator;
                //最后修改时间
                $Modified = $DateTime;

                $InsertSql = "INSERT INTO $DataIn.rep_cube (TradeId, CmptTypeId, CmptType, CmptFloors, TotalCube, FinishedCube,
                DeliveredCube, BuildFloors, DeliveredFloors, creator, created, modifier, modified) 
                VALUE (
                (SELECT Id FROM $DataIn.trade_object WHERE Forshort = '$TradeName' LIMIT 1),
                (SELECT TypeId FROM $DataIn.producttype WHERE TypeName = '$CmptType' LIMIT 1),
                '$CmptType', $CmptFloors, $TotalCube, $FinishedCube, $DeliveredCube, $BuildFloors, $DeliveredFloors, 
                $Ceator, '$Created', $Modifier, '$Modified')";
//

                $InRecode=@mysql_query($InsertSql);
                if($InRecode){
                    $x++;
                }else{
                    $LogFlag .= "$i,";
                }
            }
        }
        $LogFlag = $LogFlag?"<div class=redB>第".(rtrim($LogFlag, ','))."条记录格式错误，请检查！</div>":'';
        $Log.="$x 条记录更新rep_cube表$LogFlag";
    }
}
else{
    $Log .= "<div class=redB>加载EXCEL文件失败！</div>";
}

//database log
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
