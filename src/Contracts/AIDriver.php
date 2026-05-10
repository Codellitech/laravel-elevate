<?php

namespace Codellitech\Elevate\Contracts;

interface AIDriver
{
    /**
     * Send a prompt to the AI model.
     *
     * @param string $prompt
     * @param array $options
     * @return string
     */
    public function prompt(string $prompt, array $options = []): string;

    /**
     * Get the model name.
     *
     * @return string
     */
    public function getModel(): string;
}
