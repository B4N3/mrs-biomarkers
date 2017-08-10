
@echo off
call c:\Medcom\MRIcustomer\Seq\brp\brp-config.bat

rem
rem send the data, set status to "ready"
rem
echo "sending" > c:\%BRP%\inbox\status.txt
call c:\%BRP%\brp-send.bat data

rem
rem get the current status, should change to "received" and then "complete"
rem
:CHECK
call c:\%BRP%\brp-status.bat > c:\%BRP%\inbox\status.txt
set /P STATUS=<c:\%BRP%\inbox\status.txt
rem del c:\%BRP%\inbox\status.txt
echo %time% status = %STATUS%

rem
rem if failed, stop
rem
if "%STATUS%"=="failed" (
	exit /b
)

rem
rem if not complete, wait and try again
rem
if not "%STATUS%"=="complete" (
	rem sleep 1 seconds
	ping localhost -n 2 >nul
	goto CHECK
)

rem
rem OK, now download the result voxels.txt
rem
call c:\%BRP%\brp-receive.bat