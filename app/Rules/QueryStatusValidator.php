<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\TaskStatusEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class QueryStatusValidator implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $taskStatuses = array_map(fn($statusEnumCase) => $statusEnumCase->value, TaskStatusEnum::cases());
        if (!is_array($value)) {
            $fail(sprintf('The attribute %s must be an array', $attribute));
        }

        if (count(array_diff($value, $taskStatuses)) > 0) {
            $fail(sprintf('%s value invalid', $attribute));
        }
    }
}
