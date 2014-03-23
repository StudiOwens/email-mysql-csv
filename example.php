<?php
/*
  Modify this example as you see fit. 
  This file could be run as a CRON JOB or with a direct file call.
*/

# -----
# Load The Class File
# first we have to load the class file
require('class.email-query-results-as-csv-file.php');

# -----
# Create A New Object
# next we create a new object of the class "EmailQueryResultsAsCsv"
# we must pass in the following information upon creating the new object: 
# (a) MySQL Server, (b) MySQL Database Name, (c) MySQL Username, (d) MySQL Password
$emailCSV = new EmailQueryResultsAsCsv('localhost','database_name','username','password');

# -----
# [Optional] 
# Set the database server port - default is the standard 3306
$emailCSV->setDBinfoServerPort('3306');

# -----
# [Optional] 
# DEBUG outputs text to the screen
# you can set this to False or just delete the line all together to keep success messages
# from being printed to the screen; note that ERROR messages will always be shown if one occurs
$emailCSV->debugMode(True);

# -----
# [Optional] 
# CSV File Name
# default is "mysql_results.csv" but you can set it to be something more descriptive
$emailCSV->setCSVname("mysql_results.csv");

# -----
# Database Query
# set the query that you want to run that will be used to fill the CSV file
# example "SELECT * FROM table_name WHERE column = 'value'"
$emailCSV->setQuery("SELECT * FROM table_name");

# -----
# [Optional] 
# Email Message HTML Formatted 
# supply a message to be sent in the body of the email
$emailCSV->setEmailMessage("<h1>MySQL Query Results as CSV Attachment</h1><p>This attachment can be opened with OpenOffice.org Calc, Google Docs, or Microsoft Excel.</p>");

# -----
# [Optional] 
# Set the reusable parts of the CSV file, default options are shown in the method call below
# Excel, OpenOffice.org Calc, and Google Spreadsheet will all use the default settings just fine
# example use would to format the CSV file to the German CSV standard instead of the USA/UK format
# Fields with embedded data enclosure characters will be enclosed within a double-data enclosure, 
#   e.g. default setting action is that each embedded double-quote character must be represented by
#   a pair of double-quote characters
#   more info on the CSV file format: http://en.wikipedia.org/wiki/Comma-separated_values
# pass in the following variables: 
#   (a) data enclosure, (b) value separator, (c) newline
$emailCSV->setCSVinfo('"', ",", "\n");

# -----
# Send The Email With CSV File Attachment
# this runs the query, builds the CSV file, and attaches it to an email
# to send the email you must pass in the following information:
# (a) email sender, (b) email receiver, (c) subject of the email 
$emailCSV->sendEmail("sender@website.com","receiver@website.com","MySQL Query Results as CSV Attachment");

/*****************************************************************
  Problems Sending The Email?
  
  If you receive this message "ERROR: The Email could not be sent." 
  
  Then you probably just need to configure your mail server. The 
  situation where I see this the most is with people running WAMP 
  as a local development server. Check with your web host to find 
  out your mail server settings. You will need to know:
    * the address of the mail server
    * the port to use on the mail server
    * the address to use that you will send mail from
  Then lookup the PHP function ini_set()
    http://php.net/manual/en/function.ini-set.php
  Finally add to the top of this file code like this...
    ini_set("SMTP","mail.domain.com");
    ini_set("SMTP_PORT", 25);
    ini_set("sendmail_from","yourname@domain.com");
*****************************************************************/
?>