<?php
// © 2017 Regents of the University of Minnesota. All rights reserved.

#--------------------------------------------------------------------
# request_val
#--------------------------------------------------------------------
function request_val( $var ) {
        #return htmlspecialchars(strip_tags($_REQUEST[$var]));
        #return mysql_real_escape_string($_REQUEST[$var]);

	$val = $_REQUEST[$var];
	if( $val == "" ) {
		$val = $_POST[$var];
	}
	$val = strip_tags($val);
	$val = htmlspecialchars($val);
	$val = stripslashes($val);
	#$val = mysql_real_escape_string($val);

        return $val;
}

function request_int( $var ) {
	$val = request_val($var);

	if( preg_match("/^[0-9]$/", $val ) ) {
		return $val;
	} 

	return "";
}


