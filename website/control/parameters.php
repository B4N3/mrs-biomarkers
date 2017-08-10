<?php
// © 2017 Regents of the University of Minnesota. All rights reserved.
error_reporting( E_ALL ^ E_WARNING ^ E_NOTICE );

print "<h3>BRP parameters control page</h3>\n";
print "<p>Changing the parameters on this page will change them for all sites.</p>\n";
print "<p><button type='button' onclick='window.location=\"/\";'>&lt; Back</button></p>\n";

#--------------------------------------------------------------------
# Depending on cmd, edit or save
#--------------------------------------------------------------------
$cmd = request_val("cmd");
if( $cmd == "save" ) {
	save_params();
} else {
	edit_params();
}
#print "<pre>".`cat /website/control/parameters.txt`."</pre>";
exit;

#--------------------------------------------------------------------
# Edit parameters
#--------------------------------------------------------------------
function edit_params( $msg ) {

	$param_file = fopen("/website/control/parameters.txt", "r");
	if( $param_file ) {
		print "<pre>\n";
		while (($line = fgets($param_file)) !== false ) {
			$line = rtrim($line);
			preg_match('/^(.*)=(.*)$/',$line,$matches);
			$varname = $matches[1];
			$value = $matches[2];
			#print $line." varname=".$varname." value=".$value."\n";
			$vars{$varname} = $value;
		}
		print "</pre>\n";
	} else {
		print "parameters.txt does not exist.\n";
		exit;
	}
	
	$headsize_z 	= $vars{"HEADSIZE_Z"};
	$threshcor 	= $vars{"THRESHCOR"};
	$f_threshold 	= $vars{"F_THRESHOLD"};
	$g_threshold 	= $vars{"G_THRESHOLD"};
	$iter_lin 	= $vars{"ITER_LIN"};
	$iter_nonlin 	= $vars{"ITER_NONLIN"};
	$opencl_platform = $vars{"OPENCL_PLATFORM"};
	$opencl_device 	= $vars{"OPENCL_DEVICE"};
	
	if( $msg != "" ) {
		print "<p>$msg</p>\n";
	}

	print "<form>\n";
	print "<input type='hidden' id='cmd' name='cmd' value='save'>\n";

	print "<table>\n";
	
	print "<tr>\n";
	print "<td colspan=9><br>Parameters for FSL ROI</td>\n";
	print "</tr>\n";
	
	print "<tr>\n";
	print "<td>HEADSIZE_Z</td>\n";
	print "<td><input id='headsize_z' name='headsize_z' value='$headsize_z'></td>";
	print "</tr>\n";
	
	print "<tr>\n";
	print "<td>THRESHCOR</td>\n";
	print "<td><input id='threshcor' name='threshcor' value='$threshcor'></td>";
	print "</tr>\n";
	
	print "<tr>\n";
	print "<td colspan=9><br>Parameters for FSL Brain Extraction Tool BET</td>\n";
	print "</tr>\n";
	
	print "<tr>\n";
	print "<td>F_THRESHOLD</td>\n";
	print "<td><input id='f_threshold' name='f_threshold' value='$f_threshold'></td>";
	print "</tr>\n";
	
	print "<tr>\n";
	print "<td>G_THRESHOLD</td>\n";
	print "<td><input id='g_threshold' name='g_threshold' value='$g_threshold'></td>";
	print "</tr>\n";
	
	print "<tr>\n";
	print "<td colspan=9><br>Broccoli parameters for registration</td>\n";
	print "</tr>\n";
	
	print "<tr>\n";
	print "<td>ITER_LIN</td>\n";
	print "<td><input id='iter_lin' name='iter_lin' value='$iter_lin'></td>";
	print "</tr>\n";
	
	print "<tr>\n";
	print "<td>ITER_NONLIN</td>\n";
	print "<td><input id='iter_nonlin' name='iter_nonlin' value='$iter_nonlin'></td>";
	print "</tr>\n";
	
	print "<tr>\n";
	print "<td colspan=9><br>GPU platform and device</td>\n";
	print "</tr>\n";
	
	print "<tr>\n";
	print "<td>OPENCL_PLATFORM</td>\n";
	print "<td><input id='opencl_platform' name='opencl_platform' value='$opencl_platform'></td>";
	print "</tr>\n";
	
	print "<tr>\n";
	print "<td>OPENCL_DEVICE</td>\n";
	print "<td><input id='opencl_device' name='opencl_device' value='$opencl_device'></td>";
	print "</tr>\n";
	
	print "<tr>\n";
	print "<td></td>\n";
	print "<td><br><button type='button' onclick='this.form.submit();'>Save</button> </td>";
	print "</tr>\n";
	
	print "</table>\n";

	print "</form>\n";

	return;
}

#--------------------------------------------------------------------
# Save parameters
#--------------------------------------------------------------------
function save_params( $var ) {

	$errors = "";

	$headsize_z 	= request_int("headsize_z");
	if( $headsize_z == "" ) { $errors .= "<br>Invalid headsize_z ".request_val("headsize_z")." int expected"; }

	$threshcor 	= request_float("threshcor");
	if( $threshcor == "" ) { $errors .= "<br>Invalid threshcor ".request_val("threshcor")." float expected"; }

	$f_threshold 	= request_float("f_threshold");
	if( $f_threshold == "" ) { $errors .= "<br>Invalid f_threshold ".request_val("f_threshold")." float expected"; }

	$g_threshold 	= request_float("g_threshold");
	if( $g_threshold == "" ) { $errors .= "<br>Invalid g_threshold ".request_val("g_threshold")." float expected"; }

	$iter_lin 	= request_int("iter_lin");
	if( $iter_lin == "" ) { $errors .= "<br>Invalid iter_lin ".request_val("iter_lin")." small integer"; }

	$iter_nonlin 	= request_int("iter_nonlin");
	if( $iter_nonlin == "" ) { $errors .= "<br>Invalid iter_nonlin ".request_val("iter_nonlin")." small integer"; }

	$opencl_platform = request_int("opencl_platform");
	if( $opencl_platform == "" ) { $errors .= "<br>Invalid opencl_platform ".request_val("opencl_platform")." 0 only"; }

	$opencl_device 	= request_int("opencl_device");
	if( $opencl_device != "0" && $opencl_device != "1" ) { $opencl_device = ""; }
	if( $opencl_device == "" ) { $errors .= "<br>Invalid opencl_device ".request_val("opencl_device")." 0 or 1 only"; }

	if( $errors == "" ) {
		$param_file = fopen("/website/control/parameters.txt", "w");
		if( $param_file ) {
			fprintf( $param_file, "HEADSIZE_Z=%s\n", 	$headsize_z );
			fprintf( $param_file, "THRESHCOR=%s\n", 	$threshcor );
			fprintf( $param_file, "F_THRESHOLD=%s\n", 	$f_threshold );
			fprintf( $param_file, "G_THRESHOLD=%s\n", 	$g_threshold );
			fprintf( $param_file, "ITER_LIN=%s\n", 		$iter_lin );
			fprintf( $param_file, "ITER_NONLIN=%s\n", 	$iter_nonlin );
			fprintf( $param_file, "OPENCL_PLATFORM=%s\n", 	$opencl_platform );
			fprintf( $param_file, "OPENCL_DEVICE=%s\n", 	$opencl_device );
		} else {
			print "parameters.txt cannot be written.\n";
			exit;
		}
		fclose($param_file);
	}

	edit_params( $errors );
}

#--------------------------------------------------------------------
# request_val
#--------------------------------------------------------------------
function request_val( $var ) {

        $val = $_REQUEST[$var];
        $val = strip_tags($val);
        $val = htmlspecialchars($val);
        $val = stripslashes($val);

        return $val;
}

function request_int( $var ) {
        $val = request_val($var);

        if( preg_match("/^[0-9]+$/", $val ) ) {
                return $val;
        } 

        return "";
}

function request_float( $var ) {
        $val = request_val($var);

        if( preg_match("/^[0-9]+[.][0-9]+$/", $val ) ) {
                return $val;
        } 

	if( $val == "0" ) {
		return $val;
	}

        return "";
}

