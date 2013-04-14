This is a Havij Report converter to SQL format.

Available Parameters (GET):
-file: Havij html report file to be converted

-to (optional): Converted file name

-table(optional): SQL desired name

-createTable (only inside php)

-Generate an .sql file contaning all rows:
1135.php?file=HAVIJ_REPORT.html&table=YOUR_SQL_TABLE_NAME&to=export.sql


-Show exported data directly:

1135.php?file=HAVIJ_REPORT.html&table=YOUR_SQL_TABLE_NAME


$sql var contains the parsed data.
