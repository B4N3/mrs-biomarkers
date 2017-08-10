
@echo off
call c:\Medcom\MRIcustomer\Seq\brp\brp-config.bat

if exist c:\%BRP%\outbox\upload.zip (
	del c:\%BRP%\outbox\upload.zip
)

echo %time% status = compressing and encrypting 
cd %1
c:\%BRP%\zip\zip -e -P %PASSWORD% -q -r -9 c:\%BRP%\outbox\upload.zip .
cd \%BRP%

echo %time% status = transmitting
c:\%BRP%\curl\bin\curl --netrc-file c:\%BRP%\netrc.txt -s -k -F "cmd=put" -F "filename=upload.zip" -F "file=@/%BRP%/outbox/upload.zip" %URL% >NUL

echo %time% status = transmitting control file
c:\%BRP%\curl\bin\curl --netrc-file c:\%BRP%\netrc.txt -s -k -F "cmd=put" -F "filename=control.ini" -F "file=@/%BRP%/outbox/control.ini" %URL% >NUL

echo %time% status = setting ready
c:\%BRP%\curl\bin\curl --netrc-file c:\%BRP%\netrc.txt -s -k -F "cmd=setonly" -F "status=ready" %URL% >NUL
