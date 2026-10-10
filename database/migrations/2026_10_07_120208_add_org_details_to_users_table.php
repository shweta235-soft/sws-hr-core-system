<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            $table->string('team')->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('salary_structure', 10, 2)->default(30000.00);
            $table->date('joining_date')->default('2026-10-07');
        });
    }
    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropColumn(['team', 'manager_id', 'salary_structure', 'joining_date']);
        });
    }
};
