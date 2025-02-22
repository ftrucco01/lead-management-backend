<?php
namespace App\Services;

use App\Services\NotificationService;

class LeadService {
    private $db;
    private const VALID_SOURCES = ['facebook', 'google', 'linkedin', 'manual'];
    private $notificationService;

    public function __construct(\PDO $db, NotificationService $notificationService) {
        $this->db = $db;
        $this->notificationService = $notificationService;
    }

    /**
     * Processes a lead by validating input and inserting it into the database.
     * 
     * @param array $data The lead data to be processed.
     * @return array The result of the lead processing.
     */
    public function processLead(array $data): array {
        // Validate input before DB interaction
        $validationErrors = $this->validateInput($data);
        if (!empty($validationErrors)) {
            return [
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validationErrors,
            ];
        }
                
        try {
            // Insert the lead into the database
            $leadId = $this->insertLead($data);

            //notify to slack
            $this->notificationService->notifyExternalSystem([
                'lead_id' => $leadId,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'source' => $data['source']
            ]);

            // Return a success response with the inserted lead ID
            return [
                'status' => 'success',
                'message' => 'Lead created successfully',
                'lead_id' => $leadId,
            ];
        } catch (\PDOException $e) {
            return [
                'status' => 'error',
                'message' => 'Database error',
                'details' => $e->getMessage(),
            ];
        }
    }

    /**
     * Inserts a lead into the database.
     */
    private function insertLead(array $data): int {
        // Prepare the INSERT statement
        $stmt = $this->db->prepare("
            INSERT INTO leads (name, email, phone, source)
            VALUES (:name, :email, :phone, :source)
        ");

        // Execute the statement with the provided data
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':phone' => $data['phone'] ?? null,
            ':source' => $data['source'],
        ]);

        // Return the ID of the inserted lead
        return $this->db->lastInsertId();
    }

    /**
     * Validates the input data for the lead.
     */
    private function validateInput(array $data): array {
        $errors = [];

        if (empty($data['name']) || strlen($data['name']) < 3 || strlen($data['name']) > 50) {
            $errors['name'] = 'Name must be between 3 and 50 characters.';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'A valid email address is required.';
        }

        if (!empty($data['phone']) && !preg_match('/^\d{7,15}$/', $data['phone'])) {
            $errors['phone'] = 'Phone number must be between 7 and 15 digits.';
        }

        if (empty($data['source']) || !in_array($data['source'], self::VALID_SOURCES, true)) {
            $errors['source'] = 'Source must be one of: ' . implode(', ', self::VALID_SOURCES);
        }

        return $errors;
    }

    /**
     * Retrieves all leads from the database.
     * 
     * @return array The list of leads.
     */
    public function getLeads(): array {
        //only for practice purposes, in real life I never fetch all rows and records
        $stmt = $this->db->query("SELECT id, name, email, phone, source FROM leads");
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}