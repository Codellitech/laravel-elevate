<?php

namespace Codellitech\Elevate\Transformers;

use Codellitech\Elevate\AI\AIManager;

class AITransformer implements CodeTransformer
{
    protected AIManager $ai;

    public function __construct(AIManager $ai)
    {
        $this->ai = $ai;
    }

    public function transform(string $content, string $path): string
    {
        $prompt = "Modernize the following Laravel file: {$path}\n\nCode:\n{$content}\n\nReturn only the modernized code, no explanation.";
        
        return $this->ai->engine()->prompt($prompt);
    }
}
