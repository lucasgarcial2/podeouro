<?php

namespace ADM\QuickDevBar\Service\App;

use ADM\QuickDevBar\Api\ServiceInterface;

class Cache implements ServiceInterface
{
    protected array $cacheEvents = [];

    public function addCache(string $event, string $identifier): void
    {
        // Inicializa o identificador se não estiver presente
        if (!isset($this->cacheEvents[$identifier])) {
            $this->cacheEvents[$identifier] = ['load' => 0, 'save' => 0, 'remove' => 0];
        }

        // Verifica se o evento é válido antes de incrementar
        if (array_key_exists($event, $this->cacheEvents[$identifier])) {
            $this->cacheEvents[$identifier][$event]++;
        }
    }

    public function pullData(): arra
    {
        return $this->cacheEvents;
    }
}
