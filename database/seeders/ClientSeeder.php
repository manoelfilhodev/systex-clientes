<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Subscription;

class ClientSeeder extends Seeder
{
    public function run()
    {
        // criar 20 clientes e para cada 0..3 assinaturas
        Client::factory()
            ->count(20)
            ->create()
            ->each(function (Client $client) {
                Subscription::factory()
                    ->count(rand(0, 3))
                    ->create([
                        'client_id' => $client->id,
                    ]);
            });
    }
}
