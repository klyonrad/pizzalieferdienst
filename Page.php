<?php	// UTF-8 marker äöüÄÖÜß€
/**
 * Class Page for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 * 
 * PHP Version 5
 *
 * @category File
 * @package  Pizzaservice
 * @author   Bernhard Kreling, <b.kreling@fbi.h-da.de> 
 * @author   Ralf Hahn, <ralf.hahn@h-da.de> 
 * @license  http://www.h-da.de  none 
 * @Release  1.2 
 * @link     http://www.fbi.h-da.de 
 */
 
/**
 * This abstract class is a common base class for all 
 * HTML-pages to be created. 
 * It manages access to the database and provides operations 
 * for outputting header and footer of a page.
 * Specific pages have to inherit from that class.
 * Each inherited class can use these operations for accessing the db
 * and for creating the generic parts of a HTML-page.
 *
 * @author   Bernhard Kreling, <b.kreling@fbi.h-da.de> 
 * @author   Ralf Hahn, <ralf.hahn@h-da.de> 
 */ 
abstract class Page
{
    // --- ATTRIBUTES ---

    /**
     * Reference to the MySQLi-Database that is
     * accessed by all operations of the class.
     */
    protected $_database = null;
    
    // --- OPERATIONS ---
    
    /**
     * Connects to DB and stores 
     * the connection in member $_database.  
     * Needs name of DB, user, password.
     *
     * @return none
     */
    protected function __construct() 
    {
		try {
			require_once '/opt/lampp/var/www/pizzalieferdienst/private/pwd.php'; // read password & co.
			$this->_database = new MySQLi($host, $user, $pwd, "pizzalieferdienst");
			
			if (mysqli_connect_errno()) {
				throw new Exception("Connect failed: ".mysqli_connect_error());
			}
			
			if (!$this->_database->set_charset("utf8")) {
				throw new Exception("Charset failed: ".$this->_database->error);
			}			
		}
				
		catch (Exception $e) {
			echo $e->getMessage();
			}
    }
    
    /**
     * Closes the DB connection and cleans up
     *
     * @return none
     */
    protected function __destruct()    
    {
		try {
			$this->_database->close();
		}
		catch (Exception $e) {
			echo $e->getMessage();
			}		
    }
    
    /**
     * Generates the header section of the page.
     * i.e. starting from the content type up to the body-tag.
     * Takes care that all strings passed from outside
     * are converted to safe HTML by htmlspecialchars.
     *
     * @param $headline $headline is the text to be used as title of the page
     *
     * @return none
     */
    protected function generatePageHeader($headline = "", $refreshlink = NULL) 
    {
        $headline = htmlspecialchars($headline);
        if ($refreshlink != NULL)
			$refreshlink = htmlspecialchars($refreshlink);
        header("Content-type: text/html; charset=UTF-8");
        
        echo<<<EOT
        <!DOCTYPE html>
		<html>
		<head>
		<link rel="stylesheet" type="text/css" href="style.css" />
                <meta charset="UTF-8" />
EOT;
		if ($refreshlink != NULL)
			echo "<meta http-equiv=\"refresh\" content = \"5; URL=$refreshlink\">"; //see webtips.dan.info/refresh.html
		echo<<<EOT
                <title>$headline</title>
        </head>        
        <body>
EOT;
        // to do: output common beginning of HTML code 
        // including the individual headline
    }

    /**
     * Outputs the end of the HTML-file i.e. /body etc.
     *
     * @return none
     */
    protected function generatePageFooter() 
    {
	echo<<<EOT
	  </body>
	  </html>
EOT;
        // to do: output common end of HTML code
    }

    /**
     * Processes the data that comes via GET or POST i.e. CGI.
     * If every page is supposed to do something with submitted
     * data do it here. E.g. checking the settings of PHP that
     * influence passing the parameters (e.g. magic_quotes).
     *
     * @return none
     */
    protected function processReceivedData() 
    {
        if (get_magic_quotes_gpc()) {
            throw new Exception
                ("Bitte schalten Sie magic_quotes_gpc in php.ini aus!");
        }
    }
} // end of class

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >
