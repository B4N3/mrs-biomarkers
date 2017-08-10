
@echo off
call c:\Medcom\MRIcustomer\Seq\brp\brp-config.bat

c:\%BRP%\curl\bin\curl --netrc-file c:\%BRP%\netrc.txt -s -k -F "cmd=status" %URL% | more +1

