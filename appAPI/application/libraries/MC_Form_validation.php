<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class  MC_Form_validation extends CI_Form_validation {

	    public function __construct()
	    {
	        parent::__construct();
	    }
     /*@param array $params 需要验证的参数
          @param array $rules  验证的规则数组
          @return string|Ambigous  */
       public  function  getValidator($params,$rules){
              if(count($params)==0)return "";
              if(count($rules)==0)return "";
              foreach($rules as $field=>$row){
                       if(array_key_exists ($row['field'],$params)){
                            $error = $this->executeValidator($row,explode('|',$row['rules']),$params[$row['field']]);
                            if($error!="")break;
                        }else{
                             $error=$row['field']."字段必须存在!"; 
                             break;
                           }
                 }
                 return $error;
     }

        private function executeValidator($row,$rules,$postdata = NULL,$cycles =0){
            // 循环验证规则文件
            foreach($rules as $rule){
                  if($rule =="required" &&  ($postdata=="" || $postdata==NULL)){
                           $error = $row['field']."的值不可以为空!"; break;
                       }
                  if($rule =="email" && valid_email($postdata)){
                           $error = $row['field']."不合乎Email规范!"; break;
                       }  
                  if($rule =="integer" && $this->integer($postdata)){
                           $error = $row['field']."必须为整数!"; break;
                       }  
                  if($rule =="numberic_no_zero" && $this->is_natural_no_zero($postdata)){
                           $error = $row['field']."必须为非零的正整数！!"; break;
                       }  
                  if($rule =="numberic" && is_numeric($postdata)){
                           $error = $row['field']."必须为数字!"; break;
                       }  

                $callback = FALSE;
                if(substr($rule,0,9)=='callback_'){
                     $rule = substr($rule,9); $callback=TRUE;
                      }
                 $param = false;
                 if(preg_match("/(.*?)\[(.*)\]/", $rule,$match)){
                        $rule = $match[1];
                       $param = $match[2];
                     }
                 if($callback===TRUE){
                       $error = $this->valid_function($row,$rule,$postdata,$param);
                  }else{
                        $error = $this->valid_function($row,$rule,$postdata,$param);
                   }
            }
           return $error;
}
        private function valid_function($row,$rule,$postdata){
              $rule = substr($rule,9); 
             $param = FALSE;
             if(preg_match("/(.*?)\[(.*)\]/", $rule,$match)){
                    $rule = $match[1];
                   $param = $match[2];
                 }
           if(! method_exists($this->CI,$rule))return "";
            $result = $this->CI->rule($postdata,$param);//调用类的方法
            return $this->result_format($row,$result);
        }

        private function result_format($row,$result){
            if(!$result)return $row['field']."验证失败!";
            return "";
           }
}