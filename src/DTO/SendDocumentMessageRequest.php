<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class SendDocumentMessageRequest implements RequestPayload
{
    private function __construct(
        public string  $phone,
        public string  $document,
        public ?string $fileName,
        public ?string $mimetype,
    )
    {
    }

    /**
     * @param string $document URL pública ou base64 do arquivo.
     */
    public static function create(
        string  $phone,
        string  $document,
        ?string $fileName = null,
        ?string $mimetype = null,
    ): self
    {
        return new self(
            PayloadValidator::phone($phone),
            PayloadValidator::media($document, 'document'),
            PayloadValidator::optionalString($fileName, 'fileName'),
            PayloadValidator::optionalString($mimetype, 'mimetype'),
        );
    }

    public function toArray(): array
    {
        $payload = [
            'phone' => $this->phone,
            'document' => $this->document,
        ];

        if ($this->fileName !== null) {
            $payload['fileName'] = $this->fileName;
        }

        if ($this->mimetype !== null) {
            $payload['mimetype'] = $this->mimetype;
        }

        return $payload;
    }
}
