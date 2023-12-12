<?php

namespace Zekreto;

use Zekreto\Exceptions\ZekretoClientException;

class ZekretoClient
{
    private string $apiKey;
    private string $apiUrl;
    public bool $throwErrors = false;
    private \Monolog\Logger $logger;

    const DEFAULT_SERVER_URL = 'https://zekreto.com/api/';

    /**
     * Creates a new ZekretoClient instance.
     * 
     * @param string $apiKey The API key from your Zekreto account.
     * @param string $apiUrl The URL of the Zekreto API. Defaults to .
     * @param array $monologHandlers An array of Monolog handlers to use for logging. Defaults to none.
     */
    public function __construct(string $apiKey, array $monologHandlers = [])
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = getenv('ZEKRETO_API_URL') ?? static::DEFAULT_SERVER_URL;
        $this->logger = new \Monolog\Logger('zekreto');
        foreach ($monologHandlers as $handler) {
            $this->logger->pushHandler($handler);
        }
        $this->throwErrors = (filter_var(getenv('ZEKRETO_EMPTYSTR_ON_ERROR'), FILTER_VALIDATE_BOOLEAN) == false);
    }

    /**
     * Encrypts a secret.
     * 
     * @param string $secret The secret to encrypt.
     * @return string The encrypted secret. If an error occurs an empty string is returned.
     */
    public function encrypt(string $secret): string
    {
        $encryptedData = $this->sendRequest("encrypt", ['secret' => $secret]);
        return $encryptedData;
    }

    /**
     * Decrypts an encrypted secret.
     * 
     * @param string $encryptedSecret The encrypted secret to decrypt.
     * @return string The decrypted secret. If an error occurs an empty string is returned.
     */
    public function decrypt(string $encryptedSecret): string
    {
        $data = $this->sendRequest("decrypt", ['secret' => $encryptedSecret]);
        return $data;
    }

    /**
     * Completes the request to the Zekreto API. 
     * 
     * @param string $endpoint The endpoint to call.
     * @param array $requestBody The request body.
     * @return string The encrypted or decrypted data. If an error occurs an empty string is returned.
     */
    private function sendRequest(string $endpoint, array $requestBody): string
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->apiUrl,
            'timeout'  => 2.0,
        ]);
        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $body = array_merge([], $requestBody);
        try {
            $response = $client->request('POST', $endpoint, ['headers' => $headers, 'body' => json_encode($body)]);
            $result = json_decode($response->getBody()->getContents())->secret;
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            $this->logger->error("Error " . $e->getMessage());
            $result = "";

            if ($this->throwErrors) {
                $this->logger->error("Converting Exception to " . ZekretoClientException::class);
                throw new ZekretoClientException($e->getMessage());
            }
        }

        return $result;
    }
}
