<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Services\LeadService;

class LeadsController {
    private $leadService;

    public function __construct(LeadService $leadService) {
        $this->leadService = $leadService;
    }

    /**
     * Create a new lead
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        // Get the request body
        $data = $request->getParsedBody();

        // Process the lead
        $result = $this->leadService->processLead($data);

        // Write the result to the response body
        $response->getBody()->write(json_encode($result));

        // Return the response with the correct content type
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * List all leads
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function list(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        // Retrieve leads from the service
        $leads = $this->leadService->getLeads();

        // Write the leads to the response body
        $response->getBody()->write(json_encode($leads));

        // Return the response with the correct content type
        return $response->withHeader('Content-Type', 'application/json');
    }
}