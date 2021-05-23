<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  PageAction{
/*
 APP操作执行菜单显示
*/
    var $appcolor  ='#459FD1';
    var $lightgreen='#00AA00';
    
    var $actions=array(
			  'remark' => array('Name'=>'备注','Action'=>'remark','Color'=>'#459FD1'),
			'register' => array('Name'=>'登记','Action'=>'register','Color'=>'#00AA00'),
			   'stock' => array('Name'=>'备料','Action'=>'stock','Color'=>'#459FD1'),
			 'picking' => array('Name'=>'领料','Action'=>'picking','Color'=>'#459FD1'),
			 'picking_ws' => array('Name'=>'领料','Action'=>'picking_ws','Color'=>'#459FD1'),
			'shipping' => array('Name'=>'出货','Action'=>'shipping','Color'=>'#459FD1'),
			   'print' => array('Name'=>'打印','Action'=>'print','Color'=>'#459FD1'),
	       'printtask' => array('Name'=>'打印任务','Action'=>'printtask','Color'=>'#459FD1'),
	      'printlabel' => array('Name'=>'打印标签','Action'=>'printlabel','Color'=>'#459FD1'),   
			   'allot' => array('Name'=>'分配','Action'=>'allot','Color'=>'#459FD1'),
			 'stockin' => array('Name'=>'入库','Action'=>'stockin','Color'=>'#459FD1'),
			 'operate' => array('Name'=>'操作','Action'=>'operate','Color'=>'#459FD1'),
			  'arrive' => array('Name'=>'到达','Action'=>'arrive','Color'=>'#459FD1'),
			  'goback' => array('Name'=>'退回','Action'=>'goback','Color'=>'#FF0000'),
			  'change' => array('Name'=>'更改','Action'=>'change','Color'=>'#459FD1'),
		 'returnstock' => array('Name'=>'退料','Action'=>'returnstock','Color'=>'#FF0000'),
		    'qcreport' => array('Name'=>'品检报告','Action'=>'qcreport','Color'=>'#459FD1'),
	      'changeline' => array('Name'=>'更改拉线','Action'=>'changeline','Color'=>'#459FD1'),
			'settasks' => array('Name'=>'设置当前任务','Action'=>'settasks', 'Color'=>'#459FD1'),
		   'markemset' => array('Name'=>'喷码机设置',  'Action'=>'markemset','Color'=>'#459FD1'),
		   'signature' => array('Name'=>'签名','Action'=>'signature','Color'=>'#459FD1'),
		     'destroy' => array('Name'=>'销毁','Action'=>'destroy','Color'=>'#FF0000'),
		   'refreshtv' => array('Name'=>'刷新TV','Action'=>'refreshTV','Color'=>'#FF0000'),
			  'delete' => array('Name'=>'删除','Action'=>'delete','Color'=>'#FF0000'),
			  'occupy' => array('Name'=>'占用','Action'=>'occupy','Color'=>'#459FD1'),
			 'feeding' => array('Name'=>'补料','Action'=>'feeding','Color'=>'#FF0000'),
			  'affirm' => array('Name'=>'确认','Action'=>'affirm','Color'=>'#00AA00'),
			  'update' => array('Name'=>'更新','Action'=>'update','Color'=>'#459FD1'),
			  'reset' => array('Name'=>'重置纪录','Action'=>'reset','Color'=>'#459FD1'),
			  'alterweek'=> array('Name'=>'更改交期','Action'=>'alterweek','Color'=>'#459FD1'),
			  'backset'  => array('Name'=>'还原','Action'=>'backset','Color'=>'#459FD1'),
			  'resetstock'=>array('Name'=>'重置库存','Action'=>'resetstock','Color'=>'#459FD1'),
			  'split' => array('Name'=>'拆分','Action'=>'split','Color'=>'#459FD1')
		);
		
    public function get_actions($names){
        $actionArray=array();
        $nameArray=explode(',', $names);
       
        for($i=0,$counts=count($nameArray);$i<$counts;$i++){
	        $name=$nameArray[$i];
	        
	        if (array_key_exists($name, $this->actions)){
		        $actionArray[]=$this->actions[$name];
	        }
        }
	    return $actionArray;
    }
    
    
    public function get_action($name){
        
	        if (array_key_exists($name, $this->actions)){
		        return $this->actions[$name];
	        }
        
	    return null;
    }

}