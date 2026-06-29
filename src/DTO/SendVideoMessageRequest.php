<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class SendVideoMessageRequest implements RequestPayload
{
    private function __construct(
        public string  $phone,
        public string  $video,
        public ?string $caption,
    )
    {
    }

    /**
     * @param string $video URL pública ou base64 do vídeo.
     */
    public static function create(string $phone, string $video, ?string $caption = null): self
    {
        return new self(
            PayloadValidator::phone($phone),
            PayloadValidator::media($video, 'video'),
            PayloadValidator::optionalString($caption, 'caption', 4096),
        );
    }

    public function toArray(): array
    {
        $payload = [
            'phone' => $this->phone,
            'video' => $this->video,
        ];

        if ($this->caption !== null) {
            $payload['caption'] = $this->caption;
        }

        return $payload;
    }
}
