<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Unified Web / Mobile / Desktop Check-In Endpoint
    public function checkIn(Request $request)
    {
        $userId = $request->user()->id;
        $today = Carbon::today()->toDateString();

        // ❌ Duplicate Check-In Protection
        $existing = Attendance::where('user_id', $userId)->where('date', $today)->first();
        if ($existing) {
            return response()->json(['message' => 'Rejected: Double check-in detected for today.'], 400);
        }

        $attendance = Attendance::create([
            'user_id' => $userId,
            'date' => $today,
            'check_in' => Carbon::now(),
            'status' => Carbon::now()->hour >= 10 ? 'Late' : 'Present'
        ]);

        return response()->json(['message' => 'Checked in successfully via device portal.', 'data' => $attendance], 200);
    }

    // Live Break Start Trigger
    public function breakStart(Request $request)
    {
        $userId = $request->user()->id;
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $userId)->where('date', $today)->first();
        if (!$attendance) {
            return response()->json(['message' => 'Rejected: Must check in before taking a break.'], 400);
        }

        // ❌ Double Break Start Protection
        if ($attendance->break_start_time && !$attendance->break_end_time) {
            return response()->json(['message' => 'Rejected: Double break start. Break session already running.'], 400);
        }

        $attendance->update(['break_start_time' => Carbon::now()]);

        return response()->json(['message' => 'Break started successfully.', 'data' => $attendance], 200);
    }

    // Live Break End Trigger & Calculation Link
    public function breakEnd(Request $request)
    {
        $userId = $request->user()->id;
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $userId)->where('date', $today)->first();
        
        // ❌ Break End without Break Start Protection
        if (!$attendance || !$attendance->break_start_time) {
            return response()->json(['message' => 'Rejected: Break end called without a matching break start.'], 400);
        }

        if ($attendance->break_end_time) {
            return response()->json(['message' => 'Rejected: Break already concluded for this session.'], 400);
        }

        $start = Carbon::parse($attendance->break_start_time);
        $end = Carbon::now();
        $diffInMinutes = $start->diffInMinutes($end);

        $attendance->update([
            'break_end_time' => $end,
            'total_break_minutes' => $attendance->total_break_minutes + $diffInMinutes
        ]);

        return response()->json([
            'message' => 'Break ended. Logged interval successfully.',
            'logged_break_minutes' => $diffInMinutes,
            'total_break_minutes' => $attendance->fresh()->total_break_minutes
        ], 200);
    }

    // Unified Check-Out & Auto Math Calculator Core
    public function checkOut(Request $request)
    {
        $userId = $request->user()->id;
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $userId)->where('date', $today)->first();
        
        if (!$attendance) {
            return response()->json(['message' => 'Rejected: No active session found for today.'], 400);
        }

        // ❌ Double Check-Out Protection
        if ($attendance->check_out) {
            return response()->json(['message' => 'Rejected: Double check-out called. Session already closed.'], 400);
        }

        $checkIn = Carbon::parse($attendance->check_in);
        $checkOut = Carbon::now();
        
        // Automated Gross System Math
        $grossMinutes = $checkIn->diffInMinutes($checkOut);
        $breakMinutes = $attendance->total_break_minutes;
        $workedMinutes = max(0, $grossMinutes - $breakMinutes);

        $attendance->update([
            'check_out' => $checkOut,
            'worked_minutes' => $workedMinutes
        ]);

        return response()->json([
            'message' => 'Checked out successfully. Shift calculations locked.',
            'metrics' => [
                'gross_time_hours' => round($grossMinutes / 60, 2),
                'break_time_hours' => round($breakMinutes / 60, 2),
                'net_worked_hours' => round($workedMinutes / 60, 2)
            ],
            'data' => $attendance
        ], 200);
    }

    // Fetch History & Attendance Logs
    public function getHistory(Request $request) {
        $logs = Attendance::where('user_id', $request->user()->id)->get();
        return response()->json(['attendance_history' => $logs], 200);
    }
}
