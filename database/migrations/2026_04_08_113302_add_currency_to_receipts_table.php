<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyToReceiptsTable extends Migration
{
    public function up()
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->string('currency', 3)->default('UGX')->after('amount');
        });
    }

    public function down()
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
}
