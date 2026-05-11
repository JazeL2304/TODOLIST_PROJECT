<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = getDatabaseConnection();
$user_id = $_SESSION['user_id'];
$error_message = '';
$success = true;

// Ambil informasi pengguna dari database
$user_query = $conn->query("SELECT username, email, photo FROM users WHERE id = $user_id");
if ($user_query) {
    $user = $user_query->fetch_assoc();
    if ($user) {
        $username = $user['username'];
        $email = $user['email'];
        $photo = $user['photo'] ? $user['photo'] : 'default_profile.png';
    } else {
        header("Location: dashboard.php");
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}

// Proses update profil
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update username jika ada
    if (!empty($_POST['new_username'])) {
        $new_username = $_POST['new_username'];
        
        // Cek apakah username sudah ada
        $check_username = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $check_username->bind_param("si", $new_username, $user_id);
        $check_username->execute();
        $result_username = $check_username->get_result();
        
        if ($result_username->num_rows > 0) {
            $error_message .= "Username already exists! ";
            $success = false;
        } else {
            $update_username = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
            $update_username->bind_param("si", $new_username, $user_id);
            $update_username->execute();
            $update_username->close();
        }
        $check_username->close();
    }

    // Update email jika ada
    if (!empty($_POST['new_email'])) {
        $new_email = $_POST['new_email'];
        
        // Cek apakah email sudah ada
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check_email->bind_param("si", $new_email, $user_id);
        $check_email->execute();
        $result_email = $check_email->get_result();
        
        if ($result_email->num_rows > 0) {
            $error_message .= "Email already exists! ";
            $success = false;
        } else {
            $update_email = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
            $update_email->bind_param("si", $new_email, $user_id);
            $update_email->execute();
            $update_email->close();
        }
        $check_email->close();
    }

    // Update password jika ada
    if (!empty($_POST['new_password'])) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $new_password, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Update foto profil jika ada upload baru
    if (!empty($_FILES['photo']['name'])) {
        $photo_name = $_FILES['photo']['name'];
        $photo_tmp = $_FILES['photo']['tmp_name'];
        $upload_dir = "user_uploads/$user_id/";

        // Buat direktori jika belum ada
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Pindahkan file yang diunggah ke direktori user
        if (move_uploaded_file($photo_tmp, $upload_dir . $photo_name)) {
            // Simpan jalur foto di database
            $photo_path = $upload_dir . $photo_name;
            $update_photo = $conn->prepare("UPDATE users SET photo = ? WHERE id = ?");
            $update_photo->bind_param("si", $photo_path, $user_id);
            $update_photo->execute();
            $update_photo->close();
        }
    }

    // Jika tidak ada error, redirect ke dashboard
    if ($success) {
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Edit Profile | TaskDo</title>
    <!-- Google Fonts: Inter & Hanken Grotesk -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Hanken+Grotesk:wght@600;700&display=swap" rel="stylesheet"/>
    <!-- Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "background": "#f6fafe",
                    "tertiary-container": "#a96400",
                    "primary": "#006386",
                    "error": "#ba1a1a",
                    "surface-container-lowest": "#ffffff",
                    "surface-container-highest": "#dfe3e7",
                    "secondary-fixed": "#c3e8ff",
                    "surface-container": "#eaeef3",
                    "on-primary": "#ffffff",
                    "tertiary-fixed-dim": "#ffb86e",
                    "on-error": "#ffffff",
                    "error-container": "#ffdad6",
                    "on-secondary-container": "#005573",
                    "on-tertiary-container": "#fffbff",
                    "surface-container-low": "#f0f4f8",
                    "secondary-container": "#66ccff",
                    "on-secondary-fixed-variant": "#004c68",
                    "on-secondary-fixed": "#001e2c",
                    "surface-variant": "#dfe3e7",
                    "on-primary-container": "#fbfcff",
                    "on-background": "#171c1f",
                    "on-primary-fixed-variant": "#004c68",
                    "outline-variant": "#bdc8d0",
                    "on-tertiary-fixed": "#2c1600",
                    "inverse-on-surface": "#edf1f6",
                    "surface": "#f6fafe",
                    "primary-fixed": "#c3e8ff",
                    "primary-fixed-dim": "#78d1ff",
                    "on-surface": "#171c1f",
                    "primary-container": "#007da8",
                    "on-tertiary": "#ffffff",
                    "on-primary-fixed": "#001e2c",
                    "secondary": "#006689",
                    "outline": "#6e7880",
                    "surface-bright": "#f6fafe",
                    "secondary-fixed-dim": "#79d1ff",
                    "surface-dim": "#d6dadf",
                    "surface-container-high": "#e4e9ed",
                    "on-secondary": "#ffffff",
                    "surface-tint": "#006689",
                    "on-tertiary-fixed-variant": "#693c00",
                    "on-surface-variant": "#3e484f",
                    "on-error-container": "#93000a",
                    "inverse-surface": "#2c3134",
                    "inverse-primary": "#78d1ff",
                    "tertiary-fixed": "#ffdcbd",
                    "tertiary": "#864f00"
            },
            "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
            },
            "spacing": {
                    "xl": "64px",
                    "container-max": "1200px",
                    "xs": "4px",
                    "sm": "12px",
                    "md": "24px",
                    "gutter": "24px",
                    "lg": "40px",
                    "base": "8px"
            },
            "fontFamily": {
                    "label-md": ["Inter"],
                    "headline-sm": ["Hanken Grotesk"],
                    "headline-md": ["Hanken Grotesk"],
                    "body-sm": ["Inter"],
                    "headline-lg": ["Hanken Grotesk"],
                    "body-lg": ["Inter"],
                    "display-lg": ["Hanken Grotesk"],
                    "headline-lg-mobile": ["Hanken Grotesk"],
                    "body-md": ["Inter"]
            },
            "fontSize": {
                    "label-md": ["14px", {"lineHeight": "16px", "fontWeight": "600"}],
                    "headline-sm": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                    "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                    "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                    "headline-lg": ["32px", {"lineHeight": "40px", "fontWeight": "600"}],
                    "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                    "display-lg": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                    "headline-lg-mobile": ["28px", {"lineHeight": "36px", "fontWeight": "600"}],
                    "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}]
            }
          },
        },
      }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .ambient-shadow {
            box-shadow: 0px 10px 30px rgba(0, 153, 204, 0.08);
        }
        .blue-gradient {
            background: linear-gradient(135deg, #006386 0%, #0099CC 100%);
        }
        .soft-bg-gradient {
            background: linear-gradient(180deg, #f6fafe 0%, #e6f4f9 100%);
        }
        body {
            -webkit-font-smoothing: antialiased;
        }
        /* Custom alert styles to match design */
        .custom-alert {
            animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateCurrentPasswordDisplay() {
            const newPasswordInput = document.getElementById('new_password');
            const currentPasswordInput = document.getElementById('current_password');
            currentPasswordInput.value = '*'.repeat(newPasswordInput.value.length || 8);
        }

        function updateClock() {
            const now = new Date();
            const options = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
            const timeString = now.toLocaleTimeString([], options);
            if (document.getElementById('currentTime')) {
                document.getElementById('currentTime').textContent = timeString;
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const newPasswordInput = document.getElementById('new_password');
            if (newPasswordInput) {
                newPasswordInput.addEventListener('input', updateCurrentPasswordDisplay);
            }

            // Auto hide alert after 3 seconds
            setTimeout(function() {
                const alert = document.querySelector('.custom-alert');
                if(alert) {
                    $(alert).fadeOut(500);
                }
            }, 3000);

            // Handle file input display
            const fileInput = document.getElementById('photo');
            const fileNameDisplay = document.getElementById('file-name-display');
            if (fileInput && fileNameDisplay) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        fileNameDisplay.textContent = this.files[0].name;
                    }
                });
            }

            setInterval(updateClock, 1000);
            updateClock();
        });
    </script>
</head>
<body class="bg-surface min-h-screen font-body-md text-on-surface pt-20 flex flex-col">

<!-- TopNavBar (Copied from Dashboard) -->
<header class="fixed top-0 w-full z-50 bg-surface/80 backdrop-blur-md shadow-sm shadow-primary/5 transition-all">
    <div class="flex justify-between items-center px-md py-sm max-w-container-max mx-auto h-20">
        <!-- Left: Profile -->
        <div class="flex items-center gap-sm cursor-pointer" onclick="window.location.href='dashboard.php'">
            <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-primary/20">
                <img alt="Profile Picture" class="w-full h-full object-cover" src="<?= htmlspecialchars($photo) ?>?<?= time() ?>"/>
            </div>
            <div class="hidden md:block">
                <span class="font-label-md text-on-surface-variant">Welcome,</span>
                <span class="font-label-md text-primary block"><?= htmlspecialchars($username) ?></span>
            </div>
        </div>
        <!-- Center: Logo -->
        <div class="flex flex-col items-center">
            <img alt="TaskDo Logo" class="h-20 w-auto" src="FOTO/taskdo.png"/>
        </div>
        <!-- Right: Action & Clock -->
        <div class="flex items-center gap-md">
            <div class="hidden md:flex items-center gap-sm px-md py-xs bg-surface-container-low rounded-full">
                <span class="material-symbols-outlined text-primary text-[20px]">schedule</span>
                <span class="font-label-md text-on-surface-variant" id="currentTime"></span>
            </div>
        </div>
    </div>
</header>

<main class="flex-grow pt-8 pb-xl flex flex-col items-center justify-center soft-bg-gradient px-md">
    
    <?php if (!empty($error_message)): ?>
    <div class="custom-alert fixed top-24 left-1/2 -translate-x-1/2 z-[100] w-full max-w-md">
        <div class="bg-error-container text-error px-md py-sm rounded-xl shadow-lg border border-error/10 flex items-center gap-sm">
            <span class="material-symbols-outlined">error</span>
            <p class="font-label-md m-0"><?= $error_message ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Edit Profile Card -->
    <div class="w-full max-w-[800px] bg-surface-container-lowest rounded-[1.5rem] ambient-shadow p-xl md:p-lg relative z-10 my-md">
        <div class="text-center mb-xl">
            <h1 class="font-headline-lg text-on-background mb-xs">Edit Profile</h1>
            <p class="font-body-sm text-on-surface-variant">Manage your account information and visual identity.</p>
        </div>

        <form method="POST" enctype="multipart/form-data" class="space-y-lg">
            <!-- Profile Image Section -->
            <div class="flex flex-col items-center mb-xl">
                <div class="relative group">
                    <div class="w-32 h-32 rounded-full border-4 border-surface-container p-1 overflow-hidden">
                        <img src="<?= htmlspecialchars($photo) ?>?<?= time() ?>" alt="Profile Picture" class="w-full h-full object-cover rounded-full">
                    </div>
                    <label class="absolute bottom-0 right-0 bg-primary text-on-primary p-2 rounded-full cursor-pointer hover:scale-105 transition-transform shadow-lg flex items-center justify-center" for="photo">
                        <span class="material-symbols-outlined text-[20px]">photo_camera</span>
                        <input class="hidden" id="photo" name="photo" type="file" accept="image/*"/>
                    </label>
                </div>
                <p id="file-name-display" class="mt-sm font-label-md text-primary">Change Avatar</p>
            </div>

            <!-- Two-Column Grid for Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                <!-- Username Row -->
                <div class="space-y-xs">
                    <label class="font-label-md text-on-surface-variant px-1">Current Username</label>
                    <input class="w-full bg-surface-container-low border-outline-variant text-on-surface-variant rounded-xl font-body-md cursor-not-allowed px-md py-3" readonly type="text" value="<?= htmlspecialchars($username) ?>"/>
                </div>
                <div class="space-y-xs">
                    <label class="font-label-md text-on-surface px-1" for="new_username">New Username</label>
                    <input class="w-full border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary rounded-xl font-body-md px-md py-3 bg-surface-bright transition-all" id="new_username" name="new_username" placeholder="Enter new username" type="text"/>
                </div>

                <!-- Email Row -->
                <div class="space-y-xs">
                    <label class="font-label-md text-on-surface-variant px-1">Current Email</label>
                    <input class="w-full bg-surface-container-low border-outline-variant text-on-surface-variant rounded-xl font-body-md cursor-not-allowed px-md py-3" readonly type="email" value="<?= htmlspecialchars($email) ?>"/>
                </div>
                <div class="space-y-xs">
                    <label class="font-label-md text-on-surface px-1" for="new_email">New Email Address</label>
                    <input class="w-full border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary rounded-xl font-body-md px-md py-3 bg-surface-bright transition-all" id="new_email" name="new_email" placeholder="Enter new email" type="email"/>
                </div>

                <!-- Password Row -->
                <div class="space-y-xs">
                    <label class="font-label-md text-on-surface-variant px-1">Current Password</label>
                    <input class="w-full bg-surface-container-low border-outline-variant text-on-surface-variant rounded-xl font-body-md cursor-not-allowed px-md py-3" readonly type="password" id="current_password" value="********"/>
                </div>
                <div class="space-y-xs">
                    <label class="font-label-md text-on-surface px-1" for="new_password">New Password (Optional)</label>
                    <input class="w-full border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary rounded-xl font-body-md px-md py-3 bg-surface-bright transition-all" id="new_password" name="new_password" placeholder="Enter new password" type="password"/>
                    <p class="font-body-sm text-[12px] text-on-surface-variant px-1">Leave empty to keep current password.</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col md:flex-row items-center gap-md pt-lg">
                <button class="w-full md:flex-1 blue-gradient text-on-primary font-label-md py-4 rounded-xl hover:opacity-90 transition-all active:scale-[0.98] shadow-md shadow-primary/20 border-0" type="submit">
                    Save Changes
                </button>
                <a href="dashboard.php" class="w-full md:w-1/3 border border-outline-variant text-on-surface-variant font-label-md py-4 rounded-xl hover:bg-surface-container-high transition-colors text-center no-underline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>

<!-- Footer (Copied from Dashboard) -->
<footer class="w-full z-40 mt-auto flex flex-col items-center gap-sm py-md px-gutter bg-surface-container-low/80 backdrop-blur-sm">
    <p class="font-body-sm text-secondary">
        TaskDo &copy; 2024 | All Rights Reserved
    </p>
</footer>

</body>
</html>
