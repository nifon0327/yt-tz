<?php 
//开发进度信息
 $mySql="SELECT A.Date,A.Remark,M.Name 
							    FROM  $DataIn.stuffdevelop_log A
								 LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Operator
								WHERE  A.Mid='$Mid' ORDER BY A.Date DESC,A.Id DESC";
$myResult = mysql_query($mySql,$link_id);
 if($myRow = mysql_fetch_assoc($myResult)){
    do{
         $jsonArray[]=array(
                      "Title"=>$myRow["Remark"],
                      "Col1"=>$myRow["Date"],
                      "Col3"=>$myRow["Name"]
                   );
        }while($myRow = mysql_fetch_assoc($myResult));
}
?>