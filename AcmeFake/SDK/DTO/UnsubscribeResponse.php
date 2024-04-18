<?php

declare(strict_types=1);

namespace Platform\Bundle\MTN\MADAPI\SDK\Response;

class SubscriptionTerminateResponse
{
    private int $responseCode;
    private string $description;
    private string $transactionId;
    private string $timestamp;

    public function __construct(int $responseCode, string $description, string $transactionId, string $timestamp)
    {
        $this->responseCode = $responseCode;
        $this->description = $description;
        $this->transactionId = $transactionId;
        $this->timestamp = $timestamp;
    }

    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    // Method to parse the response from the HTTP response headers
    public static function fromHttpResponseHeaders(array $headers): ?SubscriptionTerminateResponse
    {
        // Check if the response headers are valid
        if (!isset($headers['responsecode']) || !isset($headers['description']) || !isset($headers['transactionid']) || !isset($headers['timestamp'])) {
            return null;
        }

        // Extract data from headers
        $responseCode = (int)$headers['responsecode'];
        $description = $headers['description'];
        $transactionId = $headers['transactionid'];
        $timestamp = $headers['timestamp'];

        // Create and return the SubscriptionTerminateResponse object
        return new SubscriptionTerminateResponse($responseCode, $description, $transactionId, $timestamp);
    }
}
