<?php 
//审核项目
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
$mModuleName="audit";
$info=explode("|", $info);
switch($dModuleId){
	 case "main": //二级主页面
	      $LoginUserId=$LoginNumber;
	      $NextPage=$NextPage=="" || $NextPage==0?1:$NextPage;
	       include "audit/audit_item_read.php";
	     break;
	 case "list"://明细
	       switch($sModuleId){
		          case 1347:
		                  $Number=$info[0]; 
		                  $ReadBranchSign=1;
		                  include "audit/kq_qj_list.php";
		              break;
		        default:
		             break;
	       }
	     break;
	 case "update"://审核操作
	       $Log_Item="";
	       
	       $ReadAccessSign=5;
	        include "user_access.php";  //用户权限
	        if ($Login_uType==4 && $LoginNumber!='50019')  break; //外部人员帐号不能操作。
	        
	       $Id=$info[0];  $ActionId=$info[1];  $ReturnReasons=$info[2];
	       switch($sModuleId){
		          case 1347://请假
		                  include "audit/kq_qj_updated.php";
		              break;
		         case 1533://补休
		                  include "audit/kq_bx_updated.php";
		              break;
		         case 1107://行政费用
                          include "audit/hz_qk_updated.php";
                      break;
                case 1301://供应商税款
                          include "audit/gys_sk_updated.php";
                      break;
               case 1048://预付订金
                          include "audit/cg_dj_updated.php";
                      break;
              case 1371://其它收入
                         include "audit/cw_otherin_updated.php";
                      break;
              case 1047://采购请款
                          include "audit/cg_fk_updated.php";
                      break;
              case 1360://采购扣款
                          include "audit/cg_kk_updated.php";
                      break;
             case 1413://货款返利
                          include "audit/cg_fkhk_updated.php";
                      break;
              case 1046://异常采单
                          include "audit/cg_m_updated.php";
                      break;
               case 10460://补货单
                          include "audit/cg_replenish_updated.php";
                      break;
                case 1524://订单锁定
                         include "audit/order_lock_updated.php";
                      break;
               case 1525://配件锁定
                         include "audit/stuff_lock_updated.php";
                      break;
               case 1463://配件退换
                          include "audit/ck_th_updated.php";
                      break;
               case 1135://配件报废
                          include "audit/ck_bf_updated.php";
                      break;
               case 1268://配件名称
                          include "audit/stuff_name_updated.php";
                      break;
              case 1269://采单删除审核
              case 1591://统计配件异动审核
                          include "audit/cg_del_updated.php";
                      break;
               case 1361://拆分订单
                          include "audit/order_split_updated.php";
                      break;  
               case 1356://删除订单
                          include "audit/order_del_updated.php";
                      break; 
                case 1261://产品资料
                          include "audit/productdata_updated.php";
                      break;
               case 1381://客户退款
                          include "audit/cg_tkout_updated.php";
                      break;
              case 1197://样品邮费
                          include "audit/ch_samplemailing_updated.php";
                      break;  
               case 1520://助学补助
                          include "audit/childstudyfee_updated.php";
                      break;            
               case 1456://工伤费
                          include "audit/staff_hurtfee_updated.php";
                      break;  
              case 1409://体验费
                          include "audit/staff_tjfee_updated.php";
                      break; 
				
				case 1436: //免抵退
					include "audit/cw_mdtax_updated.php";
					break;
					
					case 1051: //杂费
					include "audit/cw_zf_updated.php";
					break;
					
				case 1620: //备品转入
					include "audit/ck_bp_update.php";
					break;
				case 1359:
					include "audit/cw_kk_updated.php";
				case 1161://社保公积金
                          include "audit/sbgjj_m_updated.php";
                      break; 	  
				case 1595://车辆费用审核
                          include "audit/carfee_m_updated.php";
                      break;  
					  
				case 1598://离职补助
                          include "audit/staff_subsidy_updated.php";
                      break;   
					  
				case 1108: //快递
					include "audit/ch_express_updated.php";
					break;                
		        default:
		             break;
	       }
	       if ($Log_Item!="")
	        {
		         $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
                 $IN_res=@mysql_query($IN_recode);
                 $jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR");
	        }
	     break;
	default:
	    break;
}
?>