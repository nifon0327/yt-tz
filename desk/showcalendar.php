
<?php   
//电信---yang 20120801
	$Today=date("Y-m-d");
	$Yesterday=date("Y-m-d",strtotime("$Today  -1   day") );
	$mySql = "select Id,Title,Content,Date,Operator FROM (";
	$mySql.="             SELECT O.Id,'加班通知' as Title, O.Content,O.Date,O.Operator FROM $DataPublic.msg2_overtime O WHERE 1 AND O.Date>='$Yesterday'";
	$mySql.="  UNION ALL SELECT N.Id, '人事通知' as Title,  N.Content,N.Date,N.Operator FROM $DataPublic.msg3_notice N WHERE 1  AND  N.Date>='$Yesterday' ";
	$mySql.="  UNION ALL SELECT B.Id,B.Title,B.Content,B.Date,B.Operator FROM $DataPublic.msg1_bulletin B WHERE 1 AND B.Date>='$Yesterday' AND Type=1 ";
	$mySql.=" ) A order by A.Date desc";
	//echo "$mySql";
	$myResult = mysql_query($mySql,$link_id);
	if($myRow = mysql_fetch_array($myResult)){  //说明有最新的通知，则要弹出
	 echo "<script language='javascript'> 
	       window.open('../desk/calendar.php','Calendar','height=700,width=1300,top=200,left=200,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no');
	      </script>";
		
	}
?>
