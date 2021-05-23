<?php 
 $Log_Item="出入登记";
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;

switch($ActionId){
        case "UPDATE":
             $Log_Funtion="更新";
             $Id=$info[0];$Sign=$info[1];
             $upSTR="";
             $checkSql=mysql_query("SELECT * FROM  $DataPublic.come_data WHERE  Id ='$Id' AND Estate>0 ",$link_id);
              if  ($checkRow = mysql_fetch_array($checkSql)){
                   $OpNumber=$checkRow["Operator"];
                   if ($checkRow["TypeId"]==3 && $Sign==1) $Sign=3;
              }
             $delState=$OpNumber==$Operator?1:0;
             
             $CheckJobId=mysql_fetch_array(mysql_query("SELECT  JobId FROM $DataPublic.staffmain WHERE Number='$Operator'",$link_id));
             $JobId=$CheckJobId["JobId"];
             
            if ($JobId=="38" || $Operator=="10341" || $delState==1 || $Operator=="10868"){//保安  //$delState
                     switch($Sign){
	                     case 1:   $Estate=2; $upSTR=",InTime='$DateTime',InOperator='$Operator' ";break;
	                     case 2:   $Estate=0; $upSTR=",OutTime='$DateTime',OutOperator='$Operator' ";break;
	                     case 4:   $Estate=0; break;
	                     case 3:   $Estate=0; $upSTR=",InTime='$DateTime',OutTime='$DateTime',InOperator='$Operator',OutOperator='$Operator' ";break;//包裹直接签完
	                     default: $Estate=0; $upSTR=",OutTime='$DateTime',OutOperator='$Operator' ";break;
                     }
                      //更新数据库记录
                    $updateSql = "UPDATE $DataPublic.come_data  SET Estate='$Estate'$upSTR WHERE Id =$Id"; 
                    $updateRresult = mysql_query($updateSql);
                    if($updateRresult && mysql_affected_rows()>0){
                            $OperationResult="Y";
                            $Log=$Log_Item .$Log_Funtion ."成功!<br>";
                            $infoSTR=$Log_Funtion ."数据成功";
                             //推送信息
                             if ($Estate==2 || $Sign==3){
	                              include "../subpush/visitor_push.php";
                             }
                            }
                    else{
                            $Log="<div class='redB'>$Log_Item $Log_Funtion</div><br>";
                            $infoSTR=$Log_Funtion ."数据失败";
                            }
              }
              else{
                    $Log=$Log_Item .$Log_Funtion ."无权限操作!<br>";
                    $infoSTR=$Log_Funtion ."无权限";
              }
            break;
        case "ADD":
          $Log_Funtion="保存";
          $TypeId = $info[0];
          $Name =  $info[1];
          $Person=  $info[2];
          $ComeDate =  $info[3];
          $Remark =  $info[4];
          $Estate= $info[5];
          if ($Estate==2){
               $inRecode="INSERT INTO  $DataPublic.come_data (Id ,cSign ,TypeId ,Name ,Persons ,ComeDate ,Remark ,InTime ,InOperator ,OutTime ,OutOperator ,CompanyId ,Mid ,Estate ,Locks ,Date ,Operator)VALUES (NULL,  '7',  '$TypeId',  '$Name',  '$Person',  '$ComeDate', '$Remark', '$DateTime' ,  '0', NULL ,  '0',  '0',  '0',  '$Estate',  '0',  '$curDate',  '$Operator')";  
          }
          else{
               $inRecode="INSERT INTO  $DataPublic.come_data (Id ,cSign ,TypeId ,Name ,Persons ,ComeDate ,Remark ,InTime ,InOperator ,OutTime ,OutOperator ,CompanyId ,Mid ,Estate ,Locks ,Date ,Operator)VALUES (NULL,  '7',  '$TypeId',  '$Name',  '$Person',  '$ComeDate', '$Remark', NULL ,  '0', NULL ,  '0',  '0',  '0',  '$Estate',  '0',  '$curDate',  '$Operator')";
          }
       
        $inAction=@mysql_query($inRecode);
        if ($inAction){ 
                $Log=$Log_Item .$Log_Funtion . "成功!<br>";
                $OperationResult="Y";
                $infoSTR=$Log_Funtion ."数据成功";
                } 
        else{
                $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inRecode </div><br>";
                $infoSTR=$Log_Funtion ."数据失败";
                }
            break;
   } 
  

//$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
//$IN_res=@mysql_query($IN_recode);

$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR");
?>