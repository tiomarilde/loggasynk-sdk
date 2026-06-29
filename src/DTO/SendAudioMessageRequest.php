<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class SendAudioMessageRequest implements RequestPayload
{
    private function __construct(
        public string  $phone,
        public string  $audio,
        public bool    $ptt,
        public ?string $mimetype,
    )
    {
    }

    /**
     * @param string $audio URL pública ou base64 do áudio.
     */
    public static function create(string $phone, string $audio, bool $ptt = false, ?string $mimetype = null): self
    {
        return new self(
            PayloadValidator::phone($phone),
            PayloadValidator::media($audio, 'audio'),
            $ptt,
            PayloadValidator::optionalString($mimetype, 'mimetype'),
        );
    }

    public function toArray(): array
    {
        $payload = [
            'phone' => $this->phone,
            'audio' => $this->audio,
            'ptt' => $this->ptt,
        ];

        if ($this->mimetype !== null) {
            $payload['mimetype'] = $this->mimetype;
        }

        return $payload;
    }
}
