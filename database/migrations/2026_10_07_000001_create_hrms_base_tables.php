<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // Users Table with Roles (RBAC)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['Admin', 'HR', 'Accounts', 'Manager', 'Employee'])->default('Employee');
            $table->rememberToken();
            $table->timestamps();
        });

        // Attendance Table
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->integer('total_break_minutes')->default(0);
            $table->enum('status', ['Present', 'Absent', 'Late', 'Half-Day'])->default('Present');
            $table->timestamps();
        });

        // Tasks Table
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('tracked_seconds')->default(0);
            $table->enum('status', ['Pending', 'Ongoing', 'Completed'])->default('Pending');
            $table->timestamps();
        });

        // Leaves Table
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('reason');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Payroll Table
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('month_year'); 
            $table->decimal('base_salary', 10, 2);
            $table->decimal('allowances', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2);
            $table->enum('status', ['Draft', 'Approved', 'Paid'])->default('Draft');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('users');
    }
};
