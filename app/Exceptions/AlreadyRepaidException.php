<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;

class AlreadyRepaidException extends \Exception
{
    public function validationException(): ValidationException
    {
        return ValidationException::withMessages([
           'Paid' => 'Already paid!',
        ]);
    }
}
