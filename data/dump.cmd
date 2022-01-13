@ECHO OFF

CALL env.cmd

IF NOT EXIST dumps MD dumps

CD dumps

SET TIME2=%TIME: =0%
SET DATETIME=%DATE:~6,4%-%DATE:~3,2%-%DATE:~0,2% %TIME2:~0,-3%
SET DATETIME=%DATETIME:/=_%
SET DATETIME=%DATETIME:\=_%
SET DATETIME=%DATETIME:-=_%
SET DATETIME=%DATETIME::=_%
SET DATETIME=%DATETIME:,=_%
SET DATETIME=%DATETIME:;=_%
SET DATETIME=%DATETIME:.=_%
SET DATETIME=%DATETIME: =_%

MD %DATETIME%
CD %DATETIME%

ECHO.
ECHO Dumping database %SQL-DB%...
ECHO.

ECHO Creating a compact version of the schema...

MYSQLDUMP -h%SQL-HOST% -u%SQL-USER% -p%SQL-PASS% --compatible=no_table_options,no_key_options,no_field_options --skip-comments --skip-disable-keys --skip-add-locks --skip-add-drop-table --default-character-set=utf8 --set-charset --no-data %SQL-DB% -r %SQL-DB%_schema_compact.sql >nul 2>nul
IF "%ERRORLEVEL%"=="2" GOTO ExitError

MYSQLDUMP -h%SQL-HOST% -u%SQL-USER% -p%SQL-PASS% --compatible=no_table_options,no_key_options,no_field_options --skip-comments --skip-disable-keys --skip-add-locks --skip-add-drop-table --default-character-set=utf8 --set-charset --no-data --xml %SQL-DB% -r %SQL-DB%_schema_compact.xml >nul 2>nul
IF "%ERRORLEVEL%"=="2" GOTO ExitError

ECHO Creating a full version of the schema...

MYSQLDUMP -h%SQL-HOST% -u%SQL-USER% -p%SQL-PASS% --compatible=no_table_options --skip-comments --default-character-set=utf8 --set-charset --no-data %SQL-DB% -r %SQL-DB%_schema_full.sql >nul 2>nul
IF "%ERRORLEVEL%"=="2" GOTO ExitError

MYSQLDUMP -h%SQL-HOST% -u%SQL-USER% -p%SQL-PASS% --compatible=no_table_options --skip-comments --default-character-set=utf8 --set-charset --no-data --xml %SQL-DB% -r %SQL-DB%_schema_full.xml >nul 2>nul
IF "%ERRORLEVEL%"=="2" GOTO ExitError

ECHO Creating a file with the table contents...

MYSQLDUMP -h%SQL-HOST% -u%SQL-USER% -p%SQL-PASS% --compatible=no_table_options,no_key_options,no_field_options --skip-comments --skip-disable-keys --skip-add-locks --skip-add-drop-table --default-character-set=utf8 --set-charset --no-create-info %SQL-DB% -r %SQL-DB%_data.sql >nul 2>nul
IF "%ERRORLEVEL%"=="2" GOTO ExitError

ECHO Creating an ansi version of the schema...

MYSQLDUMP -h%SQL-HOST% -u%SQL-USER% -p%SQL-PASS% --compatible=ansi,no_table_options,no_key_options,no_field_options --skip-comments --skip-disable-keys --skip-add-locks --skip-add-drop-table --default-character-set=utf8 --set-charset --no-data %SQL-DB% -r %SQL-DB%_schema_ansi.sql >nul 2>nul
IF "%ERRORLEVEL%"=="2" GOTO ExitError

MYSQLDUMP -h%SQL-HOST% -u%SQL-USER% -p%SQL-PASS% --compatible=ansi,no_table_options,no_key_options,no_field_options --skip-comments --skip-disable-keys --skip-add-locks --skip-add-drop-table --default-character-set=utf8 --set-charset --no-data --xml %SQL-DB% -r %SQL-DB%_schema_ansi.xml >nul 2>nul
IF "%ERRORLEVEL%"=="2" GOTO ExitError

ECHO Creating an ansi version of the table contents...

MYSQLDUMP -h%SQL-HOST% -u%SQL-USER% -p%SQL-PASS% --compatible=ansi,no_table_options,no_key_options,no_field_options --skip-comments --skip-disable-keys --skip-add-locks --skip-add-drop-table --default-character-set=utf8 --set-charset --no-create-info %SQL-DB% -r %SQL-DB%_data_ansi.sql >nul 2>nul
IF "%ERRORLEVEL%"=="2" GOTO ExitError

ECHO.
ECHO Dumping completed.
ECHO.

CD ..
CD ..

COPY env.cmd dumps\%DATETIME% >nul
COPY restore.cmd dumps\%DATETIME% >nul

GOTO Exit

:ExitError
CD ..
CD ..
ECHO.
ECHO Error: Unable to dump database %SQL-DB%.

:Exit
ECHO.

PAUSE
