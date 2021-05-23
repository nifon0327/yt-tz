<?php
 //刀模图档
 $CutDrawing="";
 $CutStr = "";
 $drawingSql=mysql_query("SELECT D.Picture ,D.CutId,C.CutName,
 C.Picture AS cutPicture,C.cutSign 
 FROM $DataIn.slice_cutdie   D  
 LEFT JOIN $DataIn.pt_cut_data    C   ON  C.Id  = D.CutId
 WHERE  D.StuffId='$mStuffId' ",$link_id);
 while($drawingRow=mysql_fetch_array($drawingSql)){
      $drawingPicture=$drawingRow["Picture"];
      $fd=anmaIn($drawingPicture,$SinkOrder,$motherSTR);
      if($drawingPicture){
             if($CutDrawing!=""){
			            $CutDrawing=$CutDrawing."<br>"."<a href=\"../admin/openorload.php?d=$dw&f=$fd&Type=&Action=6\"target=\"download\"><img src='../images/down.gif' title='刀模图档' width='18' height='18'></a>";

                   }else{
			            $CutDrawing="<a href=\"../admin/openorload.php?d=$dw&f=$fd&Type=&Action=6\"target=\"download\"><img src='../images/down.gif' title='刀模图档' width='18' height='18'></a>";
                }
         }
		$CutId=$drawingRow["CutId"];
		$CutName=$drawingRow["CutName"];
		$cutSign=$drawingRow["cutSign"];
		include "../pt/subprogram/getCuttingIcon.php";
		//刀模名称
		$cutPicture=$drawingRow["cutPicture"];
        if($cutPicture==1){
               $fn=anmaIn("C".$CutId.".jpg",$SinkOrder,$motherSTR);
             $CutName="<a href=\"../admin/openorload.php?d=$dt&f=$fn&Type=&Action=6\"target=\"download\">$CutName</a>";
         }
     if($CutStr!=""){
                $CutStr  = $CutStr."<br>".$CutIconFile .$CutName;
          }else{
                $CutStr  = $CutIconFile.$CutName;
         }
   }
   $CutDrawing  = $CutDrawing==""?"&nbsp;":$CutDrawing; 
   $CutStr  = $CutStr==""?"&nbsp;":$CutStr; 

?>