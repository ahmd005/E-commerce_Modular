<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // 1) طلبات المستخدم مرتّبة بالأحدث (paginateUserOrders)
            $table->index(['user_id', 'created_at'], 'idx_orders_user_created');

            // 2) طلبات الإدارة مفلترة بالحالة ومرتّبة بالأحدث (paginateAllOrders مع status)
            $table->index(['status', 'created_at'], 'idx_orders_status_created');

            // 3) طلبات الإدارة بدون فلترة، مرتّبة بالأحدث فقط (paginateAllOrders بدون status)
            $table->index('created_at', 'idx_orders_created');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_user_created');
            $table->dropIndex('idx_orders_status_created');
            $table->dropIndex('idx_orders_created');
        });
    }
};