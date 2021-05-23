<?php     	//输出切割图档
   $cutimgFile="&nbsp;";$cutPicture="&nbsp;";$displayFlag=false;
   $checkResult=mysql_query("SELECT Id,Type,Estate,Picture FROM $DataIn.diecutimg WHERE ProductId='$ProductId'",$link_id);
   if($checkRow = mysql_fetch_array($checkResult)){
	  do{
	     $imgEstate=$checkRow["Estate"];
		 $imgType=$checkRow["Type"];
		 $imgFile=$checkRow["Picture"];
		 switch($imgEstate){
			case 1:
		      $displayFlag=true;
			  break;
			case 2:
			  if($aubitAction==1) {
				  $displayFlag=true;
			      }
			  else{
				   if($imgType==1){
			         $cutPicture="<div style='color:#00F;'>未审核</div>"; 
			        }
			     else{
                     $cutimgFile="<div style='color:#00F;'>未审核</div>";
		            }
			  }
			  break;
			case 3:
			   if($imgType==1){
			         $cutPicture="<div style='color:#00F;'>审核退回</div>"; 
			        }
			     else{
                     $cutimgFile="<div style='color:#00F;'>审核退回</div>";
		            }
	             break;   
		   }
		  if ($displayFlag){ //显示图档文件
			   if ($imgType==1){
		          $d1=anmaIn("download/diecutimg/",$SinkOrder,$motherSTR);
		          $f1=anmaIn($imgFile,$SinkOrder,$motherSTR);
			      $cutPicture="<div><a  href='openorload.php?d=$d1&f=$f1&Type=cut' target='download'>查  看</a></div>"; 
			      }
			  else{
	              $ServerIp=$_SERVER["SERVER_ADDR"];
                      $Agent = $_SERVER['HTTP_USER_AGENT'];
                      if (preg_match('/Mac/',$Agent) && preg_match('/OS/',$Agent)) { //MacOS
                          $cutimgFile="<a href='smb:\\\\$ServerIp\\diecutfile\\$ProductId' target='_blank'>网络目录</a><br>";
                      }else{
                          $cutimgFile="<a href='\\\\$ServerIp\\diecutfile\\$ProductId' target='_blank'>网络目录</a><br>";
                      }
		       $cutimgFile.="<a href='../download/diecutfile/$imgFile'>下 载</a>";
			      }
		  }
	   }while($checkRow = mysql_fetch_array($checkResult));
   }

?>