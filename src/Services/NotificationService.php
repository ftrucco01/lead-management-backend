<?php

namespace App\Services;

use Psr\Log\LoggerInterface;

/**
 * Handles sending notifications to an external system with retry logic.
 * Utilizes cURL for HTTP POST requests and logs attempts and errors.
 */
class NotificationService {
    private string $externalApiUrl;
    private $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
        $this->externalApiUrl = $_ENV['EXTERNAL_API_URL'];
    }

    /**
     * Sends a notification to the external system with retries.
     *
     * @param array $leadData ['lead_id', 'name', 'email', 'source']
     * @return bool True if successful, false otherwise.
     */
    public function notifyExternalSystem(array $leadData): bool {
        $maxAttempts = 3;
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            $attempts++;

            try {
                $responseCode = $this->postRequest($leadData);

                if ($responseCode >= 200 && $responseCode < 300) {
                    return true;
                }

                $this->logger->warning("Attempt #$attempts failed with response code $responseCode.");
            } catch (\Exception $e) {
                $this->logger->error("Attempt #$attempts failed: " . $e->getMessage());
            }

            sleep(2);  // Wait before retrying
        }

        $this->logger->error("Failed to notify external system after $maxAttempts attempts.");
        return false;
    }

    /**
     * Sends a POST request to the external API.
     *
     * @param array $payload
     * @return int HTTP response code
     * @throws \Exception on request failure
     */
    private function postRequest(array $leadData): int {
        $ch = curl_init($this->externalApiUrl);
    
        // Generate a friendly message with pipe separators
        $formattedMessage = sprintf(
            ":rocket: *New Lead Created!*\n Lead ID: %s | Name: %s | Email: %s | Phone: %s | Source: %s",
            $leadData['lead_id'],
            $leadData['name'],
            $leadData['email'],
            $leadData['phone'] ?? 'N/A',
            ucfirst($leadData['source'])
        );
    
        // Prepare the JSON payload as a string
        $jsonPayload = json_encode(['text' => $formattedMessage]);
    
        if ($jsonPayload === false) {
            throw new \Exception('Failed to encode payload: ' . json_last_error_msg());
        }
    
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonPayload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonPayload),
            ],
            CURLOPT_RETURNTRANSFER => true,
        ]);
    
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if (curl_errno($ch)) {
            throw new \Exception('cURL Error: ' . curl_error($ch));
        }
    
        curl_close($ch);
    
        return $httpCode;
    }
    
}