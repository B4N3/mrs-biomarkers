<?php
// © 2017 Regents of the University of Minnesota. All rights reserved.
error_reporting( E_ALL ^ E_WARNING ^ E_NOTICE );

require_once("config.php");
require_once("request.val.php");

$version = "1.0";

$cmd = request_val("cmd"); 		#print "cmd=$cmd\n";
$study = request_val("study"); 		#print "study=$study\n";
$status = request_val("status"); 	#print "status=$status\n";
$filename = request_val("filename"); 	#print "filename=$filename\n";
$output = request_val("output"); 	#print "output=$output\n";

if( $study == "" ) { $study = "default"; }
if( $output != "xml" ) {
	$output = "txt";
}

#-----------------------------------------------------------
# Choose command to do
#-----------------------------------------------------------
switch( $cmd ) {
	case "new":	new_cmd( $study ); break;
	case "delete":	delete_cmd( $study ); break;

	case "list":	list_cmd( $status ); break;
	case "files":	files_cmd( $study ); break;
	case "status":	status_cmd( $study ); break;
	case "log":	log_cmd( $study ); break;

	case "put":	put_cmd( $study, $filename ); break;
	case "get":	get_cmd( $study, $filename ); break;

	case "set":	set_cmd( $study, $status ); break;
	case "setonly":	setonly_cmd( $study, $status ); break;
	case "unset":	unset_cmd( $study, $status ); break;

	case "version":	version_cmd(); break;
	case "help":	usage(); break;
	case "":	usage(); break;

	default:	error_exit("Unrecognized command."); 
}
exit;

#-----------------------------------------------------------
# version
#-----------------------------------------------------------
function version_cmd() {
	global $output, $version;

	if( $output == "txt" ) {
		$values = "$version";
	} else {
		$values = "  <version>$version</version>\n";
	}

	ok_exit("version", $values );
}

#-----------------------------------------------------------
# usage
#-----------------------------------------------------------
function usage() {
	global $version, $folders_url;
	global $cmd, $status, $study, $filename, $output;

	# We want to track the downloads, which get logged with cmd=get but not this way.
	# We may even want to add special security to the study folders, requiring a temp download key.
	#print "direct download with curl of dicom.tgz from study1:
	#print "curl -o saveme.tgz -O ".$folders_url."/study1/dicom.tgz
	#print "

	header("Content-Type: text/plain");

	print <<<ENDOFHELP
API $version usage summary
=====================

General syntax rules
--------------------
file and study names can only contain a-z A-Z 0-9 _ .
file and study names cannot begin with .
file and study names cannot be longer than 100 characters

Getting this help
-----------------
api.php
api.php?cmd=help
   display usage

API version
-----------
api.php?cmd=version
   display API version

Getting results in txt or XML
-----------------------------
api.php?cmd=version&output=txt
api.php?cmd=version&output=xml
   Commands give their results in either txt or XML format, default=txt
   for example, display API version in txt and XML results format

Creating new study folders
--------------------------
api.php?cmd=new&study=study1
   create study1 directory

Deleting study folders
----------------------
api.php?cmd=delete&study=study1
   move study1 directory to trash

Listing studies
---------------
api.php?cmd=list
api.php?cmd=list&status=readyqc
api.php?cmd=list&status=received
api.php?cmd=list&status=qccomplete
   list all studies, or studies with a certain status

Listing study files
-------------------
api.php?cmd=files&study=study1
   List files for study1

Getting study statuses
----------------------
api.php?cmd=status&study=study1
   List statuses for study1

Getting a log of study events
-----------------------------
api.php?cmd=log&study=study1
   print the log of events for study1

Uploading study files
---------------------
api.php?cmd=put&study=study1&filename=dicom.zip
api.php?cmd=put&study=study1&filename=results.zip
   put dicom.zip or results.zip into study1
   send zip file as POST multi-part form data with using field name file
   allowed extensions zip, gz, tgz, txt, ini
   example with curl:
curl -F 'cmd=put' -F 'study=study1' -F 'filename=dicom.tgz' -F 'file=@/tmp/upload.test/20140428-ST003-phantom.tgz' https://.../api.php

Downloading study files
-----------------------
api.php?cmd=get&study=study1&filename=dicom.zip
api.php?cmd=get&study=study1&filename=results.zip
   download dicom.zip or results.zip from study1
   get a file with curl:
curl -L -F 'cmd=get' -F 'study=study1' -F 'filename=dicom.tgz' -o saveas.tgz -O https://.../api.php

Setting study statuses
----------------------
api.php?cmd=set&study=study1&status=readyqc
api.php?cmd=set&study=study1&status=received
api.php?cmd=set&study=study1&status=qccomplete
   set the status of study1
   using set and unset, a study can have multiple statuses
   using setonly a study will have only one status

Unsetting study statuses
------------------------
api.php?cmd=unset&study=study1&status=readyqc
api.php?cmd=unset&study=study1&status=received
api.php?cmd=unset&study=study1&status=qccomplete
   unset the status of study1
   using set and unset a study can have multiple statuses
   using setonly a study will have only one status

Setting one study status (unsetting others)
-------------------------------------------
api.php?cmd=setonly&study=study1&status=readyqc
api.php?cmd=setonly&study=study1&status=received
api.php?cmd=setonly&study=study1&status=qccomplete
   set the status of study1 and remove any other status
   using set and unset a study can have multiple statuses
   using setonly a study will have only one status

Example usage with curl
=======================

1. Get a list of studies
------------------------

# curl -F 'cmd=list' https://.../api.php
OK list
123
20140425-ST001-gosia_PR_phantom
20140502-ST001-Phantom
20140502-ST001-phantom_ACR
study1
study3
study4
study5

2. Get a list of studies that are ready for QC
----------------------------------------------

# curl -F 'cmd=list' -F 'status=readyqc' https://.../api.php
OK list status readyqc
123
20140425-ST001-gosia_PR_phantom
20140502-ST001-Phantom
20140502-ST001-phantom_ACR

3. Check the status of a study
------------------------------

# curl -F 'cmd=status' -F 'study=20140502-ST001-phantom_ACR' https://.../api.php
OK status study 20140502-ST001-phantom_ACR
readyqc

4. Get the names of files in a study folder
-------------------------------------------

# curl -F 'cmd=files' -F 'study=20140502-ST001-phantom_ACR' https://.../api.php
OK files study 20140502-ST001-phantom_ACR
total 10080
-rw------- 1 apache apache 3440153 May  5 15:43 20140502-ST001-phantom_ACR.tgz
-rw------- 1 apache apache 3412769 May  5 16:28 20140502-ST001-phantom_ACR.zip
-rw------- 1 apache apache 3440153 May  5 16:16 20140502-ST001-phantom_ACR.zip.20140505.162853

5. Download a file from a study. (You need the -L for the redirect and the -o to save as a different name.)
-----------------------------------------------------------------------------------------------------------

# curl -L -F 'cmd=get' -F 'study=20140502-ST001-phantom_ACR' -F 'filename=20140502-ST001-phantom_ACR.zip' -o /tmp/downloadas.zip -O https://.../api.php
  % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                 Dload  Upload   Total   Spent    Left  Speed
100 3332k  100 3332k    0     0  4902k      0 --:--:-- --:--:-- --:--:-- 4902k

6. Set the status on a study to received, then check the status
---------------------------------------------------------------

# curl -F 'cmd=setonly' -F 'study=20140502-ST001-phantom_ACR' -F 'status=received' https://.../api.php
OK setonly study 20140502-ST001-phantom_ACR status received

# curl -F 'cmd=status' -F 'study=20140502-ST001-phantom_ACR' https://.../api.php
OK status study 20140502-ST001-phantom_ACR
received

7. Save the results back to the study folder
--------------------------------------------

# curl -F 'cmd=put' -F 'study=20140502-ST001-phantom_ACR' -F 'filename=results.tgz' -F 'file=@/tmp/upload.test/dicom.tgz' https://.../api.php
OK put study 20140502-ST001-phantom_ACR filename results.tgz

8. Set the study status to QC complete
--------------------------------------

# curl -F 'cmd=setonly' -F 'study=20140502-ST001-phantom_ACR' -F 'status=qccomplete' https://.../api.php
OK setonly study 20140502-ST001-phantom_ACR status qccomplete

# curl -F 'cmd=status' -F 'study=20140502-ST001-phantom_ACR' https://.../api.php
OK status study 20140502-ST001-phantom_ACR
qccomplete

9. Get a log of events on each study
------------------------------------

unknown in the log is replaced by the authenticated web user when web server authentication is enabled.

# curl -F "cmd=log" -F "study=20140502-ST001-phantom_ACR" https://.../api.php
OK log study 20140502-ST001-phantom_ACR
Mon May  5 13:53:21 2014 160.94.164.194 unknown new
Mon May  5 13:53:21 2014 160.94.164.194 unknown set status readyqc
Mon May  5 15:43:16 2014 160.94.164.194 unknown put 20140502-ST001-phantom_ACR.tgz orig_filename 20140502-ST001-phantom_ACR.tgz type application/octet-stream size 3440153
Mon May  5 15:43:16 2014 160.94.164.194 unknown set status readyqc
Mon May  5 16:16:08 2014 160.94.164.194 unknown put 20140502-ST001-phantom_ACR.zip orig_filename 20140502-ST001-phantom_ACR.zip type application/octet-stream size 3440153
Mon May  5 16:16:08 2014 160.94.164.194 unknown set status readyqc
Mon May  5 16:28:53 2014 160.94.164.194 unknown put 20140502-ST001-phantom_ACR.zip saved old version as 20140502-ST001-phantom_ACR.zip.20140505.162853
Mon May  5 16:28:53 2014 160.94.164.194 unknown put 20140502-ST001-phantom_ACR.zip orig_filename 20140502-ST001-phantom_ACR.zip type application/octet-stream size 3412769
Mon May  5 16:28:53 2014 160.94.164.194 unknown set status readyqc
Mon May  5 16:31:36 2014 134.84.19.184 unknown get 20140502-ST001-phantom_ACR.zip
Mon May  5 16:48:58 2014 160.94.164.138 unknown get 20140502-ST001-phantom_ACR.zip
Mon May  5 16:49:50 2014 160.94.164.138 unknown setonly status received remove status readyqc
Mon May  5 16:49:50 2014 160.94.164.138 unknown setonly status received
Mon May  5 16:53:50 2014 160.94.164.138 unknown put results.tgz orig_filename dicom.tgz type application/octet-stream size 14702879
Mon May  5 16:54:18 2014 160.94.164.138 unknown setonly status qccomplete remove status received
Mon May  5 16:54:18 2014 160.94.164.138 unknown setonly status qccomplete

10. Using user/password authentication with curl
------------------------------------------------

If user/password authentication is used with api.php, you can pass this safely to api.php this way.
Create a curl config file e.g. myconfig which contains the argument

user=username:password

Use the curl config file with -K. This keeps the username and password off of the command line, which is visible to anyone via ps.

# curl -K myconfig -F "cmd=list" https://.../api.php

You can also add the "silent" argument and other curl arguments to your config file this way. See man curl

Received arguments:
cmd=$cmd status=$status study=$study filename=$filename output=$output

ENDOFHELP;

	exit;
}

#-----------------------------------------------------------
# new_cmd
#-----------------------------------------------------------
function new_cmd( $study ) {
	global $folders_dir;

	check_study_name( $study );

	if( file_exists( "$folders_dir/$study" ) ) { 
		error_exit( "Study already exists." ); 
	}

	mkdir("$folders_dir/$study", 0700, true);

	log_event( $study, "new" );

	ok_exit( "new study $study" );
}

#-----------------------------------------------------------
# delete_cmd
#-----------------------------------------------------------
function delete_cmd( $study ) {
	global $folders_dir, $trash_dir, $status_dir;

	check_study_name( $study );
	check_study_exists( $study );

	$timestamp = strftime("%Y%m%d.%H%M%S",time());

	rename("$folders_dir/$study", "$trash_dir/$study.$timestamp");

	log_event( $study, "delete to $study.$timestamp" );

	$statuses = array();
	exec("cd $status_dir; ls", $statuses, $ret);
	if( $ret == 0 ) {
		foreach( $statuses as $status ) {
			if( file_exists( "$status_dir/$status/$study" ) ) {
				log_event( $study, "remove status $status" );
				`rm $status_dir/$status/$study`;
			}
		}
	}

	ok_exit( "delete study $study" );
}

#-----------------------------------------------------------
# list_cmd
#-----------------------------------------------------------
function list_cmd( $status ) {
	global $output, $folders_dir, $status_dir;

	if( $output == "txt" ) {
		if( $status == "" ) {
			$values = `cd $folders_dir; ls`;
			ok_exit( "list", $values );
		}
	
		check_status($status);
	
		$values = `cd $status_dir/$status; ls`;

	} else {
		if( $status == "" ) {
			$values = "  <studies>\n";
			$studies = array();
			exec("cd $folders_dir; ls", $studies, $ret);
			if( $ret == 0 ) {
				foreach( $studies as $study ) {
					$values .= "    <study>$study</study>\n";
				}
			}
			$values .= "  </studies>\n";
	
			ok_exit( "list", $values );
		}
	
		check_status($status);
	
		$values = "  <studies>\n";
		$studies = array();
		exec("cd $status_dir/$status; ls", $studies, $ret);
		if( $ret == 0 ) {
			foreach( $studies as $study ) {
				$values .= "    <study>$study</study>\n";
			}
		}
		$values .= "  </studies>\n";
	}

	ok_exit( "list status $status", $values );
}

#-----------------------------------------------------------
# files_cmd
#-----------------------------------------------------------
function files_cmd( $study ) {
	global $output, $folders_dir, $status_dir;

	check_study_exists( $study );

	if( $output == "txt" ) {
		$values = `cd $folders_dir/$study; ls -l`;
	} else {
		$values = "  <files>\n";
		$files = array();
		exec("cd $folders_dir/$study; ls", $files, $ret);
		if( $ret == 0 ) {
			foreach( $files as $filename ) {
				$size = filesize( "$folders_dir/$study/$filename" );
				$mtime = filemtime( "$folders_dir/$study/$filename" );
				$show_datetime = strftime( "%Y%m%d:%H%M", $mtime );
				$show_date = strftime( "%c", $mtime );
				$values .= "    <file>\n";
				$values .= "      <filename>$filename</filename>\n";
				$values .= "      <size>$size</size>\n";
				$values .= "      <datetime>$show_datetime</datetime>\n";
				$values .= "      <date>$show_date</date>\n";
				$values .= "      <mtime>$mtime</mtime>\n";
				$values .= "    </file>\n";
			}
		}
		$values .= "  </files>\n";
	}

	ok_exit( "files study $study", $values );
}

#-----------------------------------------------------------
# status_cmd
#-----------------------------------------------------------
function status_cmd( $study ) {
	global $output, $folders_dir, $status_dir;

	check_study_exists( $study );

	if( $output == "txt" ) {
		$values = `cd $status_dir; ls */$study | cut -f1 -d/`;
	} else {
		$values = "  <statuses>\n";
		$statuses = array();
		exec("cd $status_dir; ls */$study | cut -f1 -d/", $statuses, $ret);
		if( $ret == 0 ) {
			foreach( $statuses as $status ) {
				$values .= "    <status>$status</status>\n";
			}
		}
		$values .= "  </statuses>\n";
	}

	ok_exit( "status study $study", $values );
}

#-----------------------------------------------------------
# put_cmd
#-----------------------------------------------------------
function put_cmd( $study, $filename ) {
	global $output, $folders_dir;

	$allowed_extensions = array( "txt", "ini", "zip", "gz", "tgz" );

	check_study_exists( $study );

	check_filename($filename);
	$orig_filename = $_FILES['file']['name'];
	check_filename($orig_filename);

	#
	# Look for any upload errors
	#
	if( $_FILES['file']['error'] > 0 ) {
		error_exit( "upload error code ".$_FILES['file']['error']);
	}

	#
	# Check the extension of the filename we want to save as.
	#
	$filename_parts = explode( '.', $filename );
	$extension = end( $filename_parts );
	$extension = strtolower( $extension );
	if( ! in_array( $extension, $allowed_extensions ) ) {
		error_exit( "Upload file extension $extension not allowed in save filename." );
	}

	#
	# Check the extension of the filename they are uploading (name on their computer).
	#
	$orig_filename_parts = explode( '.', $orig_filename );
	$orig_extension = end( $orig_filename_parts );
	$orig_extension = strtolower( $orig_extension );
	if( ! in_array( $orig_extension, $allowed_extensions ) ) {
		error_exit( "Upload file extension $orig_extension not allowed in original filename." );
	}

	#
	# Does the file already exist in the study? If so, move it aside.
	#
	if( file_exists( "$folders_dir/$study/$filename" ) ) {
		$timestamp = strftime("%Y%m%d.%H%M%S",time());
		`mv $folders_dir/$study/$filename $folders_dir/$study/$filename.$timestamp`;
		log_event( $study, "put $filename saved old version as $filename.$timestamp" );
	}

	#
	# Save the uploaded file into the study directory.
	#
	move_uploaded_file( $_FILES['file']['tmp_name'], "$folders_dir/$study/$filename" );
	chmod( "$folders_dir/$study/$filename", 0600 );


	$type = $_FILES['file']['type'];
	$size = $_FILES['file']['size'];
	log_event( $study, "put $filename orig_filename $orig_filename type $type size $size" );

	if( $output == "txt" ) {
		$values = "";
	} else {
		$values =  "  <orig_filename>$orig_filename</orig_filename>\n";
		$values .= "  <type>$type</type>\n";
		$values .= "  <size>$size</size>\n";
	}

	ok_exit( "put study $study filename $filename", $values );
}

#-----------------------------------------------------------
# get_cmd
#-----------------------------------------------------------
function get_cmd( $study, $filename ) {
	global $folders_dir, $folders_url;

	check_study_exists( $study );
	check_filename($filename);

	if( ! file_exists( "$folders_dir/$study/$filename" ) ) {
		error_exit( "File does not exist." );
	}

	log_event( $study, "get $filename" );
	#print "OK get study $study filename $filename\n";

	#
	# Make sure this is the first header, before anything else is printed, or it won't work right.
	#
	header("Location: $folders_url/$study/$filename");

	exit;
}

#-----------------------------------------------------------
# set_cmd
#-----------------------------------------------------------
function set_cmd( $study, $status ) {
	global $status_dir;

	check_study_exists( $study );
	check_status( $status );

	`touch $status_dir/$status/$study`;

	log_event( $study, "set status $status" );

	ok_exit( "set study $study status $status" );
}

#-----------------------------------------------------------
# setonly_cmd
#-----------------------------------------------------------
function setonly_cmd( $study, $status ) {
	global $status_dir;

	check_study_exists( $study );
	check_status( $status );

	$statuses = array();
	exec("cd $status_dir; ls", $statuses, $ret);
	if( $ret == 0 ) {
		foreach( $statuses as $other_status ) {
			if( file_exists( "$status_dir/$other_status/$study" ) ) {
				log_event( $study, "setonly status $status remove status $other_status" );
				`rm $status_dir/$other_status/$study`;
			}
		}
	}

	`touch $status_dir/$status/$study`;

	log_event( $study, "setonly status $status" );

	ok_exit( "setonly study $study status $status" );
}

#-----------------------------------------------------------
# unset_cmd
#-----------------------------------------------------------
function unset_cmd( $study, $status ) {
	global $status_dir;

	check_study_exists( $study );
	check_status( $status );

	if( ! file_exists("$status_dir/$status/$study") ) {
		error_exit("Study does not have that status.");
	}

	`rm $status_dir/$status/$study`;

	log_event( $study, "unset status $status" );

	ok_exit( "unset study $study status $status" );
}

#-----------------------------------------------------------
# log_cmd
#-----------------------------------------------------------
function log_cmd( $study ) {
	global $output, $logs_dir;

	check_study_name( $study );
	#check_study_exists( $study ); # may be asking about a deleted study

	if( ! file_exists("$logs_dir/$study") ) { 
		error_exit("No log for study."); 
	}

	if( $output == "txt" ) {
		$values = `cat $logs_dir/$study`;
	} else {
		$values = "  <loglines>\n";
		$loglines = array();
		exec("cat $logs_dir/$study", $loglines, $ret);
		if( $ret == 0 ) {
			foreach( $loglines as $logline ) {
				$values .= "    <logline>$logline</logline>\n";
			}
		}
		$values .= "  </loglines>\n";
	}

	ok_exit( "log study $study", $values );
}

#-----------------------------------------------------------
# check_study_name
#-----------------------------------------------------------
function check_study_name( $study ) {

	if( $study == "" ) { 
		error_exit("Study must be specified."); 
	}

	if( strlen( $study ) > 100 ) {
		error_exit("Study name cannot be longer than 100 characters.");
	}

	if( $study == "." ) {
		error_exit("Study cannot be ."); 
	}

	if( $study == "OK" ) {
		error_exit("Study cannot be named OK"); 
	}

	if( $study == "ERROR" ) {
		error_exit("Study cannot be named ERROR"); 
	}

	if( substr($study,0,1) == "." ) {
		error_exit("Study cannot begin with ."); 
	}

	$sanitized = preg_replace('/[^a-zA-Z0-9-_\.]/','', $study);
	if( $study != $sanitized ) {
		error_exit("Study names can only contain a-z A-Z 0-9 - _ .");
	}

	return;
}

#-----------------------------------------------------------
# check_study_exists
#-----------------------------------------------------------
function check_study_exists( $study ) {
	global $folders_dir;

	check_study_name( $study );

	if( ! is_dir( "$folders_dir/$study" ) ) { 
		error_exit("Study does not exist."); 
	}

	return;
}

#-----------------------------------------------------------
# check_status
#-----------------------------------------------------------
function check_status( $status ) {
	global $status_dir;

	if( $status == "" ) {
		error_exit("Status must be specified.");
	}

	if( strlen( $status ) > 100 ) {
		error_exit("Status cannot be longer than 100 characters.");
	}

	if( $status == "OK" ) {
		error_exit("Status cannot be named OK"); 
	}

	if( $status == "ERROR" ) {
		error_exit("Status cannot be named ERROR"); 
	}

	if( $status == "status" ) {
		error_exit("Status cannot be named status"); 
	}

	#if( $status != "readyqc" && $status != "received" && $status != "qccomplete" ) {
	#	error_exit("Unrecognized status.");
	#}

	$sanitized = preg_replace('/[^a-zA-Z0-9-_\.]/','', $status);
	if( $status != $sanitized ) {
		error_exit("Statuses can only contain a-z A-Z 0-9 - _ .");
	}

	if( ! is_dir( "$status_dir/$status" ) ) { 
		error_exit("Unrecognized status - missing status directory.");
	}

	return;
}

#-----------------------------------------------------------
# check_filename
#-----------------------------------------------------------
function check_filename( $filename ) {

	if( $filename == "" ) { 
		error_exit("File name must be specified."); 
	}

	if( strlen( $filename ) > 100 ) {
		error_exit("File name cannot be longer than 100 characters.");
	}

	if( $filename == "." ) {
		error_exit("File cannot be ."); 
	}

	if( $filename == "OK" ) {
		error_exit("File cannot be named OK"); 
	}

	if( $filename == "ERROR" ) {
		error_exit("File cannot be named ERROR"); 
	}

	if( substr($filename,0,1) == "." ) {
		error_exit("File cannot begin with ."); 
	}

	$sanitized = preg_replace('/[^a-zA-Z0-9-_\.]/','', $filename);
	if( $filename != $sanitized ) {
		error_exit("File names can only contain a-z A-Z 0-9 - _ .");
	}

	return;
}

#-----------------------------------------------------------
# log_event
#-----------------------------------------------------------
function log_event( $study, $message ) {
	global $logs_dir;

	$logtime = strftime("%c");

	$log_fn = "$logs_dir/$study";

	$remote_addr = $_SERVER['REMOTE_ADDR'];
	$remote_user = $_SERVER['REMOTE_USER'];
	if( $remote_user == "" ) { 
		$remote_user = "unknown"; 
	}

	error_log( "$logtime $remote_addr $remote_user $message\n", 3, $log_fn );

	return;
}

#-----------------------------------------------------------
# ok_exit
#-----------------------------------------------------------
function ok_exit( $message, $values = "" ) {
	global $output;

	$values = rtrim($values); # remove any extra trailing newlines, spaces

	if( $output == "txt" ) {
		header("Content-Type: text/plain");
		print "OK $message\n";
		if( $values != "" ) {
			print "$values\n";
		}
	} else {
		header("Content-Type: application/xml");
		print "<result>
  <status>OK</status>
  <message>$message</message>
$values
</result>
";
	}

	exit;
}

#-----------------------------------------------------------
# error_exit
#-----------------------------------------------------------
function error_exit( $error ) {
	global $output;

	if( $output == "txt" ) {
		header("Content-Type: text/plain");
		print "ERROR $error\n";
	} else {
		header("Content-Type: application/xml");
		print "<result>
  <status>ERROR</status>
  <message>$error</message>
</result>
";
	}

	exit;
}

