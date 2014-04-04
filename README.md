# Email MySQL Query Results as a CSV File Attachment
===============

A PHP class file that will email the results of a MySQL query as a CSV file attachment. 
```php
# minimum code use example
# see example.php for more options that you can use
require 'class.email-query-results-as-csv-file.php';
$emailCSV = new EmailQueryResultsAsCsv('localhost','database_name','username','password');
$emailCSV->setQuery("SELECT * FROM table_name WHERE column = 'value'");
$emailCSV->sendEmail("sender@website.com","receiver@website.com",
    "MySQL Query Results as CSV Attachment");
```

This can be run by opening it in a web browser, or run as a CRON job. The more common way to use it, is to integrate it into some other program. The source code in this PHP file is well commented and can be modified to fit your needs.

## License
This software is licensed under the [GNU GPL version 3.0](http://www.gnu.org/licenses/gpl-3.0-standalone.html) or later.

## What it does do:

1. Connects to a MySQL Database.
2. Runs one (or more) MySQL query that you define.
3. Builds a correctly formatted CSV file from the query results. Can send one or many CSV file attachements in a single email.
4. Emails the CSV file as an attachment to an address you specify. 

## What it doesn't do:

This code doesn't save the results to a file on the server.
If you are looking to save a file to the server, try Google search.

# Basic Trouble Shooting

## Problems Sending The Email?
  
If you receive this message "ERROR: The Email could not be sent." 
  
Then you probably just need to configure your mail server. The situation where I see this the most is with people running WAMP as a local development server. Check with your web host to find out your mail server settings. You will need to know:
* the address of the mail server
* the port to use on the mail server
* the address to use that you will send mail from

Then lookup the PHP function [ini_set()](http://php.net/manual/en/function.ini-set.php)

Finally add to the top of this file code like this...
```php
ini_set("SMTP","mail.domain.com");
ini_set("SMTP_PORT", 25);
ini_set("sendmail_from","yourname@domain.com");
```

## The script says the mail was sent, but it has never been received.

### Script Says Email Sent

The PHP mail() function returns true when the outgoing mail server accepts the message for delivery. This doesn't mean the email has been delivered correctly. 

### Example Successful Script Output
```
Step 1: Connected to MySQL server successfully. Step 2: MySQL database successfully 
selected. Step 3 (repeats for each attachment): MySQL query ran successfully. Step 4 
(repeats for each attachment): CSV file built. Step 5: Email sent with attachment. 
FINISHED.
```

### Some Things To Check

There are many issues that could cause email sent via the PHP mail() function to not be received. 

You will need to troubleshoot possibilities, unrelated to this script, to find the point of failure. Some examples:
* The mail was received, but marked as spam.
* The recipient's address was incorrect.
* There is a slow mail queue on the sending mail server.
* There is a slow mail queue on the receiving mail server.

The problem is likely a server configuration problem, you will want to contact your web hosting provider.

### Test Sending An Email Using PHP mail() Function
Make sure you can still send email from your server. Once you know if you can send email from your server, you can move forward with other possible solutions. You can also try sending the mail to different recipients to help narrow down the problem. 

If you have ssh/command line access, you can try this:

```
php -r 'mail( 'you@yourdomain.com', "subject: test", "message: test", 'From: you@yourdomain.com');'
```

If you have FTP access, put this into a file called mailer.php (or whatever.php you want) and then navigate to it with a browser:

```php
<?php
  mail( 'you@yourdomain.com', "subject: test", "message: test", 'From: you@yourdomain.com'); 
  echo "Mail should have been sent, check your inbox"; 
?>
```

# New beginner instructions on how to use the class, and example files.

## Step 1: View then Edit the Example File

The source code is well documented. Start by opening the driver file "example.php" in your code editor. You could try Notepad++ for windows, and gEdit (pimped) for Linux.

* Each of the lines of code is very well commented and documented in this file.
* Follow the directions outlined in comments of the file.
* Some items are optional, and they are noted as such.

## Step 2: Upload the Class File and Driver File to Your Web Sever

After you fill in your specific details, upload the two files to your web server:

* class.email-query-results-as-csv-file.php
* example.php

## Setp 3: Run the Driver File from a Web Browser

Run the file "example.php" from a web browser.

## debugMode(True)

If you are using the object in debugMode(True) then it will output messages of success or error at each step. Error messages are always output regardless if the debugMode is active or not.

## Attaching Multiple Files to One Email

To attach multiple files to a single email follow the code in the file "example-multi.php".

# Best of luck to you.
Ask questions in the comments on this [project's code page at Studio-Owens.com](http://www.studio-owens.com/code/email-mysql-query-results-as-a-csv-file-attachment.htm)
