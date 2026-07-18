<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class PhoneValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $clean = preg_replace('/[^\d+]/', '', $value);

        // Normalize leading 00 to +
        if (str_starts_with($clean, '00')) {
            $clean = '+'.substr($clean, 2);
        }

        if (strlen($clean) < 7 || strlen($clean) > 15) {
            $fail('The :attribute must be a valid phone number between 7 and 15 digits.');

            return;
        }

        // 1. Detect Pakistan prefix
        if (str_starts_with($clean, '+92') || str_starts_with($clean, '92') || (str_starts_with($clean, '03') && strlen($clean) === 11)) {
            if (! preg_match('/^(\+?92|0)3[0-9]{9}$/', $clean) && ! preg_match('/^(\+?92|0)[1-9][0-9]{8,9}$/', $clean)) {
                $fail('The phone number must be a valid Pakistan number (e.g. 03001234567 or +923001234567).');
            }

            return;
        }

        // 2. Detect US / Canada prefix
        if (str_starts_with($clean, '+1') || str_starts_with($clean, '1') || (strlen($clean) === 10 && in_array(substr($clean, 0, 1), ['2', '3', '4', '5', '6', '7', '8', '9']))) {
            if (! preg_match('/^(\+?1)?[2-9][0-9]{9}$/', $clean)) {
                $fail('The phone number must be a valid US/Canada number (e.g. 2025550125 or +12025550125).');
            }

            return;
        }

        // 3. Detect UK prefix
        if (str_starts_with($clean, '+44') || str_starts_with($clean, '44') || (str_starts_with($clean, '0') && (strlen($clean) === 11 || strlen($clean) === 10) && ! str_starts_with($clean, '03'))) {
            if (! preg_match('/^(\+?44|0)[1-9][0-9]{9}$/', $clean) && ! preg_match('/^(\+?44|0)7[0-9]{9}$/', $clean)) {
                $fail('The phone number must be a valid United Kingdom number (e.g. 07700900077 or +447700900077).');
            }

            return;
        }

        // 4. General fallback (International E.164)
        if (! preg_match('/^\+?[1-9][0-9]{6,14}$/', $clean)) {
            $fail('The phone number must be a valid international phone number.');
        }
    }
}
