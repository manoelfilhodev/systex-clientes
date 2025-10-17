{{-- Cabeçalho --}}
<x-filament::section heading="💼 Fatura #{{ $record->invoice_number }}">
    <ul class="text-sm space-y-1">
        <li><strong>Emitida em:</strong> {{ $record->created_at->format('d/m/Y') }}</li>
        <li><strong>Cliente:</strong> {{ $record->client->name ?? '—' }}</li>
        <li><strong>Valor:</strong> R$ {{ number_format($record->amount, 2, ',', '.') }}</li>
        <li><strong>Status:</strong>
            @php
                $colors = ['paid' => 'success', 'pending' => 'warning', 'canceled' => 'danger'];
            @endphp
            <x-filament::badge color="{{ $colors[$record->status] ?? 'gray' }}">
                {{ ucfirst($record->status) }}
            </x-filament::badge>
        </li>
        <li><strong>Vencimento:</strong> {{ \Carbon\Carbon::parse($record->due_date)->format('d/m/Y') }}</li>
    </ul>
</x-filament::section>
@if (!empty($record->pix_qr_code))
    @php
        $qrPath = $record->pix_qr_code;
    @endphp

    @if (Storage::disk('public')->exists($qrPath))
        <div class="flex justify-center mb-3">
            <img src="{{ asset('storage/' . $qrPath) }}" alt="QR Code PIX" loading="lazy"
                class="rounded-lg shadow w-52 h-52 object-contain border border-gray-300 dark:border-gray-700">
        </div>
    @else
        <p class="text-xs text-gray-500 dark:text-gray-400 italic text-center">
            QR Code ainda não disponível.
        </p>
    @endif
@endif
{{-- Pagamento via PIX --}}
@if (!empty($record->pix_copia_cola))
    <x-filament::section heading="💰 Pagamento via PIX">
        <div class="grid md:grid-cols-2 gap-6 items-center">
            {{-- QR Code --}}
            @if (!empty($record->pix_qr_code) && Storage::exists($record->pix_qr_code))
                <div class="flex justify-center">
                    <img src="{{ Storage::url($record->pix_qr_code) }}" alt="QR Code PIX" loading="lazy"
                        class="rounded-lg shadow w-52 h-52 object-contain border border-gray-300 dark:border-gray-700">
                </div>
            @endif

            {{-- Copia e Cola --}}
            <div x-data="{ copied: false }">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Código Copia e Cola:</p>
                <textarea readonly
                    class="w-full text-xs p-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800"
                    rows="3">{{ $record->pix_copia_cola }}</textarea>

                <x-filament::button size="xs" color="gray"
                    x-on:click="navigator.clipboard.writeText(`{{ $record->pix_copia_cola }}`); copied=true; setTimeout(()=>copied=false,2000)">
                    <span x-show="!copied">📋 Copiar código PIX</span>
                    <span x-show="copied" class="text-green-500">✅ Copiado!</span>
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
@endif

{{-- Pagamento via Boleto --}}
@if ($record->boleto_linha_digitavel)
    <x-filament::section heading="🏦 Pagamento via Boleto">
        <p class="font-mono text-xs mb-2 break-all">
            {{ $record->boleto_linha_digitavel }}
        </p>

        <div class="flex items-center gap-2">
            @if ($record->boleto_url)
                <x-filament::button size="sm" tag="a" target="_blank" href="{{ $record->boleto_url }}"
                    color="gray" icon="heroicon-o-arrow-down-tray">
                    Baixar Boleto
                </x-filament::button>
            @endif
        </div>
    </x-filament::section>
@endif
@php $boletoColor = $record->status === 'pending' ? 'success' : 'gray'; @endphp
<x-filament::button size="sm" tag="a" target="_blank" href="{{ $record->boleto_url }}"
    color="{{ $boletoColor }}" icon="heroicon-o-arrow-down-tray">
    Baixar Boleto
</x-filament::button>

{{-- Botão de Voltar --}}
<div class="mt-6 text-center">
    <x-filament::button tag="a" href="{{ route('filament.admin.resources.invoices.index') }}" color="gray">
        ← Voltar
    </x-filament::button>
</div>
