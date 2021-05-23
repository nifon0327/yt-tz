<?
//助学补助记录

 /*
 $mySql="SELECT S.Month,S.Amount FROM $DataIn.cw19_studyfeesheet    S 
			      LEFT JOIN  $DataPublic.childinfo A  ON A.Id=S.cId
			      WHERE A.Number='$Number' AND   (S.Estate=0 OR S.Estate=3)
			UNION ALL 
			       SELECT S.Month,S.Amount FROM $DataOut.cw19_studyfeesheet   S 
			       LEFT JOIN  $DataPublic.childinfo A  ON A.Id=S.cId
			       WHERE A.Number='$Number' AND   (S.Estate=0 OR S.Estate=3)
		 ORDER BY Month ";
 */
 	
 //modify by cabbage 20150122 傳回比較完整的明細資料, 以下sql參考自 public/childstudyfee_read.php
 $mySql = "SELECT S.*, A.ChildName, C.Name AS ClassName
			FROM
			(
			  SELECT cId, NowSchool, Amount, Month, Attached, Date, Estate
			  FROM $DataIn.cw19_studyfeesheet S
			    UNION ALL
			  SELECT cId, NowSchool, Amount, Month, Attached, Date, Estate
			  FROM $DataOut.cw19_studyfeesheet S
			) AS S
			LEFT JOIN $DataPublic.childinfo A  ON A.Id=S.cId
			LEFT JOIN $DataPublic.childclass C ON C.Id=S.NowSchool
			LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Number
			WHERE S.Estate IN (0, 3) AND A.Number = '$Number' ORDER BY A.Id ASC, S.Date DESC";

//echo $mySql;
  $myResult = mysql_query($mySql);
  while($myRow = mysql_fetch_assoc($myResult))
 {
	        $Month=$myRow["Month"];
	        $Amount= "¥".number_format($myRow["Amount"]);
	        
	        //add by cabbage 20150122 補上更多詳細資料
	        $childName = $myRow["ChildName"];
	        $date = $myRow["Date"];
	        $className = $myRow["ClassName"];
	        
	        $attachment = "";
	        $attachFile = "/download/childinfo/".$myRow["Attached"];
	        if (strlen($attachFile) > 0) {	        
				$domain = $_SERVER["SERVER_NAME"];
				$protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
		        $attachment = "$protocol://{$domain}{$attachFile}";
	        }
	        
	        $jsonArray[] = array(
		        "Title" => "$Month",
		        "Value" => "$Amount",
		        "Col1" => "$childName",
		        "Col2" => "$date",
		        "Remark" => "$className",
		        "Attachment" => "$attachment",
	        );
    }
?>