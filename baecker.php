<?php	// UTF-8 marker äöüÄÖÜß€
/**
 * Class PageTemplate for the exercises of the EWA lecture
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

// to do: change name 'PageTemplate' throughout this file
require_once './Page.php';

/**
 * This is a template for top level classes, which represent 
 * a complete web page and which are called directly by the user.
 * Usually there will only be a single instance of such a class. 
 * The name of the template is supposed
 * to be replaced by the name of the specific HTML page e.g. baker.
 * The order of methods might correspond to the order of thinking 
 * during implementation. 
 * @author   Bernhard Kreling, <b.kreling@fbi.h-da.de> 
 * @author   Ralf Hahn, <ralf.hahn@h-da.de> 
 */
class baecker extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks
    
    /**
     * Instantiates members (to be defined above).   
     * Calls the constructor of the parent i.e. page class.
     * So the database connection is established.
     *
     * @return none
     */
    protected function __construct() 
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks
    }
    
    /**
     * Cleans up what ever is needed.   
     * Calls the destructor of the parent i.e. page class.
     * So the database connection is closed.
     *
     * @return none
     */
    protected function __destruct() 
    {
        parent::__destruct();
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is stored in an easily accessible way e.g. as associative array.
     *
     * @return none
     */
    protected function getViewData()
    {
		try {
			$SQLabfrage = "SELECT `pizzaname`, `status` FROM orderedPizza WHERE status <= 2;";
			$this->recordset = $this->_database->query ($SQLabfrage);			
		}
		
		
		catch (Exception $e) {
			echo $e->getMessage();
		}
		var_dump($this->recordset);
		var_dump($this->recordset->fetch_assoc());
    }
    
    /**
     * First the necessary data is fetched and then the HTML is 
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if avaialable- the content of 
     * all views contained is generated.
     * Finally the footer is added.
     *
     * @return none
     */
    protected function generateView() 
    {
        $this->getViewData();
        $this->generatePageHeader('Bäckerübersicht');
        echo<<<EOT
        <h1>Bäcker</h1>
        <br/>
	<table>
	<tr>
        <td></td>
        <td><strong>bestellt</strong></td>
        <td><strong>im Ofen</strong></td>
        <td><strong>fertig</strong></td>
	</tr>
        
EOT;
		while ($record = $this->recordset->fetch_assoc()){
			$this->insert_pizza($record['pizzaname'], $record['status']);
		}	
		echo "</table>";
        $this->generatePageFooter();
    }
    
    // status needs to be a number from 0 to 2
    private function insert_pizza($name = "", $status){
		static $cnt = 0; // to do: should $cnt be a class attribute?
		//should it be the pizzaID?
		$link = 'http://www.fbi.h-da.de/cgi-bin/Echo.pl?pizza';
		echo "\t<tr>\n<td>$name</td>\n";
		echo "\t\t<td><input type=\"radio\" name=\"pizza$cnt\" value=\"bestellt\" onclick=\"window.location.href='$link$cnt=bestellt'\" ";		
		if ($status === "bestellt")
            echo "checked";
        echo "/></td>\n";
        
		echo "\t\t<td><input type=\"radio\" name=\"pizza$cnt\" value=\"inOfen\" onclick=\"window.location.href='$link$cnt=inOfen'\" ";
		if ($status === "inOfen") 
            echo "checked";
        echo "/></td>\n";		
        
		echo "\t\t<td><input type=\"radio\" name=\"pizza$cnt\" value=\"fertig\" onclick=\"window.location.href='$link$cnt=fertig'\" ";
		if ($status === "fertig") 
            echo "checked";
        echo "/></td>\n";
        		
		echo "\t</tr>\n";
		$cnt++;
    }
    
    /**
     * Processes the data that comes via GET or POST i.e. CGI.
     * If this page is supposed to do something with submitted
     * data do it here. 
     * If the page contains blocks, delegate processing of the 
	 * respective subsets of data to them.
     *
     * @return none 
     */
    protected function processReceivedData() 
    {
        parent::processReceivedData();
        // to do: call processReceivedData() for all members
    }

    /**
     * This main-function has the only purpose to create an instance 
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the HTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
     *
     * @return none 
     */    
    public static function main() 
    {
        try {
            $page = new baecker();
            $page->processReceivedData();
            $page->generateView();
        }
        catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
baecker::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >
