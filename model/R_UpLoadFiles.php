<?php
include "../basic/parameter.inc";

include "../basic/downloadFileIP.php";  //取得下载文档的IP

//echo "123---- $donwloadFileIP";
if ($donwloadFileIP!="") {   //不为空表示走远程加载

	switch ($UpFileSign){  //$UpFileSign:
		case "stuffG":   //stuffG: 表示配件图档文件
			include "../remoteDloadFile/R_stuffimg_GfileUpLoad.php";
			$tempStr="OK";
		break;

		case "stuffPDF":   //stuffG: 表示配件图片文件PDF  //stuffdata_updated.php Action=40
			//echo "stuffPDF";
			//$url="$donwloadFileIP/remoteDloadFile/R_UpLoadFiles.php?Login_P_Number=$Login_P_Number&UpFileSign=stuffG";
            $url=$donwloadFileIP."/remoteDloadFile/R_UpLoadFiles.php?Login_P_Number=$Login_P_Number&UpFileSign=$UpFileSign&upPicture=$upPicture&StuffId=$StuffId&doAction=$doAction";
	        //echo "$url <br>";
			$str=file_get_contents(iconv("UTF-8","GB2312",$url));				//注意：要将地址转为GB2312，否则读取失败
			$content=$str;
			$start="^";
			$strP=strpos($content,$start);
			$tempStr=substr($content,$strP);
			echo "$tempStr";
		break;

		case "productFile":   //产品原图
			//$url=$donwloadFileIP."/remoteDloadFile/R_UpLoadFiles.php?Login_P_Number=$Login_P_Number&UpFileSign=$UpFileSign&originalPicture=$originalPicture&ProductId=$ProductId&doAction=$doAction";
			switch ($doAction){
				case "UpPackZip": // 带包装高清图,图在本地服务器上
					$returnstr="";
					include "../model/R_productimg_fileUpLoad.php";
					$tempStr=$returnstr;
					echo "^$tempStr";
					break;
				case "UpNoPackZip": // 不带包装高清图，图在本地服务器上
					$returnstr="";
					include "../model/R_productimg_fileUpLoad.php";
					$tempStr=$returnstr;
					echo "^$tempStr";
					break;
				default:  //走远程的产品原图
					$url=$donwloadFileIP."/remoteDloadFile/R_UpLoadFiles.php?Login_P_Number=$Login_P_Number&UpFileSign=$UpFileSign&originalPicture=$originalPicture&ProductId=$ProductId&doAction=$doAction";
					//echo "$url <br>";
					$str=file_get_contents(iconv("UTF-8","GB2312",$url));				//注意：要将地址转为GB2312，否则读取失败
					$content=$str;
					$start="^";
					$strP=strpos($content,$start);
					$tempStr=substr($content,$strP);
					echo "$tempStr";
				break;
			}

		break;

		case "QCFile":   //QC原图
			//$FileRemark=mb_convert_encoding($FileRemark, 'UTF-8','ascii,GB2312,gbk,UTF-8,BIG5');
			$url=$donwloadFileIP."/remoteDloadFile/R_UpLoadFiles.php?Login_P_Number=$Login_P_Number&UpFileSign=$UpFileSign&originalPicture=$originalPicture&ProductId=$ProductId&FileRemark=$FileRemark&doAction=$doAction";
			//echo "$url <br>";
			/*
			$str=file_get_contents(iconv("UTF-8","GB2312",$url));				//注意：要将地址转为GB2312，否则读取失败
			$content=$str;
			$start="^";
			$strP=strpos($content,$start);
			$tempStr=substr($content,$strP);
			//$tempStr="$url";
			echo "$tempStr";
			*/
			//$url = "http://s.jb51.net";
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10);
			$str = curl_exec($ch);
			$content=$str;
			$start="^";
			$strP=strpos($content,$start);
			$tempStr=substr($content,$strP);
			echo "$tempStr";

		break;

	}
}
if($tempStr=="") {  //表示联系服务器失败！
   echo "^读取服务器数据失败！|-1|-1|";
}
?>