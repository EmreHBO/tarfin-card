<?php

namespace App\Services;

use App\Constants\PaymentStatus;
use App\Exceptions\AmountHigherThanOutstandingAmountException;
use App\Models\Loan;
use App\Models\ScheduledRepayment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

    // public array $scheduledRepayments;

    /**
     * @param $user
     * @param $amount
     * @param $currencyCode
     * @param $terms
     * @param $processedAt
     * @param string $status
     * @return mixed
     */
    public function createLoan($user, $amount, $currencyCode, $terms, $processedAt, $status = PaymentStatus::DUE)
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
            $maxInstallment = $amount-($minInstallment*($terms-1));
            if($index == $terms-1){$installment = $maxInstallment;}
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

    public function repayLoan($loan, $receivedRepayment, $currencyCode, $receivedAt)
    {

        $scheduledRepayment = ScheduledRepayment::where(['loan_id' => $loan->id, 'due_date' => $receivedAt])->get();
        $scheduledRepaymentId = $scheduledRepayment[0]['id'];
        $scheduledRepayment = ScheduledRepayment::find($scheduledRepaymentId);
        //dd((int)$scheduledRepayment->outstanding_amount);

        if ($scheduledRepayment->outstanding_amount - $receivedRepayment == 0){
            $scheduledRepayment->outstanding_amount = 0;
            $scheduledRepayment->status = PaymentStatus::REPAID;
        }
        elseif ($scheduledRepayment->outstanding_amount - $receivedRepayment > 0) {
            $scheduledRepayment->outstanding_amount = $scheduledRepayment->outstanding_amount - $receivedRepayment;
            $scheduledRepayment->status = PaymentStatus::PARTIAL;
        }
        else {
            throw new AmountHigherThanOutstandingAmountException();
        }
        $scheduledRepayment->save();
       // dd($scheduledRepayment, $scheduledRepayment->outstanding_amount);

        $loan = Loan::find($loan->id);
        $loanOutstanding = $loan->outstanding_amount;
        $loan->outstanding_amount = $loanOutstanding - $receivedRepayment;
        $loan->save();

        return $loan;
    }

}
