<?php
require __DIR__ . '/../autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

$connector = new FilePrintConnector("/dev/usb/lp0");
$printer = new Printer($connector);

$servername = "localhost";
$username = "print";
$password = "root";
$dbname = "printer";


$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 


//$sql = "SELECT id FROM comidas";
$sql = "SELECT * FROM printer.comidas ORDER BY id DESC LIMIT 1";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        //echo "id: " . $row["id"];
        $restick = $row["id"]+1;
        echo $restick;
    }
} else {
    echo "0 results";
}



// Set to something sensible for the rest of the examples
$printer->setBarcodeHeight(40);
$printer->setBarcodeWidth(2);
$printer-> setJustification(Printer::JUSTIFY_CENTER);

date_default_timezone_set("America/Mexico_City");

function randomNumber($length) {
    $result = '';

    for($i = 0; $i < $length; $i++) {
        $result .= mt_rand(0, 9);
    }

    return $result;
}
$randnum = randomNumber(12);
//echo $randnum;


/* Barcode types */
$standards = array (
        Printer::BARCODE_UPCA => array (
                "title" => "GESTAMP",
                "caption" => "Servicio de comedor GGM Puebla.",
                "example" => array (
                        array (
                                "caption" => date('j \of M Y h:i:s A'),
                                "content" => $randnum,
                                "ticket"  => "Ticket # ". $restick
                        )
                )
        )
);
$printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
foreach ($standards as $type => $standard) {
    $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
    $printer->text($standard ["title"] . "\n");
    $printer->selectPrintMode();
    $printer->text($standard ["caption"] . "\n\n");
    foreach ($standard ["example"] as $id => $barcode) {
	$printer-> setJustification(Printer::JUSTIFY_LEFT); 
        $printer->setEmphasis(true);
        $printer->text($barcode ["caption"] . "\n");
        $printer->setEmphasis(false); 	      
        $printer->barcode($barcode ["content"], $type);
        $printer->text($barcode ["ticket"] . "\n");
        $printer->feed(3);
    }
}
$printer->cut();
$printer->close();
$conn->close();
