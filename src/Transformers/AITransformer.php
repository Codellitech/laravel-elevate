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
        $prompt = "Modernize the following Laravel file: {$path}\n\n" .
                 "Apply modern PHP 8.2+ features (readonly, typed properties, etc.) and Laravel best practices.\n" .
                 "Code:\n{$content}\n\n" .
                 "Return ONLY the modernized code without any explanation or markdown formatting.";
        
        return $this->ai->engine()->prompt($prompt);
    }
}
