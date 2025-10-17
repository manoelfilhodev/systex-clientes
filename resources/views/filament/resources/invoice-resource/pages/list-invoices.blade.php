<x-filament-panels::page>
    <x-filament::section heading="📑 Faturas">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border-separate border-spacing-y-1">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-400">
                        <th class="px-3 py-2 text-left">#</th>
                        <th class="px-3 py-2 text-left">Cliente</th>
                        <th class="px-3 py-2 text-left">Valor</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-left">Vencimento</th>
                        <th class="px-3 py-2 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records->take(50) as $invoice)
                        <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 transition rounded-lg">
                            <td class="px-3 py-2 font-mono text-gray-600 dark:text-gray-300">
                                {{ $invoice->invoice_number }}
                            </td>
                            <td class="px-3 py-2">{{ $invoice->client->name ?? '—' }}</td>
                            <td class="px-3 py-2 font-semibold">
                                R$ {{ number_format($invoice->amount, 2, ',', '.') }}
                            </td>
                            <td class="px-3 py-2">
                                @php
                                    $colors = ['paid' => 'success', 'pending' => 'warning', 'canceled' => 'danger'];
                                @endphp
                                <x-filament::badge color="{{ $colors[$invoice->status] ?? 'gray' }}">
                                    {{ ucfirst($invoice->status) }}
                                </x-filament::badge>
                            </td>
                            <td class="px-3 py-2">
                                {{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                <x-filament::button
                                    tag="a"
                                    color="gray"
                                    size="xs"
                                    href="{{ route('filament.admin.resources.invoices.view', $invoice) }}">
                                    Ver
                                </x-filament::button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-400">
                                Nenhuma fatura encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->count() > 50)
            <div class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400">
                Exibindo as 50 faturas mais recentes.
            </div>
        @endif
    </x-filament::section>

    <div class="mt-6 text-center">
        <x-filament::button tag="a" href="{{ route('filament.admin.resources.invoices.create') }}" color="success">
            ➕ Nova Fatura
        </x-filament::button>
    </div>
</x-filament-panels::page>
