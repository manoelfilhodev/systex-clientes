<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Cria somente os campos que ainda não existem
            if (!Schema::hasColumn('invoices', 'boleto_barcode')) {
                $table->string('boleto_barcode')->nullable();
            }

            if (!Schema::hasColumn('invoices', 'inter_charge_id')) {
                $table->string('inter_charge_id')->nullable();
            }

            // Renomeia boleto_url -> boleto_pdf_url (se desejar padronizar)
            if (Schema::hasColumn('invoices', 'boleto_url') && !Schema::hasColumn('invoices', 'boleto_pdf_url')) {
                $table->renameColumn('boleto_url', 'boleto_pdf_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'boleto_barcode')) {
                $table->dropColumn('boleto_barcode');
            }

            if (Schema::hasColumn('invoices', 'inter_charge_id')) {
                $table->dropColumn('inter_charge_id');
            }

            if (Schema::hasColumn('invoices', 'boleto_pdf_url') && !Schema::hasColumn('invoices', 'boleto_url')) {
                $table->renameColumn('boleto_pdf_url', 'boleto_url');
            }
        });
    }
};
