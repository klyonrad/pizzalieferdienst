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
class kunde extends Page
{
    private $orderID;
    private $recordset;
    private $pizzen = array();
    
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
		$this->orderID = 10;
		$this->orderID = $_SESSION["orderID"];
        $SQLabfrage = "SELECT pizzaID, pizzaname, `status` FROM orderedPizza
		WHERE orderID = $this->orderID ;";
		try {
			$this->recordset = $this->_database->query ($SQLabfrage);			
		}
		
		catch (Exception $e) {
			echo $e->getMessage();
		}
		
		while ($idrecord = $this->recordset->fetch_assoc()) {
			$this->pizzen[] = $idrecord;
		}
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
        $this->generatePageHeader('Bestellübersicht', 'kunde.php');
        
        echo<<<EOT
        <h1>Kunde</h1>
        <br/>
<table>
<tr>
        <td></td>
        <td><strong>bestellt</strong></td>
        <td><strong>im Ofen</strong></td>
        <td><strong>fertig</strong></td>
        <td><strong>unterwegs</strong></td>
</tr>
EOT;

		foreach ($this->pizzen as $it)
			$this->showOnePizza($it["pizzaname"], $it["pizzaID"], $it["status"]);
			
        echo<<<EOT
        </table>
<div class="navitem"><a href="bestellung.php">Neue Bestellung</a> </div>
EOT;
	
		$this->generatePageFooter();
	}

    private function showOnePizza($pizza, $number, $status)
    {
        echo<<<EOT
        <tr>
        <td>$pizza</td>
EOT;
        echo "<td><input type=\"radio\" name=\"pizza$number\" value=\"bestellt\" disabled ";
        if ($status === "bestellt") 
            echo "checked";
        echo "></td>";

        echo "<td><input type=\"radio\" name=\"pizza$number\" value=\"imOfen\" disabled ";
        if ($status === "inOfen") 
            echo "checked";
        echo "></td>";

        echo "<td><input type=\"radio\" name=\"pizza$number\" value=\"fertig\" disabled ";
        if ($status === "gebacken") 
            echo "checked";
        echo "></td>";

        echo "<td><input type=\"radio\" name=\"pizza$number\" value=\"unterwegs\" disabled ";
        if ($status === "unterwegs") 
            echo "checked";
        echo "></td>";
        
        echo "</tr>";
//EOT;
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
            $page = new kunde();
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
session_start();
kunde::main();

