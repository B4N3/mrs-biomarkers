<?php
// © 2017 Regents of the University of Minnesota. All rights reserved.

error_reporting( E_ALL ^ E_WARNING ^ E_NOTICE );

$site = request_val("site");
$log = request_val("log");
$date = request_val("date");

if( $site == "" ) { show_sites(); }
if( $site == "main" ) { show_main_log(); }
if( $site == "maintail" ) { tail_main_log(); }
if( $date == "" ) { show_logs( $site, $date ); }
if( $log == "" ) { show_logs( $site, $date ); }
show_log( $site, $log );
exit;

#--------------------------------------------------------------------
# show_sites
#--------------------------------------------------------------------
function show_sites() {
	print "<h3>BRP logs</h3>";
	print "<p>This page shows the logs on the brp server. </p>\n";
	print "<p>\n";
	print "<button type='button' onclick='window.location=\"/\";'>&lt; Back</button> \n";
	print "</p>\n";

	print "<h3>Main log</h3>\n";
	print "<ul>\n";
	print "<li><a href='log.php?site=maintail'>Main log (tail)</a></li>\n";
	print "<li><a href='log.php?site=main'>Main log (full)</a></li>\n";
	print "</ul>\n";

	print "<h3>Site logs</h3>\n";
	print "<ul>\n";
	print "<li><a href='log.php?site=cmrr'>CMRR BRP site</a></li>\n";
	print "<li><a href='log.php?site=jhu'>JHU BRP site</a></li>\n";
	print "<li><a href='log.php?site=duke'>Duke BRP site</a></li>\n";
	print "<li><a href='log.php?site=mgh'>MGH BRP site</a></li>\n";
	print "<li><a href='log.php?site=test'>Test BRP site</a></li>\n";
	print "</ul>\n";
	exit;
}

#--------------------------------------------------------------------
# show_logs
#--------------------------------------------------------------------
function show_logs( $site, $date ) {
	print "<h3>BRP logs</h3>";
	print "<p>This page shows the logs on the brp server. </p>\n";
	print "<p>\n";
	print "<button type='button' onclick='window.location=\"/\";'>&lt; Back</button> \n";
	print "<button type='button' onclick='window.location=\"log.php\";'>&lt; Back to Sites</button> \n";
	print "</p>\n";

	switch($site) {
		case "cmrr":
		case "jhu":
		case "duke":
		case "mgh":
		case "test":
			break;
		default: 
			show_sites();
			exit;
	}

	print "<h3>Log files for site $site</h3>\n";

	if( $date == "" ) {
		$ls_output = `ls -1t /opt/brp/log/$site | cut -c9-16 | sort -unr`;
		$logs = explode("\n",$ls_output);

		print "<ul>\n";
		foreach( $logs as $date ) {
			if( $date != "" ) {
				print "<li><a href='log.php?site=$site&date=$date'>$date</a></li>\n";
			}
		}
		print "</ul>\n";
	} else {
		$ls_output = `cd /opt/brp/log/$site; ls -1t brp.log.$date*`;
		$logs = explode("\n",$ls_output);

		print "<ul>\n";
		foreach( $logs as $log ) {
			if( $log != "" ) {
				print "<li><a href='log.php?site=$site&date=$date&log=$log'>$log</a></li>\n";
			}
		}
		print "</ul>\n";
	}

	print "<p>";
	print "<button type='button' onclick='window.location=\"/\";'>&lt; Back</button> ";
	print "<button type='button' onclick='window.location=\"log.php\";'>&lt; Back to Sites</button> ";
	print "</p>\n";

	exit;
}

#--------------------------------------------------------------------
# show_log
#--------------------------------------------------------------------
function show_log( $site, $log ) {
	print "<h3>BRP logs</h3>";
	print "<p>This page shows the logs on the brp server. </p>\n";
	print "<p>";
	print "<button type='button' onclick='window.location=\"/\";'>&lt; Back</button> ";
	print "<button type='button' onclick='window.location=\"log.php\";'>&lt; Back to Sites</button> \n";
	print "<button type='button' onclick='window.location=\"log.php?site=$site\";'>&lt; Back to Site $site logs</button> ";
	print "</p>\n";

	print "<h3>Log file for site $site $log</h3>\n";

	print "<pre>\n";
	print `cat /opt/brp/log/$site/$log`;
	print "</pre>\n";

	print "<p>";
	print "<button type='button' onclick='window.location=\"/\";'>&lt; Back</button> ";
	print "<button type='button' onclick='window.location=\"log.php\";'>&lt; Back to Sites</button> \n";
	print "<button type='button' onclick='window.location=\"log.php?site=$site\";'>&lt; Back to Site $site logs</button> ";
	print "</p>\n";

	exit;
}

#--------------------------------------------------------------------
# show_main_log
#--------------------------------------------------------------------
function show_main_log() {
	print "<h3>BRP logs</h3>";
	print "<p>This page shows the logs on the brp server. </p>\n";
	print "<p>";
	print "<button type='button' onclick='window.location=\"/\";'>&lt; Back</button> ";
	print "<button type='button' onclick='window.location=\"log.php\";'>&lt; Back to Sites</button> ";
	print "</p> \n";

	print "<h3>Main log file </h3>\n";

	print "<pre>\n";
	print `cat /opt/brp/log/brp.log`;
	print "</pre>\n";

	print "<p>";
	print "<button type='button' onclick='window.location=\"/\";'>&lt; Back</button> ";
	print "<button type='button' onclick='window.location=\"log.php\";'>&lt; Back to Sites</button> ";
	print "</p> \n";

	exit;
}

#--------------------------------------------------------------------
# tail_main_log
#--------------------------------------------------------------------
function tail_main_log() {

	$sec = 3;
	header("Refresh: $sec; url=/control/log.php?site=maintail");

	print "<h3>BRP logs</h3>";
	print "<p>This page shows the logs on the brp server. </p>\n";

	print "<p>\n";
	print "<button type='button' onclick='window.location=\"/\";'>&lt; Back</button> ";
	print "<button type='button' onclick='window.location=\"log.php\";'>&lt; Back to Sites</button> ";
	print "</p>\n";

	print "<h3>Main log file (most recent 40 lines)</h3>\n";

	print "<pre>\n";
	print `tail -40 /opt/brp/log/brp.log`;
	print "</pre>\n";

	print "<p>\n";
	print "<button type='button' onclick='window.location=\"/\";'>&lt; Back</button> ";
	print "<button type='button' onclick='window.location=\"log.php\";'>&lt; Back to Sites</button> ";
	print "</p>\n";

	exit;
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

