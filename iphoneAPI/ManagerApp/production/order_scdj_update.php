<?php 
 $Log_Item="生产备注";
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
 switch($ActionId){
          case "ADD":
              $Log_Funtion="备注保存";
              $GroupId = '0';   $Remark=$info[1]; $DateIn = $info[0];
			   $isGroup = 0;
			   if($info[2] && $info[2]!='') {
				   $GroupId = $info[2];
				   $isGroup = 1;
			   }
			   $updateOld = "update $DataIn.sc1_cjtj_log set estate=0 
			   where date='$DateIn' 
			   and GroupId='$GroupId'";
			   $inAction=@mysql_query($updateOld);
			   if ($inAction) {
				    $inRecode="INSERT INTO $DataIn.sc1_cjtj_log
(`Id`,
`OPdatetime`,
`Date`,
`Estate`,
`GroupId`,
`Remark`,
`Operator`
)
VALUES
(NULL,'$DateTime','$DateIn',1,'$GroupId','$Remark','$Operator');
";
                $inAction=@mysql_query($inRecode);
                 if ($inAction){ 
                        $Log=$Log_Item .$Log_Funtion . "成功!<br>";
                        $OperationResult="Y";
                        $infoSTR=$Log_Funtion ."成功";
                }  else{
                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inRecode </div><br>";
                        $infoSTR=$Log_Funtion ."失败";
                 }
			   }
                 
                else{
                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inRecode </div><br>";
                        $infoSTR=$Log_Funtion ."失败";
                 }
            break;  
        
			
     }

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR");
?>