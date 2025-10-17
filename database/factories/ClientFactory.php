<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company(), // ou name() se preferir pessoa física
            'document' => $this->faker->numerify('##############'), // CNPJ/CPF genérico (somente números)
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
            'cep' => $this->faker->numerify('#####-###'),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
