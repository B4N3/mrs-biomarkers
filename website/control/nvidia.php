<?php
// © 2017 Regents of the University of Minnesota. All rights reserved.
error_reporting( E_ALL ^ E_WARNING ^ E_NOTICE );

print "<h3>BRP NVIDIA status page</h3>\n";
print "<p>Checking the status of the NVIDIA GPU card...</p>\n";
print "<p><button type='button' onclick='window.location=\"/\";'>&lt; Back</button></p>\n";

print "<h4>nvidia-smi</h4>\n";
print "<pre>";
print `nvidia-smi`;
print "</pre>";

print "<h4>List kernel modules</h4>\n";
print "<pre>";
print `lsmod | grep nvi`;
print "</pre>";

exit;

