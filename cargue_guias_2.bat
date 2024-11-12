@ECHO OFF
for /f "tokens=1-4 delims=/ " %%i in ("%date%") do (
     set dow=%%i
     set month=%%j
     set day=%%k
     set year=%%l
)
SET /a _rand=(%RANDOM%*500/32768)+1
set datestr=%year%_%month%_%day%
E:\php-7.2.16-Win32-VC15-x64\php-win.exe -f E:\OI_OrfeoPHP7_64\orfeo\cargue_guias_2.php >> E:\logs\cargue_guias_2_%datestr%_%_rand%.log
