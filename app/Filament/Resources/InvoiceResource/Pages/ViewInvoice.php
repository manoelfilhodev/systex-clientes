<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    public function getHeading(): string
    {
        return 'Detalhes da Fatura #' . $this->record->invoice_number;
    }

    /**
     * ✅ Exibe o conteúdo customizado (PIX + Boleto)
     * logo abaixo do formulário padrão do Filament.
     */
    public function getFooter(): ?View
    {
        return view('filament.resources.invoice-resource.pages.view-invoice', [
            'record' => $this->record,
        ]);
    }
}
