<?php
//电信-zxq 2012-08-01
$Date = date("Ymd");
$Filename ="ASH".$Date.".vcf";
header("Content-type:text/vcf");
header("Content-Disposition:attachment;filename=".$Filename);
header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
header('Expires:0');
header('Pragma:public');
include "basic/parameter.inc";
include "model/modelfunction.php";
$mySql= "SELECT A.cSign,A.Name,A.Mail,B.Mobile,B.Dh,C.Address 
FROM $DataPublic.staffmain M 
LEFT JOIN $DataPublic.staffsheet B ON B.Number=A.Number 
LEFt JOIN $DataPublic.staffworkadd C ON C.Id=A.WorkAdd

WHERE 1 AND A.Estate='1' AND (A.Mail!='' OR B.Mobile!='')GROUP BY A.Number";//全部
//$mySql= "SELECT A.Name,A.Mail,B.Mobile,B.Dh FROM $DataPublic.staffmain M LEFT JOIN $DataPublic.staffsheet S ON B.Number=A.Number WHERE 1 AND A.Estate='1' AND (A.BranchId!=5 OR A.JobId=11) AND A.Mail!='' GROUP BY A.Number";
$csvFrist="";
$Result=mysql_query($mySql,$link_id);
if($myRow=mysql_fetch_array($Result)){
   do{
	   //读取姓名，并拆分
	   $Name=$myRow["Name"];
	  // $Name_S="ASH";
	  // $Name_E=$Name;
	   if(strlen($Name)>9){
		   $Name_S=substr($Name,0,6);
		   $Name_E=substr($Name,6,6);
		   }
	   else{
		   $Name_S=substr($Name,0,3);
		   $Name_E=substr($Name,3,6);
		   }
	  // 读取短号
	   $Dh=$myRow["Dh"];
	   //读取手机号
	   $Mobile=str_replace('+','',$myRow["Mobile"]);
	   //读取邮件
	    $Mail=$myRow["Mail"];
	       //  $csvFrist.=iconv('UTF-8','GB2312//IGNORE',"$Name,$Mobile,$ExtNo,$Mail,$Type,$Address\n");}
		$AddEss="宝安区西乡镇".$myRow["Address"];
		$CompanyName=$myRow["cSign"]==7?"上海市研砼包装有限公司":"上海市研砼包装有限公司";
$csvFrist.="BEGIN:VCARD
VERSION:3.0
PRODID:-//Apple Inc.//Address Book 6.1.2//EN
N:$Name_S;$Name_E;;;
FN:$Name_S $Name_E
ORG:$CompanyName;
EMAIL;type=INTERNET;type=WORK;type=pref:$Mail
item1.TEL;type=pref:$Dh
item1.X-ABLabel:短号
TEL;type=IPHONE;type=CELL;type=VOICE: $Mobile
TEL;type=WORK;type=VOICE:61139580
TEL;type=WORK;type=FAX:61139585
item2.ADR;type=WORK;type=pref:;;$AddEss;上海市;广东省;518102;中国
item2.X-ABADR:cn
UID:af18fa6f-2c81-4a76-bd85-14689e6a7a32
X-ABUID:844765EB-097A-45F0-A53F-5D08D011EB26:ABPerson
END:VCARD\n
";
	 }while($myRow=mysql_fetch_array($Result));
	}
echo $csvFrist;
?>

