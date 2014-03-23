<?php
# minimum code use example
# see example.php for more options that you can use, and for trouble shooting tips
require 'class.email-query-results-as-csv-file.php';
$emailCSV = new EmailQueryResultsAsCsv('localhost','database_name','username','password');
$emailCSV->setQuery("SELECT * FROM table_name WHERE column = 'value'");
$emailCSV->sendEmail("sender@website.com","receiver@website.com","MySQL Query Results as CSV Attachment");
?>
