Email MySQL Query Results as a CSV File Attachment
===============

A PHP class file that will email the results of a MySQL query as a CSV file attachment. 

```php
# minimum code use example
# see example.php for more options that you can use
require 'class.email-query-results-as-csv-file.php';
$emailCSV = new EmailQueryResultsAsCsv('localhost','database_name','username','password');
$emailCSV->setQuery("SELECT * FROM table_name WHERE column = 'value'");
$emailCSV->sendEmail("sender@website.com","receiver@website.com","MySQL Query Results as CSV Attachment");
```

This can be run by opening it in a web browser, or run as a CRON job. The more common way to use it, is to integrate it into some other program. The source code in this PHP file is well commented and can be modified to fit your needs.

# License
This software is licensed under the [GNU GPL version 3.0](http://www.gnu.org/licenses/gpl-3.0-standalone.html) or later.

# What it does do:

1. Connects to a MySQL Database.
2. Runs one (or more) MySQL query that you define.
3. Builds a correctly formatted CSV file from the query results. Can send one or many CSV file attachements in a single email.
4. Emails the CSV file as an attachment to an address you specify. 

# What it doesn't do:

This code doesn't save the results to a file on the server.
If you are looking to save a file to the server, try Google search.

More information can be found on the [Project Code Page at Studio-Owens.com](http://www.studio-owens.com/code/email-mysql-query-results-as-a-csv-file-attachment.htm)
