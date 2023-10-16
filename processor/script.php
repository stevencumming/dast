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
require_once('./tools/TOOL_a2sv.php');
require_once('./tools/TOOL_cdnCheck.php');
require_once('./tools/TOOL_cURL.php');
require_once('./tools/TOOL_XSRFProbe.php');
require_once('./tools/TOOL_XSStrike.php');
require_once('./tools/TOOL_sqlmap.php');
require_once('./tools/TOOL_Commix.php');

// ... more tools here

// Vulnerabiliites
require_once('./vulns/VULN.php');
require_once('./vulns/VULN_Dummy.php');
require_once('./vulns/VULN_SecurityMscfg.php');
require_once('./vulns/VULN_SSRF.php');
require_once('./vulns/VULN_CSRF.php');
require_once('./vulns/VULN_Sitemap.php');
require_once('./vulns/VULN_BrokenAccessCtl.php');
require_once('./vulns/VULN_CryptographicFlrs.php');
require_once('./vulns/VULN_XSS.php');
require_once('./vulns/VULN_HostInfo.php');
require_once('./vulns/VULN_SQLInjection.php');
require_once('./vulns/VULN_CMDInjection.php');



// ====================================
// Run PHP script continuously
while (true) {


// ========================================================================
//                           DATABASE CONNECTION
// ========================================================================
// Connection variables
$servername = "170.187.240.98";
$username = "root";
$password = "DAST34swin@";
$dbname = "u428402158_dast";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }


// ========================================================================
//                           INITIALISATION
// ========================================================================
// Retrieve Permitted Domains
$sql = "SELECT * FROM allowed_domains";
$result = $conn->query($sql);

$PERMITTED_DOMAINS = array();

if ($result->num_rows > 0) {
    // There is a scan ready to process
    $scanWaiting = true;

    // Grab the scan data
    while($row = $result->fetch_assoc()) {
        array_push($PERMITTED_DOMAINS, $row["domain"] );
    }
} else {
    // no scans currently waiting
}

// ====================================
// Scan Data
$SCAN;

// Check if there are any scans waiting
$scanWaiting = false;       // flag

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
//                                  DOMAIN RESTRICTION
// ========================================================================
// Check to see if the domain is authorised
$permitted = false;     // flag
foreach ($PERMITTED_DOMAINS as $domain) {
    // Loop through each of the permitted domains and check if it matches the current scan
    if (parse_url($SCAN->getTarget())["host"] == $domain) {
        $permitted = true;
    }
}

if (!$permitted) {
    // Domain requested as target is not authorised
    
    // set SCAN status to error ('domain_unauthorized') and die();
    // TODO
}


// ========================================================================
//                                  TOOLS
// ========================================================================
// Execute each of the tools:

// example tool
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

// MG TOOL
$TOOL_Nmap = new TOOL_Nmap($SCAN, "Nmap");
$TOOL_Nmap->Execute();

// MG TOOL
$TOOL_cURL = new TOOL_cURL($SCAN, "cURL");
$TOOL_cURL->Execute();

// MG TOOL
$TOOL_XSRFProbe = new TOOL_XSRFProbe($SCAN, "XSRFProbe");
$TOOL_XSRFProbe->Execute();

// LC TOOL
$TOOL_XSStrike = new TOOL_XSStrike($SCAN, "XSStrike");
$TOOL_XSStrike->Execute();

// SC TOOL
$TOOL_GoSpider = new TOOL_GoSpider($SCAN, "GoSpider");
$TOOL_GoSpider->Execute();

print_r($TOOL_GoSpider->getOutput());

// SC TOOL
$TOOL_Gobuster = new TOOL_Gobuster($SCAN, "Gobuster");
$TOOL_Gobuster->Execute();

// SC TOOL
$TOOL_sqlmap = new TOOL_sqlmap($SCAN, "sqlmap");
$TOOL_sqlmap->Execute();

// SC TOOL
$TOOL_Commix = new TOOL_Commix($SCAN, "commix");
$TOOL_Commix->Execute();

// ... next tool
//$TOOL_Nmap = new TOOL_Nmap($SCAN, "nmap");

print_r($TOOL_Dummy);
print_r($TOOL_a2sv);
print_r($TOOL_cdnCheck);
print_r($TOOL_ProTravel);

// ========================================================================
//                                  VULNERABILITIES
// ========================================================================

// Analyse each of the vulnerabilities:
//$VULN_Dummy = new VULN_Dummy($SCAN, [$TOOL_GoSpider, $TOOL_Dummy]);
//$VULN_Dummy->Analyse();

// PY VULNERABILITY
$VULN_InsecureServer = new VULN_InsecureServer($SCAN, [$TOOL_a2sv]);
$VULN_InsecureServer->Analyse();

// PY VULNERABILITY
$VULN_DDOS = new VULN_DDOS($SCAN, [$TOOL_cdnCheck]);
$VULN_DDOS->Analyse();

// PY VULNERABILITY
$VULN_PathTraversal = new VULN_Dummy($SCAN, [$TOOL_ProTravel]);
$VULN_PathTraversal->Analyse();

// MG VULNERABILITY
$VULN_SecurityMscfg = new VULN_SecurityMscfg($SCAN, [$TOOL_Nmap, $TOOL_Dirbuster]);
$VULN_SecurityMscfg->Analyse();

// MG VULNERABILITY
$VULN_CSRF = new VULN_CSRF($SCAN, [$TOOL_XSRFProbe]);
$VULN_CSRF->Analyse();

// MG VULNERABILITY
$VULN_SSRF = new VULN_SSRF($SCAN, [$TOOL_cURL]);
$VULN_SSRF->Analyse();

// LC VULNERABILITY
$VULN_BrokenAccessCtl = new VULN_BrokenAccessCtl($SCAN, [$TOOL_XSRFProbe, $TOOL_cURL]);
$VULN_BrokenAccessCtl->Analyse();

// LC VULNERABILITY
$VULN_CryptographicFlrs = new VULN_CryptographicFlrs($SCAN, [$TOOL_cURL]);
$VULN_CryptographicFlrs->Analyse();


// LC VULNERABILITY
$VULN_XSS = new VULN_XSS($SCAN, [$TOOL_XSStrike]);
$VULN_XSS->Analyse();

// SC VULNERABILITY
$VULN_HostInfo = new VULN_HostInfo($SCAN, [$TOOL_Nmap]);
$VULN_HostInfo->Analyse();

// SC VULNERABILITY
$VULN_Sitemap = new VULN_Sitemap($SCAN, [$TOOL_Gobuster]);
$VULN_Sitemap->Analyse();

// SC VULNERABILITY
$VULN_SQLInjection = new VULN_SQLInjection($SCAN, [$TOOL_sqlmap]);
$VULN_SQLInjection->Analyse();

// SC VULNERABILITY
$VULN_CMDInjection = new VULN_CMDInjection($SCAN, [$TOOL_Commix]);
$VULN_CMDInjection->Analyse();



$html = "<article> ... stuff inside ... </article>";


// Persist the report
// save that html to the db


// Update the scan status
































echo "Finished. Sleeping.";
sleep(100);

} ?>