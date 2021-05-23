<?php
 //app展示图
		$AppFileJPGPath="../download/productIcon/" .$ProductId.".jpg";
		$AppFilePNGPath="../download/productIcon/" .$ProductId.".png";
		$AppFilePath ="";
        if(file_exists($AppFilePNGPath)){
	       $AppFilePath  = $AppFilePNGPath;
        }else{
           if(file_exists($AppFileJPGPath)){
	          $AppFilePath =  $AppFileJPGPath; 
           }
	       else{
		       $AppFilePath ="";
	       }
        }
        
		if($AppFilePath!=""){
		       $noStatue="onMouseOver=\"window.status='none';return true\"";
			   $AppFileSTR="<span class='list' >View<span><img src='$AppFilePath' $noStatue/></span></span>";
			}
        else{
	          $AppFileSTR="&nbsp;";
        }

?>