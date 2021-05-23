<?php   
    //*******************评价
$pjResult=mysql_fetch_array(mysql_query("SELECT pj_times FROM $DataIn.product_pj WHERE ProductId='$ProductId' ",$link_id));
$pj_times=$pjResult["pj_times"]==""?0:$pjResult["pj_times"];
$pjStr="<input type='hidden' id='pjtimes$i' name='pjtimes$i' value='$pj_times'>";
if(preg_match('/^192\.168/',$Login_IP)){//公司内部人员才人操作
        switch($pj_times){
              case 0:
                         $pjgif="<img src='../images/pj_gray.gif' style='CURSOR: pointer;' onclick='pjclick(1,$ProductId,$i)'><img src='../images/pj_gray.gif' 
style='CURSOR: pointer;' onclick='pjclick(2,$ProductId,$i)'><img src='../images/pj_gray.gif' style='CURSOR: pointer;' onclick='pjclick(3,$ProductId,$i)'>";
              break;
              case 1:
                         $pjgif="<img src='../images/pj_yellow.gif' style='CURSOR: pointer;' onclick='pjclick(1,$ProductId,$i)'><img src='../images/pj_gray.gif' style='CURSOR: pointer;' onclick='pjclick(2,$ProductId,$i)'><img src='../images/pj_gray.gif' style='CURSOR: pointer;' onclick='pjclick(3,$ProductId,$i)'>";
              break;
              case 2:
                         $pjgif="<img src='../images/pj_yellow.gif' style='CURSOR: pointer;' onclick='pjclick(1,$ProductId,$i)'><img src='../images/pj_yellow.gif' style='CURSOR: pointer;' onclick='pjclick(2,$ProductId,$i)'><img src='../images/pj_gray.gif' style='CURSOR: pointer;' onclick='pjclick(3,$ProductId,$i)'>";
              break;
              case 3:
                         $pjgif="<img src='../images/pj_yellow.gif' style='CURSOR: pointer;' onclick='pjclick(1,$ProductId,$i)'><img src='../images/pj_yellow.gif' style='CURSOR: pointer;' onclick='pjclick(2,$ProductId,$i)'><img src='../images/pj_yellow.gif' style='CURSOR: pointer;' onclick='pjclick(3,$ProductId,$i)'>";
              break;     
               }
            }
    else{
           switch($pj_times){
                case 0:$pjgif="<img src='../images/pj_gray.gif' ><img src='../images/pj_gray.gif'><img src='../images/pj_gray.gif'>";break;
                case 1:$pjgif="<img src='../images/pj_yellow.gif'><img src='../images/pj_gray.gif''><img src='../images/pj_gray.gif'>";break;
                case 2:$pjgif="<img src='../images/pj_yellow.gif'><img src='../images/pj_yellow.gif'><img src='../images/pj_gray.gif'>";break;
                case 3:$pjgif="<img src='../images/pj_yellow.gif'><img src='../images/pj_yellow.gif'><img src='../images/pj_yellow.gif'>";break;     
               }
          }
?>