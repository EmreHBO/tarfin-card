<?php

namespace Database\Factories;

use App\Constants\CurrencyType;
use App\Constants\PaymentStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'amount' => $this->faker->numberBetween(1000, 9999),
            'terms' => $this->faker->numberBetween(1, 255),
            'outstanding_amount' => $this->faker->numberBetween(1000, 9999),
            'currency_code' => CurrencyType::ALL[$this->faker->numberBetween(0, sizeof(CurrencyType::ALL) - 1)],
            'processed_at' => $this->faker->dateTime(),
            'status' => PaymentStatus::ALL[$this->faker->numberBetween(0, sizeof(PaymentStatus::ALL) - 1)],
        ];
    }

}
