<?php

  class common extends dbconnect
  {
  	
    public function get_user_result(){
       $sql="select id,loginname,password,truename,telephone,objectid from replenish_user";
       return $this->result($sql);
    }

    /*
     * 保存用户信息
     */
    public function save_user(){
    	$sql="insert into replenish_user(
               loginname,password,truename,telephone,enabled,description,
							 createon,createby,modifiedon,modifieduserid,modifiedby)
			VALUES ('text002','88888','测试人员','18925556',1,'',now(),'',now(),1,modifiedby)";
		  return $this->exec_insertid($sql);
    }
   
    /**
     * 用于登陆--根据用户名密码查询用户信息
     */
    public function get_userbynameandpwd($name,$password){
       $sql="select id,loginname,password, truename,telephone,objectid
               from replenish_user where loginname='$name' and enabled=1";
       return $this->row($sql);
    }

    public function getUseByNameAndPwdExt($name,$password){
      $md5_pwd=md5($password);

      $sql="SELECT
                U.Number,
                U.uName,
                U.uPwd,
                U.openid as OpenId,
                O.NAME as Name,
                GROUP_CONCAT( T.Forshort) AS Forshort ,
                T.Id
            FROM
              usertable U
              LEFT JOIN ot_staff O ON O.Number = U.Number
              LEFT JOIN trade_object T ON FIND_IN_SET( T.Id, O.Forshort ) 
            WHERE
              U.uType = 4 and U.uName='$name'
            GROUP BY
              O.Forshort";
      $useInfo=$this->row($sql);
      if(empty($useInfo)){
        $this->StatusCode(101,'','用户名错误');
      }

      if($md5_pwd!=$useInfo['uPwd']){
        $this->StatusCode(101,'','用户密码错误');
      }
      $this->StatusCode(0,$useInfo);
    }
    
    /**
     *  获取项目集合
     */
    public function get_trade_object(){
      $sql="    SELECT O.Id,O.CompanyId,O.Forshort,O.ExpNum  
                  FROM trade_object O 
            INNER JOIN trade_info I ON I.TradeId = O.Id";
	    return $this->result($sql);
    }
    /*
     * 模糊查询项目信息
     * 
     */
    public function get_tradebylikename($forshor){
      $sql="SELECT Id,CompanyId,Forshort,ExpNum 
              FROM trade_object 
             WHERE Estate=1 AND Forshort  LIKE '$forshor%' LIMIT 10";
      return $this->result($sql);
    }
    
    /*
     * 根据用户objectid 获取对应的项目
     */
    public function get_tradebyid($id){
       $sql="select Id,CompanyId,Forshort,ExpNum 
              from trade_object 
             where Estate=1 and Id=$id";
      return $this->result($sql);
    }
    
    public function getTradeByNumber($number){
      $sql="SELECT A.Id,A.CompanyId,A.Forshort,A.ExpNum  
              FROM trade_object A 
        inner join ot_staff B on FIND_IN_SET(a.Id,B.Forshort)
             WHERE B.Number='$number'";
      return $this->result($sql);       
    }

    /**
     * 获取楼栋
     */
    public function get_buildings($tradeid){
      $sql="SELECT BuildingNo,TradeId FROM trade_drawing 
             WHERE  TradeId=$tradeid  
          GROUP BY BuildingNo,TradeId 
          ORDER BY cast(BuildingNo as SIGNED)";
      return $this->result($sql);
    }
    /**
     * 获取项目楼层
     */
    public function get_buildings_floor($tradeid,$buildid){
      $sql="SELECT BuildingNo,TradeId,FloorNo from trade_drawing
             WHERE  TradeId=$tradeid AND BuildingNo=$buildid  
          GROUP BY BuildingNo,TradeId,FloorNo 
          Order By cast(FloorNo as SIGNED)";
      return $this->result($sql);
    }
    
    /*
      查询要货信息
     */
    public function get_trade_info($tradeid,$buildingno,$floorno){
      $sql="select a.Id,TradeId,CmptTypeId,CmptType,BuildingNo,FloorNo,b.RequestDateTime,b.ReqId,b.Status
              from trade_drawing a left join replenish_trade_request b on a.Id=b.DrawingId
             where a.TradeId=$tradeid and a.BuildingNo=$buildingno and FloorNo=$floorno";
      return $this->result($sql);       
    }

    public function getTradeInfoPageExt($tradeid,$building,$floorno,$current=0,$pagenum=15)
    {
      $array=array();
      $current=(empty($current) || $current===1)?1:$current;
      $start=($current-1)*$pagenum;
      $sql="SELECT a.TradeId,a.CmptTypeId,a.CmptType,a.BuildingNo,a.FloorNo,
                   IFNULL(b.RequestDateTime,'') as RequestDateTime,
                   IFNULL(b.ReqId,0) as ReqId,
                   IFNULL(b.ReqName,'') as ReqName,
                   IFNULL(b.Status,0)  as Status,
                   CASE  b.Status 
                       WHEN 1 THEN  '未提交'
                       WHEN 2 THEN  '待审核'
                       WHEN 3 THEN '已审核'
                       ELSE '' 
                   END AS StatusName
              FROM trade_drawing a 
         LEFT JOIN replenish_trade_request b ON a.TradeId=b.TradeId 
                   AND a.BuildingNo=b.BuildingNo AND  a.FloorNo=b.FloorNo 
                   AND a.CmptTypeId=b.CmptTypeId
             WHERE a.TradeId=$tradeid AND a.BuildingNo=$building 
                   AND a.FloorNo=$floorno 
         GROUP BY a.TradeId,a.BuildingNo,a.FloorNo,a.CmptTypeId
            LIMIT $start,$pagenum";
      $trades=$this->result($sql);
      $array['data']=$trades;
      $sql_01="SELECT count(*) as cnt FROM (
                       SELECT a.Id  FROM trade_drawing a 
                   LEFT JOIN replenish_trade_request b ON a.TradeId=b.TradeId 
                             AND a.BuildingNo=b.BuildingNo 
                             AND  a.FloorNo=b.FloorNo AND a.CmptTypeId=b.CmptTypeId
                       where a.TradeId=$tradeid  AND a.BuildingNo=$building 
                             AND a.FloorNo=$floorno 
                       GROUP BY a.TradeId,a.BuildingNo,a.FloorNo,a.CmptTypeId) t";
      $recordcount=empty($this->row($sql_01))?0:$this->row($sql_01)['cnt'];
      $pagesize=$recordcount%$pagenum==0?$recordcount/$pagenum:(floor($recordcount/$pagenum)+1);
      $array['recordcount'] = $recordcount;
      $array['pagesize']    = $pagesize;
      $array['current']     = $current;
      $array['pagenum']     = $pagenum;
      return $array;
    }
    


    
    /*
      设置要货时间
     */
    public function set_trade_time($drawingid,$requestdatetime,$reqid,$reqname,$openid){
      $sql="DELETE FROM replenish_trade_request WHERE DrawingId=$drawingid";
      $this->query($sql);
      $sql="INSERT INTO replenish_trade_request(DrawingId,RequestDateTime,ReqId,ReqName,
                                                ReqDateTime,OpenID,Status)
            VALUES ($drawingid,'$requestdatetime',$reqid,'$reqname',now(),'$openid',0)";
      $rtnval=$this->query($sql);
      return $rtnval;
    }

    /*
      提交要货状态
     */
    public function set_tradeCommit($drawingid,$status){
      $sql="UPDATE replenish_trade_request SET Status=$status WHERE DrawingId=$drawingid";
      return $this->execute($sql);
    }


    public function get_pctrade_info($tradeid,$buildingno,$floorno,$RequestDateTime){

       $_wh=" where a.TradeId=$tradeid ";
       if(!empty($buildingno)){
         $_wh.=" and a.BuildingNo=$buildingno";
       }
       if(!empty($floorno)){
         $_wh.=" and FloorNo=$floorno"; 
       }
       if(!empty($RequestDateTime)){
         $_wh.=" and a.RequestDateTime=".$RequestDateTime;
       }
       $sql="SELECT a.Id,TradeId,CmptTypeId,CmptType,BuildingNo,FloorNo,b.RequestDateTime,
                    b.ReqId,b.ReqName,b.Status,b.Checker,b.CheckDate
               FROM trade_drawing a INNER JOIN replenish_trade_request b ON a.Id=b.DrawingId
              $_wh order by a.BuildingNo,a.FloorNo";
      return $this->result($sql);
    }


    /**
     * 根据用户信息获取楼栋
     * $tradeid    : 项目ID
     * $buildingno : 楼栋ID
     * $floorno    : 楼层ID
     */
    public function get_tradesent_plan($tradeid,$buildingno,$floorno,$current=0,$pagesize=50){

      $_where=" where 1=1";
      if(empty($tradeid)){
        $this->StatusCode(101,'','项目ID不能为空');
        die();
      }
      $_where.=" and TradeId=$tradeid";
      if(!empty($buildingno)){
        $_where.=" and BuildingNo=$buildingno";
      }
      $sql="SELECT TradeId,BuildingNo 
              FROM trade_drawing $_where 
          GROUP BY TradeId,BuildingNo 
          ORDER BY cast(BuildingNo as SIGNED)";
      $arr_buildings=$this->result($sql);
      $Orders   = $this->getOrderSheetMainExt($tradeid,$buildingno,$floorno);
      $CmptTypes = $this->getCmptTypeExt($tradeid,$buildingno,$floorno);
      $array=array(); 
      $floors=$this->combineCmptInfo($Orders);
      sort($floors);
      $array['Floor']    = $floors;
      $array['CmptType'] = $CmptTypes;
      $array['Orders']   = $Orders;
      $this->StatusCode(0,$array);
    }



    /**
     * 获取楼层
     */
    public function get_sentplan_floorno($tradeid,$buildingno,$floorno,$current=0,$pagenum=15){
      $arr=array();
      $current=(empty($current) || $current===1)?0:$current-1;
      $start=$current*$pagenum;
      $_wh="where TradeId=$tradeid and BuildingNo=$buildingno";
      if(!empty($floorno)){
         $_wh.=" and FloorNo=$floorno";
      }
      $sql="  SELECT FloorNo FROM trade_drawing  $_wh  
            GROUP BY TradeId,BuildingNo,FloorNo 
            order by   cast(FloorNo as SIGNED) limit $current,$pagenum";
      $floors=$this->result($sql);
      foreach ($floors as $key => $floor) {
        $array['FloorNo']   = $floor['FloorNo'];
        $array['CmptTypes'] = $this->get_sentplan_cmpttype($tradeid,$buildingno,$floor['FloorNo']);
        array_push($arr,$array);
      }
      return $arr;
    }
    
    public function getSentPlanFloorNoPage($tradeid,$buildingno,$floorno,$current=0,$pagenum=15)
    {
      $arr=array();
      $_wh="where TradeId=$tradeid and BuildingNo=$buildingno";
      if(!empty($floorno)){
        $_wh.=" and FloorNo=$floorno";
      }
      $sql="SELECT count(*) cot FROM( 
               SELECT FloorNo FROM trade_drawing  $_wh  
              GROUP BY TradeId,BuildingNo,FloorNo 
              order by   cast(FloorNo as SIGNED)) T";
      $recordcount=empty($this->row($sql))?0:$this->row($sql)['cot'];
      $pagesize=$recordcount%$pagenum==0?$recordcount/$pagenum:(floor($recordcount/$pagenum)+1);
      $arr['current']     = $current+1;
      $arr['pagenum']     = $pagenum;
      $arr['recordcount'] = $recordcount;
      $arr['pagesize']    =$pagesize;
      return $arr;
    }


    public function get_sentplan_cmpttype($tradeid,$buildingno,$floorno){
      $arr=array();
      $_wh="WHERE a.TradeId=$tradeid and a.BuildingNo=$buildingno and a.FloorNo=$floorno";

      $sql="SELECT a.TradeId,a.BuildingNo,a.FloorNo,a.CmptType,a.CmptTypeId, 
                   IFNULL(MAX(b.DeliveryTime),'')  as DeliveryTime
              FROM  trade_drawing a  LEFT JOIN replenish_shipments_time b 
                ON  a.TradeId=b.TradeId and a.BuildingNo=b.BuildingNo  and a.FloorNo=b.FloorNo and a.CmptTypeId=b.CmptTypeId
             $_wh
          GROUP By a.TradeId,a.BuildingNo,a.FloorNo,a.CmptType,a.CmptTypeId";
      $cmpttypes=$this->result($sql);
      foreach ($cmpttypes as $key => $type) {
        $cmpttypeid= $type['CmptTypeId'];
        // $ArrayQty=$this->getOrderAndShipQty($tradeid,$buildingno,$floorno,$cmpttypeid);
        // $array=array();
        // $array['CmptType']     = $type['CmptType'];
        // $array['CmptTypeId']   = $type['CmptTypeId'];
        // $array['DeliveryTime'] = $type['DeliveryTime'];
        // $array['OrderQty']     = $ArrayQty['OrderQty'];
        // $array['ShipQty']      = $ArrayQty['ShipQty'];
        // $array['NoneShipQty']  = $ArrayQty['NoneShipQty'];
        // $array['ShipDate']     = $ArrayQty['ShipDate'];
        // array_push($arr,$array); 
      }
      return $arr;
    }


    public function setTradeTimeExt($tradeid,$buildingno,$floorno,$cmpttypeid,$requestdatetime,$reqid,$reqname,$openid){
       $sql="DELETE FROM replenish_trade_request 
             where  TradeId=$tradeid   and buildingNo='$buildingno' 
                    and FloorNo='$floorno' and CmptTypeId=$cmpttypeid";
       $this->query($sql);
       $sql="INSERT Into replenish_trade_request(TradeId,buildingNo,FloorNo,CmptTypeId,
                                                 RequestDateTime,ReqId,ReqName,ReqDateTime,OpenID)
                  VALUES ($tradeid,'$buildingno','$floorno',$cmpttypeid,
                          '$requestdatetime',$reqid,'$reqname',now(),'$openid')";
       $rtnval=$this->query($sql);
       return $rtnval;
    }

    public function setTradeStateExt($tradeid,$buildingno,$floorno,$cmpttypeid,$state=1,$checkid=0,$checker='',$checkopenid=''){
      $sql="UPDATE replenish_trade_request SET status=$state,
                   CheckDate=now(),Checker='$checker',
                   CheckID=$checkid,CheckOpenID='$checkopenid' 
            WHERE TradeId=$tradeid and buildingNo='$buildingno' and FloorNo='$floorno' and CmptTypeId=$cmpttypeid";
      return $this->execute($sql);

    }

    public function setTradeStateExtByIds($statecode,$ids,$checkid=0,$checker='',$checkopenid=''){
      $sql="UPDATE replenish_trade_request SET status='$statecode',
                   CheckDate=now(),Checker='$checker',
                   CheckID='$checkid',CheckOpenID='$checkopenid' 
             WHERE FIND_IN_SET(Id,'$ids')";
      return $this->execute($sql);

    }

    public function getUserTableByOpenId($openid){
      $sql="select Id,uName from usertable where openid='$openid' limit 1";
      $usertable=$this->row($sql);
      return $usertable;
    } 

    public function getPMCTradeRequestInfoPageExt($tradeid,$buildingno,$floorno,$requestdatetime,$current=0,$pagenum=15){
      $array=array();
      $current=(empty($current) || $current===1)?0:($current-1);
      $start=$current*$pagenum;
      $_wh=" where a.TradeId=$tradeid ";
      if(!empty($buildingno)){
        $_wh.=" and a.BuildingNo=$buildingno";
      }
      if(!empty($floorno)){
        $_wh.=" and a.FloorNo=$floorno"; 
      }
      if(!empty($requestdatetime)){
         $_wh.=" and b.RequestDateTime='$requestdatetime'";
      }
      $sql="SELECT   a.TradeId,a.CmptTypeId,a.CmptType,a.BuildingNo,a.FloorNo,
                     IFNULL(b.RequestDateTime,'') as RequestDateTime,b.ReqId,b.ReqName,
                     b.Status,IFNULL(b.Checker,'') as Checker,
                     IFNULL(b.CheckDate,'') as CheckDate,b.Id as  ReplenishID,
                     CASE  b.Status 
                       WHEN 1 THEN  '未提交'
                       WHEN 2 THEN  '待审核'
                       WHEN 3 THEN '已审核'
                       ELSE '未知' 
                    END AS StatusName
               FROM   trade_drawing a inner join replenish_trade_request b 
                 ON  b.TradeId=a.TradeId and a.BuildingNo=b.BuildingNo 
                      and a.FloorNo=b.FloorNo and a.CmptTypeId=b.CmptTypeId
              $_wh
           GROUP BY a.TradeId,a.CmptTypeId,a.CmptType,a.BuildingNo,a.FloorNo
           ORDER By b.BuildingNo ASC,b.FloorNo ASC,b.RequestDateTime ASC 
                   limit $start,$pagenum";
      $array['data']=$this->result($sql);
      $sql="SELECT count(*) as cot FROM(
              SELECT   a.Id
               FROM   trade_drawing a inner join replenish_trade_request b 
                 ON  b.TradeId=a.TradeId and a.BuildingNo=b.BuildingNo 
                      and a.FloorNo=b.FloorNo and a.CmptTypeId=b.CmptTypeId
               $_wh
           GROUP BY a.TradeId,a.CmptTypeId,a.CmptType,a.BuildingNo,a.FloorNo) T";
      $recordcount=empty($this->row($sql))?0:$this->row($sql)['cot'];
      $pagesize=$recordcount%$pagenum==0?$recordcount/$pagenum:(floor($recordcount/$pagenum)+1);
      $array['recordcount'] = $recordcount;
      $array['pagesize']    = $pagesize;
      $array['current']     = $current;
      $array['pagenum']     = $pagenum;
      return $array;
    }

    public function setShipMentsTime($array=array()){
      $TradeId    = $array['TradeId'];
      $BuildingNo = $array['BuildingNo'];
      $FloorNo    = $array['FloorNo'];
      $CmptTypeId = $array['CmptTypeId'];
      $delsql="DELETE FROM replenish_shipments_time 
                     WHERE TradeId='$TradeId' AND  BuildingNo= '$BuildingNo'
                     AND FloorNo='$FloorNo' AND CmptTypeId='$CmptTypeId'";
      $this->execute($delsql);
      $sql="INSERT into replenish_shipments_time 
                    (
                      TradeId,
                      BuildingNo,
                      FloorNo,
                      CmptTypeId,
                      DeliveryTime,
                      CreateBy,
                      CreateUseId,
                      CreateDateTime
                    ) VALUES ( 
                      '$TradeId',
                      '$BuildingNo',
                      '$FloorNo',
                      '$CmptTypeId',
                      '".$array['DeliveryDate']."',
                      '".$array['CreateBy']."',
                      ".$array['CreateUseId'].",
                      now()
                    )";
      return $this->execute($sql);
    } 

    public function getOrderAndShipQty($tradeid,$buildingno,$floorno,$cmpttypeid){
      $_where="  c.TradeId=$tradeid and c.BuildingNo=$buildingno 
             and c.FloorNo=$floorno and c.CmptTypeId=$cmpttypeid";
      $sql="SELECT Ta.TradeId,Ta.BuildingNo,Ta.FloorNo,Ta.CmptTypeId ,IFNULL(Ta.OrderQty,0) as OrderQty,
                   IFNULL(Tb.ShipQty,0) as ShipQty,
                   (IFNULL(Ta.OrderQty,0) - IFNULL(Tb.ShipQty,0)) as NoneShipQty, IFNULL(Tb.ShipModified,0) as ShipDate 
            from (
                  SELECT sum(a.Qty) as OrderQty,c.TradeId,c.BuildingNo,c.FloorNo,c.CmptTypeId from yw1_ordersheet a 
              inner join productdata b on a.ProductId=b.ProductId 
              inner join trade_drawing c on c.ProdcutCname=b.cName
                   where $_where
                GROUP BY c.TradeId,c.BuildingNo,c.FloorNo,c.CmptTypeId) Ta 
            left join 
            (
              SELECT   sum(a.Qty)  as ShipQty, 
                       max(a.modified)     as ShipModified ,
                       c.TradeId,c.BuildingNo,c.FloorNo,c.CmptTypeId  
                    from ch1_shipsheet a 
              inner join ch1_shipmain g on g.Id=a.Mid
              inner join productdata b on a.ProductId=b.ProductId 
              inner join trade_drawing c on c.ProdcutCname=b.cName
                   where $_where and a.Estate=1
              GROUP BY c.TradeId,c.BuildingNo,c.FloorNo,c.CmptTypeId
            ) Tb  on Ta.TradeId=Tb.TradeId and Ta.BuildingNo=Tb.BuildingNo and Ta.FloorNo=Ta.FloorNo and Ta.CmptTypeId=Ta.CmptTypeId";
        return $this->row($sql);
    }

    public function setReplenishTransportRecord($array){
      $typeid            = $array['TypeID'];
      $address           = $array['Address'];
      $createdatetime    = $array['CreateDateTime'];
      $createby          = $array['CreateBy'];
      $createuserid      = $array['CreateUserID'];
      $groupusername     = $array['GroupUserName'];
      $groupuserid       = $array['GroupUserID'];
      $col01             = $array['Col01'];
      $col02             = $array['Col02'];
      $carno             = $array['CarNo'];
      $carnumber         = $array['CarNumber'];
      $tradeId           = $array['TradeId']; 
      $buildingno        = $array['BuildingNo']; 
      $floorno           = $array['FloorNo']; 
      $sql="  DELETE FROM replenish_transport_record 
              where TradeId=$tradeId and BuildingNo=$buildingno and FloorNo='$floorno'
                    and CarNo='$carno' and CarNumber='$carnumber' and TypeID=$typeid";
      $this->execute($sql);
      $sql="INSERT INTO replenish_transport_record
                        (TypeID,Address,CreateDateTime,CreateBy,CreateUserID,
                         GroupUserName,GroupUserID,Col01,Col02,CarNo,CarNumber,TradeId,BuildingNo,FloorNo)
                 VALUES ($typeid,'$address','$createdatetime','$createby',$createuserid,
                         '$groupusername',$groupuserid,'$col01','$col02',
                         '$carno','$carnumber',$tradeId,$buildingno,'$floorno')";
     
      return $this->execute($sql);
    }
    
    public function getReplenishTransportRecordJson($carnumber){
       if(empty($carnumber)){
          $this->StatusCode(101,'','参数不能为空');
       }
       $ships=$this->getReplenishShipAndTransportRecord($carnumber);
       if(empty($ships)){
          $this->StatusCode(102,'','查询为空');
       }
       $TradeId    = $ships['TradeId'];
       $BuildingNo = $ships['BuildingNo'];
       $FloorNo    = $ships['FloorNo'];
       $CarNo      = $ships['CarNo'];
       $CarNumber  = $ships['CarNumber'];
       $ships['Records']=$this->getReplenishTransportRecord($TradeId,$BuildingNo,$FloorNo,$CarNo,$CarNumber);
       $this->StatusCode(0,$ships);
    }

    public function getReplenishShipAndTransportRecord($CarNumber){
      $sql="SELECT CarNumber,CarNo,TradeId,BuildingNo,FloorNo,Forshort, 
                   REPLACE(group_concat(concat(BuildingNo,'#',NameRule,Qty,'块')),',','+') as ShipInfo 
              FROM (  
                          SELECT  a.CarNumber,a.CarNo,c.FloorNo,c.BuildingNo,TD.TradeId,sum(b.Qty) as Qty,
                                  TB.Forshort,c.TypeId,d.TypeName,d.NameRule
                            FROM  ch1_shipmain a 
                      INNER JOIN  ch1_shipsheet b on a.Id=b.Mid 
                      INNER JOIN  productdata c on c.ProductId=b.ProductId
                      INNER JOIN  producttype d on d.TypeId=c.TypeId
                       LEFT JOIN  trade_drawing TD ON TD.ProdcutCname = c.cName 
                       LEFT JOIN  trade_object  TB ON TB.Id=TD.TradeId
                           WHERE  a.CarNumber='$CarNumber'
                        GROUP BY a.CarNumber,a.CarNo,c.FloorNo,c.BuildingNo,c.TypeId,TB.Forshort,d.NameRule) T
          GROUP BY CarNumber,CarNo,TradeId,BuildingNo,FloorNo,Forshort"; 
      return $this->row($sql);
    }


    public function getReplenishTransportRecord($TradeId,$BuildingNo,$FloorNo,$CarNo,$CarNumber){
       $sql="SELECT Id, CreateBy,CreateUserID,CreateDateTime,
                      GroupUserID,GroupUserName,TypeID,Address,Col01,Col02,
                      IFNULL(CheckerBy,'') As CheckerBy,
                      IFNULL(CheckerUserID,0) As CheckerUserID,
                      IFNULL(CheckGroupID,0) As  CheckGroupID,
                      IFNULL(CheckGroupName,'') As CheckGroupName,CheckDateTime 
               FROM  replenish_transport_record
              WHERE  TradeId=$TradeId AND BuildingNo=$BuildingNo AND FloorNo='$FloorNo'
                     AND CarNo='$CarNo' AND CarNumber='$CarNumber' order by TypeID ASC ";
       return $this->result($sql);
    }


    public function getShipsAndReplenishTransportRecordPc($CarNumber,$CarNo,$Date,$TradeId){
        if(empty($TradeId)){
           $this->StatusCode('101','','TradeId参数为空');
        }
        $WH = empty($TradeId)?"":"  TD.TradeId='$TradeId'";
        $WH.= empty($CarNumber)?"":" and a.CarNumber like '$CarNumber%'";
        $WH.= empty($CarNo)?"":" and a.CarNo='$CarNo'";
        $WH.= empty($Date)?"":" and a.Date='$Date'";
       
        $sql="    SELECT  a.CarNumber,a.CarNo,c.FloorNo,c.BuildingNo,TD.TradeId,sum(b.Qty) as Qty,TB.Forshort,
                 c.TypeId,d.TypeName,
              IFNULL(RR01.CreateDateTime,'') as  InFactoryCreateDateTime,IFNULL(RR01.CreateUserID,0) as InFactoryCreateUserID,
                 IFNULL(RR01.CreateBy,'') as InFactoryCreateBy,IFNULL(RR01.Address,'') as InAddress,
              IFNULL(RR02.CreateDateTime,'') as  OutFactoryCreateDateTime,IFNULL(RR02.CreateUserID,0) as OutFactoryCreateUserID,
                IFNULL(RR02.CreateBy,'') as OutFactoryCreateBy,IFNULL(RR02.Col01,'') as OutAttachment,IFNULL(RR02.Address,'') as OutAddress,
              IFNULL(RR03.CreateDateTime,'') as  WorkFactoryCreateDateTime,IFNULL(RR03.CreateUserID,0) as WorkFactoryCreateUserID,
                 IFNULL(RR03.CreateBy,'') as WorkFactoryCreateBy,IFNULL(RR03.Col01,'') as WorkInfo,IFNULL(RR03.Address,'') as WorkAddress,
             IFNULL(RR04.CreateDateTime,'') as  LeftFactoryCreateDateTime,IFNULL(RR04.CreateUserID,0) as LeftFactoryCreateUserID,
                 IFNULL(RR04.CreateBy,'') as LeftFactoryCreateBy,IFNULL(RR04.Col01,'') as LeftInfo,IFNULL(RR04.Address,'') as LeftAddress,
             IFNULL(RR05.CreateDateTime,'') as  EscortFactoryCreateDateTime,IFNULL(RR05.CreateUserID,0) as EscortFactoryCreateUserID,
                 IFNULL(RR05.CreateBy,'') as EscortFactoryCreateBy,IFNULL(RR05.Col01,'') as EscortMessage,IFNULL(RR05.Address,'') as EscortAddress
             
                  FROM  ch1_shipmain a 
            INNER JOIN  ch1_shipsheet b on a.Id=b.Mid 
            INNER JOIN  productdata c on c.ProductId=b.ProductId
            INNER JOIN  producttype d on d.TypeId=c.TypeId
            LEFT JOIN trade_drawing TD ON TD.ProdcutCname = c.cName 
            LEFT JOIN trade_object  TB ON TB.Id=TD.TradeId
            LEFT JOIN replenish_transport_record RR01 
                      ON  RR01.TradeId=TD.TradeId  AND RR01.BuildingNo=TD.BuildingNo 
                      AND RR01.FloorNo=TD.FloorNo  AND RR01.CarNo=a.CarNo AND RR01.CarNumber=a.CarNumber and RR01.TypeID=1
            LEFT JOIN replenish_transport_record RR02 
                      ON  RR02.TradeId=TD.TradeId  AND RR02.BuildingNo=TD.BuildingNo 
                      AND RR02.FloorNo=TD.FloorNo  AND RR02.CarNo=a.CarNo AND RR02.CarNumber=a.CarNumber and RR02.TypeID=2
            LEFT JOIN replenish_transport_record RR03 
                      ON  RR03.TradeId=TD.TradeId  AND RR03.BuildingNo=TD.BuildingNo 
                      AND RR03.FloorNo=TD.FloorNo  AND RR03.CarNo=a.CarNo AND RR03.CarNumber=a.CarNumber and RR03.TypeID=3
            LEFT JOIN replenish_transport_record RR04 
                      ON  RR04.TradeId=TD.TradeId  AND RR04.BuildingNo=TD.BuildingNo 
                      AND RR04.FloorNo=TD.FloorNo  AND RR04.CarNo=a.CarNo AND RR04.CarNumber=a.CarNumber and RR04.TypeID=4
            LEFT JOIN replenish_transport_record RR05 
                      ON  RR05.TradeId=TD.TradeId  AND RR05.BuildingNo=TD.BuildingNo 
                      AND RR05.FloorNo=TD.FloorNo  AND RR05.CarNo=a.CarNo AND RR05.CarNumber=a.CarNumber and RR05.TypeID=5
            where $WH
            GROUP BY a.CarNumber,a.CarNo,c.FloorNo,c.BuildingNo,c.TypeId,TB.Forshort";
        $ships=$this->result($sql);
        $this->StatusCode(0,$ships);
    }
  
    public function Cardata(){
      $sql="SELECT Id,CarNo,Maintainer FROM cardata";
      $cars=$this->result($sql);
      $this->StatusCode(0,$cars);
    } 

    public function getUserInfo($openid='op_TywzfD8ig7rVjvVLa7ayMbO-E'){
      if(empty($openid)){
        $this->StatusCode(101,'','openid参数非法');
      }
      $WH=empty($openid)?"":"WHERE U.openid='$openid'";

      $sql="   SELECT U.Id as UId,U.uName as UName,S.Name as TrueName,
                      BD.Id as GroupId, 
                      BD.Name as GroupName,U.openid as OpenId,
                      A.name as RoleName,A.id as RoleId   FROM UserTable U
           LEFT JOIN staffmain S ON S.Number = U.Number
           LEFT JOIN companys_group C ON C.cSign = S.cSign
           LEFT JOIN branchdata BD ON BD.Id = S.BranchId
           LEFT JOIN ac_roles A ON A.id = U.roleId
          $WH";
      return $this->row($sql);

    }


    public function getWxCode(){
      $sql="SELECT max(CASE CodeName WHEN 'AppId' THEN CodeValue ELSE '' END) As AppId,
                   max(CASE CodeName WHEN 'AppSecret' THEN CodeValue ELSE '' END) As AppSecret,
                   max(CASE CodeName WHEN 'access_token' THEN CodeValue ELSE '' END) As AccessToken,
                   max(CASE CodeName WHEN 'EncodingAESKey' THEN CodeValue ELSE '' END ) As EncodingAESKey
            FROM  wx_code";
      return $this->row($sql);
    }


    public function WXInit($redirectURI,$errorURL){
      $TST='develop';
      if($TST=='develop'){
        $appId="wx39190f186cd2c4ff";
        $appSecret="01b01c021b008c0d23a7ea0d89976d43";
      }else{
        $wxApp=$this->getWxCode();
        $appId=$wxApp['AppId'];
        $appSecret=$wxApp['AppSecret'];
      }
      $redirectURI='http://'.$_SERVER['SERVER_NAME'].$redirectURI;
      if(isset($_GET['code'])){
         $code=$_GET['code'];
         $tokenURI="https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appId&secret=$appSecret&code=$code&grant_type=authorization_code";
         $access=json_decode(file_get_contents($tokenURI));
         if(!isset($access->openid)){
            header("Location:$errorURL");
            die();
         }
         $openid=$access->openid;
         $userInfo=$this->getUserInfo($openid);
         if(empty($userInfo)){
            header('Location:noright.php');
            die();
         }
         return $userInfo;
      }else{
        $authorizeURI="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appId."&redirect_uri=".urlencode($redirectURI)."&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
        header("Location: $authorizeURI");
        die();
      }
    }


    public function getCarNumber($carNum){
      $sql="SELECT CarNumber As label,CarNumber As value FROM ch1_shipmain 
            WHERE CarNumber like '$carNum%' GROUP BY CarNumber  limit  15";
      return $this->result($sql);
    }

    public function getCarNo($carnumber){
      $sql="SELECT CarNo,CarNumber FROM ch1_shipmain
             WHERE CarNumber ='$carnumber'
          GROUP BY CarNo,CarNumber
         ";
      return $this->result($sql);
    }


    

    public function setReviewedBy($checkerBy,$checkerUserID,$checkGroupID,$checkGroupName,$id){
       $sql="  UPDATE replenish_transport_record   
                  SET CheckerBy='$checkerBy',
                      CheckerUserID=$checkerUserID,
                      CheckGroupID=$checkGroupID,
                      CheckGroupName='$checkGroupName',
                      CheckDateTime=now()
                WHERE Id=$id";
       return $this->execute($sql);
    }

    public function getOrderSheetMainExt($tradeid,$buildingno,$floorno){
        $wh=" c.TradeId=$tradeid and c.BuildingNo=$buildingno";
        $wh.=empty($floorno)?'':" and c.FloorNo=$floorno";
        $sql=" SELECT c.TradeId,c.BuildingNo,c.FloorNo,c.CmptTypeId,
                      MAX(c.ModifiedDateTime) as ModifiedDateTime,
                      SUM(c.SendQty) as SendQty,
                      SUM(c.NoneSendQty) as NoneSendQty , 
                      IFNULL(d.DeliveryTime,'') as DeliveryTime
                 FROM (
                          SELECT c.TradeId,c.BuildingNo,c.FloorNo,c.CmptTypeId, a.Estate,
                                 SUM(a.Qty) as Qty,
                                  (CASE when a.Estate=0 then IFNULL(MAX(a.modified),'') else '' end) as ModifiedDateTime,
                                 (CASE when a.Estate=0 then SUM(a.Qty) else 0 end)  SendQty,
                                 (CASE when a.Estate=1 then SUM(a.Qty) else 0 end) as NoneSendQty
                            FROM yw1_ordersheet a 
                      inner join productdata b on a.ProductId=b.ProductId 
                      inner join trade_drawing c on c.ProdcutCname=b.cName
                           where $wh
                        GROUP BY  c.TradeId,c.BuildingNo,c.FloorNo,c.CmptTypeId, a.Estate) AS c
            LEFT JOIN replenish_shipments_time d on d.TradeId=c.TradeId
                                                           and d.BuildingNo=c.BuildingNo 
                                                           and d.FloorNo=c.FloorNo
                                                           and d.CmptTypeId=c.CmptTypeId
             group by c.TradeId,c.BuildingNo,c.FloorNo,c.CmptTypeId
             ";
         var_dump($sql);
        $orderSheets = $this->result($sql);
        return $orderSheets;
    }


    public function getCmptTypeExt($tradeid,$buildingno,$floorno){
       $where='';
       $where.=empty($tradeid)    ? '':" c.TradeId='$tradeid'";
       $where.=empty($buildingno) ? '':" and c.BuildingNo='$buildingno'";
       $where.=empty($floorno)    ? '':" and c.FloorNo='$floorno'";
       $sql="SELECT c.CmptType,c.CmptTypeId  
               FROM  trade_drawing c
              WHERE $where 
           GROUP BY c.CmptType,c.CmptTypeId";
       $cmpttypes = $this->result($sql);
       return $cmpttypes;
    }

    public function combineCmptInfo($orders){
       $arr=array();
       foreach($orders as $order){
          $floorNo=(int)$order['FloorNo'];
          if(!in_array($floorNo,$arr)){
              array_push($arr, $floorNo);
          }
       }
     
       return $arr;
    }

    public function getCarNoByTradeID($tradeid){
        $sql="SELECT CarNo FROM  replenish_transport_record 
               WHERE TradeId=$tradeid
            group by CarNo";
        return $this->result($sql);
    } 


    public function getCarNumberByCarNo($tradeid,$carno){
       $sql="SELECT CarNumber FROM  replenish_transport_record 
               WHERE TradeId=$tradeid and CarNo='$carno'
            group by CarNumber";
       return $this->result($sql);
    }
  }