<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;

class AmountHigherThanOutstandingAmountException extends \Exception
{

    public function validationException(): ValidationException
    {
        return ValidationException::withMessages([
            'Higher Than Outstanding' => 'Amount higher than outstanding amount!',
        ]);
    }
}
