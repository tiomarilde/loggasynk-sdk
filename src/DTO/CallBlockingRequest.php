<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class CallBlockingRequest implements RequestPayload
{
    private function __construct(
        public bool    $rejectCalls,
        public ?string $rejectMessage,
        public ?bool   $autoRead,
        public ?bool   $autoReadStatus,
    )
    {
    }

    public static function enabled(): self
    {
        return new self(true, null, null, null);
    }

    public static function disabled(): self
    {
        return new self(false, null, null, null);
    }

    /**
     * Configuração completa das automações da instância. Campos null são
     * omitidos (não alteram o valor atual). rejectMessage vazio ("") limpa a
     * mensagem automática.
     */
    public static function create(
        bool    $rejectCalls,
        ?string $rejectMessage = null,
        ?bool   $autoRead = null,
        ?bool   $autoReadStatus = null,
    ): self
    {
        return new self(
            $rejectCalls,
            PayloadValidator::optionalText($rejectMessage, 'rejectMessage', 1000),
            $autoRead,
            $autoReadStatus,
        );
    }

    public function toArray(): array
    {
        $payload = ['reject_calls' => $this->rejectCalls];

        if ($this->rejectMessage !== null) {
            $payload['reject_message'] = $this->rejectMessage;
        }

        if ($this->autoRead !== null) {
            $payload['auto_read'] = $this->autoRead;
        }

        if ($this->autoReadStatus !== null) {
            $payload['auto_read_status'] = $this->autoReadStatus;
        }

        return $payload;
    }
}
