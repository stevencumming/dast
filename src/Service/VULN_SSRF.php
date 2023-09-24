<?php

namespace App\Service;

use App\Entity\Vulnerability;
use App\Entity\Tool;

use App\Repository\ToolRepository;

class VULN_SSRF {
    /*
        Vulnerability:          Server Side Request Forgery
        Responsible:            MG
        OpenProject Phase #:    456

        Summary:
            ... quick summary on the vulnerability and what tools are required
            Making the server send a request to an unauthorised destination without first validating the target URL
            This can be used to do things like read the contents of etc/passwd or gain access to the admin dashboard of a web app
            cURL will be used to request the target to display the contents of its etc/passwd 
            and will also be used to navigate to the admin console if a /admin page was found during directory busting


        Output (HTML): TODO
            HTML formatted output to go straight into the Report.
    */

    private ToolRepository $ToolsRepository;
    private Tool $TCURL;
    private $Severity;
    private $HTML;

    
    // TODO look up the results of a Tool entity from the database
    // I think it's something like: ??

    public function __construct(private Vulnerability $vulnerability){
        $this->ToolsRepository = $entityManager->getRepository(Tool::class);
        $this->$TCURL = $this->ToolsRepository->findOneBy([
            'name' => 'curl',
            'scan' => $vulnerability->getScanId(),
        ]);

    }
    
    
    // Now that I have the tool, analyse the output of it

    // Information output
    public function Analyse() {
        // process the data from the tool
        
        // fetch the data and decode the JSON (not sure if this is done by Symfony...?)
        //$output = json_decode($this->$TCURL->getResults(), true);
        $output = $this->$TCURL->getResults();

        // analyse it somehow TODO
        // will need to analyse the results of curl to see whether or not contents of etc/passwd were discovered
        // and/or whether the admin page was able to be reached (will just have to rely on the reply codes for both I think)

        // calculate the severities and store
        // this shou;d be a moderate severity vulnerability I think, as either of the two things that are being tested
        // can become significant attack vectors if they are not enough to compromise a system on their own
        $this->Severity = 2;

        // and the HTML:
        $this->HTML = "<p>The results are: " . $output . ". Yes, this will need more formatting and extraction
        of $output...";

    }

    public function getSeverity(): ?int
    {
        return $this->Severity;
    }
    public function getHTML(): ?int
    {
        return $this->HTML;
    }

}


