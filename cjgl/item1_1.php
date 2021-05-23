<?php

switch($fromActionId){

	case 101: //入库
	      include "item1_1_bz.php";
	break;
	case 104: //dajian
        include "item1_1_jz.php";
        // include "item1_1_gx.php";
	break;
	case 103: //钢筋下料
	       include "item1_1_jz.php";
        //include  "item1_1_kl.php";
	break;

	case 102: //jiaoban
	    // include  "item1_1_kl.php";
        include "item1_1_jz.php";
	break;

	case 106: //浇捣养护
	    // include "item1_1_107.php";
        include "item1_1_jz.php";
	break;
}

?>
</form>
</body>
<div id='divShadow' class="divShadow" style="display:none;z-index:2;" onDblClick="closeMaskDiv()"></div>
<div id="divPageMask" class="divPageMask" style="display:none; background-color:rgba(0,0,0,0.6);"></div>
</html>
