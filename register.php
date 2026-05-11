<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Get the plain text password

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
 
    $conn = getDatabaseConnection();
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $email_exists = false;
        $username_exists = false;

        $stmt2 = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt2->bind_param("s", $email);
        $stmt2->execute();
        $stmt2->store_result();
        if ($stmt2->num_rows > 0) {
            $email_exists = true;
        }

        $stmt3 = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt3->bind_param("s", $username);
        $stmt3->execute();
        $stmt3->store_result();
        
        if ($stmt3->num_rows > 0) {
            $username_exists = true;
        }

        if ($email_exists && $username_exists) {
            $error_message = "Username dan email ini sudah terdaftar. Silahkan gunakan username dan email lain.";
        } elseif ($email_exists) {
            $error_message = "Email ini sudah terdaftar. Silahkan gunakan email lain.";
        } elseif ($username_exists) {
            $error_message = "Username ini sudah terdaftar. Silahkan gunakan username lain.";
        }

    } else {
        // Use the hashed password when inserting into the database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password); // Bind hashed password
        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $error_message = "Registrasi gagal. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Register - TaskDo</title>
    
    <!-- External Libraries to Keep -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Tailwind and Fonts from New Design -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700&amp;family=Inter:wght@400;600&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "on-error": "#ffffff",
                    "on-tertiary-container": "#fffbff",
                    "secondary-fixed-dim": "#79d1ff",
                    "surface-container-highest": "#dfe3e7",
                    "inverse-surface": "#2c3134",
                    "outline": "#6e7880",
                    "on-surface": "#171c1f",
                    "surface-bright": "#f6fafe",
                    "secondary": "#006689",
                    "primary-container": "#007da8",
                    "error": "#ba1a1a",
                    "on-primary-fixed-variant": "#004c68",
                    "surface-container-low": "#f0f4f8",
                    "tertiary-fixed-dim": "#ffb86e",
                    "on-primary-container": "#fbfcff",
                    "on-secondary-fixed-variant": "#004c68",
                    "on-background": "#171c1f",
                    "error-container": "#ffdad6",
                    "on-error-container": "#93000a",
                    "inverse-primary": "#78d1ff",
                    "surface-dim": "#d6dadf",
                    "outline-variant": "#bdc8d0",
                    "background": "#f6fafe",
                    "tertiary-fixed": "#ffdcbd",
                    "on-primary-fixed": "#001e2c",
                    "inverse-on-surface": "#edf1f6",
                    "surface-container-high": "#e4e9ed",
                    "primary-fixed": "#c3e8ff",
                    "primary-fixed-dim": "#78d1ff",
                    "secondary-fixed": "#c3e8ff",
                    "surface-tint": "#006689",
                    "surface": "#f6fafe",
                    "on-secondary-fixed": "#001e2c",
                    "on-primary": "#ffffff",
                    "on-surface-variant": "#3e484f",
                    "surface-container-lowest": "#ffffff",
                    "tertiary-container": "#a96400",
                    "on-tertiary-fixed-variant": "#693c00",
                    "surface-container": "#eaeef3",
                    "surface-variant": "#dfe3e7",
                    "tertiary": "#864f00",
                    "secondary-container": "#66ccff",
                    "on-tertiary-fixed": "#2c1600",
                    "on-secondary": "#ffffff",
                    "on-tertiary": "#ffffff",
                    "on-secondary-container": "#005573",
                    "primary": "#006386"
            },
            "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
            },
            "spacing": {
                    "base": "8px",
                    "gutter": "24px",
                    "xl": "64px",
                    "xs": "4px",
                    "lg": "40px",
                    "container-max": "1200px",
                    "md": "24px",
                    "sm": "12px"
            },
            "fontFamily": {
                    "body-md": ["Inter"],
                    "label-md": ["Inter"],
                    "headline-lg-mobile": ["Hanken Grotesk"],
                    "body-sm": ["Inter"],
                    "display-lg": ["Hanken Grotesk"],
                    "body-lg": ["Inter"],
                    "headline-lg": ["Hanken Grotesk"],
                    "headline-sm": ["Hanken Grotesk"],
                    "headline-md": ["Hanken Grotesk"]
            },
            "fontSize": {
                    "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                    "label-md": ["14px", {"lineHeight": "16px", "fontWeight": "600"}],
                    "headline-lg-mobile": ["28px", {"lineHeight": "36px", "fontWeight": "600"}],
                    "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                    "display-lg": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                    "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                    "headline-lg": ["32px", {"lineHeight": "40px", "fontWeight": "600"}],
                    "headline-sm": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                    "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}]
            }
          },
        },
      }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .bg-login-gradient {
            background: linear-gradient(135deg, #66CCFF 0%, #F6FAFE 100%);
        }
        .btn-gradient {
            background: linear-gradient(135deg, #0099CC 0%, #66CCFF 100%);
        }
        .ambient-shadow {
            box-shadow: 0px 10px 30px rgba(0, 153, 204, 0.08);
        }
        .floating-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            z-index: 0;
        }
        .wave-container {
            position: fixed;
            bottom: 0;
            width: 100%;
            height: 100px;
            overflow: hidden;
            z-index: 5;
            pointer-events: none;
        }
        .wave {
            position: absolute;
            bottom: 0;
            width: 200%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 300'%3E%3Cpath d='M0 150 C150 100 250 200 400 150 C550 100 650 200 800 150 L800 300 L0 300 Z' fill='%2366CCFF' opacity='0.2'/%3E%3C/svg%3E") repeat-x;
            background-size: 50% 100%;
        }
        
        /* Reset some Bootstrap styles that might conflict with Tailwind */
        a { text-decoration: none; }

        /* Specific to register for password strength */
        .password-strength {
            height: 5px;
            border-radius: 2.5px;
            margin-top: 5px;
            transition: all 0.3s ease;
        }
        
        /* Hide native Edge password reveal icon */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }
    </style>
</head>
<body class="bg-background font-body-md text-on-background min-h-screen flex flex-col relative overflow-x-hidden">
    <!-- Decorative Floating Circles -->
    <div class="floating-circle w-32 h-32 top-10 left-10 opacity-40"></div>
    <div class="floating-circle w-48 h-48 top-40 -left-10 opacity-20"></div>
    <div class="floating-circle w-64 h-64 bottom-20 right-10 opacity-30"></div>
    <div class="floating-circle w-24 h-24 top-1/4 right-1/4 opacity-50"></div>
    
    <!-- Header -->
    <header class="flex justify-center items-center w-full py-lg z-10">
        <div class="flex items-center gap-xs">
            <img alt="TaskDo Logo" class="h-24 w-auto" src="FOTO/taskdo.png"/>
        </div>
    </header>
    
    <main class="flex-grow flex items-center justify-center px-gutter py-xl relative z-20">
        <!-- Register Card -->
        <div class="bg-surface-container-lowest ambient-shadow rounded-[1.5rem] w-full max-w-[480px] p-lg md:p-xl border border-outline-variant/10">
            <div class="flex flex-col items-center mb-lg">
                <h1 class="font-headline-lg text-headline-lg text-on-surface mb-xs">Register</h1>
                <p class="font-body-md text-body-md text-on-surface-variant">Create your account</p>
            </div>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger error-message mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= $error_message ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-md needs-validation" novalidate>
                <!-- Username Input -->
                <div class="space-y-xs">
                    <label class="font-label-md text-label-md text-on-surface-variant px-xs" for="username">Username</label>
                    <div class="relative flex items-center">
                        <i class="fas fa-user absolute left-md text-outline"></i>
                        <input class="w-full pl-[52px] pr-md py-md bg-surface border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all font-body-md text-body-md placeholder:text-outline-variant" id="username" name="username" placeholder="Choose your username" type="text" required/>
                    </div>
                </div>

                <!-- Email Input -->
                <div class="space-y-xs">
                    <label class="font-label-md text-label-md text-on-surface-variant px-xs" for="email">Email address</label>
                    <div class="relative flex items-center">
                        <i class="fas fa-envelope absolute left-md text-outline"></i>
                        <input class="w-full pl-[52px] pr-md py-md bg-surface border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all font-body-md text-body-md placeholder:text-outline-variant" id="email" name="email" placeholder="Enter your email" type="email" required/>
                    </div>
                </div>
                
                <!-- Password Input -->
                <div class="space-y-xs">
                    <label class="font-label-md text-label-md text-on-surface-variant px-xs" for="password">Password</label>
                    <div class="relative flex items-center">
                        <i class="fas fa-lock absolute left-md text-outline"></i>
                        <input class="w-full pl-[52px] pr-[52px] py-md bg-surface border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all font-body-md text-body-md placeholder:text-outline-variant" id="password" name="password" placeholder="Create a strong password" type="password" required/>
                        <button class="absolute right-md text-outline hover:text-primary transition-colors border-0 bg-transparent" type="button" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="passwordStrength"></div>
                    <small class="text-muted" id="passwordFeedback"></small>
                </div>
                
                <!-- Register Button -->
                <div class="pt-sm">
                    <button class="btn-gradient w-full py-md px-lg text-on-primary font-label-md text-label-md rounded-xl ambient-shadow hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-xs" type="submit">
                        <i class="fas fa-user-plus"></i>
                        Register
                    </button>
                </div>
                
                <div class="flex justify-center pt-md">
                    <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="login.php">
                        Already have an account? <span class="text-primary font-bold">Login here</span>
                    </a>
                </div>
            </form>
        </div>
    </main>
    
    <!-- Wave Animation Decoration -->
    <div class="wave-container">
        <div class="wave"></div>
    </div>
    
    <!-- Footer -->
    <footer class="mt-auto w-full z-10 flex flex-col items-center gap-sm py-md px-gutter bg-surface-container-low/80 backdrop-blur-sm">
        <p class="font-body-sm text-body-sm text-secondary">
            TaskDo &copy; 2024 | All Rights Reserved
        </p>
        <div class="flex gap-md">
            <a class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-all underline" href="#">Privacy Policy</a>
            <a class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-all underline" href="#">Terms of Service</a>
            <a class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-all underline" href="#">Help Center</a>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Password Strength Checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            updatePasswordStrengthUI(strength);
        });

        function checkPasswordStrength(password) {
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            return strength;
        }

        function updatePasswordStrengthUI(strength) {
            const strengthBar = document.getElementById('passwordStrength');
            const feedback = document.getElementById('passwordFeedback');
            const passwordInput = document.getElementById('password').value;
            
            if (!passwordInput) {
                strengthBar.style.width = '0%';
                feedback.textContent = '';
                return;
            }
            
            const colors = ['#ff4444', '#ffbb33', '#00C851', '#33b5e5', '#2BBBAD'];
            const messages = [
                'Very weak',
                'Weak',
                'Fair',
                'Good',
                'Strong'
            ];

            strengthBar.style.width = `${(strength / 5) * 100}%`;
            strengthBar.style.backgroundColor = colors[Math.max(0, strength - 1)];
            feedback.textContent = messages[Math.max(0, strength - 1)];
            feedback.style.color = colors[Math.max(0, strength - 1)];
        }

        // Form Validation
        (function () {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>