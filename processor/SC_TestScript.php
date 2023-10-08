<?php
/*
    DAST Backend Processing
*/
// ====================================
// Includes / Imports

// Scan object
require_once('./Scan.php');

// Tools
require_once('./tools/TOOL.php');
require_once('./tools/TOOL_Dummy.php');
require_once('./tools/TOOL_GoSpider.php');
require_once('./tools/TOOL_Gobuster.php');
require_once('./tools/TOOL_Nmap.php');
require_once('./tools/TOOL_sqlmap.php');
require_once('./tools/TOOL_Commix.php');
// ... more tools here

// Vulnerabiliites
require_once('./vulns/VULN.php');
require_once('./vulns/VULN_Dummy.php');
require_once('./vulns/VULN_Sitemap.php');
require_once('./vulns/VULN_HostInfo.php');
require_once('./vulns/VULN_SQLInjection.php');
require_once('./vulns/VULN_CMDInjection.php');




// ====================================
// Run PHP script continuously
while (true) {


// ====================================
// Connection variables
$servername = "170.187.240.98";
$username = "root";
$password = "DAST34swin@";
$dbname = "u428402158_dast";



// ====================================
// Scan Data
$SCAN;

// Check if there are any scans waiting
$scanWaiting = false;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM scan WHERE scan.status = 'waiting' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // There is a scan ready to process
    $scanWaiting = true;

    // Grab the scan data
    while($row = $result->fetch_assoc()) {
        $SCAN = new Scan($row["target"], $row["id"], $row["user_id"]);
    }
} else {
    // no scans currently waiting
}
$conn->close();


// ====================================
if  (!$scanWaiting) {
    //  No scans waiting
    echo "No scans to process.";
    echo "Last updated: " . date("Y-m-d H:i:s");
    
    // Sleep for 10 seconds then go back to the start
    sleep(10);
    continue;
}


// ====================================
// There is a scan to process

// ========================================================================
//                                  TOOLS
// ========================================================================
// Execute each of the tools:

// example tool
// $TOOL_Dummy = new TOOL_Dummy($SCAN, "DummyTool");
// $TOOL_Dummy->Execute();

// MG TOOL
// $TOOL_Nmap = new TOOL_Nmap($SCAN, "Nmap");
// $TOOL_Nmap->Execute();

// SC TOOL
// $TOOL_GoSpider = new TOOL_GoSpider($SCAN, "GoSpider");
// $TOOL_GoSpider->Execute();



// // SC TOOL
// $TOOL_Gobuster = new TOOL_Gobuster($SCAN, "Gobuster");
// $TOOL_Gobuster->Execute();

// SC TOOL
// $TOOL_sqlmap = new TOOL_sqlmap($SCAN, "sqlmap");
// $TOOL_sqlmap->Execute();

// SC TOOL
$TOOL_Commix = new TOOL_Commix($SCAN, "commix");
$TOOL_Commix->Execute();
print_r($TOOL_Commix);


// ========================================================================
//                                  VULNERABILITIES
// ========================================================================

// Analyse each of the vulnerabilities:
//$VULN_Dummy = new VULN_Dummy($SCAN, [$TOOL_GoSpider, $TOOL_Dummy]);
//$VULN_Dummy->Analyse();


// SC VULNERABILITY
// $VULN_Sitemap = new VULN_Sitemap($SCAN, [$TOOL_Gobuster, $TOOL_GoSpider]);
// $VULN_Sitemap->Analyse();

// SC VULNERABILITY
// $VULN_HostInfo = new VULN_HostInfo($SCAN, [$TOOL_Nmap]);
// $VULN_HostInfo->Analyse();

// SC VULNERABILITY
// $VULN_SQLInjection = new VULN_SQLInjection($SCAN, [$TOOL_sqlmap]);
// $VULN_SQLInjection->Analyse();

// SC VULNERABILITY
$VULN_CMDInjection = new VULN_CMDInjection($SCAN, [$TOOL_Commix]);
$VULN_CMDInjection->Analyse();

print_r($VULN_CMDInjection->getHTML());


// 
// echo "\n\n\n\n=======aaa======\n\n\n\n";
// var_dump($TOOL_Gobuster);


// 
// echo "\n\n\n\n=============\n\n\n\n";
//print_r($VULN_Sitemap->getHTML());


// Prepare the report
// go through each vuln html and sever and produce some other html file?



// Persist the report
// save that html to the db


// Update the scan status
































echo "Finished. Sleeping.";
sleep(100);

} ?>