<?php
// 矪瞶SQL琩高Α﹃
function GetSQLValue($value, $type) 
{
  	switch ($type) 
	{
    	case "text":
		    $value = ($value != "") ? "'" . htmlspecialchars($value, ENT_QUOTES) . "'" : "NULL";
      		break;
    	case "int":
	        $value = ($value != "") ? intval($value) : "NULL";
      		break;
	    case "double":
    	    $value = ($value != "") ? "'" . doubleval($value) . "'" : "NULL";
      		break;
		case "date":
		    $value = ($value != "") ? "'" . htmlspecialchars($value, ENT_QUOTES) . "'" : "NULL";
      		break;
  	}
  
  	return $value;
}

// 盢JavaScriptescapeㄧ计┮肚筁ㄓURL把计, 锣传Θ UTF-8
function uniDecode($str)
{
 	return preg_replace_callback("/%u[0-9A-Za-z]{4}/", toUtf8, $str);
}
// 盢JavaScriptescapeㄧ计┮肚筁ㄓURL把计, 锣传Θ UTF-8
function toUtf8($ar)
{
	foreach( $ar as $val)
  	{
    	$val = intval(substr($val,2), 16);

    	if ($val < 0x7F)
		{        
			// 0000-007F
	        $c .= chr($val);
    	}
		elseif ($val < 0x800) 
		{ 
	    	// 0080-0800
	        $c .= chr(0xC0 | ($val / 64));
    	    $c .= chr(0x80 | ($val % 64));
	    }
		else
		{   
		    // 0800-FFFF
        	$c .= chr(0xE0 | (($val / 64) / 64));
	        $c .= chr(0x80 | (($val / 64) % 64));
    	    $c .= chr(0x80 | ($val % 64));
	    }
  	}
	
  	return $c;
}
?>