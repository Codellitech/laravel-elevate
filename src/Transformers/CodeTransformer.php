<?php

namespace Codellitech\Elevate\Transformers;

interface CodeTransformer
{
    /**
     * Transform the given file content.
     *
     * @param string $content
     * @param string $path
     * @return string
     */
    public function transform(string $content, string $path): string;
}
