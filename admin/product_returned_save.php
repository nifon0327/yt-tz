<?php   
//步骤1 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
$Log_Item="退货记录";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
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
//步骤3：需处理
//读取导入的XLS文件，核对后写入数据表
$FilePath="phpExcelReader/excelfile/";
     if(!file_exists($FilePath)){ 
         makedir($FilePath);
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
            $data->setOutputEncoding('gbk');
            $data->read($PreFileName);
			error_reporting(E_ALL ^ E_NOTICE);
			$rs = $data -> getExcelResult();
			//echo $data->sheets[0]['cells'][2][3];
           // print_r($rs);
			$RowNum=$data->sheets[0]['numRows'];
			$ColNum=$data->sheets[0]['numCols'];
			        for($i=2;$i<=$RowNum;$i++){
					   // for($j=1;$j<=$ColNum;$j++){
						  if($rs[$i][1]!=NULL || $rs[$i][1]!=""){
						     //echo $rs[$i][$j];
							 $eCode=trim($rs[$i][3]);
							 $Qty=trim($rs[$i][4]);
							 $Price=trim($rs[$i][6]);
							 $mySql="SELECT ProductId,CompanyId FROM $DataIn.productdata WHERE eCode='$eCode' ORDER BY cName ASC ";  
							 $myResult=mysql_query($mySql,$link_id);
							 if($myRow=mysql_fetch_array($myResult)){
							      $ProductId=$myRow["ProductId"];
								  $CompanyId=$myRow["CompanyId"];
							      }
							  else{
							      $ProductId=0;$CompanyId=0;
							      }
							 $InSql="INSERT INTO $DataIn.product_returned(Id, CompanyId, ProductId, ReturnMonth, eCode, Qty, Price, Date, Estate, Locks, Operator)VALUES(NULL,'$CompanyId','$ProductId','$Month','$eCode','$Qty','$Price','$Date','1','0','$Operator')";
							 $InRecode=@mysql_query($InSql);
							 if($InRecode){ $x++; } 
                           }
					}
				 $Log.="$x 条记录插入product_returned表中";
              }
          }
	  else{
           echo "加载EXCEL文件失败！";
          }

//步骤4：
include "../model/logpage.php";
?>
