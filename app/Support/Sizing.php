<?php

namespace App\Support;

use App\Models\Department;

class Sizing
{
    /** @var list<string> */
    public const LETTER_SIZES = ['S', 'M', 'L', 'XL'];

    /** @var list<string> */
    public const AGE_SIZES = [
        '0-1 year',
        '1-2 years',
        '2-3 years',
        '3-4 years',
        '4-5 years',
        '5-6 years',
        '6-7 years',
        '7-8 years',
        '8-9 years',
        '9-10 years',
        '10-11 years',
        '11-12 years',
    ];

    /** @var list<string> */
    public const AGE_DEPARTMENT_SLUGS = [
        'kids-boys',
        'kids-girls',
    ];

    public static function departmentUsesAgeSizing(?Department $department): bool
    {
        return $department !== null
            && in_array($department->slug, self::AGE_DEPARTMENT_SLUGS, true);
    }

    /** @return array<string, string> */
    public static function selectOptions(?Department $department): array
    {
        $sizes = self::departmentUsesAgeSizing($department)
            ? self::AGE_SIZES
            : self::LETTER_SIZES;

        return array_combine($sizes, $sizes);
    }

    /** @return list<string> */
    public static function sizeOrder(?Department $department): array
    {
        return self::departmentUsesAgeSizing($department)
            ? self::AGE_SIZES
            : self::LETTER_SIZES;
    }

    public static function sizePickerLabel(?Department $department): string
    {
        return self::departmentUsesAgeSizing($department) ? 'Age' : 'Size';
    }
}
