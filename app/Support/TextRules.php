<?php

namespace App\Support;

use Closure;

/** Plain-text validation shared by the section admin forms (mirrors the live checks in admin.js). */
class TextRules
{
    public const MESSAGE = "The :attribute may only contain letters, numbers, spaces and . , ' - & ! ? (no emojis, special characters, leading or repeated spaces).";

    /** Letters, digits, spaces and . , ' - & ! ?; no leading/repeated spaces; no word longer than $wordMax. */
    public static function line(bool $required, int $min, int $max, int $wordMax): array
    {
        return [
            $required ? 'required' : 'nullable',
            'string',
            "min:{$min}",
            "max:{$max}",
            "regex:/^(?! )(?!.*  )[A-Za-z0-9 .,'&!?-]+$/",
            function (string $attribute, mixed $value, Closure $fail) use ($wordMax) {
                foreach (preg_split('/\s+/', (string) $value) as $word) {
                    if (mb_strlen($word) > $wordMax) {
                        $fail("Words in the :attribute may not be longer than {$wordMax} characters.");

                        return;
                    }
                }
            },
        ];
    }

    public const MESSAGE_NO_DIGITS = "The :attribute may only contain letters, spaces and . , ' - & ! ? (no numbers, emojis, special characters, leading or repeated spaces).";

    /** Same as line() but numbers are not allowed either: letters, spaces and . , ' - & ! ? only. */
    public static function lineNoDigits(bool $required, int $min, int $max, int $wordMax): array
    {
        $rules = self::line($required, $min, $max, $wordMax);
        $rules[4] = "regex:/^(?! )(?!.*  )[A-Za-z .,'&!?-]+$/";

        return $rules;
    }

    /** Messages for lineNoDigits() fields. */
    public static function messagesNoDigits(array $fields): array
    {
        $out = [];
        foreach ($fields as $field) {
            $out["{$field}.regex"] = self::MESSAGE_NO_DIGITS;
        }

        return $out;
    }

    /** The shared regex message for each field. */
    public static function messages(array $fields): array
    {
        $out = [];
        foreach ($fields as $field) {
            $out["{$field}.regex"] = self::MESSAGE;
        }

        return $out;
    }
}
