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
class fahrer extends Page
{
	private $fahrerResult = array();
	private $recordset;
	private $pizzasForOrderID = array();
    
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
		$SQLabfrage = 
		"SELECT o.orderID, o.address,
		GROUP_CONCAT(op.pizzaname) AS pizzen,
		SUM(price) AS completeprice,
		op.status
		FROM `Order` AS o JOIN orderedPizza AS op JOIN Angebot AS a
		WHERE o.orderID = op.orderID AND
		op.pizzaname = a.pizzaname AND
		op.status IN ('gebacken', 'unterwegs') AND
		op.orderID NOT IN 
		(SELECT orderID from orderedPizza
		WHERE status IN ('bestellt', 'inOfen', 'geliefert'))
		GROUP BY orderID;";   
        try {
			$this->recordset = $this->_database->query ($SQLabfrage);			
		}		
		
		catch (Exception $e) {
			echo $e->getMessage();
		}
		
		while ($record = $this->recordset->fetch_assoc()){
			array_push($this->fahrerResult, $record);					
		}
		
		//store the relevant pizzaIDs somewhere:
		foreach ($this->fahrerResult as $record) {
			$currentID = (int)$record["orderID"]; //workaround because of errors in SQL string
			$SQLabfrage = "SELECT pizzaID from orderedPizza
			WHERE orderID = $currentID ;";
			try {
				$this->recordset = $this->_database->query ($SQLabfrage);			
			}		
		
			catch (Exception $e) {
				echo $e->getMessage();
			}
			$pizzenForSpecificOrder = array();
			while ($idrecord = (int)$this->recordset->fetch_assoc()["pizzaID"]) {
				$pizzenForSpecificOrder[] = $idrecord;
			}
			$this->pizzasForOrderID[$currentID] = $pizzenForSpecificOrder;			
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
        $this->generatePageHeader('Fahrer');
        echo "\t<h1>Fahrer</h1>\n";
        echo "\n";
        $this->cnt = 1;
        foreach($this->fahrerResult as $record) {
			$this->outputOneOrder($record["address"], $record["pizzen"], $record["completeprice"], $record["orderID"], $record["status"]);		
		}	
	
        $this->generatePageFooter();
    }


    private function outputOneOrder($address, $pizzen, $completeprice = -1.0, $orderID, $orderstatus)
    {
        $completeprice = number_format($completeprice, 2, ",", ".");      
        $link = "fahrer.php";
        $serializedIDarray = serialize($this->pizzasForOrderID[$orderID]);

        echo<<<EOT
        <div class="adressen">
        <h2>$address</h2>   
        $pizzen     
        <br/>
        <br/>
        Preis: $completeprice € <br/>
        <br/>
<table>
<tr>
        <td>gebacken</td>
        <td>unterwegs</td>
        <td>ausgeliefert</td>
</tr>
<tr>   
 
EOT;
        echo "<td><input type=\"radio\" name=\"order$orderID\" value=\"gebacken\" onclick=\"window.location.href='$link?arr=$serializedIDarray&state=gebacken'\" ";
 		if ($orderstatus === "gebacken") 
            echo "checked";
        echo "></td> \n";	 
        
        echo "<td><input type=\"radio\" name=\"order$orderID\" value=\"unterwegs\" onclick=\"window.location.href='$link?arr=$serializedIDarray&state=unterwegs'\" ";
 		if ($orderstatus === "unterwegs") 
            echo "checked";
        echo "></td> \n";	 

        echo "<td><input type=\"radio\" name=\"order$orderID\" value=\"geliefert\" onclick=\"window.location.href='$link?arr=$serializedIDarray&state=geliefert'\" ";
 		if ($orderstatus === "geliefert") 
            echo "checked";
        echo "></td> \n";
        
		echo<<<EOT
</tr>
</table>
        </div>
EOT;
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
        var_dump($_GET);
        $idArray = unserialize($_GET["arr"]);
        $newStatus = mysql_real_escape_string($_GET["state"]);
        
        echo "here is the ID array: ";
        var_dump($idArray);
        
        foreach($idArray as $pizzaID){	
			try {
				$this->_database->query ("UPDATE orderedPizza SET `status` = '$newStatus' WHERE pizzaID = $pizzaID;");			
			}		
		
			catch (Exception $e) {
				echo $e->getMessage();
			}
		}        
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
            $page = new fahrer();
            if (!empty($_GET)) { // suppress error messages when there is nothing to process
				$page->processReceivedData();
				}
			$page->getViewData();	
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
fahrer::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >
