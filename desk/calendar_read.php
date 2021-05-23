<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php";
include "../basic/parameter.inc";
$SignType=$_GET["SignType"];
if ($SignType=="")
  {
   $SignType=1;
  }
  $dateType=$_GET["dateType"];
  
 //读取通知的详细内容
 if ($SignType=="8"){
	$arr_data=explode("|", $dateType);  
	switch($arr_data[0]){
	case 1:
         $mySql=" SELECT Id,'加班通知' as Title,Content,Date,Operator FROM $DataPublic.msg2_overtime WHERE Id='$arr_data[1]'";
	     break;
	case 2:
		 $mySql="SELECT Id,'人事通知' AS Title, Content, Date, Operator FROM $DataPublic.msg3_notice WHERE Id='$arr_data[1]'"; 
	     break;
	case 3:
		 $mySql="SELECT Id,Title,Content,Date,Operator FROM $DataPublic.msg1_bulletin WHERE Id='$arr_data[1]'";
	 break;
    } 	
  //$myResult = mysql_query($mySql,$link_id);
  if ($myResult = mysql_query($mySql,$link_id)) 
  {
	 while ($myRow =mysql_fetch_array($myResult))
	   {
                  $Id=$myRow["Id"];
		  $Title=$myRow["Title"];
		  $Content=nl2br($myRow["Content"]);	
		  $Date=$myRow["Date"];
		  $Operator=$myRow["Operator"];
		  include "../admin/subprogram/staffname.php";
		  echo "<p>$Title</p><b>发&nbsp;&nbsp;布&nbsp;&nbsp;人：</b>$Operator</br><b>发布日期：</b>$Date</br></br>$Content";
	   }
           if ($arr_data[0]==3)
           {
             //读取图片
             $FilePath="../download/msgfile/";
             $imgSql = "SELECT Picture FROM $DataPublic.msg1_picture WHERE Mid='$Id'";
             if ($imgResult = mysql_query($imgSql,$link_id)) 
             {
                 while ($imgRow =mysql_fetch_array($imgResult)) 
                 {  
                   $Picture=$FilePath . $imgRow["Picture"];
                   echo "<p><img src='$Picture' width='500px'/></p>"; 
                     
                 }
             }
           }
   }
  else {
        echo "<Font color='red' size='5' align='center'>读取通告内容错误！</Font>";
    }
 }
 
 //读取通知标题
else {
if ($dateType=="")
  {
   $dateType=1;
  } 
 $Today=date("Y-m-d");
switch($dateType){
	case 5:
	 $SelDate=date("Y-m-d",strtotime("$Today  -7  day"));
	 break;
	case 1:
	 $SelDate=date("Y-m-d",strtotime("$Today  -1   month"));
	 break;
	case 2:
	 $SelDate=date("Y-m-d",strtotime("$Today  -3   month"));
	 break;
	case 3:
	 $SelDate=date("Y-m-d",strtotime("$Today  -6   month"));
	 break;
	case 4:
	 $SelDate=date("Y-m-d",strtotime("$Today  -1   Year")); 
	 break;
	case 0: 
	 $SelDate="2007-1-1";
	 break;
} 
switch($SignType){
     case 1:  //显示一周内公告通知及长期公告
	  $mySql = "select TableName,Id,Title,Date,Operator FROM (";
	  $mySql.="SELECT 1 as TableName,O.Id,'加班通知' as Title,O.Date,O.Operator FROM $DataPublic.msg2_overtime O  WHERE 1 AND O.Date>='$SelDate' AND O.cSign=$Login_cSign ";
	  $mySql.="  UNION ALL SELECT 2 as TableName,N.Id, '人事通知' as Title,N.Date,N.Operator FROM $DataPublic.msg3_notice N  WHERE 1  AND  N.Date>='$SelDate'  AND N.cSign=$Login_cSign ";
	  $mySql.="  UNION ALL SELECT 3 as TableName,B.Id,B.Title,B.Date,B.Operator FROM $DataPublic.msg1_bulletin B WHERE 1 AND B.Date>='$SelDate'  OR Type=0  AND B.cSign=$Login_cSign ";
	  $mySql.=") A order by A.Date desc";
	 break;
	case 2:
         $mySql=" SELECT 1 as TableName,Id,'加班通知' as Title,Date,Operator FROM $DataPublic.msg2_overtime WHERE Date>='$SelDate'   AND cSign=$Login_cSign  order by Date desc";
	     break;
	case 3:
		 $mySql="SELECT 2 as TableName,Id,'人事通知' AS Title, Date, Operator FROM $DataPublic.msg3_notice WHERE Date >='$SelDate'   AND cSign=$Login_cSign ORDER BY Date DESC"; 
	     break;
	case 4:
		 $mySql="SELECT 3 as TableName,Id,Title,Date,Operator FROM $DataPublic.msg1_bulletin  WHERE (Date>='$SelDate' OR Type=0)  AND cSign=$Login_cSign order by Date desc";
	 break;
} 	
//echo $mySql;
  if ($myResult = mysql_query($mySql,$link_id)) 
  {
    $idIndex=0;
	while ($myRow =mysql_fetch_array($myResult)){
	    $TodaySpan="";
	    $Todayaclass="";
		$TableName=$myRow["TableName"];
		$Id=$myRow["Id"];
		$Title=$myRow["Title"];
		//$Content=nl2br($myRow["Content"]);	
		$Date=$myRow["Date"];
		if ($Date==$Today) 
		  {
		     $TodaySpan=" class=\"spantoday\" ";//今日颜色
		     $Todayaclass=" class=\"Atoday\" ";
		   }
		$Operator=$myRow["Operator"];
		include "../admin/subprogram/staffname.php";
		$Content=$TableName . "|" . $Id;
		//$Content="<p>$Title</p><b>发&nbsp;&nbsp;布&nbsp;&nbsp;人：</b>$Operator</br><b>发布日期：</b>$Date</br></br>$Content";	
		echo "<li id=$idIndex onclick='listClick($idIndex)'><span $TodaySpan>$Date</span><a href='#' title='发布人：$Operator' $Todayaclass>$Title</a></li>"; 
		echo "<textarea id='textMsg$idIndex' name='textMsg$idIndex'>$Content</textarea>";
		$idIndex+=1;
	 }
  }
  else {
    echo "<Font color='red' size='5' align='center'>未有通告内容,查询更多请调整查询日期！</Font>";
   }
  }
 
?>