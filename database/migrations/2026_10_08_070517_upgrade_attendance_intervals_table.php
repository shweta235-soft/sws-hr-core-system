<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('attendances', function (Blueprint $table) {
            $table->timestamp('break_start_time')->nullable();
            $table->timestamp('break_end_time')->nullable();
            $table->integer('worked_minutes')->default(0);
        });
    }
    public function down() {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['break_start_time', 'break_end_time', 'worked_minutes']);
        });
    }
};
