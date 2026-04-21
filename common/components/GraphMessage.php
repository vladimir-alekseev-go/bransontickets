<?php

declare(strict_types=1);

namespace common\components;

use yii\mail\BaseMessage;

class GraphMessage extends BaseMessage
{
    private string $_subject = '';
    private array|string|null $_to = null;
    private array|string|null $_cc = null;
    private array|string|null $_bcc = null;
    private ?string $_htmlBody = null;
    private ?string $_textBody = null;

    private array $_attachments = [];
    private array $_headers = [];

    public function getCharset(): string { return 'utf-8'; }
    public function setCharset($charset): self { return $this; }

    public function getFrom(): array|string|null { return null; }
    public function setFrom($from): self { return $this; }

    public function getTo(): array|string|null { return $this->_to; }
    public function setTo($to): self { $this->_to = $to; return $this; }

    public function getCc(): array|string|null { return $this->_cc; }
    public function setCc($cc): self { $this->_cc = $cc; return $this; }

    public function getBcc(): array|string|null { return $this->_bcc; }
    public function setBcc($bcc): self { $this->_bcc = $bcc; return $this; }

    public function getSubject(): string { return $this->_subject; }
    public function setSubject($subject): self { $this->_subject = $subject; return $this; }

    public function setHtmlBody($html): self { $this->_htmlBody = $html; return $this; }
    public function getHtmlBody(): ?string { return $this->_htmlBody; }

    public function setTextBody($text): self { $this->_textBody = $text; return $this; }
    public function getTextBody(): ?string { return $this->_textBody; }

    public function getReplyTo(): array|string|null { return null; }
    public function setReplyTo($replyTo): self { return $this; }

    /** Добавление вложения из файла */
    public function attach($fileName, array $options = []): self
    {
        $contentBytes = base64_encode(file_get_contents($fileName));
        $this->_attachments[] = [
            '@odata.type' => '#microsoft.graph.fileAttachment',
            'name' => $options['fileName'] ?? basename($fileName),
            'contentBytes' => $contentBytes,
        ];
        return $this;
    }

    /** Добавление вложения из строки */
    public function attachContent($content, array $options = []): self
    {
        $this->_attachments[] = [
            '@odata.type' => '#microsoft.graph.fileAttachment',
            'name' => $options['fileName'] ?? 'attachment.txt',
            'contentBytes' => base64_encode($content),
        ];
        return $this;
    }

    /** Встраивание файла (например, картинка) */
    public function embed($fileName, array $options = []): string
    {
        $cid = uniqid('cid_', true);
        $contentBytes = base64_encode(file_get_contents($fileName));
        $this->_attachments[] = [
            '@odata.type' => '#microsoft.graph.fileAttachment',
            'name' => $options['fileName'] ?? basename($fileName),
            'contentId' => $cid,
            'isInline' => true,
            'contentBytes' => $contentBytes,
        ];
        return "cid:$cid";
    }

    /** Встраивание контента напрямую */
    public function embedContent($content, array $options = []): string
    {
        $cid = uniqid('cid_', true);
        $this->_attachments[] = [
            '@odata.type' => '#microsoft.graph.fileAttachment',
            'name' => $options['fileName'] ?? 'inline.txt',
            'contentId' => $cid,
            'isInline' => true,
            'contentBytes' => base64_encode($content),
        ];
        return "cid:$cid";
    }

    public function getAttachments(): array
    {
        return $this->_attachments;
    }

    public function toString(): string
    {
        return "Subject: {$this->_subject}";
    }

    public function addHeader($name, $value): self
    {
        $this->_headers[$name][] = $value;
        return $this;
    }

    public function setHeader($name, $value): self
    {
        $this->_headers[$name] = [$value];
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->_headers;
    }
}
