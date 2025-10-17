<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition()
    {
        $cycles = ['mensal', 'trimestral', 'semestral', 'anual'];
        $statuses = ['ativa', 'suspensa', 'cancelada', 'encerrada'];

        $start = $this->faker->dateTimeBetween('-1 years', '+1 month');
        $end = (rand(0, 4) === 0) ? null : $this->faker->dateTimeBetween($start, '+1 years');

        return [
            // 'client_id' => will be provided when creating via relationship or seeder
            'plan_name' => $this->faker->randomElement([
                'Plano Básico', 'Plano Profissional', 'Plano Empresarial', 'Contrato Premium'
            ]),
            'amount' => $this->faker->randomFloat(2, 29, 1999),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end ? $end->format('Y-m-d') : null,
            'billing_cycle' => $this->faker->randomElement($cycles),
            'status' => $this->faker->randomElement($statuses),
            'notes' => $this->faker->optional(0.6)->sentence(),
        ];
    }
}
