 <?php  
      if($Code!=""){
                    $CodeNum=explode("|", $Code);
                    $TempCode="";
                    $CodeResult=mysql_fetch_array(mysql_query("SELECT D.StuffCname
				           FROM $DataIn.pands A
				           LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
				          WHERE A.ProductId='$ProductId' AND D.TypeId=9033 AND D.StuffCname LIKE '%条码%'",$link_id));
                           $CodeStuffCname=$CodeResult["StuffCname"];
                           $CodeArray=explode("-", $CodeStuffCname);
                           $CodeCount=count($CodeArray);
                           for($s=0;$s<$CodeCount;$s++){
                                if(is_numeric(trim($CodeArray[$s])) && (strlen(trim($CodeArray[$s]))==12 || strlen(trim($CodeArray[$s]))==13)){
                                        $TempCode=$CodeArray[$s];
                                        break;
                                        }
                               }
                        if($TempCode!=""){
                                if($TempCode!=$CodeNum[1])$TempCodeNum="<span class='redB'>$CodeNum[1]</span>";
                                 else $TempCodeNum=$CodeNum[1];
                           }
                 }
         else $TempCodeNum="";
?>