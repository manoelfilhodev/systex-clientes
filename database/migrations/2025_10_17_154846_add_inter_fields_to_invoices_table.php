<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {

            // Adiciona apenas os campos que ainda não existem
            if (!Schema::hasColumn('invoices', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('amount');
            }

            // PIX - adicione apenas os que NÃO existem
            if (!Schema::hasColumn('invoices', 'pix_txid')) {
                $table->string('pix_txid')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'pix_copia_cola')) {
                $table->text('pix_copia_cola')->nullable();
            }

            // BOLETO
            if (!Schema::hasColumn('invoices', 'boleto_nosso_numero')) {
                $table->string('boleto_nosso_numero')->nullable();
                $table->string('boleto_linha_digitavel')->nullable();
                $table->string('boleto_barcode')->nullable();
                $table->text('boleto_pdf_url')->nullable();
            }

            // ID da cobrança no Banco Inter
            if (!Schema::hasColumn('invoices', 'inter_charge_id')) {
                $table->string('inter_charge_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'pix_txid',
                'pix_copia_cola',
                'boleto_nosso_numero',
                'boleto_linha_digitavel',
                'boleto_barcode',
                'boleto_pdf_url',
                'inter_charge_id',
            ]);
        });
    }
};
