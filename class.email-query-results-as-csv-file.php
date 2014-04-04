<?php
/*
Email MySQL Query Results as a CSV File Attachment
Author:  Stephen R. Owens  (www.Studio-Owens.com)
Version: 2.2 [2:41 AM Saturday, February 22, 2014]

Sends an email with a CSV file attachment that contains the results of a MySQL query.
Copyright (C) 2009-2014 Stephen R. Owens

LICENSE:
This software is licensed under the GNU GPL version 3.0 or later.
http://www.gnu.org/licenses/gpl-3.0-standalone.html

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class EmailQueryResultsAsCsv {

  # MySQL Server
  private $mySQL_server = '';
  private $mySQL_server_port = '3306';

  # MySQL Database Name
  private $mySQL_database = '';

  # MySQL Username
  private $mySQL_user = '';

  # MySQL Password
  private $mySQL_password = '';

  # MySQL Query
  # something like "SELECT * FROM table_name"
  private $mySQL_query = '';

  # CSV File Name
  # filename for the attached file, use something like "mysql_results.csv"
  private $csv_file_name = '';
  
  # Multiple File Data array
  # File Name & reuseable elements + mySQL_query
  private $arr_file_data = array();
  
  # CSV file reuseable elements
  private $csv_contain = '';
  private $csv_separate = '';
  private $csv_end_row = '';

  # Email Message
  # This is an HTML formatted message
  private $email_html_msg = "<h1>MySQL Query Results as CSV Attachment</h1>
  <p>This attachment can be opened with OpenOffice.org Calc, Google Docs, or Microsoft Excel.</p>";
  
  # used to output success messages to the screen
  private $debugFlag = False;
  
  # -------------------------------------- 
  #  Methods
  # -------------------------------------- 
    
  # constructor
  public function __construct($s, $d, $u, $p) {
    $this->setDBinfo($s, $d, $u, $p);
    $this->setCSVinfo();
    $this->setCSVname();
  }
  
  # destructor
  public function __destruct() {
    
  }
  
  public function setDBinfo($s, $d, $u, $p) {
    $this->mySQL_server = $s;
    $this->mySQL_database = $d;
    $this->mySQL_user = $u;
    $this->mySQL_password = $p;
  }
  
  public function setDBinfoServerPort($p) {
    $this->mySQL_server_port = $p;
  }
  
  public function setQuery($sql) {
    $this->mySQL_query = $sql;
  }
  
  public function setEmailMessage($msg) {
    $this->email_html_msg = $msg;
  }
  
  public function setCSVname($fn = "mysql_results.csv") {
    $this->csv_file_name = $fn;
  }
  
  public function setCSVinfo($c = '"', $s = ",", $er = "\n") {
    $this->csv_contain = $c;
    $this->csv_separate = $s;
    $this->csv_end_row = $er;
  }
  
  public function setMultiFile($fn, $sql) {
    $this->arr_file_data[] = array("csv_file_name" => $fn, 
      "mySQL_query" => $sql,
      "csv_contain" => $this->csv_contain,
      "csv_separate" => $this->csv_separate,
      "csv_end_row" => $this->csv_end_row);
  }
  
  public function debugMode($bool) {
    $this->debugFlag = $bool;
  }
  
  public function sendEmail($email_from, $email_to, $email_subject) {
    # check to see if the array for file info and queries has data if not add the single file data
    if(!isset($this->arr_file_data[0]["csv_file_name"])) {
      $this->arr_file_data[0] = array("csv_file_name" => $this->csv_file_name, 
        "mySQL_query" => $this->mySQL_query,
        "csv_contain" => $this->csv_contain,
        "csv_separate" => $this->csv_separate,
        "csv_end_row" => $this->csv_end_row);
    }

    # -------------------------------------- 
    #   CONNECT TO MYSQL DATABASE
    $mysqli = new mysqli($this->mySQL_server, $this->mySQL_user, $this->mySQL_password, $this->mySQL_database, $this->mySQL_server_port);
    if ($mysqli->connect_errno) {
         die('ERROR: Could not connect to MySQL server: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
    }
    if ($this->debugFlag) {
      echo "Step 1: Connected to MySQL server successfully. \n\n";
    }
    
    /*
    -------------------------------------- 
        SEND EMAIL WITH ATTACHMENT
        Below you will notice I'm using complex variable parsing {curly braces surrounding variable}
          For more information see: PHP docs for String Variable Parsing using Complex (curly) syntax
          http://www.php.net/manual/en/language.types.string.php#language.types.string.parsing.complex
    */

    # start setting up the email header
    $headers = "From: ".$email_from;

    # create boundary string
    # boundary string must be unique using MD5 to generate a pseudo random hash
    $random_hash = md5(date('r', time())); 
    $mime_boundary = "==Multipart_Boundary_x{$random_hash}x";

    # set email header as a multipart/mixed message
    # this allows the sending of an attachment combined with the HTML message
    $headers .= "\nMIME-Version: 1.0\n" .
    "Content-Type: multipart/mixed;\n" .
    " boundary=\"{$mime_boundary}\"";

    # multipart boundary for the HTML message
    $email_message = "This is a multi-part message in MIME format.\n\n" .
    "--{$mime_boundary}\n" .
    "Content-Type:text/html; charset=\"UTF-8\"\n" .
    "Content-Transfer-Encoding: 7bit\n\n" .
    $this->email_html_msg . "\n\n";
    
    # count how many files were created
    $files_attached_cnt = 0;
    foreach ($this->arr_file_data AS $file_data) {
      
      # build the CSV file
      $csv_file = $this->buildCSV($file_data, $mysqli);
      
      if ($csv_file != "") {
        $files_attached_cnt += 1;
      
        # encode CSV file with MIME base64
        # required for sending it as an email attachment
        $data = chunk_split(base64_encode($csv_file)); 
        
        # multipart boundary for the email attachment
        $email_message .= "--{$mime_boundary}\n";
        
        # attach the file
        $email_message .= "Content-Type: application/octet-stream;\n" .
        " name=\"{$file_data['csv_file_name']}\"\n" .
        "Content-Disposition: attachment;\n" .
        " filename=\"{$file_data['csv_file_name']}\"\n" .
        "Content-Transfer-Encoding: base64\n\n" .
        $data . "\n\n";
      }
    }
    # end the multipart message with this mime boundary, notice the ending --
    $email_message .= "--{$mime_boundary}--\n";
    
    if ($files_attached_cnt > 0) {
      # try to send the email and verify the results

      $sendit = @mail($email_to, $email_subject, $email_message, $headers);
      if(!$sendit) {
        die("ERROR: The Email could not be sent.");
      }

      if ($this->debugFlag) {
        echo "Step 4: Email sent with attachment. \n\n";
      }
    } else {
      if ($this->debugFlag) {
        echo "Step 4: No data found for query. Email NOT sent. \n\n";
      }
    }

    # close the link to the MySQL database
    //$mysqli->close();
    
    # reset the attachment array so the object can be used anew
    $this->arr_file_data = array();

    if ($this->debugFlag) {
      echo "FINISHED.";
    }
  }
  
  private function buildCSV($file_data, $mysqli) {
    # container to hold the CSV file as it's built
    $csv_file = "";
    
    # run the MySQL query and check to see if results were returned
    $result = $mysqli->query($file_data["mySQL_query"]);
    if (!$result) {
      die("ERROR: Invalid query \n MySQL error: (" . $mysqli->errno . ")" . $mysqli->error . "\n Your query: " . $this->mySQL_query);
    }
    
    # only return a non blank data set with query returns at least one record
    if ($result->num_rows > 0) {
      if ($this->debugFlag) {
        echo "Step 2 (repeats for each attachment): MySQL query ran successfully. \n\n";
      }
      
      # store the number of columns and field data from the results
      $columns = $mysqli->field_count;
      $column_data = $result->fetch_fields();
      
      # Build a header row using the mysql field names
      $header_row = '';
      $i = 0;
      foreach ($column_data as $col) {
      //for ($i = 0; $i < $columns; $i++) {
        $column_title = $file_data["csv_contain"] . stripslashes($col->name) . $file_data["csv_contain"];
        $column_title .= ($i < $columns-1) ? $file_data["csv_separate"] : ''; #the last column does not have the column separator
        $header_row .= $column_title;
        $i++;
      }
      $csv_file .= $header_row . $file_data["csv_end_row"]; # add header row to CSV file
      
      # Build the data rows by walking through the results array one row at a time
      $data_rows = '';
      while ($row = $result->fetch_array(MYSQLI_NUM)) {
        for ($i = 0; $i < $columns; $i++) {
          # clean up the data; strip slashes; replace double quotes with two single quotes
          $data_rows .= $file_data["csv_contain"] . preg_replace('/'.$file_data["csv_contain"].'/', $file_data["csv_contain"].$file_data["csv_contain"], stripslashes($row[$i])) . $file_data["csv_contain"];
          $data_rows .= ($i < $columns-1) ? $file_data["csv_separate"] : '';
        }
        $data_rows .= $this->csv_end_row; # add data row to CSV file
      }
      $csv_file .= $data_rows; # add the data rows to CSV file
      
      if ($this->debugFlag) {
        echo "Step 3 (repeats for each attachment): CSV file built. \n\n";
      }
    }  else {
       echo "Step 2 (repeats for each attachment): MySQL query ran successfully \n\n";
       echo "Step 3 (repeats for each attachment): NO results were returned for this query. No file will be sent for the following query: \n " . $this->mySQL_query ." \n\n";
    }
    
    # Return the completed file
    return $csv_file;
  }
}
?>
