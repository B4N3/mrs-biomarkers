
@echo off
call c:\Medcom\MRIcustomer\Seq\brp\brp-config.bat

if exist c:\%BRP%\inbox\coordinates.ini (
	del c:\%BRP%\inbox\coordinates.ini
)

rem
rem silent (s) insecure/no signed cert (k) follow redirects (L)
rem
c:\%BRP%\curl\bin\curl --netrc-file c:\%BRP%\netrc.txt -s -k -L -F "cmd=get" -F "filename=coordinates.ini" %URL% > c:\%BRP%\inbox\coordinates.ini

