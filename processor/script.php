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
// ... more tools here

// Vulnerabiliites
require_once('./vulns/VULN.php');
require_once('./vulns/VULN_Dummy.php');



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


// Execute each of the tools:
$TOOL_GoSpider = new TOOL_GoSpider($SCAN, "GoSpider");
//$TOOL_GoSpider->Execute();

// next tool
$TOOL_Dummy = new TOOL_Dummy($SCAN, "DummyTool");
$TOOL_Dummy->Execute();

// PY TOOL
$TOOL_a2sv = new TOOL_a2sv($SCAN, "a2sv");
$TOOL_a2sv->Execute();

// PY TOOL
$TOOL_cdnCheck = new TOOL_cdnCheck($SCAN, "cdnCheck");
$TOOL_cdnCheck->Execute();

// PY TOOL
$TOOL_ProTravel = new TOOL_ProTravel($SCAN, "ProTravel");
$TOOL_ProTravel->Execute();

// ... next tool

print_r($TOOL_Dummy);
print_r($TOOL_a2sv);
print_r($TOOL_cdnCheck);
print_r($TOOL_ProTravel);

// Analyse each of the vulnerabilities:
$VULN_Dummy = new VULN_Dummy($SCAN, [$TOOL_GoSpider, $TOOL_Dummy]);
$VULN_Dummy->Analyse();

// PY VULNERABILITY
$VULN_InsecureServer = new VULN_InsecureServer($SCAN, [$TOOL_a2sv]);
$VULN_InsecureServer->Analyse();

// PY VULNERABILITY
$VULN_DDOS = new VULN_DDOS($SCAN, [$TOOL_cdnCheck]);
$VULN_DDOS->Analyse();

// PY VULNERABILITY
$VULN_PathTraversal = new VULN_Dummy($SCAN, [$TOOL_ProTravel]);
$VULN_PathTraversal->Analyse();

// Prepare the report




// Update the scan status
































echo "Finished. Sleeping.";
sleep(100);

} ?>