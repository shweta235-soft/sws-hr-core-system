<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'month_year', 
        'base_salary', 
        'allowances', 
        'deductions', 
        'net_salary', 
        'status'
    ];
      // 👤 Payroll belongs to a unique User profile node
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 📥 Payroll ➔ hasOne Payslip Instance (Represented by the file configuration)
    public function payslip()
    {
        // Inside this compressed operational target context, the payroll instance 
        // acts directly as its own verified downloadable payslip document state.
        return $this;
    }
}
  

