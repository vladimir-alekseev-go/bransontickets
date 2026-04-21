<?php

declare(strict_types=1);

namespace common\components;

use Yii;
use yii\mail\BaseMailer;
use yii\httpclient\Client;
use yii\base\Exception;

class GraphMailer extends BaseMailer
{
    public $messageClass = GraphMessage::class;

    public string $clientId;
    public string $clientSecret;
    public string $tenantId;
    public string $senderEmail;

    protected function sendMessage($message): bool
    {
        try {
            $accessToken = $this->getAccessToken();

            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl("https://graph.microsoft.com/v1.0/users/{$this->senderEmail}/sendMail")
                ->addHeaders([
                    'Authorization' => "Bearer {$accessToken}",
                    'Content-Type' => 'application/json',
                ])
                ->setContent(json_encode([
                    "message" => [
                        "subject" => $message->getSubject(),
                        "body" => [
                            "contentType" => "HTML",
                            "content" => $message->getHtmlBody() ?: $message->getTextBody(),
                        ],
                        "toRecipients" => $this->convertAddresses($message->getTo()),
                        "ccRecipients" => $this->convertAddresses($message->getCc()),
                        "bccRecipients" => $this->convertAddresses($message->getBcc()),
                    ],
                    "saveToSentItems" => false
                ], JSON_THROW_ON_ERROR))
                ->send();

            if (!$response->isOk) {
                Yii::error("MS Graph API Error: " . $response->content, __METHOD__);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Yii::error("Mailer Exception: " . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    private function getAccessToken(): string
    {
        $cacheKey = "ms_graph_token_" . md5($this->clientId);

        return Yii::$app->cache->getOrSet($cacheKey, function () {
            $client = new Client(['transport' => 'yii\httpclient\CurlTransport']);

            $response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl("https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token")
                ->setData([
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'scope' => 'https://graph.microsoft.com/.default',
                    'grant_type' => 'client_credentials',
                ])
                ->send();

            if (!$response->isOk) {
                throw new Exception("Microsoft Auth Failed: " . $response->content);
            }

            return (string)$response->data['access_token'];
        }, 3000);
    }

    private function convertAddresses(array|string|null $addresses): array
    {
        if (empty($addresses)) return [];
        $normalized = (array)$addresses;
        $result = [];
        foreach ($normalized as $email => $name) {
            $address = is_int($email) ? $name : $email;
            $result[] = ["emailAddress" => ["address" => $address]];
        }
        return $result;
    }
}
