<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->enum('payment_status', ['0', '1', '2'])->default('0')->after('status')->comment('0=Unpaid, 1=Paid, 2=Processing');
            $table->decimal('pay_amount', 10, 2)->nullable()->after('payment_status');
            $table->string('currency', 10)->nullable()->after('pay_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'pay_amount', 'currency']);
        });
    }
};
