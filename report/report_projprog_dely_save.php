<?php
include "../model/modelhead.php";

$fromWebPage = "report_projprog_dely_add";
$Log_Item="项目进度报表";
$nowWebPage="report_projprog_dely_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="导入";
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
		$RtFlag = '';
        for($i=2;$i<=$RowNum;$i++){
            //项目名称为必填，已此为判断是否有数据
            if($rs[$i][2]!=NULL || $rs[$i][2]!=""){
                //项目编号
                $TradeNo = trim($rs[$i][1]);
                //项目名称
                $TradeName = trim($rs[$i][2]);
                //构件类别
                $CmptType = trim($rs[$i][3]);
                //吊装总层数
                $HoistFloors = trim($rs[$i][4]);
				//施工楼层
				$WorkFloor = trim($rs[$i][5]);
                //总方量（m³）
                $TotalCube = trim($rs[$i][6]);
				//构件总数量
				$CmptTotal = trim($rs[$i][7]);
                //生产完成方量（m³）
                $FinishedCube = trim($rs[$i][8]);
                //发货方量（m³）
                $DeliveredCube = trim($rs[$i][9]);
                //首批构件供货时间
                $FCmptDTime = trim($rs[$i][10]);
                //构件生产结束时间
                $CmptETime = trim($rs[$i][11]);
                //跟新时间
                $UpdatedTime = trim($rs[$i][12]);
                //创建者
                $Creator = $Operator;
                //创建时间
                $Created = $DateTime;


                $InsertSql = "INSERT INTO $DataIn.rp_projectprogress 
				(TradeNo, TradeName, CmptType, HoistFloors, WorkFloor, TotalCube, CmptTotal, FinishedCube,
                DeliveredCube, FCmptDTime, CmptETime, UpdatedTime, creator, created) 
                select
                '$TradeNo', '$TradeName','$CmptType', '$HoistFloors', '$WorkFloor', $TotalCube, '$CmptTotal', $FinishedCube,
                 $DeliveredCube, '$FCmptDTime', '$CmptETime', '$UpdatedTime', $Creator, '$Created'
				from dual
				where not exists(
					select * from 
					$DataIn.rp_projectprogress
					where
					TradeNo = '$TradeNo' 
					and 
					TradeName = '$TradeName' 
					and 
					CmptType = '$CmptType' 
					and 
					HoistFloors = '$HoistFloors' 
					and 
					WorkFloor = '$WorkFloor' 
					and 
					TotalCube = '$TotalCube' 
					and 
					CmptTotal= '$CmptTotal' 
					and 
					FinishedCube = '$FinishedCube' 
					and 
					DeliveredCube = '$DeliveredCube' 
					and 
					FCmptDTime = '$FCmptDTime' 
					and 
					CmptETime = '$CmptETime' 
					and 
					UpdatedTime = '$UpdatedTime' 
				)";

                $InRecode=@mysql_query($InsertSql);
                if($InRecode){
                    if(mysql_affected_rows()){
						$x++;
					}else{
						$RtFlag .= "$i,";		
					}
                }else{
                    $LogFlag .= "$i,";
                }
            }
        }
		$RtFlag = $RtFlag?'<div class=redB>第'.(rtrim($RtFlag, ',')).'条为重复数据；':'';
        $LogFlag = $LogFlag?"<div class=redB>第".(rtrim($LogFlag, ','))."条记录格式错误，请检查！</div>":'';
        $LogFlag = $RtFlag.$LogFlag;
		$Log.="$x 条记录更新rp_projectprogress表$LogFlag";
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
