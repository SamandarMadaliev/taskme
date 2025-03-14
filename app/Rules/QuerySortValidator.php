<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class QuerySortValidator implements ValidationRule
{
    private array $sortTypes = ['asc', 'desc'];
    private array $sortColumnNames = [
        'created_at',
        'status',
        'priority',
        'due_date'
    ];

    /**
     * Run the validation rule.
     *
     * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail(sprintf('The attribute %s must be an array', $attribute));
        }
        foreach ($value as $columnName => $sortType) {
            if (!in_array($columnName, $this->sortColumnNames, true)) {
                $fail(sprintf('Sorting by this column %s is invalid', $columnName));
            }
            if (!in_array($sortType, $this->sortTypes, true)) {
                $fail(sprintf('The attribute %s must be a valid sort type', $attribute));
            }
        }
    }
}
