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
class bestellung extends Page
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
        // to do: fetch data for this view from the database
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
        $this->generatePageHeader('Bestellung');
        // to do: call generateView() for all members
        // to do: output view of this page
        $link = 'http://www.fbi.h-da.de/cgi-bin/Echo.pl?';
        echo<<<EOT
        <script type="text/javascript">
			gesamt = 0.0;
			var Map = {};
			function Hinzufuegen (pizza, preis) {
				Map[pizza] = preis;
				NeuerEintrag = new Option(pizza, pizza, false, false);
				document.input.pizzasToOrder.options[document.input.pizzasToOrder.length] = NeuerEintrag;
				gesamt = gesamt + preis;
				document.input.price.value = "Preis: " + gesamt.toFixed(2) + "€";
			}
			
			function resetAll(){
				document.input.pizzasToOrder.options.length = 0;
				gesamt = 0.0;
				document.input.price.value = "Preis: " + gesamt.toFixed(2) + "€";
				document.input.addressentry.value = "";
			}
			
			function deleteSelected(){
			index =document.input.pizzasToOrder.selectedIndex;
			
				pizza = document.input.pizzasToOrder.options[index].value;
				if (pizza in Map)
					gesamt = gesamt - Map[pizza];
				document.input.pizzasToOrder.options[document.input.pizzasToOrder.selectedIndex] = null;
				document.input.price.value = "Preis: " + gesamt.toFixed(2) + "€";
				document.input.pizzasToOrder.selectedIndex = index;
			}
				
			function bestellen(){
				bestellung = "$link";
				if(document.input.pizzasToOrder.length > 0 && document.input.addressentry.value != ""){
					bestellung = bestellung + "Name=" + document.input.addressentry.value;
					for(i =0; i < document.input.pizzasToOrder.length; i++){
						bestellung = bestellung + "&" + "Pizza" + i + "=" + document.input.pizzasToOrder.options[i].value;
					}
					
					window.location.href = bestellung;
				}else {
					alert("Die Bestellung ist nicht vollständig! \n\nHaben sie evtl:\n  keine Pizza ausgewählt?\n  keine Adresse angegeben?");
				}
			}	
	</script>
	
	<h1>Bestellung</h1>
	<form name="input" action="$link" method="get">
	<div class="artikel">
	      <table>        
EOT;
	//to do: SQL abfrage und einfügen über insert_pizzen()
	
	//just a template for tests ########################################################################################
	$this->insert_pizzen('Margherita', 4.0);
        $this->insert_pizzen('Salami', 4.5);
        $this->insert_pizzen('Hawaii', 5.5);
        //tests	############################################################################################################
        
        echo<<<EOT
        
	      </table>
	</div>
	<div class="content">
	  <div class="bestellung">
		<select name="pizzasToOrder" size="10" style="width: 250px"></select>
	  </div>
	  <div class="bestellung">
		<input name="price" type="text" value="Preis: 0.00€" size="30" disabled>
	  </div>
	  <div class="bestellung">
		<input type="text" name="addressentry" size="30" placeholder="Name, Straße Hausnummer"/>
	  </div>
	  <div class="bestellung">
		<button type="button" onClick="resetAll()">Alle Löschen</button> 
		<button type="button" onClick="deleteSelected()">Auswahl Löschen</button> 	
		<button type="button" onClick="bestellen()">Bestellen</button> 
	  </div>
	</div>
	</form>
EOT;
        
        $this->generatePageFooter();
    }
    
    
    private function insert_pizzen($name = "", $price = -1.0){
	echo "\t\t\t<tr><td><img src=\"pizza.png\" alt=\"$name\" height=\"90\" width=\"130\" onClick=\"Hinzufuegen('$name', $price)\"> $name&nbsp;$price€</td></tr>\n";
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
            $page = new bestellung();
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
bestellung::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >