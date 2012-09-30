2-. The exporter runs with GET parameters here are all listed:

-Generate an .sql file contaning all rows:
Code:
1135.php?file=HAVIJ_REPORT.html&table=YOUR_SQL_TABLE_NAME&to=export.sql

By default it will use havij column names, but if you have different column names in your db use this:

Code:
1135.php?file=HAVIJ_REPORT.html&table=YOUR_SQL_TABLE_NAME&to=export.sql&columns=column1,column2,etc...
(columns param can be used everywhere)

-Show exported data directly:
Code:
1135.php?file=HAVIJ_REPORT.html&table=YOUR_SQL_TABLE_NAME

-Disable All-in-one query:
Code:
1135.php?file=HAVIJ_REPORT.html&table=YOUR_SQL_TABLE_NAME&to=export.sql&aio=false
(file will be less lighter, can be used everywhere)

-Get results in php array to work it as i want:
Edit 1135.php put $array_import to true.

Instructions will be printed each time, so you don't need to come again to this thread, unless you want to see any news or say thankyou :) 