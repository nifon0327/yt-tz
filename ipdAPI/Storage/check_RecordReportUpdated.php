<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	error_reporting(E_ALL & ~E_NOTICE);  
	
	//接收送来的数据
	$Id = $_POST["Id"];
	$CheckQty = $_POST["CheckQty"];
	$CheckAQL = $_POST["CheckAQL"];
	$ReQty = $_POST["ReQty"];
	$sumQty = $_POST["SumQty"];
	$otherCause = $_POST["OtherCause"];
	$causes = $_POST["cause"];
	$badQtys = $_POST["badQty"];
	$hasPic = $_POST["hasPic"];
	
	$CauseId = explode("^", $causes);
	$badQty = explode("^", $badQty);
	$PictureName = explode("^", $hasPic);
	
	$otherbadQty = $_POST["otherbadQty"];
	$otherCause = $_POST["OtherCause"];
	$otherPictureName = $_POST["otherPic"];
	
	$Login_P_Number = $_POST["Login_P_Number"];
	
	$FromqcCause=1;
	$Date=date("Y-m-d");
	$Estate=$sumQty==0?0:1; 
	
	$succeedFlag = "Y";
	$descript = "";
	
	$upSql="UPDATE $DataIn.qc_badrecord SET Qty='$sumQty' WHERE Id='$Id' AND Estate=1 LIMIT 1";
    $upAction=@mysql_query($upSql);
       
    $Mid=$Id;
    //删除原有的品检明细表
    $delSql="DELETE FROM $DataIn.qc_badrecordsheet WHERE Mid='$Mid'";
    $delResult=mysql_query($delSql);
      
    if ($sumQty>0)
    {  //有不良品
	    $FileType=".jpg";
	    $FilePath="../../download/qcbadpicture/";
	    if(!file_exists($FilePath))
	    {
		    makedir($FilePath);
        }               
        $counts=count($badQty);
        for ($i=0;$i<$counts;$i++)
        {
        	if ($badQty[$i]>0)
        	{
            //生成明细表
            	$Picture= ($PictureName[$i]=="")?0:1; 
                                
                $insheetSql="INSERT INTO $DataIn.qc_badrecordsheet  (Id, Mid, CauseId, Qty, Reason,Picture) VALUES (NULL, '$Mid', '$CauseId[$i]', '$badQty[$i]', '','$Picture')";
                $insheetAction=@mysql_query($insheetSql,$link_id);
                if (!$insheetAction)
                {
                	$succeedFlag = "N";	
                	$qcError=1;break;
                }
                else
                {
                	$Sid=mysql_insert_id();
                    //上传不良图片
                    $PreFileName="Q".$Sid.$FileType;
                    $fileName = "fileinput".$i;
                    if ($_FILES[$fileName]["tmp_name"])
                    {	
                    	$copymes=copy($_FILES[$fileName]["tmp_name"],"$FilePath" . "$PreFileName"); 
                        if($copymes)
                        {
                        //更新刚才的记录
                        	if ($Picture==0)
                        	{
                            	$sql = "UPDATE $DataIn.qc_badrecordsheet SET Picture='1' WHERE Id=$Sid";
                                $result = mysql_query($sql);
                             }
                         }
                         else
                         {
                         	if ($Picture==1)
                         	{
                            	$sql = "UPDATE $DataIn.qc_badrecordsheet SET Picture='0' WHERE Id=$Sid";
                                $result = mysql_query($sql);
                            }
                            $succeedFlag = "N";	
                            $descript .= "上传图片失败";		
                         }
                     }
                     else
                     {
                     //更名
                     if ($Picture==1)
                     {
                     	$oldPictureName=$FilePath . $PictureName[$i];
                        rename($oldPictureName,"$FilePath" . "$PreFileName");
                     }
                }
                                  }
            } 
        }//end for
        //有其它不良原因
        if ($otherbadQty>0)
        {
        	//生成明细表
            $Picture=$otherPictureName==""?0:1; 
            $insheetSql="INSERT INTO $DataIn.qc_badrecordsheet  (Id, Mid, CauseId, Qty, Reason,Picture) VALUES (NULL, '$Mid', '-1', '$otherbadQty', '$otherCause','$Picture')";
            $insheetAction=@mysql_query($insheetSql,$link_id);
            if (!$insheetAction)
            {
            	$succeedFlag = "N";	
            	$qcError=1;break;
            }
            else
            {
            	$Sid=mysql_insert_id();
                //上传不良图片
                $PreFileName="Q".$Sid.$FileType;
                if ($_FILES["otherfileinput"]["tmp_name"])
                {
                	$copymes=copy($_FILES["otherfileinput"]["tmp_name"],"$FilePath" . "$PreFileName"); 
                    if($copymes)
                    {
                    //更新刚才的记录
                    	if ($Picture==0)
                    	{
                        	$sql = "UPDATE $DataIn.qc_badrecordsheet SET Picture='1' WHERE Id=$Sid";
                            $result = mysql_query($sql);
                        }
                     }
                     else
                     {
                     	if ($Picture==1)
                     	{
                        	$sql = "UPDATE $DataIn.qc_badrecordsheet SET Picture='0' WHERE Id=$Sid";
                            $result = mysql_query($sql);
                        }
                        
                        $descript .= "上传图片失败";				
                     }
                 }
                else
                {
                //更名
                	if ($Picture==1)
                	{
                		$oldPictureName=$FilePath . $otherPictureName;
                		@rename($oldPictureName,"$FilePath" . "$PreFileName");
                	}   
                }
            }
        }
                        
        if($qcError==1)
        { 
    		$descript .= "来料品检不良明细记录保存失败！";
    	}
    	else
    	{
    		$descript .="来料品检不良明细记录保存成功！";
    	}
    }

	echo json_encode(array($succeedFlag, $descript));
	
?>