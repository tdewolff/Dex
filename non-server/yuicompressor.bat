@echo off
set batch_p=%~p0
setlocal enabledelayedexpansion

call:compress css styles\core\ ..\core\resources\styles\
call:compress js scripts\core\ ..\core\resources\scripts\

call:compress css styles\menu\ ..\modules\menu\resources\styles\
call:compress js scripts\menu\ ..\modules\menu\resources\scripts\

call:compress js scripts\groningenbijles\ ..\themes\groningenbijles\resources\scripts\

call:compress css styles\pure\ ..\themes\pure\resources\styles\
call:compress js scripts\pure\ ..\themes\pure\resources\scripts\

pause
goto:eof

:compress
echo.
echo %~2 =^> %~3
for /r .\%~2 %%f in (*) do (
	set filename=%%f
	set filename_p=%%~pf
	set filename_p=!filename_p:%batch_p%%~2=!
	set new_filename=%~3!filename_p!%%~nf.min%%~xf

	echo. !filename! =^> !new_filename!

	set filename=!filename:\=\\!
	set new_filename=!new_filename:\=\\!

	java -jar yuicompressor-2.4.8.jar --type %~1 -o "!new_filename!" !filename!
)
goto:eof