@ECHO OFF

CALL env.cmd

ECHO.
ECHO Restoring database %SQL-DB%...
ECHO.

IF NOT EXIST %SQL-DB%_schema_full.sql GOTO Err_InvalidDatabase

MYSQL -h%SQL-HOST% -u%SQL-USER% -p%SQL-PASS% --default-character-set=utf8 -e "SET names 'utf8'; DROP DATABASE IF EXISTS %SQL-DB%; CREATE DATABASE %SQL-DB%; USE %SQL-DB%; SOURCE %SQL-DB%_schema_full.sql;"

GOTO Exit

:Err_InvalidDatabase
ECHO.
ECHO Error: Unable to find a data file.

:Exit
ECHO.
