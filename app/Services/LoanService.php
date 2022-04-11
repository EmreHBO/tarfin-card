<?php

namespace App\Services;

class LoanService
{
    protected string $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public static function createLoan()
    {
        return 'createLoanadasd';
    }
   /*
   public static function createLoan($customer, $amount, $currencyCode, $terms, $processedAt)
    {
        return 'createLoan';
    }
*/
    public static function repayLoan($loan, int $receivedRepayment, string $currencyCode, $receivedAt)
    {
        return 'repayLoan';
    }
}
