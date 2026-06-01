<?php

namespace App\Support;

use App\Enums\EducationalLevel;

class PatronOptions
{
    public static function educationalLevelRule(): string
    {
        return 'required|in:'.implode(',', EducationalLevel::values());
    }

    /** @return list<string> */
    public static function yearOptionsFor(?string $level): array
    {
        if ($level === null || $level === '') {
            return [];
        }

        return config("patron.year_options.{$level}", []);
    }

    /** @return list<string> */
    public static function allYearOptions(): array
    {
        $merged = [];
        foreach (config('patron.year_options', []) as $options) {
            $merged = array_merge($merged, $options);
        }

        return array_values(array_unique($merged));
    }
}
