<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // 9. OVERTIME LAYER (Linked safely to users)
        if (!Schema::hasTable('overtime')) {
            Schema::create('overtime', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->date('date');
                $table->integer('overtime_minutes')->default(0);
                $table->string('status')->default('Pending'); // Pending, Approved
                $table->timestamps();
            });
        }

        // 13/14/15. PAYMENTS, NOTIFICATIONS & APPROVALS HUB
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('title');
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->timestamps();
            });
        }

        // 16/18. DOCUMENTS & ASSETS / VAULT ENCRYPTION SECURITY REGISTRY
        if (!Schema::hasTable('documents_vault')) {
            Schema::create('documents_vault', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('document_name');
                $table->string('encrypted_file_path');
                $table->string('device_trust_signature')->nullable(); // 22. Device Trust
                $table->string('idempotency_key')->unique(); // 21. Offline Idempotency Blocker
                $table->timestamps();
            });
        }

        // 17/19/20. SCREEN CAPTURE, PERFORMANCE & AUDIT SYSTEM LOGS
        if (!Schema::hasTable('audit_performance_logs')) {
            Schema::create('audit_performance_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('action_performed');
                $table->string('screen_capture_url')->nullable();
                $table->ipAddress('ip_address')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down() {
        Schema::dropIfExists('audit_performance_logs');
        Schema::dropIfExists('documents_vault');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('overtime');
    }
};
