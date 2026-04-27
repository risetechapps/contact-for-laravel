<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->boolean('is_primary')->default(false)->after('department');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('is_primary');

            $table->index('is_primary');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex(['is_primary']);
            $table->dropIndex(['sort_order']);
            $table->dropColumn(['is_primary', 'sort_order']);
        });
    }
};
