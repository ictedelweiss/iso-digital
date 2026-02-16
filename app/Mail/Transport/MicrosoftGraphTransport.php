<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mime\Email;
use Exception;

class MicrosoftGraphTransport extends AbstractTransport
{
    protected string $tenantId;
    protected string $clientId;
    protected string $clientSecret;
    protected string $fromAddress;

    public function __construct(string $tenantId, string $clientId, string $clientSecret, string $fromAddress)
    {
        parent::__construct();
        $this->tenantId = $tenantId;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->fromAddress = $fromAddress;
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $token = $this->getAccessToken();

        $payload = [
            'message' => [
                'subject' => $email->getSubject(),
                'body' => [
                    'contentType' => 'HTML',
                    'content' => $email->getHtmlBody() ?: $email->getTextBody(), // Fallback to text if HTML is empty
                ],
                'toRecipients' => $this->mapRecipients($email->getTo()),
                'ccRecipients' => $this->mapRecipients($email->getCc()),
                'bccRecipients' => $this->mapRecipients($email->getBcc()),
                // Attachments would go here
            ],
            'saveToSentItems' => false,
        ];

        // Handle Attachments
        if ($email->getAttachments()) {
            $attachments = [];
            foreach ($email->getAttachments() as $attachment) {
                $attachments[] = [
                    '@odata.type' => '#microsoft.graph.fileAttachment',
                    'name' => $attachment->getFilename(),
                    'contentType' => $attachment->getContentType(),
                    'contentBytes' => base64_encode($attachment->getBody()),
                ];
            }
            $payload['message']['attachments'] = $attachments;
        }

        $endpoint = "https://graph.microsoft.com/v1.0/users/{$this->fromAddress}/sendMail";

        $response = Http::withToken($token)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($endpoint, $payload);

        if (!$response->successful()) {
            throw new Exception("Microsoft Graph API Error: " . $response->body());
        }
    }

    protected function getAccessToken(): string
    {
        $endpoint = "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token";

        $response = Http::asForm()->post($endpoint, [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => 'https://graph.microsoft.com/.default',
            'grant_type' => 'client_credentials',
        ]);

        if (!$response->successful()) {
            throw new Exception("Failed to obtain Access Token: " . $response->body());
        }

        return $response->json('access_token');
    }

    protected function mapRecipients(array $recipients): array
    {
        $mapped = [];
        foreach ($recipients as $recipient) {
            $mapped[] = [
                'emailAddress' => [
                    'address' => $recipient->getAddress(),
                    'name' => $recipient->getName(),
                ],
            ];
        }
        return $mapped;
    }

    public function __toString(): string
    {
        return 'microsoft-graph';
    }
}
