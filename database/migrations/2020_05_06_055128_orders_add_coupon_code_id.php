<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrdersAddCouponCodeId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // onDelete('set null') 代表如果这个订单有关联优惠券并且该优惠券被删除时将自动把 coupon_code_id 设成 null
            $table->unsignedBigInteger('coupon_code_id')->nullable()->after('paid_at');
            $table->foreign('coupon_code_id')->references('id')->on('coupon_codes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // dropForeign() 方法的参数可以是字符串也可以是一个数组，如果是字符串则代表删除外键名为该字符串的外键，而如果是数组的话则会删除该数组中字段所对应的外键。我们这个 coupon_code_id 字段默认的外键名是 orders_coupon_code_id_foreign，因此需要通过数组的方式来删除。
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['coupon_code_id']);
            $table->dropColumn('coupon_code_id');
        });
    }
}
