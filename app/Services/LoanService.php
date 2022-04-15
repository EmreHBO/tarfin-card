<?php

namespace App\Services;

use App\Constants\PaymentStatus;
use App\Exceptions\AlreadyRepaidException;
use App\Exceptions\AmountHigherThanOutstandingAmountException;
use App\Models\Loan;
use App\Models\ReceivedRepayment;
use App\Models\ScheduledRepayment;
use App\Models\User;

class LoanService
{
    protected string $secretKey;

    /**
     * @param string $secretKey
     */
    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * @param User $user
     * @param int $amount
     * @param string $currencyCode
     * @param int $terms
     * @param $processedAt
     * @param string $status
     * @return mixed
     */
    public function createLoan(User $user, int $amount, string $currencyCode, int $terms, $processedAt, string $status = PaymentStatus::DUE): mixed
    {

        $loan = Loan::create(array(
            "user_id" => $user->id,
            "amount" => $amount,
            "currency_code" => $currencyCode,
            "terms" => $terms,
            "outstanding_amount" => $amount,
            "status" => $status,
            "processed_at" => $processedAt,
        ));

        $installment = $minInstallment = floor($amount / $terms);
        for ($index = 0; $index < $terms; $index++) {
            $maxInstallment = $amount - ($minInstallment * ($terms - 1));
            if ($index == $terms - 1) {
                $installment = $maxInstallment;
            }
            $scheduledRepayment = ScheduledRepayment::create(array(
                "loan_id" => $loan->id,
                "amount" => $installment,
                "outstanding_amount" => $installment,
                "currency_code" => $currencyCode,
                "due_date" => $processedAt->clone()->addMonth($index + 1),
                "status" => PaymentStatus::DUE,
            ));
        }

        return $loan;
    }

    /**
     * @param Loan $loan
     * @param int $receivedRepayment
     * @param string $currencyCode
     * @param $receivedAt
     * @return Loan
     * @throws AlreadyRepaidException
     * @throws AmountHigherThanOutstandingAmountException
     */
    public function repayLoan(Loan $loan, int $receivedRepayment, string $currencyCode, $receivedAt)
    {
        if ($loan->status == PaymentStatus::REPAID) {
            throw new AlreadyRepaidException();
        }

        $loanOutstanding = $loan->outstanding_amount;

        $scheduledRepayment = $loan->scheduledRepayments()->where('status', '<>', PaymentStatus::REPAID)->first();

        if (is_null($scheduledRepayment)) {
            throw new AlreadyRepaidException();
        }

        $scheduledOutstanding = $scheduledRepayment->outstanding_amount;

        //dd($receivedAt,$scheduledRepayment->due_date,$loanOutstanding,$scheduledOutstanding, $receivedRepayment);  /*2022-04-20 00:00:00 , 1668*/
        if ($receivedRepayment > $loanOutstanding) {
            throw new AmountHigherThanOutstandingAmountException();
        } else {
            if ($scheduledOutstanding == $receivedRepayment) {
                $scheduledRepayment->status = PaymentStatus::REPAID;
                $scheduledOutstanding = 0;
                $loanOutstanding -= $receivedRepayment;
                if ($loanOutstanding == 0) {
                    $loan->status = PaymentStatus::REPAID;
                }
            } else if ($scheduledOutstanding > $receivedRepayment) {
                $scheduledOutstanding -= $receivedRepayment;
                $scheduledRepayment->status = PaymentStatus::PARTIAL;
                $loanOutstanding -= $receivedRepayment;
            } else {
                $diff = $receivedRepayment - $scheduledOutstanding;
                $nextScheduledRepayment = $loan->scheduledRepayments()->where('id', '>', $scheduledRepayment->id)->first();
                $nextScheduledRepayment->update(['status' => PaymentStatus::PARTIAL, 'outstanding_amount' => $nextScheduledRepayment->outstanding_amount - $diff]);

                $scheduledRepayment->status = PaymentStatus::REPAID;
                $loanOutstanding -= ($scheduledOutstanding + $diff);
                $scheduledOutstanding = 0;
            }

            $loan->outstanding_amount = $loanOutstanding;
            $scheduledRepayment->outstanding_amount = $scheduledOutstanding;

            $scheduledRepayment->save();
            $loan->save();

            ReceivedRepayment::create(array(
                "loan_id" => $loan->id,
                "amount" => $receivedRepayment,
                "currency_code" => $currencyCode,
                "received_at" => $receivedAt,
            ));

            return $loan;
        }
    }
}
