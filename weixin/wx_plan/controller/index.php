<?php
   session_start();
   include '../config/dbconnect.php';
   include '../config/common.model.php';
   $common=new common();
   header('Content-Type:application/json;charset=utf-8');
   header("Access-Control-Allow-Origin: *");
   header("access-control-allow-methods: GET, POST");
   $action =isset($_POST['action'])?$_POST['action']:'';
   switch ($action) {
   	case 'get_user':
   		   $users=$common->get_user_result();
   		break;
   	case 'save':
   	      $userid=$common->save_user();
   	    break;
   	case 'ckLogin':  //用户登陆
   	       if(!isset($_POST['name']))
   	       {
                $common->StatusCode('101','','用户名不能为空');
                die();
   	       }
           $name=$_POST['name'];
           if(!isset($_POST['password'])){
                $common->StatusCode('102','','密码不能为空');
                die();
           }
           $password=$_POST['password'];
   	       $userinfo=$common->get_userbynameandpwd($name,$password);
   	       if(empty($userinfo)){
   	       	  $common->StatusCode('103','','用户名错误或已禁用');
   	       	  die();
   	       }
   	       if($userinfo['password']!=$password){
   	       	 $common->StatusCode('104','','密码错误');
   	         die();
   	       }
           $_SESSION['UID']          = $userinfo['id'];
           $_SESSION['TrueName']     = $userinfo['truename'];
           $common->StatusCode('0',$userinfo,'登陆成功');
   	      break;
    case 'ckLoginExt':
          if(!isset($_POST['name']))
           {
              $common->StatusCode('101','','用户名不能为空');
           }
           $name=$_POST['name'];
           if(!isset($_POST['password'])){
              $common->StatusCode('102','','密码不能为空');
           }
           $password=$_POST['password'];
           $common->getUseByNameAndPwdExt($name,$password);
         break;
   	case 'tradeObject': //获取项目
   	      $objects=$common->get_trade_object();
   	      if(empty($objects)){
   	      	$common->StatusCode('101','','查询为空');
   	      	die();
   	      }
          $common->StatusCode('0',$objects,'查询成功');
   	    break;
    case 'searchTradeById':
          if(!isset($_POST['objectid'])){
            $common->StatusCode('101','','参数异常');
            die();
          }
          $objid=$_POST['objectid'];
          $objects=$common->get_tradebyid($objid);
          $common->StatusCode('0',$objects);

        break;
    case 'getTradeByNumber':
          if(!isset($_POST['number'])){
            $common->StatusCode('101','','参数异常');
          }
          $number=$_POST['number'];
          $objects=$common->getTradeByNumber($number);
          $common->StatusCode('0',$objects);
      break;
    case 'searchTradeObject':
          if(!isset($_POST['forshort'])){
            $common->StatusCode('101','','项目名不能为空');
            die();
          }
          $forshort=urldecode($_POST['forshort']);
          $objectes=$common->get_tradebylikename($forshort);
          $common->StatusCode('0',$objectes);
       break;
   	case 'buildings':
          if(!isset($_POST['tradeid'])){
            $common->StatusCode('101','','参数异常');
          }

          $tradeid=$_POST['tradeid'];
          $buildings=$common->get_buildings($tradeid);
          $buildings=$common->StatusCode(0,$buildings);
          break;
    case 'floor':
        if(!isset($_POST['tradeid'])){
          $common->StatusCode('101','','参数异常');
        }  
        $tradeid=$_POST['tradeid'];
        if(!isset($_POST['buildid'])){
          $common->StatusCode('101','','参数异常');
        }
        $buildid=$_POST['buildid'];
        $floors=$common->get_buildings_floor($tradeid,$buildid);
        $buildings=$common->StatusCode(0,$floors);
        break;
    case 'tradeInfo':
       if(!isset($_POST['tradeid'])){
          $common->StatusCode('101','','参数异常');
          die();
        }
        $tradeid=$_POST['tradeid'];

        if(!isset($_POST['buildingno'])){
          $common->StatusCode('101','','参数异常');
          die();
        }
        $buildingno=$_POST['buildingno'];
   
        if(!isset($_POST['floorno'])){
          $common->StatusCode('101','','参数异常');
          die();
        }
        $floorno=$_POST['floorno'];
        $tradeInfo=$common->get_trade_info($tradeid,$buildingno,$floorno);
        $buildings=$common->StatusCode(0,$tradeInfo);
        break;
    case 'getTradeInfoPageExt':
        if(!isset($_POST['tradeid'])){
          $common->StatusCode('101','','参数异常');
        }  
        $tradeid=$_POST['tradeid'];

        if(!isset($_POST['buildingno'])){
          $common->StatusCode('101','','参数异常');
        }
        $buildingno=$_POST['buildingno'];
   
        if(!isset($_POST['floorno'])){
          $common->StatusCode('101','','参数异常');
        }
        $floorno=$_POST['floorno'];
        $current=isset($_POST['current'])?$_POST['current']:0;
        $pagenum=isset($_POST['pagenum'])?$_POST['pagenum']:10;
        $tradeInfo=$common->getTradeInfoPageExt($tradeid,$buildingno,$floorno,$current,$pagenum);
        $common->StatusCode(0,$tradeInfo);
        break;
    case 'setTradeTime':
        if(!isset($_POST['drawingid'])){
          $common->StatusCode('101','','参数异常');
          die();
        }
        $drawingid=$_POST['drawingid'];
        if(!isset($_POST['requestdatetime'])){
          $common->StatusCode('101','','参数异常');
          die();
        }
        $requestdatetime=$_POST['requestdatetime'];

        if(!isset($_POST['reqid'])){
          $common->StatusCode('101','','参数异常');
          die();
        }
        $reqid=$_POST['reqid'];

        if(!isset($_POST['reqname'])){
          $common->StatusCode('101','','参数异常');
          die();
        }
        $reqname=$_POST['reqname'];
        $openid= isset($_POST['openid'])?$_POST['openid']:'';
        $flag=$common->set_trade_time($drawingid,$requestdatetime,$reqid, $reqname,$openid);
        if($flag===-1){
          $common->StatusCode('102','','时间设置失败');
          die();
        }else{
          $common->StatusCode('0','');
          die();
        }
      break;
    case 'setCommitPMC':
        if(!isset($_POST['drawingid'])){
          $common->StatusCode('101','','参数异常');
          die();
        }
        $drawingid=$_POST['drawingid'];
        $rtv=$common->set_tradeCommit($drawingid,1);
        if(empty($rtv)){
          $common->StatusCode('102','','提交PMC失败');
          die();
        }else{
          $common->StatusCode('0','');
          die();
        }
      break;
    case 'searchPCTradeInfo':
        if(!isset($_POST['tradeid'])){
          $common->StatusCode('101','','参数异常');
          die();
        }
        $tradeid=$_POST['tradeid'];
        $buildingno=isset($_POST['buildingno'])?$_POST['buildingno']:'';
        $floorno=isset($_POST['floorno'])?$_POST['floorno']:'';
        $requestdatetime=isset($_POST['requestdatetime'])?$_POST['requestdatetime']:'';
        $tradeInfo=$common->get_pctrade_info($tradeid,$buildingno,$floorno,$requestdatetime);
        $buildings=$common->StatusCode(0,$tradeInfo);
        break;
    case 'getTradeSentPlan':
         $tradeid    = isset($_POST['objectid'])?$_POST['objectid']:'0';
         $buildingno = isset($_POST['buildingno'])?$_POST['buildingno']:'0';
         $floorno    = isset($_POST['floorno'])?$_POST['floorno']:'0';
         $current    = isset($_POST['current'])?$_POST['current']:'0';
         $pagesize   = isset($_POST['pagesize'])?$_POST['pagesize']:'50';
         $common->get_tradesent_plan($tradeid,$buildingno,$floorno,$current,$pagesize);
        break;
    case 'setTradeTimeExt':
       $uid      =isset($_SESSION['UID'])?$_SESSION['UID']:0;
       $truename =isset($_SESSION['TrueName'])?$_SESSION['TrueName']:0;
       if(!isset($_POST['tradeid'])){
          $common->StatusCode('101','','tradeid参数异常');
       }
       $tradeid=$_POST['tradeid'];
       
       if(!isset($_POST['buildingno'])){
         $common->StatusCode('101','','buildingno参数异常');
       }
       $buildingno=$_POST['buildingno'];

       if(!isset($_POST['floorno'])){
         $common->StatusCode('101','','floorno参数异常');
       }
       $floorno=$_POST['floorno'];

       if(!isset($_POST['cmpttypeid'])){
         $common->StatusCode('101','','cmpttypeid参数异常');
       }
       $cmpttypeid=$_POST['cmpttypeid'];
       $requestdatetime=isset($_POST['requestdatetime'])?$_POST['requestdatetime']:date("Y-m-d");
       $reqid   = isset($_POST['reqid'])  ?$_POST['reqid']:$uid;
       $reqname = isset($_POST['reqname'])?$_POST['reqname']:$truename;
       $openid  = isset($_POST['openid']) ?$_POST['openid']:'';
       $flag=$common->setTradeTimeExt($tradeid,$buildingno,$floorno,$cmpttypeid,$requestdatetime,$reqid,$reqname,$openid);
       if($flag===-1){
          $common->StatusCode('102','','时间设置失败');
       }else{
          $common->StatusCode('0','');
       }
       break;
    case 'setTradeStateExt':

      if(!isset($_POST['tradeid'])){
        $common->StatusCode('101','','tradeid参数异常');
      }
      $tradeid=$_POST['tradeid'];
       
      if(!isset($_POST['buildingno'])){
        $common->StatusCode('101','','buildingno参数异常');
      }
      $buildingno=$_POST['buildingno'];

      if(!isset($_POST['floorno'])){
        $common->StatusCode('101','','floorno参数异常');
      }
      $floorno=$_POST['floorno'];

      if(!isset($_POST['cmpttypeid'])){
        $common->StatusCode('101','','cmpttypeid参数异常');
      }
      $cmpttypeid  = isset($_POST['cmpttypeid']) ?$_POST['cmpttypeid'] : 0;
      $checkid     = isset($_POST['checkid'])    ? $_POST['checkid']   : 0;
      $checker     = isset($_POST['checker'])    ? $_POST['checker']   :'';
      $checkopenid = isset($_POST['openid'])     ? $_POST['openid']    :''; 
      $state       = isset($_POST['state'])      ? $_POST['state']     : 1;
      $flag=$common->setTradeStateExt($tradeid,$buildingno,$floorno,$cmpttypeid,$state,$checkid,$checker,$checkopenid);
      if($flag===-1){
        $common->StatusCode('102','','时间设置失败');
      }else{
        $common->StatusCode('0','');
      }

      break;
    case 'setTradeStateByParamExt':
         if(!isset($_POST['param'])){
            $common->StatusCode('101','','param参数异常');
         }
         $param=$_POST['param'];
         $items=json_decode($param);
         foreach ($items as $item) {
            $tradeid    = $item->tradeid;
            $buildingno = $item->buildingno;
            $floorno    = $item->floorno;
            $cmpttypeid = $item->cmpttypeid;
            $state      = 2;
            $checkid    = 0;
            $checker    = '';
            $checkopenid= '';
            $common->setTradeStateExt($tradeid,$buildingno,$floorno,$cmpttypeid,$state,$checkid,$checker,$checkopenid);
         }
         $common->StatusCode('0','');
        break;
    case 'setTradeStateExtByIds':
        if(!isset($_POST['statecode'])){
          $common->StatusCode('101','','statecode参数异常');
        }
        $state=$_POST['statecode'];

        if(!isset($_POST['ids'])){
          $common->StatusCode('101','','ids参数异常');
        }
        $ids=$_POST['ids'];
        if(!isset($_POST['checkid'])){
          $common->StatusCode('101','','checkid参数异常');
        }
        $checkid=$_POST['checkid'];
        
        if(!isset($_POST['checker'])){
           $common->StatusCode('101','','checker参数异常');
        }
        $checker=$_POST['checker'];

        if(!isset($_POST['openid'])){
          $common->StatusCode('101','','openid参数异常');
        }
        $openid=$_POST['openid'];
        $flag=$common->setTradeStateExtByIds($state,$ids,$checkid,$checker,$openid);
        if($flag===-1){
           $common->StatusCode('102','','状态设置失败');
        }else{
           $common->StatusCode('0','');
        }

     break;
    case 'getPMCTradeRequestInfoPageExt':
        if(!isset($_POST['tradeid'])){
          $common->StatusCode('101','','参数异常');
        }
        $tradeid=$_POST['tradeid'];
        $buildingno=isset($_POST['buildingno'])?$_POST['buildingno']:'';
        $floorno=isset($_POST['floorno'])?$_POST['floorno']:'';
        $requestdatetime=isset($_POST['requestdatetime'])?$_POST['requestdatetime']:'';
        $current=isset($_POST['current'])?$_POST['current']:1;
        $pagenum=isset($_POST['pagenum'])?$_POST['pagenum']:15;

        $tradeInfo=$common->getPMCTradeRequestInfoPageExt($tradeid,$buildingno,$floorno,$requestdatetime,$current,$pagenum);
        $common->StatusCode(0,$tradeInfo);
      break;
    case 'getPMCTradeInfoPageExt':
        if(!isset($_POST['tradeid'])){
          $common->StatusCode('101','','参数异常');
        }
        $tradeid=$_POST['tradeid'];
        $buildingno=isset($_POST['buildingno'])?$_POST['buildingno']:'';
        $floorno=isset($_POST['floorno'])?$_POST['floorno']:'';
        $requestdatetime=isset($_POST['requestdatetime'])?$_POST['requestdatetime']:'';
        $current=isset($_POST['current'])?$_POST['current']:0;
        $pagenum=isset($_POST['pagenum'])?$_POST['pagenum']:15;
        $tradeInfo=$common->getPMCTradeRequestInfoPageExt($tradeid,$buildingno,$floorno,$requestdatetime,$current,$pagenum);
        $common->StatusCode(0,$tradeInfo);
      break;
    case 'setShipMentsTime':
        $arr=array();
        $checkopenid     = isset($_SESSION["openid"])?$_SESSION["openid"]:'';
        $checkid    = 0;
        $checker    = '';
        if(!empty($openid)){
          $usertable=$common->getUserTableByOpenId($openid);
          $checkid = $usertable['Id'];
          $checker = $usertable['uName'];
        }
        if(!isset($_POST['TradeId'])){
          $common->StatusCode('101','','TradeId参数异常');
        }
        $arr['TradeId']=$_POST['TradeId'];

        if(!isset($_POST['BuildingNo'])){
          $common->StatusCode('101','','BuildingNo参数异常');
        }
        $arr['BuildingNo']=$_POST['BuildingNo'];
        
        if(!isset($_POST['FloorNo'])){
          $common->StatusCode('101','','FloorNo参数异常');
        }
        $arr['FloorNo']=$_POST['FloorNo'];
        
        if(!isset($_POST['CmptTypeId']))
        {
          $common->StatusCode('101','','CmptTypeId参数异常');
        }
        $arr['CmptTypeId']=$_POST['CmptTypeId'];
         
        if(!isset($_POST['DeliveryDate']))
        {
          $common->StatusCode('101','','DeliveryDate参数异常');
        }
        $arr['DeliveryDate'] = $_POST['DeliveryDate'];

        $arr['CreateBy']     = $checker;
        $arr['CreateUseId']  = $checkid;
        $flag = $common->setShipMentsTime($arr);
        if($flag===-1){
          $common->StatusCode('102','','时间设置失败');
        }else{
          $common->StatusCode('0','');
        }
      break;
    case 'getReplenishTransportRecord':
        $carnumber=isset($_POST['carnumber'])?$_POST['carnumber']:0;
        $common->getReplenishTransportRecordJson($carnumber);
      break;
    case 'setReplenishTransportRecord':

       $openid     = isset($_SESSION["openid"])?$_SESSION["openid"]:'';
       $array=array();
       $array['TypeID']         = isset($_POST['typeid'])?$_POST['typeid']:0;
       $array['Address']        = isset($_POST['address'])?$_POST['address']:'';
       $array['CreateDateTime'] = isset($_POST['createdatetime'])?$_POST['createdatetime']:0;
       $array['CreateBy']       = isset($_POST['createby'])?$_POST['createby']:'';
       $array['CreateUserID']   = isset($_POST['createuserid'])?$_POST['createuserid']:'';
       $array['GroupUserName']  = isset($_POST['groupusername'])?$_POST['groupusername']:'';
       $array['GroupUserID']    = isset($_POST['groupuserid'])?$_POST['groupuserid']:'';
       $array['Col01']          = isset($_POST['col01'])?$_POST['col01']:0;
       $array['Col02']          = isset($_POST['col02'])?$_POST['col02']:0;
       $array['CarNo']          = isset($_POST['carno'])?$_POST['carno']:0;
       $array['CarNumber']      = isset($_POST['carnumber'])?$_POST['carnumber']:0;
       $array['TradeId']        = isset($_POST['tradeId'])?$_POST['tradeId']:0;
       $array['BuildingNo']     = isset($_POST['buildingno'])?$_POST['buildingno']:0;
       $array['FloorNo']        = isset($_POST['floorno'])?$_POST['floorno']:0;
       $flag=$common->setReplenishTransportRecord($array);
       if($flag===-1){
          $common->StatusCode('102','','时间设置失败');
       }else{
          $common->StatusCode('0','');
       }
      break;
    case 'getShipsAndReplenishTransportRecordPc';
        $createDate = isset($_POST['date'])?$_POST['date']:'';
        $tradeId    = isset($_POST['tradeId'])?$_POST['tradeId']:'';
        $carNumber  = isset($_POST['carnumber'])?$_POST['carnumber']:'';
        $carNo      = isset($_POST['carno'])?$_POST['carno']:'';
        $common->getShipsAndReplenishTransportRecordPc($carNumber,$carNo,$createDate,$tradeId);
        break;
    case 'cardata':
         $common->Cardata();
        break;
    case 'userinfo':
         $openid=isset($_POST['openid'])?$_POST['openid']:'';
        break;
    case 'getopenid':
         $openid=isset($_SESSION['openid'])?$_SESSION['openid']:'';
         $common->StatusCode('0',$openid);
        break;
    case 'getwxcode':
          $wxcode=$common->getWxCode();
          $common->StatusCode('0',$wxcode);
        break;
    case 'getcarnumber':
         $keyword=isset($_POST['keyword'])?$_POST['keyword']:'';
         $carNums=$common->getCarNumber($keyword);
         $common->StatusCode('0',$carNums);
        break;
    case 'getcarno':
         $carno=isset($_POST['carno'])?$_POST['carno']:'';
         $carNumbers=$common->getCarNo($carno);
         $common->StatusCode('0',$carNumbers);
        break;
    case 'setReviewedBy';
        if(!isset($_POST['CheckerBy'])){
          $common->StatusCode('101','','CheckerBy参数异常');
        }
        $CheckerBy=$_POST['CheckerBy'];
        if(!isset($_POST['CheckerUserID'])){
          $common->StatusCode('101','','CheckerUserID参数异常');
        }
        $CheckerUserID=$_POST['CheckerUserID'];

        if(!isset($_POST['CheckGroupID'])){
          $common->StatusCode('101','','CheckGroupID参数异常');
        }
        $CheckGroupID=$_POST['CheckGroupID'];

        if(!isset($_POST['CheckGroupName'])){
          $common->StatusCode('101','','CheckGroupName参数异常');
        }
        $CheckGroupName=$_POST['CheckGroupName'];
        if(!isset($_POST['ID'])){
          $common->StatusCode('101','','ID参数异常');
        }
        $ID=$_POST['ID'];
        $rtv=$common->setReviewedBy($CheckerBy,$CheckerUserID,$CheckGroupID,$CheckGroupName,$ID);
        if($rtv===-1){
            $common->StatusCode('102','','用户设置失败');
        }else{
            $common->StatusCode('0','');
        }
        break;
    case 'getCarNoByTradeID':
         $tradeid=isset($_POST['tradeid'])?$_POST['tradeid']:'0';
         $carnoes=$common->getCarNoByTradeID($tradeid);
         $common->StatusCode('0',$carnoes);
        break;
    case 'getCarNumberByCarNo':
         $tradeid = isset($_POST['tradeid'])?$_POST['tradeid']:'0';
         $carno   = isset($_POST['carno'])?$_POST['carno']:'0';
         $carNumbers =$common->getCarNumberByCarNo($tradeid,$carno);
         $common->StatusCode(0,$carNumbers);
        break;
   	default:
   		 var_dump($_SESSION);
   		break;
   }
