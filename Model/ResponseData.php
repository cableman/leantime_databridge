<?php

namespace Leantime\Plugins\Databridge\Model;

/**
 * Response wrapper for consistent API JSON structure.
 */
readonly class ResponseData
{
    public function __construct(
        public array $parameters,
        public int $resultsCount,
        public array $results,
    ) {}

    /**
     * Convert to array for JSON serialization.
     */
    public function toArray(): array
    {
        return [
            'parameters' => $this->parameters,
            'resultsCount' => $this->resultsCount,
            'results' => $this->results,
        ];
    }
}
