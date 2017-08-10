
@echo off
call c:\Medcom\MRIcustomer\Seq\brp\brp-config.bat

echo %time% status = transmitting control file
c:\%BRP%\curl\bin\curl --netrc-file c:\%BRP%\netrc.txt -s -k -F "cmd=put" -F "filename=control.ini" -F "file=@/%BRP%/outbox/control.ini" %URL% >NUL

echo %time% status = setting ready
c:\%BRP%\curl\bin\curl --netrc-file c:\%BRP%\netrc.txt -s -k -F "cmd=setonly" -F "status=ready" %URL% >NUL
