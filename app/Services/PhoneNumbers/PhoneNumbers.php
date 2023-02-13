<?php

namespace App\Services\PhoneNumbers;

use App\Services\PhoneNumbers\Contracts\PhoneNumbers as PhoneNumbersInterface;

class PhoneNumbers implements PhoneNumbersInterface
{
    /**
     * @inheritDoc
     */
    public function storeNumbers(): string
    {
        $fileName = time().'_'.request()->file->getClientOriginalName();
        request()->file('file')->storeAs('', $fileName, 'public');

        return $fileName;
    }

    /**
     * @inheritDoc
     */
    public function getNumbers($fileName): array
    {
        $path = storage_path('app/public/' . $fileName);
        $file = fopen($path, 'r');

        $phoneNumbers = [];
        if ($file !== false) {
            while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {

                $phoneNumbers[] = $data[0];
            }

            fclose($file);
        }

        return $phoneNumbers;
    }

    /**
     * @inheritDoc
     */
    public function formatNumbers(array $phoneNumbers): array
    {
        $validNumbers = [];

        foreach ($phoneNumbers as $number)
        {
            $number = $this->convertToE164Format($number);

            // Optimize and order the list of right number blocks for Dutch phone numbers.
            $optimizedPhoneNumber = $this->optimizePhoneNumberBlocks($number);

            if ($optimizedPhoneNumber) {
                $data['number'] = $optimizedPhoneNumber;
                $validNumbers[] = $data;
            }
        }

        return $validNumbers;
    }

    /**
     * Convert number into E164 format.
     *
     * @param $number
     * @return string
     */
    private function convertToE164Format($number): ?string
    {
        // Remove all non-numeric characters from the phone number
        $numericPhoneNumber = preg_replace('/\D+/', '', $number);

        // Check if the phone number already starts with the Dutch country code
        if (substr($numericPhoneNumber, 0, 2) == '31') {
            $formattedPhoneNumber = '+' . $numericPhoneNumber;
        } else {
            // Check if the phone number starts with a leading zero
            if (substr($numericPhoneNumber, 0, 1) == '0') {
                // Replace the leading zero with the Dutch country code
                $numericPhoneNumber = '31' . substr($numericPhoneNumber, 1);
            } else {
                // Prepend the Dutch country code
                $numericPhoneNumber = '31' . $numericPhoneNumber;
            }

            // Format the phone number to E.164
            $formattedPhoneNumber = '+' . $numericPhoneNumber;
        }

        return $formattedPhoneNumber;
    }

    /**
     * Optimize phone numbers by block.
     *
     * @param $phoneNumber
     * @return false|mixed|string
     */
    private function optimizePhoneNumberBlocks($phoneNumber)
    {
        // Define the regular expressions for Dutch phone number blocks.
        $singleNumberPattern = '/^(\+31[1-9][0-9]{7,8})$/';
        $tenBlockPattern = '/^(\+31[1-9][0-9]{1})[ \-]?([0-9]{2})[ \-]?([0-9]{2})[ \-]?([0-9]{2})[ \-]?([0-9]{2})$/';
        $hundredBlockPattern = '/^(\+31[1-9][0-9]{2})[ \-]?([0-9]{2})[ \-]?([0-9]{2})[ \-]?([0-9]{2})$/';
        $thousandBlockPattern = '/^(\+31[1-9][0-9])?[ \-]?([0-9]{3})[ \-]?([0-9]{3})$/';

        $phone = false;
        // Match the phone number to each expression and return the optimized phone number.
        if (preg_match($singleNumberPattern, $phoneNumber, $matches)) {
            $phone = $matches[1];
        } else if (preg_match($tenBlockPattern, $phoneNumber, $matches)) {
            $phone = $matches[1] . $matches[2] . $matches[3] . $matches[4] . $matches[5];
        } else if (preg_match($hundredBlockPattern, $phoneNumber, $matches)) {
            $phone = $matches[1] . $matches[2] . $matches[3] . $matches[4];
        } else if (preg_match($thousandBlockPattern, $phoneNumber, $matches)) {
            $phone = $matches[1] . $matches[2] . $matches[3];
        }

        return $phone;
    }
}
