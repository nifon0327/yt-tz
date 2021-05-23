<?php   
//二合一已更新电信---yang 20120801
$Date = date("Ymd");
$Filename = $Date.".csv";
header("Content-type:text/csv"); 
header("Content-Disposition:attachment;filename=".$Filename);
header('Cache-Control:must-revalidate,post-check=0,pre-check=0'); 
header('Expires:0'); 
header('Pragma:public');
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$staff= "SELECT M.Number,M.Name,M.Mail,S.Mobile,M.ExtNo,S.Address FROM $DataPublic.staffmain M
	    LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
	    WHERE 1 AND M.Estate='1' AND M.KqSign='3' GROUP BY M.Number";
$client=" SELECT P.CompanyId AS Number,P.Forshort AS Name,L.Email AS Mail,L.Mobile,F.Tel AS ExtNo,F.Address
        FROM $DataIn.trade_object P
        LEFT JOIN $DataIn.companyinfo F ON F.CompanyId=P.CompanyId 
        LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=P.CompanyId AND L.Defaults=0
        WHERE 1 AND F.Type='1' AND P.Estate='1' AND P.cSign=$Login_cSign";
$provider=" SELECT P.CompanyId AS Number,P.Forshort AS Name,L.Email AS Mail,L.Mobile,F.Tel AS ExtNo,F.Address
        FROM $DataIn.trade_object P
        LEFT JOIN $DataIn.companyinfo F ON F.CompanyId=P.CompanyId
		LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=P.CompanyId AND L.Defaults=0
        WHERE 1 AND F.Type='2' AND P.Estate='1'";
$forward=" SELECT P.CompanyId AS Number,P.Forshort AS Name,L.Email AS Mail,L.Mobile,F.Tel AS ExtNo,F.Address
        FROM $DataPublic.freightdata P
		LEFT JOIN $DataIn.companyinfo F ON F.CompanyId=P.CompanyId
        LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=P.CompanyId AND L.Defaults=0
		WHERE 1 AND F.Type='3' AND P.Estate='1'";

$freight=" SELECT P.CompanyId AS Number,P.Forshort AS Name,L.Email AS Mail,L.Mobile,F.Tel AS ExtNo,F.Address
        FROM $DataPublic.freightdata P
		LEFT JOIN $DataIn.companyinfo F ON F.CompanyId=P.CompanyId
        LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=P.CompanyId AND L.Defaults=0
		WHERE 1 AND F.Type='4' AND P.Estate='1'";

$dealer=" SELECT P.CompanyId AS Number,P.Forshort AS Name,L.Email AS Mail,L.Mobile,F.Tel AS ExtNo,F.Address
        FROM $DataPublic.dealerdata P
		LEFT JOIN $DataIn.companyinfo F ON F.CompanyId=P.CompanyId
        LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=P.CompanyId AND L.Defaults=0
		WHERE 1 AND F.Type='5' AND P.Estate='1'";
$mySql="";		
$area_arr=array();
$area_arr=$_POST['a'];
$count=count($area_arr);
for($i=0;$i<$count;$i++){
if ($mySql!="") $mySql.=" UNION ";
if($area_arr[$i]==1){$mySql.=$staff;}
if($area_arr[$i]==2){$mySql.=$client;}
if($area_arr[$i]==3){$mySql.=$provider;}
if($area_arr[$i]==4){$mySql.=$forward;}
if($area_arr[$i]==5){$mySql.=$freight;}
if($area_arr[$i]==6){$mySql.=$dealer;}
}
$csvFrist=iconv('UTF-8','GB2312//IGNORE',"姓名,移动电话,商务电话,电子邮件,类别,商务地址\n");
$Result=mysql_query($mySql,$link_id);
if($myRow=mysql_fetch_array($Result)){
   do{
       $Number=$myRow["Number"];
	   $Address=$myRow["Address"];
	   $Address=str_replace(',','',$Address);
	   if(mb_strlen($Number)==5){$Type="员工";}
	   else{
	        if(substr($Number,0,1)==1){$Type="客户";}
			elseif(substr($Number,0,1)==2){$Type="供应商";}
			elseif(substr($Number,0,1)==3){$Type="Forward公司";}
			elseif(substr($Number,0,1)==4){$Type="货运公司";}
			else{$Type="经销商及其它";}
			}
       $Name=$myRow["Name"];
	   $Mobile=$myRow["Mobile"];
	   $Mobile=str_replace('+','',$Mobile);
	   //$ExtNo=preg_replace('/\s/','',$myRow["ExtNo"]);
	   $ExtNo=$myRow["ExtNo"];
	   $ExtNo=str_replace('+','',$ExtNo);
	   $Mail=$myRow["Mail"];
	   //$csvFrist.=iconv('UTF-8','GB2312',"$Name,$Mobile,$ExtNo,$Mail,$Type\n");
	   if($Mobile==""&&$ExtNo==""){//没移动电话，固话的不导出
	         $csvFrist.="";}
	   else{
	         $csvFrist.=iconv('UTF-8','GB2312//IGNORE',"$Name,$Mobile,$ExtNo,$Mail,$Type,$Address\n");}
	 }while($myRow=mysql_fetch_array($Result));
	}
echo $csvFrist;
?>

<?php   
/*include "../basic/chksession.php" ;
include "../basic/parameter.inc";
 export_csv();  
//导出到CSV文件  

function export_csv()  
 {  
     $filename = date('YmdHis').".csv";  
     header("Content-type:text/csv");  
     header("Content-Disposition:attachment;filename=".$filename);  
     header('Cache-Control:must-revalidate,post-check=0,pre-check=0');  
     header('Expires:0');  
     header('Pragma:public');  
     echo array_to_string(get_export_data());  
}  
   
  //导出数据转换  @param $result  
 
 function array_to_string($result)  
 {  
     if(empty($result)){  
        return i("没有符合您要求的数据！^_^");  
     }  
     $data;  
     $size_result = sizeof($result);  
     for($i = 0 ; $i < $size_result ;  $i++) {  
        $data .= i($result[$i]['name']).','.i($result[$i]['option'])."\n";  
    }  
     return $data;  
 }  
 
  //获取导出报表的数据   @return  
 
 function get_export_data()  

 {  
  //$link = mysql_connect('localhost','root','root') or die(mysql_error());  
 // mysql_select_db('joomla');  
  //$sql = 'select a.name,a.option from jos_components a limit 10';  
 //$result = mysql_query($sql); 
  $mySql="SELECT M.Number,M.Name,M.Mail,S.Mobile,M.ExtNo,'' AS Type FROM $DataPublic.staffmain M
	    LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
	    WHERE 1 AND M.Estate=1 GROUP BY M.Number
  UNION SELECT P.CompanyId AS Number,P.Forshort AS Name,L.Email AS Mail,L.Mobile,F.Tel AS ExtNo,L.Type
        FROM $DataIn.trade_object P
        LEFT JOIN $DataIn.companyinfo F ON F.CompanyId=P.CompanyId
		LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=P.CompanyId
        WHERE 1 AND F.Type=2 AND P.Estate=1
  UNION SELECT P.CompanyId AS Number,P.Forshort AS Name,L.Email AS Mail,L.Mobile,F.Tel AS ExtNo,L.Type
        FROM $DataIn.trade_object P
        LEFT JOIN $DataIn.companyinfo F ON F.CompanyId=P.CompanyId AND F.Type=1
        LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=P.CompanyId AND L.Defaults=0 AND L.Type=1
        WHERE 1 AND P.Estate='1' AND P.cSign=$Login_cSign";
$Result=mysql_query($mySql,$link_id);
$res = array();
$i = 0;  
if($myRow=mysql_fetch_array($Result)){
   do{
        $res[$i][Name] = $row[name];  
        $res[$i][option] = $row[option];     
       $Number=$myRow["Number"];
	   $Type=$myRow["Type"];
       $Name=$myRow["Name"];
	   $Mobile=$myRow["Mobile"];
	   $ExtNo=$myRow["ExtNo"];
	   $Mail=$myRow["Mail"];
	   $csvFrist.=iconv('UTF-8','GB2312',"$Name,$Mobile,$ExtNo,$Mail\n");
	   $i++;
	  }while($myRow=mysql_fetch_array($Result));
	}     
     return $res;  
 }  
 
  //编码转换  @param <type> $strInput   @return <type>  

 function i($strInput)  
 {  
     return iconv('utf-8','gb2312',$strInput);//页面编码为utf-8时使用，否则导出的中文为乱码  
 } */ 
 ?> 
