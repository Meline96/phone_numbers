<?php

namespace App\Services\PhoneNumbers\Contracts;

interface PhoneNumbers
{
    /**
     * Stores numbers file and returns filename.
     *
     * @return string
     */
    public function storeNumbers(): string;

    /**
     * Get numbers from stored file.
     *
     * @param string $fileName
     * @return array
     */
    public function getNumbers(string $fileName): array;

    /**
     * Format numbers block.
     *
     * @param array $phoneNumbers
     * @return array
     */
    public function formatNumbers(array $phoneNumbers): array;
}
