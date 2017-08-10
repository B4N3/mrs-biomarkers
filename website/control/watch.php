<?php
// © 2017 Regents of the University of Minnesota. All rights reserved.
error_reporting( E_ALL ^ E_WARNING ^ E_NOTICE );

print "<h3>BRP control watch server</h3>";
print "<p>This page shows the main brp.run process and subprocesses on the brp server. It refreshes every 3 seconds.</p>\n";
print "<p><button type='button' onclick='window.location=\"/\";'>&lt; Back</button></p>\n";

$page = $_SERVER['PHP_SELF'];
$sec = 3;
header("Refresh: $sec; url=$page");

$pid = `pidof -x brp.run`;
if( $pid == "" ) {
	print "brp.run is not running\n";
} else {
	print "<pre>\n";
	print `pstree -p -a -A $pid`;
	print "</pre>\n";
}

