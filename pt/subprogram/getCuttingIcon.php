<?php   	//输出切割图标
   //图片显示
       $CutIconFile="";
         if(!(strpos($CutName,"刀模")===false)){
                  $CutIconFile="<img src='../images/cut2.png' />";
         }
         else{
              if(!(strpos($CutName,"atom")===false)){ 
                        $CutIconFile="<img src='../images/cut3.png' />";
              }
              else{
		              if(!(strpos($CutName,"格柏机")===false)){
			             $CutIconFile="<img src='../images/cut1.png' />";
			           }
         }
      }
     
      if ($CutIconFile=="" && $cutSign>0){
	       switch($cutSign){
		       case 1: $CutIconFile="<img src='../images/cut2.png' />";break;
		       case 3: $CutIconFile="<img src='../images/cut3.png' />";break;
		       case 4: $CutIconFile="<img src='../images/cut1.png' />";break;
	       }
      }
?>