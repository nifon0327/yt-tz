<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

include "../basic/downloadFileIP.php";  //取得下载文档的IP
//echo "123---- $donwloadFileIP";
if ($donwloadFileIP!="") {   //不为空表示走远程加载
	//echo "$donwloadFileIP";
	$url="$donwloadFileIP/remoteDloadFile/R_UpLoadFiles.php?Login_P_Number=$Login_P_Number&UpFileSign=stuffG";
	//echo "$url";
	$str=file_get_contents(iconv("UTF-8","GB2312",$url));				//注意：要将地址转为GB2312，否则读取失败
	//$content= str_replace("\"","'",$str);
	$content=$str;
	$start="^";
	$strP=strpos($content,$start);
	$tempStr=substr($content,$strP+1);

	echo "远程图档加载成功:$tempStr";

}
else {
    //echo "456";
	//扫描配件图档并更新电信---yang 20120801
	//$Date=date("Y-m-d");
	$Date=date("Y-m-d H:i:s");
	$handle=opendir("../download/upload/");
	//$d=anmaIn("download/upload/",$SinkOrder,$motherSTR);
	while($Gfile=readdir($handle)) {
		$FileType=substr($Gfile,-4,4);
		if (($Gfile!=".") and ($Gfile!="..")) {
			//$Id=trim(preg_replace("[^0-9]","",$Gfile));
			$Id=trim(preg_replace("/([^0-9]+)/i","",$Gfile));
			if ($Id!=""){


				$delsql = "UPDATE $DataIn.stuffprovider SET Estate=0  WHERE StuffId=$Id";  //add by zx 2011-04-15 全部更新为0,准备用新图档
				$delresult = mysql_query($delsql,$link_id);

				$Operator=$Login_P_Number;
				$inRecode="INSERT INTO $DataIn.stuffprovider (Id,StuffId,CompanyId,Date,Estate,Operator) VALUES 
				(NULL,'$Id','0','$Date','1','$Operator')";  //记录内部上传
				$inAction=mysql_query($inRecode,$link_id);
				//echo "$inRecode";

				$sql = "UPDATE $DataIn.stuffdata SET Gfile='$Gfile',Gstate='6',GfileDate='$Date' WHERE StuffId=$Id";  //把文件名写进去
				//$sql = "UPDATE $DataIn.stuffdata SET Gstate='9' WHERE StuffId=$Id";  //把文件名写进去

				//echo "<br> $sql <br>";

				$result = mysql_query($sql,$link_id);
				$rerows=mysql_affected_rows();
				//if($result && ($rerows>0)){ //如果返回值>0则拷文件

				if($result ){ //如果返回值>0则拷文件
					$stuffFilePath="../download/stufffile_tmp/";
					//$stuffFilePath="../download/zxtest/";
					$TempFilePath="../download/upload/";
					$stuffile=$stuffFilePath.$Gfile;
					$TempFile=$TempFilePath.$Gfile;
					//echo "copy file! copy($TempFile, $stuffile)";
					if(copy($TempFile, $stuffile)) {  //拷贝成功，则删除临时文件
					  unlink($TempFile);
					  $sql = "DELETE FROM $DataIn.stuffverify WHERE StuffId='$Id'";
					  $result = mysql_query($sql);
					}  //if(copy

				}  //if($result

			}//if(id!="")
		} //
	}
	closedir($handle);
}
?>