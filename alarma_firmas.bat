@ECHO OFF
for /f "tokens=1-4 delims=/ " %%i in ("%date%") do (
     set dow=%%i
     set month=%%j
     set day=%%k
     set year=%%l
)
SET /a _rand=(%RANDOM%*500/32768)+1
set HORA_ACTUAL=%TIME%
set hora=%HORA_ACTUAL:~0,2%
set minutos=%HORA_ACTUAL:~3,2%
set segundos=%HORA_ACTUAL:~6,2%

set datestr=%year%_%month%_%day%
E:\php-7.2.16-Win32-VC15-x64\php-win.exe -f E:\OI_OrfeoPHP7_64\orfeo\firmaDocumentos.php >> E:\logs\alarma_firmas_%datestr%_%_rand%.log
