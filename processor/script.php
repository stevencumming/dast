<?php
/*
    DAST Backend Processing
*/
// ========================================================================
//                           IMPORTS / INCLUDES
// ========================================================================
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
require_once('./tools/TOOL_ProTravel.php');
require_once('./tools/TOOL_Nikto.php');

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
require_once('./vulns/VULN_InsecureServer.php');
require_once('./vulns/VULN_CMDInjection.php');
require_once('./vulns/VULN_DDOS.php');
require_once('./vulns/VULN_IDAuth.php');
require_once('./vulns/VULN_InsecDesign.php');
require_once('./vulns/VULN_VulnOutComponents.php');


// ========================================================================
//                           GLOBAL VARS / CONSTANTS
// ========================================================================
// Database Connection constants
define("DB_SERVERNAME", "170.187.240.98");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "DAST34swin@");
define("DB_DBNAME", "u428402158_dast");

// Script Update Interval (seconds)
define("SCRIPT_INTERVAL", "10");

// Flags
$FATAL = FALSE; // Fatal error encountered


// ========================================================================
//                            HELPER FUNCTIONS
// ========================================================================
function updateScanStatus($status, $SCAN) {
    // Create connection to database
    $conn = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_DBNAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // If connected, update the scan status
    $sql = "UPDATE scan SET scan.status = '" . $status . "' WHERE id = " . $SCAN->getScanID();

    if ($conn->query($sql) === TRUE) {
        return TRUE;    // Success
      } else {
        $FATAL = TRUE;  // Set fatal error flag
        return FALSE;   // Failure
      }
      
      $conn->close();
}


// ========================================================================
//                             SCRIPT - BEGIN
// ========================================================================
// Run PHP script continuously
while (TRUE) {


// ========================================================================
//                           DATABASE CONNECTION
// ========================================================================
// Create connection
$conn = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_DBNAME);
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
    $scanWaiting = TRUE;

    // Grab the scan data
    while($row = $result->fetch_assoc()) {
        array_push($PERMITTED_DOMAINS, $row["domain"] );
    }
}

// ====================================
// Scan Data (Initialise var)
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
// Close the database connection for now (we don't want to keep it open all the time)
$conn->close();


// ====================================
if  (!$scanWaiting) {
    //  No scans waiting
    echo "No scans to process.";
    echo "Last updated: " . date("Y-m-d H:i:s") . "\n\n";
    
    // Sleep for 10 seconds then go back to the start
    sleep(SCRIPT_INTERVAL);
    continue;       // break out of this iteration of while loop and go back to the start.
}

// ====================================
// There is a scan to process (continue execution with below code)

// ========================================================================
//                                  DOMAIN RESTRICTION
// ========================================================================
// Check to see if the domain is authorised
$permitted = false;     // flag
foreach ($PERMITTED_DOMAINS as $domain) {
    // Loop through each of the permitted domains and check if it matches the current scan
    if (parse_url($SCAN->getTarget())["host"] == $domain) {
        $permitted = true;

        // break out
        break;
    }
}

if (!$permitted) {
    // Domain requested as target is not authorised
    // set SCAN status to error ('domain_unauthorized') and die();
    updateScanStatus("domain_unauthorized", $SCAN);

    // break out of this scan
    continue;
} else {
    // Scan is permitted, update the status to in progress and begin scanning

    // SC TODO
    updateScanStatus("in_progress", $SCAN);

    echo "\nScan commencing for " . parse_url($SCAN->getTarget())["host"] . "\n\n";
}


// ========================================================================
//                                  TOOLS
// ========================================================================
// Execute each of the tools:

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

// SC TOOL
$TOOL_Gobuster = new TOOL_Gobuster($SCAN, "Gobuster");
$TOOL_Gobuster->Execute();

// SC TOOL
$TOOL_sqlmap = new TOOL_sqlmap($SCAN, "sqlmap");
$TOOL_sqlmap->Execute();

// SC TOOL
$TOOL_Commix = new TOOL_Commix($SCAN, "commix");
$TOOL_Commix->Execute();

// SH TOOL
$TOOL_Nikto = new TOOL_Nikto($SCAN, "Nikto");
$TOOL_Nikto->Execute();


// ========================================================================
//                                  VULNERABILITIES
// ========================================================================

// Analyse each of the vulnerabilities:

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
$VULN_SecurityMscfg = new VULN_SecurityMscfg($SCAN, [$TOOL_Nmap, $TOOL_Gobuster]);
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

// SH VULNERABILITY
$VULN_IDAuth = new VULN_IDAuth($SCAN, [$TOOL_Nikto]);
$VULN_IDAuth->Analyse();

// SH VULNERABILITY
$VULN_InsecDesign = new VULN_InsecDesign($SCAN, [$TOOL_Nikto]);
$VULN_InsecDesign->Analyse();

// SH VULNERABILITY
$VULN_VulnOutComponents = new VULN_VulnOutComponents($SCAN, [$TOOL_Nikto]);
$VULN_VulnOutComponents->Analyse();


// ========================================================================
//                            COMPILE REPORT
// ========================================================================
// go through each of the processed vulnerabilities and compile the final report
$html = "<article>";


// ====================================
//  SUMMARY
$html .= "<section>";
$html .= "<h2>Summary</h2>";
$html .= "<p>TODO</p>";
$html .= "</section>";


// ====================================
// SC VULNERABILITY - VULN_HostInfo
$html .= "<section>";
$html .= "<h2>Reconnaissance: Host Information</h2>";

// Severity Score (will always be information level):
$html .= "<p>";
$html .= "<span class='severity_score sev_0'>";
$html .= "0 – INFORMATION";
$html .= "</span></p>";

// Reconnaissance Content:
$html .= "<p>";
$html .= $VULN_HostInfo->getHTML();
$html .= "</p>";
$html .= "</section>";


// ====================================
// SC VULNERABILITY - VULN_Sitemap
$html .= "<section>";
$html .= "<h2>Reconnaissance: Sitemap</h2>";

// Severity Score (will always be information level):
$html .= "<p>";
$html .= "<span class='severity_score sev_0'>";
$html .= "0 – INFORMATION";
$html .= "</span></p>";

// Reconnaissance Content:
$html .= "<p>";
$html .= $VULN_Sitemap->getHTML();
$html .= "</p>";
$html .= "</section>";


// ====================================
// ====================================
// PY VULNERABILITY - VULN_InsecureServer
$html .= "<section>";
$html .= "<h2>Vulnerability: Insecure Server Configuration</h2>";

// Severity Score:
$html .= "<p>";
switch ($VULN_InsecureServer->getSeverity()) {
    case '0':           // INFORMATION
        $html .= "<span class='severity_score sev_0'>";
        $html .= "0 – INFORMATION";
        break;
    
    case '1':           // LOW
        $html .= "<span class='severity_score sev_1'>";
        $html .= "1 –  LOW";
        break;

    case '2':           // MEDIUM
        $html .= "<span class='severity_score sev_2'>";
        $html .= "2 –  MEDIUM";
        break;

    case '3':           // HIGH
        $html .= "<span class='severity_score sev_3'>";
        $html .= "3 –  HIGH";
        break;

    
}
$html .= "</span></p>";

// Vulnerability Content:
$html .= "<p>";
$html .= $VULN_InsecureServer->getHTML();
$html .= "</p>";
$html .= "</section>";


// ====================================
// PY VULNERABILITY - VULN_DDOS
$html .= "<section>";
$html .= "<h2>Vulnerability: Distributed Denial-of-service attack</h2>";

// Severity Score:
$html .= "<p>";
switch ($VULN_DDOS->getSeverity()) {
    case '0':           // INFORMATION
        $html .= "<span class='severity_score sev_0'>";
        $html .= "0 – INFORMATION";
        break;
    
    case '1':           // LOW
        $html .= "<span class='severity_score sev_1'>";
        $html .= "1 –  LOW";
        break;

    case '2':           // MEDIUM
        $html .= "<span class='severity_score sev_2'>";
        $html .= "2 –  MEDIUM";
        break;

    case '3':           // HIGH
        $html .= "<span class='severity_score sev_3'>";
        $html .= "3 –  HIGH";
        break;

    
}
$html .= "</span></p>";

// Vulnerability Content:
$html .= "<p>";
$html .= $VULN_DDOS->getHTML();
$html .= "</p>";
$html .= "</section>";


// ====================================
// PY VULNERABILITY - VULN_PathTraversal
$html .= "<section>";
$html .= "<h2>Vulnerability: Path Traversal</h2>";

// Severity Score:
$html .= "<p>";
switch ($VULN_PathTraversal->getSeverity()) {
    case '0':           // INFORMATION
        $html .= "<span class='severity_score sev_0'>";
        $html .= "0 – INFORMATION";
        break;
    
    case '1':           // LOW
        $html .= "<span class='severity_score sev_1'>";
        $html .= "1 –  LOW";
        break;

    case '2':           // MEDIUM
        $html .= "<span class='severity_score sev_2'>";
        $html .= "2 –  MEDIUM";
        break;

    case '3':           // HIGH
        $html .= "<span class='severity_score sev_3'>";
        $html .= "3 –  HIGH";
        break;

    
}
$html .= "</span></p>";

// Vulnerability Content:
$html .= "<p>";
$html .= $VULN_PathTraversal->getHTML();
$html .= "</p>";
$html .= "</section>";


// ====================================
// MG VULNERABILITY - VULN_SecurityMscfg
$html .= "<section>";
$html .= "<h2>Vulnerability: Security Misconfiguration</h2>";

// Severity Score:
$html .= "<p>";
switch ($VULN_SecurityMscfg->getSeverity()) {
    case '0':           // INFORMATION
        $html .= "<span class='severity_score sev_0'>";
        $html .= "0 – INFORMATION";
        break;
    
    case '1':           // LOW
        $html .= "<span class='severity_score sev_1'>";
        $html .= "1 –  LOW";
        break;

    case '2':           // MEDIUM
        $html .= "<span class='severity_score sev_2'>";
        $html .= "2 –  MEDIUM";
        break;

    case '3':           // HIGH
        $html .= "<span class='severity_score sev_3'>";
        $html .= "3 –  HIGH";
        break;

    
}
$html .= "</span></p>";

// Vulnerability Content:
$html .= "<p>";
$html .= $VULN_SecurityMscfg->getHTML();
$html .= "</p>";
$html .= "</section>";


// ====================================
// MG VULNERABILITY - VULN_CSRF
$html .= "<section>";
$html .= "<h2>Vulnerability: Cross-Site Request Forgery</h2>";

// Severity Score:
$html .= "<p>";
switch ($VULN_CSRF->getSeverity()) {
    case '0':           // INFORMATION
        $html .= "<span class='severity_score sev_0'>";
        $html .= "0 – INFORMATION";
        break;
    
    case '1':           // LOW
        $html .= "<span class='severity_score sev_1'>";
        $html .= "1 –  LOW";
        break;

    case '2':           // MEDIUM
        $html .= "<span class='severity_score sev_2'>";
        $html .= "2 –  MEDIUM";
        break;

    case '3':           // HIGH
        $html .= "<span class='severity_score sev_3'>";
        $html .= "3 –  HIGH";
        break;

    
}
$html .= "</span></p>";

// Vulnerability Content:
$html .= "<p>";
$html .= $VULN_CSRF->getHTML();
$html .= "</p>";
$html .= "</section>";


// ====================================
// MG VULNERABILITY - VULN_SSRF
$html .= "<section>";
$html .= "<h2>Vulnerability: Server Side Request Forgery</h2>";

// Severity Score:
$html .= "<p>";
switch ($VULN_SSRF->getSeverity()) {
    case '0':           // INFORMATION
        $html .= "<span class='severity_score sev_0'>";
        $html .= "0 – INFORMATION";
        break;
    
    case '1':           // LOW
        $html .= "<span class='severity_score sev_1'>";
        $html .= "1 –  LOW";
        break;

    case '2':           // MEDIUM
        $html .= "<span class='severity_score sev_2'>";
        $html .= "2 –  MEDIUM";
        break;

    case '3':           // HIGH
        $html .= "<span class='severity_score sev_3'>";
        $html .= "3 –  HIGH";
        break;

    
}
$html .= "</span></p>";

// Vulnerability Content:
$html .= "<p>";
$html .= $VULN_SSRF->getHTML();
$html .= "</p>";
$html .= "</section>";


// ====================================
// LC VULNERABILITY - VULN_BrokenAccessCtl
$html .= "<section>";
$html .= "<h2>Vulnerability: Broken Access Control</h2>";

// Severity Score:
$html .= "<p>";
switch ($VULN_BrokenAccessCtl->getSeverity()) {
    case '0':           // INFORMATION
        $html .= "<span class='severity_score sev_0'>";
        $html .= "0 – INFORMATION";
        break;
    
    case '1':           // LOW
        $html .= "<span class='severity_score sev_1'>";
        $html .= "1 –  LOW";
        break;

    case '2':           // MEDIUM
        $html .= "<span class='severity_score sev_2'>";
        $html .= "2 –  MEDIUM";
        break;

    case '3':           // HIGH
        $html .= "<span class='severity_score sev_3'>";
        $html .= "3 –  HIGH";
        break;

    
}
$html .= "</span></p>";

// Vulnerability Content:
$html .= "<p>";
$html .= $VULN_BrokenAccessCtl->getHTML();
$html .= "</p>";
$html .= "</section>";


// ====================================
// LC VULNERABILITY - VULN_CryptographicFlrs
$html .= "<section>";
$html .= "<h2>Vulnerability: VULN_Cryptographic Failures</h2>";

// Severity Score:
$html .= "<p>";
switch ($VULN_CryptographicFlrs->getSeverity()) {
    case '0':           // INFORMATION
        $html .= "<span class='severity_score sev_0'>";
        $html .= "0 – INFORMATION";
        break;
    
    case '1':           // LOW
        $html .= "<span class='severity_score sev_1'>";
        $html .= "1 –  LOW";
        break;

    case '2':           // MEDIUM
        $html .= "<span class='severity_score sev_2'>";
        $html .= "2 –  MEDIUM";
        break;

    case '3':           // HIGH
        $html .= "<span class='severity_score sev_3'>";
        $html .= "3 –  HIGH";
        break;

    
}
$html .= "</span></p>";

// Vulnerability Content:
$html .= "<p>";
$html .= $VULN_CryptographicFlrs->getHTML();
$html .= "</p>";
$html .= "</section>";


// ====================================
// LC VULNERABILITY - VULN_XSS
$html .= "<section>";
$html .= "<h2>Vulnerability: Cross-Site-Scripting</h2>";

// Severity Score:
$html .= "<p>";
switch ($VULN_XSS->getSeverity()) {
    case '0':           // INFORMATION
        $html .= "<span class='severity_score sev_0'>";
        $html .= "0 – INFORMATION";
        break;
    
    case '1':           // LOW
        $html .= "<span class='severity_score sev_1'>";
        $html .= "1 –  LOW";
        break;

    case '2':           // MEDIUM
        $html .= "<span class='severity_score sev_2'>";
        $html .= "2 –  MEDIUM";
        break;

    case '3':           // HIGH
        $html .= "<span class='severity_score sev_3'>";
        $html .= "3 –  HIGH";
        break;

    
}
$html .= "</span></p>";

// Vulnerability Content:
$html .= "<p>";
$html .= $VULN_XSS->getHTML();
$html .= "</p>";
$html .= "</section>";


// ====================================
// SC VULNERABILITY - VULN_SQLInjection
$html .= "<section>";
$html .= "<h2>Vulnerability: SQL Injection</h2>";

// Severity Score:
$html .= "<p>";
switch ($VULN_SQLInjection->getSeverity()) {
    case '0':           // INFORMATION
        $html .= "<span class='severity_score sev_0'>";
        $html .= "0 – INFORMATION";
        break;
    
    case '1':           // LOW
        $html .= "<span class='severity_score sev_1'>";
        $html .= "1 –  LOW";
        break;

    case '2':           // MEDIUM
        $html .= "<span class='severity_score sev_2'>";
        $html .= "2 –  MEDIUM";
        break;

    case '3':           // HIGH
        $html .= "<span class='severity_score sev_3'>";
        $html .= "3 –  HIGH";
        break;

    
}
$html .= "</span></p>";

// Vulnerability Content:
$html .= "<p>";
$html .= $VULN_SQLInjection->getHTML();
$html .= "</p>";
$html .= "</section>";


// ====================================
// SC VULNERABILITY - VULN_CMDInjection
$html .= "<section>";
$html .= "<h2>Vulnerability: Command Injection</h2>";

// Severity Score:
$html .= "<p>";
switch ($VULN_CMDInjection->getSeverity()) {
    case '0':           // INFORMATION
        $html .= "<span class='severity_score sev_0'>";
        $html .= "0 – INFORMATION";
        break;
    
    case '1':           // LOW
        $html .= "<span class='severity_score sev_1'>";
        $html .= "1 –  LOW";
        break;

    case '2':           // MEDIUM
        $html .= "<span class='severity_score sev_2'>";
        $html .= "2 –  MEDIUM";
        break;

    case '3':           // HIGH
        $html .= "<span class='severity_score sev_3'>";
        $html .= "3 –  HIGH";
        break;

    
}
$html .= "</span></p>";

// Vulnerability Content:
$html .= "<p>";
$html .= $VULN_CMDInjection->getHTML();
$html .= "</p>";
$html .= "</section>";


// ====================================
// SH VULNERABILITY - VULN_IDAuth
$html .= "<section>";
$html .= "<h2>Vulnerability: ID Auth</h2>";

// Severity Score:
$html .= "<p>";
switch ($VULN_IDAuth->getSeverity()) {
    case '0':           // INFORMATION
        $html .= "<span class='severity_score sev_0'>";
        $html .= "0 – INFORMATION";
        break;
    
    case '1':           // LOW
        $html .= "<span class='severity_score sev_1'>";
        $html .= "1 –  LOW";
        break;

    case '2':           // MEDIUM
        $html .= "<span class='severity_score sev_2'>";
        $html .= "2 –  MEDIUM";
        break;

    case '3':           // HIGH
        $html .= "<span class='severity_score sev_3'>";
        $html .= "3 –  HIGH";
        break;

    
}
$html .= "</span></p>";

// Vulnerability Content:
$html .= "<p>";
$html .= $VULN_IDAuth->getHTML();
$html .= "</p>";
$html .= "</section>";


// ====================================
// SH VULNERABILITY - VULN_InsecDesign
$html .= "<section>";
$html .= "<h2>Vulnerability: Insecure Design</h2>";

// Severity Score (will always be information level):
$html .= "<p>";
$html .= "<span class='severity_score sev_0'>";
$html .= "0 – INFORMATION";
$html .= "</span></p>";

// Vulnerability Content:
$html .= "<p>";
$html .= $VULN_InsecDesign->getHTML();
$html .= "</p>";
$html .= "</section>";


// ====================================
// SH VULNERABILITY - VULN_VulnOutComponents
$html .= "<section>";
$html .= "<h2>Vulnerability: Vulnerable or Outdated Components</h2>";

// Severity Score:
$html .= "<p>";
switch ($VULN_VulnOutComponents->getSeverity()) {
    case '0':           // INFORMATION
        $html .= "<span class='severity_score sev_0'>";
        $html .= "0 – INFORMATION";
        break;
    
    case '1':           // LOW
        $html .= "<span class='severity_score sev_1'>";
        $html .= "1 – LOW";
        break;

    case '2':           // MEDIUM
        $html .= "<span class='severity_score sev_2'>";
        $html .= "2 – MEDIUM";
        break;

    case '3':           // HIGH
        $html .= "<span class='severity_score sev_3'>";
        $html .= "3 – HIGH";
        break;

    
}
$html .= "</span></p>";

// Vulnerability Content:
$html .= "<p>";
$html .= $VULN_VulnOutComponents->getHTML();
$html .= "</p>";
$html .= "</section>";

$html .= "</article>";




// ========================================================================
//                            PERSIST REPORT
// ========================================================================
// write the generated html for the report to the database against this scan

// Create connection to database
$conn = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_DBNAME);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If connected, update the scan html
$sql = "UPDATE scan SET html = '" . $conn->real_escape_string($html) . "' WHERE id = " . $SCAN->getScanID();
// TODO -- I think this will need to mysqli_real_escape_string() 

if (!($conn->query($sql)) === TRUE) {
    $FATAL = TRUE;  // Set fatal error flag
  }

$conn->close();





// ========================================================================
//                      UPDATE SCAN STATUS - COMPLETED
// ========================================================================
if ($FATAL) {
    // If there was a fatal error encountered in the script return 
    updateScanStatus("failed", $SCAN);
} else {
    updateScanStatus("completed", $SCAN);

    // TODO
    // Update the time_completed against this scan
}


// ========================================================================
echo "\n\n\nFinished. Sleeping...";
sleep(SCRIPT_INTERVAL);

} ?>
