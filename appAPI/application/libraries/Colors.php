<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  Colors{
/*
 系统颜色配置
*/
    var $colors=array(
			'white' => '#ffffff',
			'black' => '#3b3e41',
			'red'   => '#ff0000',
			'green' => '#00ff00',
			'blue'  => '#0000ff',
			'yellow'=> '#ff00ff',
			'orange'=> '#FF6633',
			'purple'=> '#800080',
			'qty'   => '#459fd1',
			'lightgreen' => '#00aa00',
			'lightgray'  => '#bbbbbb',
			'lightblue'  => '#ccffff',
			'yellowgreen'=> '#c3ff64',
			'bluefont'   => '#358fc1',
			'grayfont'   => '#727171',
			'lightgray2' => '#dddddd',
			'superdark'  => '#3b3e41',
			'ordergray'  => '#b0b5ba',
			'orderorange'  => '#f09300',
			'ordergreen'  => '#01be56',
			'weekred'     =>'#ff665f',
			'weekredborder'     =>'#ffa5a0',
			'daybordergray'    =>'#cfcfcf',
			'daygray'    =>'#9a9a9a',
			'todayyellow'=>'#ffff2e'
			
			
			
		);
		
    public function get_color($name){
	    return $this->colors[$name];
    }
}