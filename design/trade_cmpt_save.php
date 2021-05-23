<?php

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
//步骤1
include "../model/modelhead.php";

//步骤2：
$type = $_POST["type"];

$fromWebPage = "trade_cmpt_add";
$Log_Item="构件数据";
$nowWebPage="trade_cmpt_save"; //$funFrom."_save"
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&type=$type";
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
$x1=0;
$x2=0;

set_time_limit(0);

@chmod("./dwgFiles/",0777);

if(empty($_FILES['ExcelFile']['tmp_name']) &&
        empty($_FILES['PordFile']['tmp_name']) &&
        empty($_FILES['MouldFile']['tmp_name']) &&
        empty($_FILES['SteelFile']['tmp_name']) &&
        empty($_FILES['EmbeddedFile']['tmp_name'])) {
    //没有选择构件资料
    $Log .= "<span style='color: red'>请选择导入资料</span><br>";
    include "../model/logpage.php";
    return;
    //echo "<script >alert('请选择构件资料(EXCEL)');</script></html>";
}

/*
if ($_POST["drawingChk"] || $_POST["steelChk"] || $_POST["embeddedChk"]) {
    if(empty($_FILES['ExcelFile']['tmp_name'])) {
        //没有选择构件资料
        echo "请选择构件资料(EXCEL)";
        include "../model/logpage.php";
        return;
    }
}
*/

$cmptNumber =  $_POST["cmptNumber"];
$buildId = $_POST["buildId"];
//取项目编号
$proId = $_POST["tradeChoose"];
if ($proId == null) {
    $Log .= "<span style='color: red'>请选择客户项目</span><br>";
    //echo "请选择客户项目";
    include "../model/logpage.php";
    return;
}
//类型
$TypeName = $_POST["TypeName"];
if ($TypeName == null) {
    $Log .= "<span style='color: red'>请选择构件类型</span><br>";
//    echo "请选择构件类型";
    include "../model/logpage.php";
    return;
}

$mySql="SELECT a.Id, a.Forshort, b.TradeNo FROM $DataIn.trade_object a
INNER JOIN $DataIn.trade_info b on a.Id = b.TradeId where a.Id = $proId";

$myResult = mysql_query($mySql, $link_id);
if($myResult  && $myRow = mysql_fetch_array($myResult)){
    $TradeNo = $myRow["TradeNo"]; 
} else {
    $Log .= "<span style='color: red'>项目资料出错</span><br>";
//    echo "项目资料出错";
    include "../model/logpage.php";
    return;
}
        
//步骤3：需处理
//读取导入的XLS文件，核对后写入数据表
$FilePath="phpExcelReader/excelfile/";
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
//         require_once "phpExcelReader/Excel/reader.php";
//         $data = new Spreadsheet_Excel_Reader();
//         $data->setOutputEncoding('utf-8');
//         $data->read($PreFileName);
       // require_once "PhpSpreadsheet/IOFactory.php";

        $filetype = ucfirst(strtolower($filetype));
        $reader = IOFactory::createReader($filetype);
        $spreadsheet = $reader->load($PreFileName);

        error_reporting(E_ALL ^ E_NOTICE);
        //echo $data->sheets[0]['cells'][2][3];
        // print_r($rs);

        $rs = $spreadsheet->getSheet(0)->toArray(null, true, true, false);

        //$rs = $data -> getExcelResult();
        $RowNum = $spreadsheet->getSheet(0)->getHighestRow();
        $ColNum = $spreadsheet->getSheet(0)->getHighestColumn();
        $ColNum = PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($ColNum);

        $tradeNoImport =trim($rs[3][2]);

        if ($TradeNo != $tradeNoImport) {
            $Log .= "<span style='color: red'>上传资料($tradeNoImport)和项目选择的项目编号($TradeNo)不一致</span><br>";
//                echo "上传图纸资料和项目选择的项目编号不一致";
            include "../model/logpage.php";
            return;
        }

        $typeName =trim($rs[3][4]);
        if ($TypeName != $typeName) {
            $Log .= "<span style='color: red'>上传资料($typeName)和项目选择的构件类型($TypeName)不一致</span><br>";
//                echo "上传图纸资料和项目选择的构件类型不一致";
            include "../model/logpage.php";
            return;
        }

        /* 楼栋编号 by.lwh 20180403 */
        $buildingNo =trim($rs[3][5]);
        if ($buildId != $buildingNo) {
            $Log .= "<span style='color: red'>上传资料($buildingNo)和项目填写的楼栋编号($buildId)不一致</span><br>";
//                echo "上传图纸资料和项目填写的楼栋编号不一致";
            include "../model/logpage.php";
            return;
        }

        $RowCount = 0;
        for($i=3;$i<$RowNum;$i++){
            if($rs[$i][1]!=NULL || $rs[$i][1]!=""){
                $RowCount = $RowCount + 1;
            }
        }
        if ($RowCount != $cmptNumber) {
            $Log .= "<span style='color: red'>构件数据($RowCount)和设定的数量($cmptNumber)不一致</span><br>";
            include "../model/logpage.php";
            return;
        }

        // 空洞  标题读取 16开始 读到 '洞口体积'
        $titles = array();
        for($i=15;$i<$ColNum;$i++){
            if($rs[2][$i]!=NULL || $rs[2][$i]!=""){

                if (strcmp($rs[2][$i], "洞口体积") == 0) {
                    break;
                } else {
                    $titles[] = urlencode($rs[2][$i]);
                }
            }
        }

        // 钢筋 规格 下料尺寸 15开始 读到 '洞口体积'
        $titlesSteel = array();
        $specsSteel = array();
        $sizesSteel = array();
        for($i=27 + count($titles);$i<$ColNum;$i++){
            if($rs[0][$i]!=NULL || $rs[0][$i]!=""){
                $titlesSteel[] = urlencode($rs[0][$i]);
                $specsSteel[] = urlencode($rs[1][$i]);
                $sizesSteel[] = urlencode($rs[2][$i]);
            } else {
                break;
            }
        }

        // 预埋
        $titlesEmbedded = array();
        $specsEmbedded = array();
        for($i=27 + count($titles) + count($titlesSteel);$i<$ColNum;$i++){
            if($rs[1][$i]!=NULL || $rs[1][$i]!=""){
                $titlesEmbedded[] = urlencode($rs[1][$i]);
                $specsEmbedded[] = urlencode($rs[2][$i]);
            }
        }

        //判断构件类型是否存在
        $TypeArr = array();
        for($i=3;$i<$RowNum;$i++){
            // for($j=1;$j<=$ColNum;$j++){

            if($rs[$i][1]!=NULL || $rs[$i][1]!=""){

                //构件类型
                $cmptType=trim($rs[$i][4]);

                $typeSql = mysql_query("SELECT TypeId FROM $DataIn.producttype where TypeName='$cmptType'",$link_id);
                $TypeId=@mysql_result($typeSql,0,"TypeId");

                if ($TypeId) {
                    $TypeArr[$cmptType] = $TypeId;
                } else {
                    $Log .= "<span style='color: red'>构件导入失败,构件类型(".$cmptType.")不存在</span><br>";
//                        echo "图纸导入失败,构件类型($cmptType)不存在";
                    include "../model/logpage.php";
                    return;
                }
            }
        }

        for($i=3;$i<$RowNum;$i++){
            // for($j=1;$j<=$ColNum;$j++){

            if($rs[$i][1]!=NULL || $rs[$i][1]!=""){

                //共通 字段
                //顺序号
                $SN = trim($rs[$i][1]);
                //项目编号
                //$tradeNo=trim($rs[$i][2]);
                //项目名称
                //$tradeName=trim($rs[$i][3]);
                //构件类型
                $cmptType=trim($rs[$i][4]);
                if ($cmptType === 0 || $cmptType === '') {
                    $Log .= "<span style='color: red'>构件类型不可为空或为0! 请查看第{$i}行相关数据。</span><br>";
//                        echo "构件类型不可为空或为0! 请查看第{$i}行相关数据。";
                    include "../model/logpage.php";
                    return;
                }

                //楼栋编号
                $bNo=trim($rs[$i][5]);
                if ($bNo === 0 || $bNo === '') {
                    $Log .= "<span style='color: red'>楼栋编号不可为空或为0! 请查看第{$i}行相关数据。</span><br>";
//                        echo "楼栋编号不可为空或为0! 请查看第{$i}行相关数据。";
                    include "../model/logpage.php";
                    return;
                }

                //楼层编号
                $floorNo=trim($rs[$i][6]);
                if ($floorNo === 0 || $floorNo === '') {
                    $Log .= "<span style='color: red'>楼层编号不可为空或为0! 请查看第{$i}行相关数据。</span><br>";
//                        echo "楼层编号不可为空或为0! 请查看第{$i}行相关数据。";
                    include "../model/logpage.php";
                    return;
                }

                //构件编号
                $cmptNo=trim($rs[$i][7]);
                if ($cmptNo === 0 || $cmptNo === '') {
                    $Log .= "<span style='color: red'>构件编号不可为空或为0! 请查看第{$i}行相关数据。</span><br>";
//                        echo "构件编号不可为空或为0! 请查看第{$i}行相关数据。";
                    include "../model/logpage.php";
                    return;
                }

                //产品条码
                $prodCode=trim($rs[$i][8]);
                //审核状态
                $eState=trim($rs[$i][9]);

                //长
                $len=trim($rs[$i][10]);
                if ($len === 0 || $len === "") {
                    $Log .= "<span style='color: red'>混凝土长度不可为空或为0! 请查看第{$i}行相关数据。</span><br>";
//                        echo "混凝土长度不可为空或为0! 请查看第{$i}行相关数据。";
                    include "../model/logpage.php";
                    return;
                }
                //宽
                $wdh=trim($rs[$i][11]);
                if ($wdh === 0 || $wdh === "") {
                    $Log .= "<span style='color: red'>混凝土宽度不可为空或为0! 请查看第{$i}行相关数据。</span><br>";
//                        echo "混凝土宽度不可为空或为0! 请查看第{$i}行相关数据。";
                    include "../model/logpage.php";
                    return;
                }
                //厚
                $thick=trim($rs[$i][12]);
                if ($thick === 0 || $thick === "") {
                    $Log .= "<span style='color: red'>混凝土厚度不可为空或为0! 请查看第{$i}行相关数据。</span><br>";
//                        echo "混凝土厚度不可为空或为0! 请查看第{$i}行相关数据。";
                    include "../model/logpage.php";
                    return;
                }

                //////////////////////////
                //图纸相关

                //模具编号
                $mouldNo=trim($rs[$i][13]);
                if ($mouldNo === 0 || $mouldNo === "") {
                    $Log .= "<span style='color: red'>模具编号不可为空或为0! 请查看第{".$i."}行相关数据。</span><br>";
//                        echo "模具编号不可为空或为0! 请查看第{$i}行相关数据。";
                    include "../model/logpage.php";
                    return;
                }
                //混凝土强度
                $cStr=trim($rs[$i][14]);
                if ($cStr === 0 || $cStr === "") {
                    $Log .= "<span style='color: red'>混凝土强度不可为空或为0! 请查看第{$i}行相关数据。</span><br>";
//                        echo "混凝土强度不可为空或为0! 请查看第{$i}行相关数据。";
                    include "../model/logpage.php";
                    return;
                }
                //空洞
                $sizes = array();
                for($j=0;$j<count($titles);$j++){
                    $sizes[] = trim($rs[$i][15 + $j]);
                }

                //洞口体积
                $HoleVol=trim($rs[$i][15 + count($titles)]);
                $HoleVol=$HoleVol==''?0:$HoleVol;
                $HoleVol=$HoleVol==''?0:$HoleVol;
                //面积
                $Area=trim($rs[$i][16 + count($titles)]);
                $Area=$Area==''?0:$Area;
                $Area=$Area==''?0:$Area;
                //图纸体积
                $DwgVol=trim($rs[$i][17 + count($titles)]);
                $DwgVol=$DwgVol==''?0:$DwgVol;
                $DwgVol=$DwgVol==''?0:$DwgVol;
                //混凝土体积
                $CVol=trim($rs[$i][18 + count($titles)]);
                if ($CVol === 0 || $CVol === "") {
                    $Log .= "<span style='color: red'>混凝土体积不可为空或为0! 请查看第{$i}行相关数据。</span><br>";
//                        echo "混凝土体积不可为空或为0! 请查看第{$i}行相关数据。";
                    include "../model/logpage.php";
                    return;
                }
                //重量
                $Weight=trim($rs[$i][19 + count($titles)]);
                if ($Weight === 0 || $Weight === "") {
                    $Log .= "<span style='color: red'>重量不可为空或为0! 请查看第{$i}行相关数据。</span><br>";
//                        echo "重量不可为空或为0! 请查看第{$i}行相关数据。";
                    include "../model/logpage.php";
                    return;
                }
                //构件成品图纸
                $EndDwg=trim($rs[$i][20 + count($titles)]);
                if ($EndDwg === 0 || $EndDwg === "") {
                    $Log .= "<span style='color: red'>构件成品图纸不可为空或为0! 请查看第{$i}行相关数据。</span><br>";
//                        echo "构件成品图纸不可为空或为0! 请查看第{$i}行相关数据。";
                    include "../model/logpage.php";
                    return;
                }
                //构件预埋图纸
                $EmbeddedDwg=trim($rs[$i][21 + count($titles)]);
                if ($EmbeddedDwg === 0 || $EmbeddedDwg === "") {
                    $Log .= "<span style='color: red'>构件预埋图纸不可为空或为0! 请查看第{$i}行相关数据。</span><br>";
//                        echo "构件预埋图纸不可为空或为0! 请查看第{$i}行相关数据。";
                    include "../model/logpage.php";
                    return;
                }
                //构件钢筋图纸
                $SteelDwg=trim($rs[$i][22 + count($titles)]);
                if ($SteelDwg === 0 || $SteelDwg === "") {
                    $Log .= "<span style='color: red'>构件钢筋图纸不可为空或为0! 请查看第{$i}行相关数据。</span><br>";
//                        echo "构件钢筋图纸不可为空或为0! 请查看第{$i}行相关数据。";
                    include "../model/logpage.php";
                    return;
                }
                //模具图纸
                $DieDwg=trim($rs[$i][23 + count($titles)]);
                if ($DieDwg === 0 || $DieDwg === "") {
                    $Log .= "<span style='color: red'>模具图纸不可为空或为0! 请查看第{$i}行相关数据。</span><br>";
//                        echo "模具图纸不可为空或为0! 请查看第{$i}行相关数据。";
                    include "../model/logpage.php";
                    return;
                }
                //导入日期
                //修改日期

                //洞口标题
                $titlesjson = urldecode (json_encode($titles));
                //echo $TitleSTR,"<br/>";
                $mySql="SELECT Id FROM $DataIn.trade_drawing_hole WHERE TradeId=$proId ";
                $myResult=mysql_query($mySql,$link_id);
                if($myRow=mysql_fetch_array($myResult)){
                    $Id=$myRow["Id"];
                    $InSql="UPDATE $DataIn.trade_drawing_hole set Titles = '$titlesjson' where Id = $Id ";
                    $InRecode=@mysql_query($InSql);
                    $dwgRes = "update";
                }
                else{
                    $InSql="INSERT INTO $DataIn.trade_drawing_hole(TradeId, Titles)VALUES($proId,'$titlesjson')";
                    $InRecode=@mysql_query($InSql);
                    $dwgRes = "insert";
                }

                $sizeSTR = json_encode($sizes);

                $drawingSql="SELECT Id FROM $DataIn.trade_drawing WHERE SN = $SN AND 
                            TradeId = $proId AND 
                            CmptTypeId = $TypeArr[$cmptType] AND
                            CmptType = '$cmptType' AND
                            BuildingNo = '$bNo' AND
                            FloorNo = '$floorNo' AND
                            CmptNo = '$cmptNo' AND
                            MouldNo = '$mouldNo' ";
                $drawingResult=mysql_query($drawingSql,$link_id);
                if($drawingRow=mysql_fetch_array($drawingResult)){
                    $Id=$drawingRow["Id"];
                    $InSql="UPDATE $DataIn.trade_drawing set SN='$SN',
                            TradeId='$proId',
                            CmptTypeId='$TypeArr[$cmptType]',
                            CmptType='$cmptType',
                            BuildingNo='$bNo',
                            FloorNo='$floorNo',
                            CmptNo='$cmptNo',
                            ProdCode='$prodCode',
                            MouldNo='$mouldNo',
                            CStr='$cStr',
                            Length='$len',
                            Width='$wdh',
                            Thick='$thick',
                            Sizes='$sizeSTR',
                            HoleVol='$HoleVol',
                            Area='$Area',
                            DwgVol='$DwgVol',
                            CVol='$CVol',
                            Weight='$Weight',
                            EndDwg='$EndDwg',
                            EmbeddedDwg='$EmbeddedDwg',
                            SteelDwg='$SteelDwg',
                            DieDwg='$DieDwg',
                            Operator='$Operator',
                            creator='$Operator',
                            created='$DateTime' where Id = $Id ";
                    $InRecode=@mysql_query($InSql);
                    $dwgRes = "update";
                }
                else{
                    $InSql="INSERT INTO $DataIn.trade_drawing(SN,
                            TradeId,
                            CmptTypeId,
                            CmptType,
                            BuildingNo,
                            FloorNo,
                            CmptNo,
                            ProdCode,
                            MouldNo,
                            CStr,
                            Length,
                            Width,
                            Thick,
                            Sizes,
                            HoleVol,
                            Area,
                            DwgVol,
                            CVol,
                            Weight,
                            EndDwg,
                            EmbeddedDwg,
                            SteelDwg,
                            DieDwg,
                            Operator,
                            creator,
                            created)VALUES($SN,$proId,$TypeArr[$cmptType],
                            '$cmptType',
                            '$bNo',
                            '$floorNo',
                            '$cmptNo',
                            '$prodCode',
                            '$mouldNo',
                            '$cStr',
                            '$len',
                            '$wdh',
                            '$thick',
                            '$sizeSTR',
                            '$HoleVol',
                            '$Area',
                            '$DwgVol',
                            '$CVol',
                            '$Weight',
                            '$EndDwg',
                            '$EmbeddedDwg',
                            '$SteelDwg',
                            '$DieDwg',
                            '$Operator',
                            '$Operator',
                            '$DateTime')";
                    $InRecode=@mysql_query($InSql);
                    $dwgRes = "insert";
                }

                /* INSERT 存在跳过，不存在插入 by.hxl 20180404 */
//                $InSql = "INSERT INTO $DataIn.trade_drawing(SN,
//                            TradeId,
//                            CmptTypeId,
//                            CmptType,
//                            BuildingNo,
//                            FloorNo,
//                            CmptNo,
//                            ProdCode,
//                            MouldNo,
//                            CStr,
//                            Length,
//                            Width,
//                            Thick,
//                            Sizes,
//                            HoleVol,
//                            Area,
//                            DwgVol,
//                            CVol,
//                            Weight,
//                            EndDwg,
//                            EmbeddedDwg,
//                            SteelDwg,
//                            DieDwg,
//                            Operator,
//                            creator,
//                            created
//                    )SELECT $SN,$proId,$TypeArr[$cmptType],
//                            '$cmptType',
//                            '$bNo',
//                            '$floorNo',
//                            '$cmptNo',
//                            '$prodCode',
//                            '$mouldNo',
//                            '$cStr',
//                            '$len',
//                            '$wdh',
//                            '$thick',
//                            '$sizeSTR',
//                            '$HoleVol',
//                            '$Area',
//                            '$DwgVol',
//                            '$CVol',
//                            '$Weight',
//                            '$EndDwg',
//                            '$EmbeddedDwg',
//                            '$SteelDwg',
//                            '$DieDwg',
//                            '$Operator',
//                            '$Operator',
//                            '$DateTime' FROM DUAL WHERE NOT EXISTS(SELECT SN,
//                            TradeId,
//                            CmptTypeId,
//                            CmptType,
//                            BuildingNo,
//                            FloorNo,
//                            CmptNo,
//                            MouldNo FROM $DataIn.trade_drawing WHERE SN = $SN AND
//                            TradeId = $proId AND
//                            CmptTypeId = $TypeArr[$cmptType] AND
//                            CmptType = '$cmptType' AND
//                            BuildingNo = '$bNo' AND
//                            FloorNo = '$floorNo' AND
//                            CmptNo = '$cmptNo' AND
//                            MouldNo = '$mouldNo' )";
//                $InRecode=@mysql_query($InSql);

                if($InRecode){ $x++; }


                //////////////////////////
                //图纸相关

                //数量
                $quantities = array();
                for($j=0;$j<count($titlesSteel);$j++){
                    $quantities[] = trim($rs[$i][27 + count($titles) + $j]);
                }

                //标题
                $titlesJson = urldecode (json_encode($titlesSteel));
                $specsJson = urldecode (json_encode($specsSteel));
                $sizesJson = urldecode (json_encode($sizesSteel));
                //echo $TitleSTR,"<br/>";
                $mySql="SELECT Id FROM $DataIn.trade_steel_data WHERE TradeId=$proId AND BuildingNo = '$buildId' AND TypeName = '$typeName'";

                $myResult=mysql_query($mySql,$link_id);
                if(@$myRow=mysql_fetch_array(@$myResult)){
                    $Id=$myRow["Id"];
                    $InSql="UPDATE $DataIn.trade_steel_data set Titles='$titlesJson',Specs='$specsJson',Sizes='$sizesJson',TypeName='$typeName' where Id = $Id ";
                    $InRecode=@mysql_query($InSql);
                    $steelRes = "update";
                }
                else{
                    $InSql="INSERT INTO $DataIn.trade_steel_data(TradeId, Titles, Specs, Sizes, BuildingNo,TypeName)VALUES($proId,'$titlesJson','$specsJson','$sizesJson','$buildId','$typeName')";
                    $InRecode=@mysql_query($InSql);
                    $steelRes = "insert";
                }

                $quantitiesJson = json_encode($quantities);

                $steelSql="SELECT Id FROM $DataIn.trade_steel WHERE SN = $SN AND CmptTypeId = $TypeArr[$cmptType] AND
                    TradeId = $proId AND 
                    CmptType = '$cmptType' AND
                    BuildingNo = '$bNo' AND
                    FloorNo = '$floorNo' AND
                    CmptNo = '$cmptNo'";

                $steelResult=mysql_query($steelSql,$link_id);
                if(@$steelRow=mysql_fetch_array(@$steelResult)){
                    $Id=$steelRow["Id"];
                    $InSql="UPDATE $DataIn.trade_steel set SN='$SN',TradeId='$proId',CmptTypeId='$TypeArr[$cmptType]',
                    CmptType='$cmptType',
                    BuildingNo='$bNo',
                    FloorNo='$floorNo',
                    CmptNo='$cmptNo',
                    ProdCode='$prodCode',
                    Length='$len',
                    Width='$wdh',
                    Thick='$thick',
                    Quantities='$quantitiesJson',
                    Operator='$Operator',
                    creator='$Operator',
                    created='$DateTime' where Id = $Id ";
                    $InRecode=@mysql_query($InSql);
                    $steelRes = "update";
                }
                else{
                    $InSql="INSERT INTO $DataIn.trade_steel(SN,TradeId,CmptTypeId,
                    CmptType,
                    BuildingNo,
                    FloorNo,
                    CmptNo,
                    ProdCode,
                    Length,
                    Width,
                    Thick,
                    Quantities,
                    Operator,
                    creator,
                    created)VALUES($SN,$proId,$TypeArr[$cmptType],
                    '$cmptType',
                    '$bNo',
                    '$floorNo',
                    '$cmptNo',
                    '$prodCode',
                    $len,
                    $wdh,
                    $thick,
                    '$quantitiesJson',
                    '$Operator',
                    '$Operator',
                    '$DateTime')";
                    $InRecode=@mysql_query($InSql);
                    $steelRes = "insert";
                }

                /* INSERT 存在跳过，不存在插入 by.hxl 20180404 */
//                $InSql = "INSERT INTO $DataIn.trade_steel(SN,TradeId,CmptTypeId,
//                    CmptType,
//                    BuildingNo,
//                    FloorNo,
//                    CmptNo,
//                    ProdCode,
//                    Length,
//                    Width,
//                    Thick,
//                    Quantities,
//                    Operator,
//                    creator,
//                    created
//                    )SELECT $SN,$proId,$TypeArr[$cmptType],
//                    '$cmptType',
//                    '$bNo',
//                    '$floorNo',
//                    '$cmptNo',
//                    '$prodCode',
//                    $len,
//                    $wdh,
//                    $thick,
//                    '$quantitiesJson',
//                    '$Operator',
//                    '$Operator',
//                    '$DateTime' FROM DUAL WHERE NOT EXISTS(SELECT SN,CmptTypeId,
//                    TradeId,
//                    CmptType,
//                    BuildingNo,
//                    FloorNo,
//                    CmptNo FROM $DataIn.trade_steel WHERE SN = $SN AND CmptTypeId = $TypeArr[$cmptType] AND
//                    TradeId = $proId AND
//                    CmptType = '$cmptType' AND
//                    BuildingNo = '$bNo' AND
//                    FloorNo = '$floorNo' AND
//                    CmptNo = '$cmptNo' )";
//                $InRecode=@mysql_query($InSql);
                if($InRecode){ $x1++; }

                ///////////////////////////
                //预埋件

                //数量
                $quantities = array();
                for($j=0;$j<count($titlesEmbedded);$j++){
                    $quantities[] = trim($rs[$i][27 + count($titles) + count($titlesSteel) + $j]);
                }

                //标题
                $titlesJson = urldecode (json_encode($titlesEmbedded));
                $specsJson = urldecode (json_encode($specsEmbedded));
                //echo $TitleSTR,"<br/>";
                $mySql="SELECT Id FROM $DataIn.trade_embedded_data WHERE TradeId=$proId AND BuildingNo = '$buildId' AND TypeName = '$typeName'";
                $myResult=mysql_query($mySql,$link_id);
                if(@$myRow=mysql_fetch_array(@$myResult)){
                    $Id=$myRow["Id"];
                    $InSql="UPDATE $DataIn.trade_embedded_data set Titles='$titlesJson',Specs='$specsJson',TypeName='$typeName' where Id = $Id ";
                    $InRecode=@mysql_query($InSql);
                    $embeddedres = "update";
                }
                else{
                    $InSql="INSERT INTO $DataIn.trade_embedded_data(TradeId, Titles, Specs, BuildingNo,TypeName)VALUES($proId,'$titlesJson','$specsJson','$buildId','$typeName')";
                    $InRecode=@mysql_query($InSql);
                    $embeddedres = "insert";
                }

                $quantitiesJson = json_encode($quantities);

                $embeddedSql = "SELECT Id FROM $DataIn.trade_embedded WHERE SN = $SN AND CmptTypeId = $TypeArr[$cmptType] AND
                TradeId = $proId AND
                CmptType = '$cmptType' AND
                BuildingNo = '$bNo' AND
                FloorNo = '$floorNo' AND
                CmptNo = '$cmptNo' ";

                $embeddedResult=mysql_query($embeddedSql,$link_id);
                if(@$embeddedRow=mysql_fetch_array(@$embeddedResult)){
                    $Id=$embeddedRow["Id"];
                    $InSql="UPDATE $DataIn.trade_embedded set SN='$SN',TradeId='$proId',CmptTypeId='$TypeArr[$cmptType]',
                    CmptType='$cmptType',
                    BuildingNo='$bNo',
                    FloorNo='$floorNo',
                    CmptNo='$cmptNo',
                    ProdCode='$prodCode',
                    Length='$len',
                    Width='$wdh',
                    Thick='$thick',
                    Quantities='$quantitiesJson',
                    Operator='$Operator',
                    creator='$Operator',
                    created='$DateTime' where Id = $Id ";
                    $InRecode=@mysql_query($InSql);
                    $embeddedres = "update";
                }
                else{
                    $InSql="INSERT INTO $DataIn.trade_embedded(SN,TradeId,CmptTypeId,
                    CmptType,
                    BuildingNo,
                    FloorNo,
                    CmptNo,
                    ProdCode,
                    Length,
                    Width,
                    Thick,
                    Quantities,
                    Operator,
                    creator,
                    created)VALUES($SN,$proId,$TypeArr[$cmptType],
                    '$cmptType',
                    '$bNo',
                    '$floorNo',
                    '$cmptNo',
                    '$prodCode',
                    $len,
                    $wdh,
                    $thick,
                    '$quantitiesJson',
                    '$Operator',
                    '$Operator',
                    '$DateTime')";
                    $InRecode=@mysql_query($InSql);
                    $embeddedres = "insert";
                }



//                $InSql = "INSERT INTO $DataIn.trade_embedded(SN,TradeId,CmptTypeId,
//                    CmptType,
//                    BuildingNo,
//                    FloorNo,
//                    CmptNo,
//                    ProdCode,
//                    Length,
//                    Width,
//                    Thick,
//                    Quantities,
//                    Operator,
//                    creator,
//                    created
//                    )SELECT $SN,$proId,$TypeArr[$cmptType],
//                    '$cmptType',
//                    '$bNo',
//                    '$floorNo',
//                    '$cmptNo',
//                    '$prodCode',
//                    $len,
//                    $wdh,
//                    $thick,
//                    '$quantitiesJson',
//                    '$Operator',
//                    '$Operator',
//                    '$DateTime' FROM DUAL WHERE NOT EXISTS(SELECT SN,CmptTypeId,
//                    TradeId,
//                    CmptType,
//                    BuildingNo,
//                    FloorNo,
//                    CmptNo FROM $DataIn.trade_embedded WHERE SN = $SN AND CmptTypeId = $TypeArr[$cmptType] AND
//                    TradeId = $proId AND
//                    CmptType = '$cmptType' AND
//                    BuildingNo = '$bNo' AND
//                    FloorNo = '$floorNo' AND
//                    CmptNo = '$cmptNo' )";
//                $InRecode=@mysql_query($InSql);
                if($InRecode){ $x2++; }

            }
        }

        if ($dwgRes == "update"){
            $Log.="trade_drawing_hole表中图纸数据更新成功";
            $Log.="<br/>";
        }elseif ($dwgRes == "insert"){
            $Log.="trade_drawing_hole表中图纸数据添加成功";
            $Log.="<br/>";
        }
        $Log.="$x 条记录插入trade_drawing表中";
        $Log.="<br/>";

        if ($steelRes == "update"){
            $Log.="trade_steel_data表中钢筋数据更新成功";
            $Log.="<br/>";
        }elseif ($steelRes == "insert"){
            $Log.="trade_steel_data表中钢筋数据添加成功";
            $Log.="<br/>";
        }
        $Log.="$x1 条记录插入trade_steel表中";
        $Log.="<br/>";

        if ($embeddedres == "update"){
            $Log.="trade_embedded_data表中预埋件数据更新成功";
            $Log.="<br/>";
        }elseif ($embeddedres == "insert"){
            $Log.="trade_embedded_data表中预埋件数据添加成功";
            $Log.="<br/>";
        }
        $Log.="$x2 条记录插入trade_embedded表中";
        $Log.="<br/>";

    }
}
else{
    //echo "加载EXCEL文件失败！";
}


$ChooseName = iconv("GB2312","UTF-8//IGNORE",$tmpFile);

//成品图纸
$FilePath="./dwgFiles/$proId/Pord/";
if(!file_exists($FilePath)){
    dir_mkdir($FilePath);
}
$i = 0;
$tmpFile = $_FILES['PordFile']['tmp_name'][$i];
if ($tmpFile != "") {
//原有文件备份
$files = read_all($FilePath);
if (count($files) > 0) {

    $FilePath_back = "./dwgFiles/$proId/Pord_bak/" . date("YmdHis") . "/";
    if (!file_exists($FilePath_back)) {
        dir_mkdir($FilePath_back);
    }
    foreach ($files as $file) {
        dir_mkdir($file, $FilePath_back . basename($file));
    }
}
foreach ($_FILES["PordFile"]["name"] as $imgname ) {
//chmod($FilePath,0777);
    $tmpFile = $_FILES['PordFile']['tmp_name'][$i];
    $ss=$imgname;
        //获取上传的文件名称
        //$imgname = $_FILES["PordFile"]["name"];
        //$imgname = iconv('GB2312', 'UTF-8//IGNORE', $imgname);

        $PreFileName = $FilePath . $imgname;
        $uploadInfo = move_uploaded_file($tmpFile, $PreFileName);


        chmod($PreFileName, 0777);

        //echo $PreFileName . "<br>";
        //echo iconv("UTF-8","gb2312",$PreFileName);
        if ($uploadInfo == "") {
            $Log .= $ss."/-/".$imgname."成品图纸上传失败";
        } else {

            if (preg_match('/\.zip$/is', $imgname)) {
                //zip
                $zip = new ZipArchive();
                $res = $zip->open($PreFileName);
                if ($res === TRUE) {
                    //解压缩到当前文件夹
                    $res = $zip->extractTo($FilePath);
                    //echo $res;
                    $zip->close();

                    @unlink($PreFileName);
                    $Log .= "成品图纸上传成功";
                } else {
                    $Log .= "成品图纸解压失败" . $res;
                    //echo 'failed, code:' . $res;
                }
            } else {
                $Log .= $ss."/-/".$imgname."成品图纸上传成功";
            }
        }

        $Log .= "<br/>";
    $i++;
    }

}
unset($imgname);
//模具图纸
$FilePath="./dwgFiles/$proId/Mould/";
if(!file_exists($FilePath)){
    dir_mkdir($FilePath);
}
$i = 0;
$tmpFile = $_FILES['MouldFile']['tmp_name'][$i];
if ($tmpFile != "") {
//原有文件备份
    $files = read_all($FilePath);
    if (count($files) > 0) {

        $FilePath_back = "./dwgFiles/$proId/Mould_bak/" . date("YmdHis") . "/";
        if (!file_exists($FilePath_back)) {
            dir_mkdir($FilePath_back);
        }
        foreach ($files as $file) {
            rename($file, $FilePath_back . basename($file));
        }
    }



    foreach ($_FILES["MouldFile"]["name"] as $imgname) {
//chmod($FilePath,0777);
        $tmpFile = $_FILES['MouldFile']['tmp_name'][$i];



            //$imgname = $_FILES["MouldFile"]["name"]; //获取上传的文件名称
            //$imgname = iconv('GB2312', 'UTF-8//IGNORE', $imgname);

            $PreFileName = $FilePath . $imgname;
            $uploadInfo = move_uploaded_file($tmpFile, $PreFileName);


            chmod($PreFileName, 0777);
            //echo iconv("UTF-8","gb2312",$PreFileName);
            if ($uploadInfo == "") {
                $Log .= $imgname."模具图纸上传失败";
            } else {
                if (preg_match('/\.zip$/is', $imgname)) {
                    //zip
                    $zip = new ZipArchive();
                    $res = $zip->open($PreFileName);
                    if ($res === TRUE) {
                        //解压缩到当前文件夹
                        $res = $zip->extractTo($FilePath);
                        //echo $res;
                        $zip->close();

                        @unlink($PreFileName);
                        $Log .= "模具图纸上传成功";
                    } else {
                        $Log .= "模具图纸解压失败" . $res;
                        //echo 'failed, code:' . $res;
                    }
                } else {
                    $Log .= $imgname."模具图纸上传成功";
                }
            }
            $Log .= "<br/>";
        $i++;
        }

    }

unset($imgname);

//钢筋图纸
$FilePath="./dwgFiles/$proId/Steel/";
if(!file_exists($FilePath)){
    dir_mkdir($FilePath);
}
$i = 0;
$tmpFile = $_FILES['SteelFile']['tmp_name'][$i];
if ($tmpFile != "") {
    //原有文件备份
    $files = read_all($FilePath);
    if (count($files) > 0) {

        $FilePath_back = "./dwgFiles/$proId/Steel_bak/" . date("YmdHis") . "/";
        if (!file_exists($FilePath_back)) {
            dir_mkdir($FilePath_back);
        }
        foreach ($files as $file) {
            rename($file, $FilePath_back . basename($file));
        }
    }
foreach ($_FILES["SteelFile"]["name"] as $imgname ) {
//chmod($FilePath,0777);
    $tmpFile = $_FILES['SteelFile']['tmp_name'][$i];



        //$imgname = $_FILES["SteelFile"]["name"]; //获取上传的文件名称
        //$imgname = iconv('GB2312', 'UTF-8//IGNORE', $imgname);

        $PreFileName = $FilePath . $imgname;
        $uploadInfo = move_uploaded_file($tmpFile, $PreFileName);


        chmod($PreFileName, 0777);
        //echo iconv("UTF-8","gb2312",$PreFileName);
        if ($uploadInfo == "") {
            $Log .= $imgname."钢筋图纸上传失败";
        } else {
            if (preg_match('/\.zip$/is', $imgname)) {
                //zip
                $zip = new ZipArchive();
                $res = $zip->open($PreFileName);
                if ($res === TRUE) {
                    //解压缩到当前文件夹
                    $res = $zip->extractTo($FilePath);
                    //echo $res;
                    $zip->close();

                    @unlink($PreFileName);
                    $Log .= "钢筋图纸上传成功";
                } else {
                    $Log .= "钢筋图纸解压失败" . $res;
                    //echo 'failed, code:' . $res;
                }
            } else {
                $Log .= $imgname."钢筋图纸上传成功";
            }
        }
        $Log .= "<br/>";
    $i++;
    }

}
unset($imgname);


//预埋件图纸
$FilePath="./dwgFiles/$proId/Embedded/";
if(!file_exists($FilePath)){
    dir_mkdir($FilePath);
}
$i = 0;
$tmpFile = $_FILES['EmbeddedFile']['tmp_name'][$i];
if ($tmpFile != "") {
    //原有文件备份
    $files = read_all($FilePath);
    if (count($files) > 0) {

        $FilePath_back = "./dwgFiles/$proId/Embedded_bak/" . date("YmdHis") . "/";
        if (!file_exists($FilePath_back)) {
            dir_mkdir($FilePath_back);
        }
        foreach ($files as $file) {
            rename($file, $FilePath_back . basename($file));
        }
    }
foreach ($_FILES["EmbeddedFile"]["name"] as $imgname ) {
//chmod($FilePath,0777);
    $tmpFile = $_FILES['EmbeddedFile']['tmp_name'][$i];



        //$imgname = $_FILES["EmbeddedFile"]["name"]; //获取上传的文件名称
        //$imgname = iconv('GB2312', 'UTF-8//IGNORE', $imgname);

        $PreFileName = $FilePath . $imgname;
        $uploadInfo = move_uploaded_file($tmpFile, $PreFileName);


        chmod($PreFileName, 0777);
        //echo iconv("UTF-8","gb2312",$PreFileName);
        if ($uploadInfo == "") {
            $Log .= $imgname."预埋件图纸上传失败";
        } else {
            if (preg_match('/\.zip$/is', $imgname)) {
                //zip
                $zip = new ZipArchive();
                $res = $zip->open($PreFileName);
                if ($res === TRUE) {
                    //解压缩到当前文件夹
                    $res = $zip->extractTo($FilePath);
                    //echo $res;
                    $zip->close();

                    @unlink($PreFileName);
                    $Log .= "预埋件图纸上传成功";
                } else {
                    $Log .= "预埋件图纸解压失败" . $res;
                    //echo 'failed, code:' . $res;
                }
            } else {
                $Log .= $imgname."预埋件图纸上传成功";
            }
        }
    $Log .= "<br/>";
    $i++;
    }

}
unset($imgname);


//读目录下文件 不包含子目录
function read_all ($dir){
    $rt = array ();
    
    if(!is_dir($dir)) return $rt;
    
    $tmp = scandir ( $dir );
    
    foreach ( $tmp as $f ) {
        // 过滤. ..
        if ($f == '.' || $f == '..')
            continue;
            
            $path = $dir . $f;
            if (is_file ( $path )) { // 如果是文件，放入容器中
                $rt [] = $path;
            }
    }
    return $rt;
}

//步骤4：
include "../model/logpage.php";
?>
