<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMS Enterprise Portal</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; 
            font-family: 'Segoe UI', sans-serif; }
        body { background: #f3f4f6; color: #1f2937; padding: 20px; }
        header { background: #1e3a8a; color: white; padding: 20px; 
            border-radius: 8px; margin-bottom: 20px; display: flex; 
            justify-content: space-between; align-items: center; }
        .badge { background: #10b981; padding: 5px 10px; 
            border-radius: 20px; font-size: 12px; font-weight: bold; }
        .auth-container { display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 20px; margin-bottom: 30px; }
        .ui-card { background: white; padding: 20px; border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
            border-top: 4px solid #3b82f6; }
        .ui-card h3 { margin-bottom: 15px; color: #1e3a8a; font-size: 18px; 
            border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; margin-bottom: 5px; 
            font-size: 13px; font-weight: 600; color: #4b5563; }
        .form-group input, .form-group select { width: 100%; 
            padding: 8px 12px; border: 1px solid #d1d5db; 
            border-radius: 6px; font-size: 14px; }
        .btn { display: inline-block; width: 100%; padding: 10px; 
            background: #2563eb; color: white; border: none; 
            border-radius: 6px; font-weight: bold; cursor: pointer; 
            text-align: center; text-decoration: none; font-size: 14px; }
        .btn:hover { background: #1d4ed8; }
        .btn-success { background: #10b981; }
        .data-grid { display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); 
            gap: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; 
            background: white; font-size: 14px; }
        th, td { border: 1px solid #e5e7eb; padding: 12px; text-align: left; }
        th { background: #f9fafb; color: #4b5563; font-weight: 600; }
        .sync-bar { background: #ffffff; padding: 12px; border-radius: 6px; 
            display: flex; justify-content: space-between; 
            align-items: center; margin-bottom: 20px; 
            border-left: 4px solid #10b981; }
        .hidden { display: none !important; }
        #logout_btn { background: #ef4444; width: auto; padding: 5px 15px; }
    </style>
</head>
<body>
    <header>
        <div>
            <h1>🎯 HRMS Enterprise Portal</h1>
            <p style="font-size: 13px; opacity: 0.9;">UI & API Redirection</p>
        </div>
        <button id="logout_btn" class="btn hidden" onclick="executeLogout()">Sign Out</button>
    </header>
    <!-- 1. लॉगिन स्क्रीन -->
       <!-- 1. नया अपडेटेड लॉगिन और पब्लिक रजिस्ट्रेशन स्क्रीन एक साथ -->
    <div id="login_screen" class="auth-container">
        
        <!-- लॉगिन बॉक्स -->
        <div class="ui-card" id="login_card">
            <h3>🔐 Live Platform Login</h3>
            <div class="form-group">
                <label>Corporate Email ID</label>
                <input type="email" id="login_email" value="rohan@hrms.com">
            </div>
            <div class="form-group">
                <label>Secure Password</label>
                <input type="password" id="login_pass" value="rohanpassword">
            </div>
            <button class="btn" onclick="executeLiveLogin()">Authorize & Sign In</button>
            <p style="text-align: center; margin-top: 15px; font-size: 13px;">
                New to company? <a href="#" onclick="toggleAuthScreens(true)" style="color: #2563eb; font-weight: bold; text-decoration: none;">Create New Account →</a>
            </p>
        </div>

        <!-- पब्लिक रजिस्ट्रेशन बॉक्स (जो शुरुआत में छुपा रहेगा) -->
        <div class="ui-card hidden" id="public_reg_card">
            <h3>📝 Employee Self-Registration</h3>
            <div class="form-group">
                <label>Full Legal Name</label>
                <input type="text" id="public_name" placeholder="Ex: Amit Kumar">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" id="public_email" placeholder="amit@hrms.com">
            </div>
            <div class="form-group">
                <label>Set Access Password</label>
                <input type="password" id="public_pass" placeholder="Minimum 6 characters">
            </div>
            <!-- सुरक्षा के लिए रोल डिफ़ॉल्ट रूप से सिर्फ 'Employee' रहेगा -->
            <button class="btn btn-success" onclick="executePublicRegistration()">Register & Join Workspace</button>
            <p style="text-align: center; margin-top: 15px; font-size: 13px;">
                Already have an account? <a href="#" onclick="toggleAuthScreens(false)" style="color: #2563eb; font-weight: bold; text-decoration: none;">← Back to Login</a>
            </p>
        </div>

    </div>


    <!-- 2. मुख्य डैशboards (लॉगिन के बाद एक्टिव होगा) -->
    <div id="main_dashboard" class="hidden">
        <div class="sync-bar">
            <span>🌐 <strong>Live Employee Dashboard:</strong> Access Granted.</span>
            <span style="color: #10b981; font-weight: bold;">● Active Session Online</span>
        </div>

        <div class="auth-container">
            <!-- नया रजिस्ट्रेशन फॉर्म (पासवर्ड इनपुट के साथ) -->
            <div id="registration_card" class="ui-card">
                <h3>📝 Onboard New Employee</h3>
                <div class="form-group">
                    <label>Full Legal Name</label>
                    <input type="text" id="reg_name" placeholder="Ex: Amit Kumar">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="reg_email" placeholder="amit@hrms.com">
                </div>
                <div class="form-group">
                    <label>Set Access Password</label>
                    <input type="password" id="reg_pass" placeholder="Enter secure password">
                </div>
                <div class="form-group">
                    <label>Allocate Role Level</label>
                    <select id="reg_role">
                        <option value="Employee">Standard Employee</option>
                        <option value="Manager">Team Lead / Manager</option>
                        <option value="HR">HR Personnel</option>
                        <option value="Accounts">Financial Accounts</option>
                    </select>
                </div>
                <button class="btn btn-success" onclick="executeLiveRegistration()">Register & Onboard</button>
            </div>
        </div>

        <!-- लाइव डेटा ग्रिड्स -->
        <div class="data-grid">
            <div class="ui-card">
                <h3>👥 Active Profiles ({{ count($employees) }})</h3>
                <table>
                    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr></thead>
                    <tbody>
                        @foreach($employees as $e)
                        <tr><td><strong>#{{ $e->id }}</strong></td><td>{{ $e->name }}</td><td>{{ $e->email }}</td><td>{{ $e->role }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="ui-card">
                <h3>⏱️ Attendance Logs</h3>
                <table>
                    <thead><tr><th>User ID</th><th>Date</th><th>Clocked In</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($attendances as $a)
                        <tr><td>#{{ $a->user_id }}</td><td>{{ $a->date }}</td><td>{{ $a->check_in }}</td><td><span style="color:#10b981; font-weight:bold;">{{ $a->status }}</span></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="ui-card">
                <h3>📝 Tasks Tracked</h3>
                <table>
                    <thead><tr><th>User ID</th><th>Title</th><th>Seconds</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($tasks as $t)
                        <tr><td>#{{ $t->user_id }}</td><td>{{ $t->title }}</td><td><code>{{ $t->tracked_seconds }}s</code></td><td>{{ $t->status }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="ui-card">
                <h3>💰 Payroll Ledger</h3>
                <table>
                    <thead><tr><th>User ID</th><th>Month</th><th>Net Salary</th><th>State</th><th>Action</th></tr></thead>
                    <tbody>
                        @foreach($payrolls as $p)
                        <tr>
                            <td>#{{ $p->user_id }}</td>
                            <td>{{ $p->month_year }}</td>
                            <td><strong>INR {{ $p->net_salary }}</strong></td>
                            <td><span style="color:#2563eb; font-weight:bold;">{{ $p->status }}</span></td>
                            <td>
                                @if($p->status === 'Paid')
                                <a class="btn" style="padding: 3px 8px; font-size: 11px; width:auto;" href="/download-payslip/{{ $p->id }}">📥 PDF</a>
                                @else
                                <em>Draft</em>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const token = localStorage.getItem('hrms_token');
            if (token) { showDashboard(); }
        });

                // डैशबोर्ड दिखाते समय रोल की जांच करने का प्रोफेशनल लॉजिक
        function showDashboard(role) {
            document.getElementById('login_screen').classList.add('hidden');
            document.getElementById('main_dashboard').classList.remove('hidden');
            document.getElementById('logout_btn').classList.remove('hidden');

            // सुरक्षा जांच: अगर रोल एडमिन या एचआर है, तभी रजिस्ट्रेशन बॉक्स दिखाओ
            const regCard = document.getElementById('registration_card');
            if (role === 'Admin' || role === 'HR') {
                regCard.classList.remove('hidden');
            } else {
                regCard.classList.add('hidden'); // आम कर्मचारियों के लिए गायब!
            }
        }

        // पेज लोड होते ही लोकल स्टोरेज से डेटा चेक करें
        document.addEventListener("DOMContentLoaded", function() {
            const token = localStorage.getItem('hrms_token');
            const role = localStorage.getItem('hrms_role'); // रोल को याद रखा
            if (token) { 
                showDashboard(role); 
            }
        });

        async function executeLiveLogin() {
            const email = document.getElementById('login_email').value;
            const password = document.getElementById('login_pass').value;

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                const data = await response.json();
                
                if (response.ok) {
                    // टोकन और रोल दोनों को ब्राउज़र की याददाश्त में सुरक्षित किया
                    localStorage.setItem('hrms_token', data.token);
                    localStorage.setItem('hrms_role', data.user.role);
                    
                    alert('Success! Login authorized as ' + data.user.role);
                    showDashboard(data.user.role);
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (err) {
                alert('Connection Failed: ' + err.message);
            }
        }

        function executeLogout() {
            localStorage.removeItem('hrms_token');
            localStorage.removeItem('hrms_role'); // लॉगआउट पर रोल भी साफ़
            alert('Signed Out Successfully.');
            window.location.reload();
        }


        async function executeLiveRegistration() {
            const name = document.getElementById('reg_name').value;
            const email = document.getElementById('reg_email').value;
            const password = document.getElementById('reg_pass').value;
            const role = document.getElementById('reg_role').value;
            const token = localStorage.getItem('hrms_token');

            if (!name || !email || !password) {
                alert('Validation Error: All fields are mandatory!');
                return;
            }

            try {
                const response = await fetch('/api/admin/employee', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({ name, email, role, password })
                });
                const data = await response.json();

                if (response.ok) {
                    alert('Success! New employee onboarded with custom password.');
                    window.location.reload();
                } else {
                    alert('Security Check Failed: ' + data.message);
                }
            } catch (err) {
                alert('Connection Failed: ' + err.message);
            }
        }

        function executeLogout() {
            localStorage.removeItem('hrms_token');
            alert('Signed Out Successfully.');
            window.location.reload();
        }
         function toggleAuthScreens(showRegister) {
            if (showRegister) {
                document.getElementById('login_card').classList.add('hidden');
                document.getElementById('public_reg_card').classList.remove('hidden');
            } else {
                document.getElementById('login_card').classList.remove('hidden');
                document.getElementById('public_reg_card').classList.add('hidden');
            }
        }

        // बाहर से सीधे बिना टोकन के रजिस्टर करने का पब्लिक फंक्शन
               async function executePublicRegistration() {
            const name = document.getElementById('public_name').value;
            const email = document.getElementById('public_email').value;
            const password = document.getElementById('public_pass').value;

            if (!name || !email || !password) {
                alert('All fields are mandatory!');
                return;
            }

            try {
                // यह सीधे आपके Laravel MySQL इंजन को हिट करेगा
                const response = await fetch('/api/register', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify({ name, email, password })
                });
                
                const data = await response.json();

                if (response.ok) {
                    alert('Success! Your account has been created in MySQL Database.\nYou can now login with your password.');
                    window.location.reload(); // पेज रिफ्रेश होकर लॉगिन स्क्रीन पर आएगा
                } else {
                    alert('Registration Failed: ' + data.message);
                }
            } catch (err) {
                alert('Connection Failed: ' + err.message);
            }
        }

    </script>
</body>
</html>
