<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\Attendance;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    // 1. Submit a Leave Application (Employee Flow)
    public function applyLeave(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string'
        ]);

        $leave = Leave::create([
            'user_id' => $request->user()->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'Pending'
        ]);

        return response()->json(['message' => 'Leave applied successfully', 'leave' => $leave], 201);
    }

    // 2. Approve or Reject Leave Applications (Manager/HR Flow)
    public function approveLeave(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected'
        ]);

        $leave = Leave::find($id);
        if (!$leave) {
            return response()->json(['message' => 'Leave application not found.'], 404);
        }

        $leave->update([
            'status' => $request->status,
            'approved_by' => $request->user()->id
        ]);

        return response()->json(['message' => 'Leave status updated to ' . $request->status, 'leave' => $leave], 200);
    }

    // 3. Generate Monthly Payroll with Calculations (Accounts/HR Flow)
    public function generatePayroll(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month_year' => 'required|string' // Format: MM-YYYY e.g., 10-2026
        ]);

        // Base Rules Simulation
        $baseSalary = 50000.00; 
        $allowances = 5000.00;  // Standard healthcare/travel rules
        $deductions = 0.00;

        // Fetch late check-ins for compliance penalization
        $lateDays = Attendance::where('user_id', $request->user_id)
            ->where('status', 'Late')
            ->count();

        // Compliance Rule: Penalty deduction of 500 INR per late arrival
        if ($lateDays > 0) {
            $deductions += ($lateDays * 500.00);
        }

        $netSalary = ($baseSalary + $allowances) - $deductions;

        $payroll = Payroll::create([
            'user_id' => $request->user_id,
            'month_year' => $request->month_year,
            'base_salary' => $baseSalary,
            'allowances' => $allowances,
            'deductions' => $deductions,
            'net_salary' => $netSalary,
            'status' => 'Draft'
        ]);

        return response()->json([
            'message' => 'Payroll generated with late penalty calculations',
            'payroll' => $payroll
        ], 201);
    }
        // 4. नया कर्मचारी बनाना (Admin Flow)
    public function createEmployee(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:Admin,HR,Accounts,Manager,Employee',
            'team' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
            'salary_structure' => 'nullable|numeric',
            'joining_date' => 'nullable|date'
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => $request->role,
            'team' => $request->team ?? 'General',
            'manager_id' => $request->manager_id,
            'salary_structure' => $request->salary_structure ?? 30000.00,
            'joining_date' => $request->joining_date ?? '2026-10-07',
            'status' => 'Active'
        ]);

        return response()->json([
            'message' => 'Employee profile initialized with strict corporate taxonomy.',
            'user' => $user
        ], 201);
    }


    // 5. पेरोल को अप्रूव और पे करना (Final Payout & Payslip Status)
    public function paySalary(Request $request, $payrollId)
    {
        $payroll = Payroll::find($payrollId);
        if (!$payroll) {
            return response()->json(['message' => 'Payroll record not found.'], 404);
        }

        // स्टेटस को Draft से Paid कर दें (यह पेमेंट और पेस्लिप जनरेशन को कम्प्लीट करता है)
        $payroll->update(['status' => 'Paid']);

        return response()->json([
            'message' => 'Salary paid successfully. Payslip is now downloadable.',
            'payslip' => $payroll
        ], 200);
    }

        // 6. पेस्लिप को PDF में एक्सपोर्ट करना
    public function downloadPayslip($id)
    {
        $payroll = Payroll::find($id);
        if (!$payroll || $payroll->status !== 'Paid') {
            return response('<h1>Error: Payslip not available or unpaid.</h1>', 404);
        }

        // एम्प्लॉई की डिटेल्स निकालने के लिए
        $user = \App\Models\User::find($payroll->user_id);

        // PDF के लिए एक क्लीन HTML टेम्पलेट
        $html = "
        <div style='font-family: Arial, sans-serif; padding: 30px; border: 1px solid #ccc;'>
            <h2 style='text-align: center; color: #333;'>HRMS COMPRESSED - PAYSLIP</h2>
            <hr>
            <p><strong>Employee Name:</strong> {$user->name}</p>
            <p><strong>Email:</strong> {$user->email}</p>
            <p><strong>Statement Month:</strong> {$payroll->month_year}</p>
            <hr>
            <table style='width: 100%; border-collapse: collapse;'>
                <tr style='background: #f4f4f4;'><th style='padding: 8px; text-align: left;'>Earnings / Deductions</th><th style='padding: 8px; text-align: right;'>Amount (INR)</th></tr>
                <tr><td style='padding: 8px;'>Base Salary</td><td style='padding: 8px; text-align: right;'>{$payroll->base_salary}</td></tr>
                <tr><td style='padding: 8px;'>Allowances</td><td style='padding: 8px; text-align: right;'>{$payroll->allowances}</td></tr>
                <tr style='color: red;'><td style='padding: 8px;'>Late Penalty Deductions</td><td style='padding: 8px; text-align: right;'>-{$payroll->deductions}</td></tr>
                <tr style='font-weight: bold; background: #e2e2e2;'><td style='padding: 8px;'>Net In-Hand Salary</td><td style='padding: 8px; text-align: right;'>{$payroll->net_salary}</td></tr>
            </table>
            <br><br>
            <p style='text-align: center; color: green; font-weight: bold;'>Status: {$payroll->status} (Bank Transferred)</p>
        </div>";

        $pdf = Pdf::loadHTML($html);
        return $pdf->download("payslip-{$payroll->month_year}.pdf");
    }


}