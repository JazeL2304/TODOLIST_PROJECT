<?php
session_start();
include 'database.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil informasi pengguna dari database
$conn = getDatabaseConnection();
$user_id = $_SESSION['user_id'];

// Ambil data pengguna
$user_query = $conn->query("SELECT username, email, photo FROM users WHERE id = $user_id");

if ($user_query) {
    $user = $user_query->fetch_assoc();
    if ($user) {
        $username = $user['username'];
        $email = $user['email'];
        $photo = $user['photo'] ? $user['photo'] : 'default_profile.png';
    } else {
        $username = "Unknown User";
        $email = "Unknown Email";
        $photo = 'default_profile.png';
    }
} else {
    $username = "Unknown User";
    $email = "Unknown Email";
    $photo = 'default_profile.png';
}


// Ambil daftar todo list
$result = $conn->query("SELECT * FROM todo_lists WHERE user_id = $user_id ORDER BY completed DESC, id DESC");
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>TaskDo Dashboard</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                "colors": {
                    "error": "#ba1a1a",
                    "secondary": "#006689",
                    "surface-container-lowest": "#ffffff",
                    "surface-container-low": "#f0f4f8",
                    "surface-container": "#eaeef3",
                    "on-surface": "#171c1f",
                    "on-surface-variant": "#3e484f",
                    "primary": "#006386",
                    "on-primary": "#ffffff",
                    "surface": "#f6fafe",
                    "background": "#f6fafe",
                    "outline": "#6e7880",
                    "outline-variant": "#bdc8d0",
                    "error-container": "#ffdad6",
                    "on-error": "#ffffff",
                    "on-error-container": "#93000a",
                    "secondary-container": "#66ccff",
                    "on-secondary-container": "#005573",
                },
                "spacing": {
                    "lg": "40px",
                    "xl": "64px",
                    "xs": "4px",
                    "base": "8px",
                    "container-max": "1200px",
                    "gutter": "24px",
                    "sm": "12px",
                    "md": "24px"
                },
                "fontFamily": {
                    "label-md": ["Inter"],
                    "headline-lg": ["Hanken Grotesk"],
                    "body-md": ["Inter"],
                    "display-lg": ["Hanken Grotesk"],
                    "body-sm": ["Inter"],
                    "headline-sm": ["Hanken Grotesk"],
                    "headline-md": ["Hanken Grotesk"],
                },
                "boxShadow": {
                    "ambient": "0px 10px 30px rgba(0, 153, 204, 0.08)",
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
    .bg-primary-gradient {
        background: linear-gradient(135deg, #0099CC 0%, #66CCFF 100%);
    }
    .task-done {
        text-decoration: line-through;
        opacity: 0.6;
    }
    .wave-bg {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%230099cc' fill-opacity='1' d='M0,160L48,176C96,192,192,224,288,213.3C384,203,480,149,576,149.3C672,149,768,203,864,229.3C960,256,1056,256,1152,229.3C1248,203,1344,149,1392,122.7L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
        mask-repeat: no-repeat;
        mask-position: top;
        background-color: #005573;
    }

    /* Checklist styling to match original UI logic */
    .form-check-input {
        cursor: pointer;
        border-radius: 4px;
        transition: all 0.3s ease;
        width: 20px !important;
        height: 20px !important;
    }

    .form-check-input:checked {
        background-color: #66CCFF;
        border-color: #66CCFF;
    }

    .form-check-input:focus {
        border-color: #66CCFF;
        box-shadow: 0 0 0 0.25rem rgba(102, 204, 255, 0.25);
    }
    
    a { text-decoration: none; }
    
    /* Fix Bootstrap modal z-index conflicts with Tailwind */
    .modal-backdrop { z-index: 1040 !important; }
    .modal { z-index: 1055 !important; }
    .modal-dialog { z-index: 1056 !important; }
    /* Notification above everything */
    #notification-container { z-index: 9999 !important; }

    /* Hero Slider Styling */
    .hero-slider-container {
        position: relative;
        height: 280px;
        overflow: hidden;
    }
    .slider-track {
        display: flex;
        height: 100%;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .slide-item {
        min-width: 100%;
        height: 100%;
        position: relative;
    }
    .slide-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .slide-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, rgba(0, 85, 115, 0.4), rgba(0, 85, 115, 0.7));
        z-index: 1;
    }
    .slider-content {
        position: absolute;
        inset: 0;
        z-index: 10;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }
    .slider-dots {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 8px;
        z-index: 20;
    }
    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .dot.active {
        background: #ffffff;
        transform: scale(1.2);
    }
    .slider-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 20;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(4px);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
    }
    .slider-arrow:hover {
        background: rgba(255, 255, 255, 0.25);
    }
    .slider-arrow.prev { left: 20px; }
    .slider-arrow.next { right: 20px; }
</style>
</head>
<body class="bg-surface min-h-screen font-body-md text-on-surface pt-20">
<!-- TopNavBar -->
<header class="fixed top-0 w-full z-50 bg-surface/80 backdrop-blur-md shadow-sm shadow-primary/5 transition-all">
    <div class="flex justify-between items-center px-md py-sm max-w-container-max mx-auto h-20">
        <!-- Left: Profile -->
        <div class="flex items-center gap-sm cursor-pointer" id="userProfile" data-bs-toggle="modal" data-bs-target="#profileModal">
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

<!-- Main Content -->
<main class="pt-8 pb-8 px-md max-w-container-max mx-auto">
    <!-- Dashboard Header Slider -->
    <div class="hero-slider-container rounded-3xl mb-xl shadow-ambient">
        <div class="slider-track" id="sliderTrack">
            <div class="slide-item">
                <img src="FOTO/motivation1.jpeg" alt="Slide 1">
                <div class="slide-overlay"></div>
            </div>
            <div class="slide-item">
                <img src="FOTO/motivation2.jpeg" alt="Slide 2">
                <div class="slide-overlay"></div>
            </div>
            <div class="slide-item">
                <img src="FOTO/workspace.jpeg" alt="Slide 3">
                <div class="slide-overlay"></div>
            </div>
        </div>
        
        <div class="slider-content">
            <h1 class="font-display-lg text-on-primary mb-xs">MY TODO LIST</h1>
            <p class="font-body-md text-on-primary/80">Stay focused, maintain clarity, and achieve more.</p>
        </div>

        <button class="slider-arrow prev" onclick="moveSlide(-1)">
            <span class="material-symbols-outlined">chevron_left</span>
        </button>
        <button class="slider-arrow next" onclick="moveSlide(1)">
            <span class="material-symbols-outlined">chevron_right</span>
        </button>

        <div class="slider-dots" id="sliderDots">
            <div class="dot active" onclick="goToSlide(0)"></div>
            <div class="dot" onclick="goToSlide(1)"></div>
            <div class="dot" onclick="goToSlide(2)"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
        <!-- Left Column: Search & Add -->
        <div class="lg:col-span-4 space-y-gutter">
            <!-- Search Card -->
            <section class="bg-surface-container-lowest rounded-3xl p-md shadow-ambient border border-white">
                <div class="relative flex items-center search-container">
                    <span class="material-symbols-outlined absolute left-4 text-outline" data-icon="search">search</span>
                    <input class="w-full pl-12 pr-12 py-3 bg-surface-container-low border-none rounded-2xl focus:ring-2 focus:ring-primary/20 font-body-sm transition-all" placeholder="Find a task..." type="text" id="searchTask" autocomplete="off"/>
                    <button class="absolute right-4 text-outline hover:text-error transition-colors" id="clearSearch" style="display: none;">
                        <span class="material-symbols-outlined text-[20px]" data-icon="close">close</span>
                    </button>
                </div>
            </section>

            <!-- Add Task Card -->
            <section class="bg-surface-container-lowest rounded-3xl p-md shadow-ambient border border-white">
                <h3 class="font-headline-sm text-on-surface mb-md">New Task</h3>
                <form id="addListForm">
                    <div class="space-y-sm">
                        <input class="w-full px-md py-3 bg-white border border-outline-variant rounded-xl focus:border-primary focus:ring-0 font-body-sm transition-all outline-none" placeholder="What needs to be done?" type="text" id="listTitle" name="title" required autocomplete="off"/>
                        <button type="submit" class="w-full bg-primary-gradient text-on-primary py-3 rounded-xl font-label-md flex items-center justify-center gap-xs hover:shadow-lg hover:shadow-primary/20 active:scale-95 transition-all border-0">
                            <span class="material-symbols-outlined" data-icon="add">add</span>
                            Add Task
                        </button>
                    </div>
                </form>
            </section>
        </div>

        <!-- Right Column: Task List -->
        <div class="lg:col-span-8">
            <section class="bg-surface-container-lowest rounded-3xl shadow-ambient border border-white overflow-hidden">
                <div class="p-md border-b border-surface-variant flex justify-between items-center bg-surface-container-low/30">
                    <h2 class="font-headline-sm text-primary">Pending Tasks</h2>
                </div>
                
                <div class="divide-y divide-surface-variant" id="todoList">
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="flex items-center justify-between p-md hover:bg-surface-container-low transition-colors group list-group-item border-0 rounded-none mb-0" id="list-<?= $row['id'] ?>">
                        <div class="flex items-center gap-md">
                            <input type="checkbox" class="form-check-input me-3 mt-0" data-id="<?= $row['id'] ?>" <?= $row['completed'] ? 'checked' : '' ?>>
                            <span class="font-body-md text-on-surface list-title <?= $row['completed'] ? 'task-done' : '' ?>" id="title-<?= $row['id'] ?>">
                                <?= htmlspecialchars($row['title']) ?>
                            </span>
                        </div>
                        <div class="flex gap-sm opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="p-2 text-tertiary hover:bg-tertiary-fixed rounded-lg transition-colors btn-action btn-edit border-0 bg-transparent" data-id="<?= $row['id'] ?>" data-title="<?= htmlspecialchars($row['title']) ?>" data-bs-toggle="modal" data-bs-target="#editModal">
                                <span class="material-symbols-outlined text-[20px]" data-icon="edit">edit</span>
                            </button>
                            <button class="p-2 text-error hover:bg-error-container rounded-lg transition-colors btn-action btn-delete border-0 bg-transparent" data-id="<?= $row['id'] ?>" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <span class="material-symbols-outlined text-[20px]" data-icon="delete">delete</span>
                            </button>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </div>
    </div>
</main>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-surface-container-lowest rounded-[1.5rem] shadow-2xl border-0 overflow-hidden" style="opacity:1!important;">
            <!-- Header -->
            <div class="p-md flex items-center justify-between" style="background: linear-gradient(135deg,#007da8 0%,#006386 100%);">
                <h4 class="font-headline-sm text-on-primary m-0">Edit Task</h4>
                <button type="button" class="material-symbols-outlined text-on-primary border-0 bg-transparent p-0 leading-none" data-bs-dismiss="modal" style="font-size:22px;">close</button>
            </div>
            <!-- Body -->
            <div class="modal-body p-xl">
                <form id="editForm" class="space-y-lg">
                    <div class="space-y-sm">
                        <label class="font-label-md text-on-surface-variant" for="editTitle">Task Title</label>
                        <input class="w-full p-md rounded-xl border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all font-body-md text-on-surface" type="text" id="editTitle" name="editTitle" required/>
                        <input type="hidden" id="editListId" name="editListId">
                    </div>
                    <div class="flex gap-md pt-sm">
                        <button type="button" class="flex-1 py-md px-lg border border-outline-variant text-on-surface-variant rounded-full font-label-md hover:bg-surface-container-low transition-all bg-transparent" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="flex-1 py-md px-lg text-on-primary rounded-full font-label-md hover:opacity-90 transition-all border-0" style="background: linear-gradient(135deg,#007da8 0%,#006386 100%);">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content bg-surface-container-lowest rounded-[1.5rem] shadow-2xl border-0" style="opacity:1!important;">
            <div class="modal-body p-xl text-center">
                <!-- Icon -->
                <div class="mx-auto w-16 h-16 bg-error-container rounded-full flex items-center justify-center text-error mb-md">
                    <span class="material-symbols-outlined" style="font-size:30px;">delete_forever</span>
                </div>
                <!-- Text -->
                <h4 class="font-headline-sm text-on-surface mb-sm">Delete Task?</h4>
                <p class="text-on-surface-variant font-body-md mb-xl">This action cannot be undone. This task will be permanently removed.</p>
                <!-- Buttons -->
                <div class="flex gap-md">
                    <button type="button"
                        class="flex-1 font-label-md rounded-full transition-all bg-transparent"
                        style="padding: 12px 24px; border: 1.5px solid #bdc8d0; color: #3e484f;"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button"
                        class="flex-1 font-label-md rounded-full transition-all border-0"
                        style="padding: 12px 24px; background:#ba1a1a; color:#ffffff;"
                        id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-surface-container-lowest rounded-[1.5rem] p-xl shadow-2xl border-0 text-center" style="opacity:1!important;">
            <div class="modal-body p-0">
                <button type="button" class="material-symbols-outlined text-on-surface-variant border-0 bg-transparent p-0 absolute top-4 right-4" data-bs-dismiss="modal" style="font-size:22px;">close</button>
                <div class="relative w-32 h-32 mx-auto mb-lg">
                    <img src="<?= htmlspecialchars($photo) ?>?<?= time() ?>" alt="Profile Picture" class="w-full h-full rounded-full object-cover border-4 border-surface-container shadow-lg">
                </div>
                <h4 class="font-headline-md text-on-surface mb-xs"><?= htmlspecialchars($username) ?></h4>
                <p class="text-on-surface-variant font-body-md mb-xl"><?= htmlspecialchars($email) ?></p>
                <div class="space-y-md">
                    <a href="edit_profile.php" class="w-full flex items-center justify-center gap-sm py-md px-lg text-on-primary rounded-full font-label-md hover:opacity-90 transition-all no-underline border-0" style="background: linear-gradient(135deg,#007da8 0%,#006386 100%);">
                        <span class="material-symbols-outlined text-base">person_edit</span>
                        Edit Profile
                    </a>
                    <a href="logout.php" class="w-full flex items-center justify-center gap-sm py-md px-lg text-error hover:bg-error-container/20 rounded-full font-label-md transition-all no-underline">
                        <span class="material-symbols-outlined text-base">logout</span>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-surface-container-lowest rounded-[1.5rem] p-xl shadow-2xl border-0" style="opacity:1!important;">
            <div class="modal-body p-0">
                <div class="mb-xl">
                    <h4 class="font-headline-sm text-on-surface mb-sm">Confirm Password</h4>
                    <p class="text-on-surface-variant font-body-sm">For your security, please re-enter your password to proceed with account changes.</p>
                </div>
                <form id="passwordForm" class="space-y-lg">
                    <div class="relative">
                        <span class="material-symbols-outlined text-on-surface-variant absolute left-md top-1/2 -translate-y-1/2" style="opacity:0.5;">lock</span>
                        <input class="w-full pl-xl pr-md py-md rounded-xl border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all font-body-md" placeholder="Enter your password" type="password" id="password" name="password" required/>
                    </div>
                    <p id="passwordError" class="text-error text-sm font-label-md" style="display:none;">Incorrect password. Please try again.</p>
                    <div class="flex gap-md">
                        <button type="button" class="flex-1 py-md px-lg border border-outline-variant text-on-surface-variant rounded-full font-label-md hover:bg-surface-container-low transition-all bg-transparent" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="flex-1 py-md px-lg text-on-primary rounded-full font-label-md hover:opacity-90 transition-all border-0" style="background: linear-gradient(135deg,#007da8 0%,#006386 100%);">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Footer -->
<footer class="w-full z-40 mt-auto flex flex-col items-center gap-sm py-md px-gutter bg-surface-container-low/80 backdrop-blur-sm">
    <p class="font-body-sm text-secondary">
        TaskDo &copy; 2024 | All Rights Reserved
    </p>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // =============================================
    // GLOBAL MODAL BACKDROP CLEANUP
    // Ensures backdrop is ALWAYS removed when any modal hides
    // =============================================
    $(document).on('hidden.bs.modal', '.modal', function() {
        // Remove stuck backdrop
        $('.modal-backdrop').remove();
        // Re-enable scrolling
        $('body').removeClass('modal-open');
        $('body').css('overflow', '');
        $('body').css('padding-right', '');
    });

    // Handle when the "Edit Profile" button is clicked
    $('a[href="edit_profile.php"]').click(function(e) {
        e.preventDefault(); // Prevent direct link
        $('#passwordModal').modal('show'); // Show the password modal
    });

    // Handle password form submission
$('#passwordForm').submit(function(e) {
    e.preventDefault();

    const password = $('#password').val().trim();

    console.log('Sending password:', password); // Debug log

      // Periksa password saat ini
      $.get('check_current_password.php', function(currentPassword) {
        console.log('Current password in database:', currentPassword);
    });

    $.ajax({
        url: 'validate_password.php',
        method: 'POST',
        data: { password: password },
        success: function(response) {
            console.log('Raw Server response:', response); // Debug log
            try {
                const result = JSON.parse(response);

                if (result.success) {
                    window.location.href = 'edit_profile.php';
                } else {
                    $('#passwordError').text(result.message);
                    $('#passwordError').show();
                    
                    if (result.debug) {
                        console.log('Debug info:', result.debug);
                    }
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                alert("Error processing server response");
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', error);
            alert("Error occurred during password validation");
        }
    });
});

// Tambahkan event untuk menyembunyikan pesan error saat input berubah
$('#password').on('input', function() {
    $('#passwordError').hide();
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let deleteListId;

    // Update real-time clock
    function updateClock() {
        const now = new Date();
        const options = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
        const timeString = now.toLocaleTimeString([], options);
        document.getElementById('currentTime').textContent = timeString;
    }

    // Call updateClock every second
    setInterval(updateClock, 1000);
    updateClock(); // Initial call

    // Handle profile modal display
    $('#userProfile').click(function() {
        $('#profileModal').modal('show'); // Tampilkan modal profil
    });

    // Handle profile picture upload
    $('#uploadProfilePicForm').submit(function(e) {
    e.preventDefault(); // Prevent normal form submission
    const formData = new FormData(this);

    $.ajax({
        url: 'upload_profile_pic.php',
        method: 'POST',
        data: formData,
        processData: false, // Important for file upload
        contentType: false, // Important for file upload
        success: function(response) {
            const result = JSON.parse(response);
            if (result.success) {
                // Update the profile picture in the modal and dashboard
               // Update the profile picture in the modal and dashboard
                $('img[alt="Profile Picture"]').attr('src', result.photo + '?' + new Date().getTime()); // Update in modal
                $('#userProfile img').attr('src', result.photo + '?' + new Date().getTime()); // Update in dashboard

                $('#uploadMessage').text(result.message).removeClass('text-danger').addClass('text-success');
            } else {
                $('#uploadMessage').text(result.message).removeClass('text-success').addClass('text-danger');
            }
        },
        error: function() {
            $('#uploadMessage').text("Error occurred while uploading the file.").removeClass('text-success').addClass('text-danger');
        }
    });
});

    // Handle edit list action
    $(document).on('click', '.editList', function() {
        const listId = $(this).data('id');
        const currentTitle = $(this).data('title');
        $('#editTitle').val(currentTitle);
        $('#editListId').val(listId);
    });

    // Handle confirm delete
    $(document).on('click', '.deleteList', function() {
        deleteListId = $(this).data('id'); // Simpan ID yang akan dihapus
    });

    // Handle confirm delete action
    $('#confirmDelete').click(function() {
        $.ajax({
            url: 'delete_list.php',
            method: 'POST',
            data: { list_id: deleteListId },
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    $('#list-' + deleteListId).remove();  // Remove the deleted list from the DOM
                    $('#deleteModal').modal('hide');  // Hide the modal
                } else {
                    alert("Failed to delete the list. Please try again.");
                }
            },
            error: function() {
                alert("Error occurred while deleting the list.");
            }
        });
    });

// Update bagian handle edit list action
$(document).on('click', '.btn-edit', function() {
    const listId = $(this).data('id');
    const currentTitle = $(this).data('title');
    $('#editTitle').val(currentTitle);
    $('#editListId').val(listId);
    $('#currentTaskTitle').text(currentTitle); // Menambahkan judul task yang sedang diedit
});

// Style tambahan untuk modal
const modalStyles = `
    .modal-content {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .modal-header {
        background: var(--primary-gradient);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 1rem 1.5rem;
    }

    #currentTaskTitle {
        font-style: italic;
        font-weight: normal;
        opacity: 0.9;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .btn-close {
        background-color: white;
        opacity: 0.8;
    }

    .btn-close:hover {
        opacity: 1;
    }
`;

// Tambahkan style ke document
if (!document.getElementById('modalStyles')) {
    const styleSheet = document.createElement("style");
    styleSheet.id = 'modalStyles';
    styleSheet.textContent = modalStyles;
    document.head.appendChild(styleSheet);
}

// Handle delete button click with event delegation
$(document).on('click', '.btn-delete', function() {
    deleteListId = $(this).data('id');
    $('#deleteModal').modal('show');
});

// Handle confirm delete action
$('#confirmDelete').click(function() {
    if (!deleteListId) return;

    $.ajax({
        url: 'delete_list.php',
        method: 'POST',
        data: { list_id: deleteListId },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.success) {
                    // Animate and remove the deleted item
                    $(`#list-${deleteListId}`).slideUp(300, function() {
                        $(this).remove();
                    });
                    $('#deleteModal').modal('hide');
                    showNotification('Task deleted successfully!', 'success');
                } else {
                    showNotification('Failed to delete the task.', 'danger');
                }
            } catch (e) {
                showNotification('Error processing the response.', 'danger');
            }
        },
        error: function() {
            showNotification('Error occurred while deleting the task.', 'danger');
        }
    });
});

// Fungsi untuk update ID todo list
function updateTodoListIds() {
    $.ajax({
        url: 'update_ids.php',
        method: 'POST',
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.success) {
                    // Update ID di tampilan
                    $('#todoList .list-group-item').each(function(index) {
                        const newId = index + 1;
                        const $item = $(this);
                        
                        // Update ID elemen
                        $item.attr('id', `list-${newId}`);
                        
                        // Update ID pada checkbox
                        $item.find('.form-check-input').attr('data-id', newId);
                        
                        // Update ID pada span title
                        $item.find('.list-title').attr('id', `title-${newId}`);
                        
                        // Update ID pada tombol edit
                        $item.find('.btn-edit')
                            .attr('data-id', newId);
                        
                        // Update ID pada tombol delete
                        $item.find('.btn-delete')
                            .attr('data-id', newId);
                    });
                }
            } catch (e) {
                console.error('Error updating IDs:', e);
            }
        },
        error: function() {
            console.error('Failed to update IDs');
        }
    });
}


// Handle edit form submission with real-time update
$('#editForm').submit(function(e) {
    e.preventDefault();
    const listId = $('#editListId').val();
    const newTitle = $('#editTitle').val().trim();

    if (!newTitle) {
        showNotification('Please enter a task title.', 'warning');
        return;
    }

    $.ajax({
        url: 'edit_list.php',
        method: 'POST',
        data: { list_id: listId, title: newTitle },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.success) {
                    // Segera update tampilan tanpa refresh
                    const $listItem = $(`#list-${listId}`);
                    const $titleElement = $(`#title-${listId}`);
                    const $editButton = $listItem.find('.btn-edit');

                    // Animate the title update
                    $titleElement.fadeOut(200, function() {
                        $(this).text(newTitle).fadeIn(200);
                    });

                    // Update data-title pada tombol edit
                    $editButton.attr('data-title', newTitle);

                    // Update current task title di modal jika masih terbuka
                    $('#currentTaskTitle').text(newTitle);

                    // Tutup modal dengan animasi
                    $('#editModal').modal('hide');

                    // Tampilkan notifikasi sukses
                    showNotification('Task updated successfully!', 'success');
                } else {
                    showNotification('Failed to update task.', 'danger');
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                showNotification('Error processing the response.', 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', error);
            showNotification('Error occurred while updating the task.', 'danger');
        }
    });
});

// Tambahkan event listener untuk modal edit
$('#editModal').on('shown.bs.modal', function(e) {
    const button = $(e.relatedTarget);
    const currentTitle = button.data('title');
    
    // Set nilai input dan judul modal
    $('#editTitle').val(currentTitle);
    $('#currentTaskTitle').text(currentTitle);
    
    // Focus pada input field
    $('#editTitle').focus();
});

// Tambahkan animasi saat mengupdate task
function updateTaskWithAnimation(listId, newTitle) {
    const $listItem = $(`#list-${listId}`);
    
    // Tambahkan class untuk animasi
    $listItem.addClass('updating');
    
    // Update konten dengan animasi
    $listItem.find('.list-title').fadeOut(200, function() {
        $(this).text(newTitle).fadeIn(200);
        
        // Hapus class animasi
        setTimeout(() => {
            $listItem.removeClass('updating');
        }, 300);
    });
}

// CSS untuk animasi update
const updateAnimationStyles = `
    .list-group-item.updating {
        background-color: rgba(102, 204, 255, 0.1);
        transition: background-color 0.3s ease;
    }

    .modal.fade .modal-content {
        transform: scale(0.95);
        transition: all 0.3s ease;
    }

    .modal.show .modal-content {
        transform: scale(1);
    }

    .list-title {
        transition: all 0.3s ease;
    }
`;

// Tambahkan styles ke document
if (!document.getElementById('updateAnimationStyles')) {
    const styleSheet = document.createElement("style");
    styleSheet.id = 'updateAnimationStyles';
    styleSheet.textContent = updateAnimationStyles;
    document.head.appendChild(styleSheet);
}

// Update bagian Handle add list form submission
$('#addListForm').submit(function(e) {
    e.preventDefault();
    const title = $('#listTitle').val();
    
    if (!title.trim()) {
        showNotification('Please enter a task title.', 'warning');
        return;
    }

    $.ajax({
        url: 'add_list.php',
        method: 'POST',
        data: { title: title },
        success: function(response) {
            try {
                const newList = JSON.parse(response);
                if (newList.success) {
                    const newListItem = `
                    <div class="flex items-center justify-between p-md hover:bg-surface-container-low transition-colors group list-group-item border-0 rounded-none mb-0" id="list-${newList.id}">
                        <div class="flex items-center gap-md">
                            <input type="checkbox" class="form-check-input me-3 mt-0" data-id="${newList.id}">
                            <span class="font-body-md text-on-surface list-title" id="title-${newList.id}">${newList.title}</span>
                        </div>
                        <div class="flex gap-sm opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="p-2 text-tertiary hover:bg-tertiary-fixed rounded-lg transition-colors btn-action btn-edit border-0 bg-transparent" data-id="${newList.id}" data-title="${newList.title}" data-bs-toggle="modal" data-bs-target="#editModal">
                                <span class="material-symbols-outlined text-[20px]" data-icon="edit">edit</span>
                            </button>
                            <button class="p-2 text-error hover:bg-error-container rounded-lg transition-colors btn-action btn-delete border-0 bg-transparent" data-id="${newList.id}" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <span class="material-symbols-outlined text-[20px]" data-icon="delete">delete</span>
                            </button>
                        </div>
                    </div>
                    `;

                    // Cari item terakhir yang di-checklist
                    const $checkedItems = $('#todoList .list-group-item').filter(function() {
                        return $(this).find('.form-check-input').is(':checked');
                    });

                    if ($checkedItems.length > 0) {
                        // Jika ada item yang di-checklist, tambahkan setelah item terakhir yang di-checklist
                        $checkedItems.last().after($(newListItem).hide().slideDown(300));
                    } else {
                        // Jika tidak ada yang di-checklist, tambahkan di awal list
                        $('#todoList').prepend($(newListItem).hide().slideDown(300));
                    }
                    
                    $('#listTitle').val('');
                    
                    // Update ID setelah menambah item baru
                    updateTodoListIds();
                    
                    // Reinitialize checkbox event handler
                    initializeCheckboxHandlers();

                    showNotification('Task added successfully!', 'success');
                } else {
                    showNotification('Failed to add task: ' + newList.message, 'danger');
                }
            } catch (e) {
                showNotification('Error processing the response.', 'danger');
            }
        },
        error: function() {
            showNotification('Error occurred while adding the task.', 'danger');
        }
    });
});

// Fungsi untuk menginisialisasi event handler checkbox
function initializeCheckboxHandlers() {
    $('.form-check-input').off('change').on('change', function() {
        const listId = $(this).data('id');
        const isChecked = $(this).is(':checked');
        const $titleElement = $(`#title-${listId}`);
        const $checkbox = $(this);
        const $listItem = $checkbox.closest('.list-group-item');

        // Update UI segera
        if (isChecked) {
            $titleElement.css('text-decoration', 'line-through');
            // Animate dan pindahkan ke atas
            $listItem.fadeOut(300, function() {
                $(this).prependTo('#todoList').fadeIn(300);
            });
        } else {
            $titleElement.css('text-decoration', 'none');
            // Animate dan pindahkan ke bawah
            $listItem.fadeOut(300, function() {
                const $uncheckedItems = $('#todoList .list-group-item').filter(function() {
                    return !$(this).find('.form-check-input').is(':checked');
                });
                
                if ($uncheckedItems.length > 0) {
                    $(this).insertBefore($uncheckedItems.first()).fadeIn(300);
                } else {
                    $(this).appendTo('#todoList').fadeIn(300);
                }
            });
        }

        // Kirim ke server
        $.ajax({
            url: 'update_status.php',
            method: 'POST',
            data: { 
                list_id: listId, 
                completed: isChecked ? 1 : 0 
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (!result.success) {
                        // Kembalikan status jika gagal
                        $checkbox.prop('checked', !isChecked);
                        $titleElement.css('text-decoration', isChecked ? 'none' : 'line-through');
                        showNotification('Failed to update status.', 'warning');
                    }
                } catch (e) {
                    // Handle error parsing
                    $checkbox.prop('checked', !isChecked);
                    $titleElement.css('text-decoration', isChecked ? 'none' : 'line-through');
                }
            },
            error: function() {
                // Handle network error
                $checkbox.prop('checked', !isChecked);
                $titleElement.css('text-decoration', isChecked ? 'none' : 'line-through');
                showNotification('Connection error. Please try again.', 'danger');
            }
        });
    });
}

// Panggil fungsi inisialisasi saat dokumen siap
$(document).ready(function() {
    initializeCheckboxHandlers();
});

// Utility function untuk menampilkan notifikasi
function showNotification(message, type) {
    // Check if notification container exists, if not create it
    if (!$('#notification-container').length) {
        $('body').append('<div id="notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>');
    }

    // Create notification element
    const notification = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert" 
             style="min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    // Add notification to container with animation
    const $notification = $(notification).appendTo('#notification-container');
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        $notification.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}

// Handle checkbox status updates
// Handle checkbox status updates (ubah kode yang ada)
$(document).on('change', '.form-check-input', function() {
    const listId = $(this).data('id');
    const isChecked = $(this).is(':checked');
    const $titleElement = $(`#title-${listId}`);
    const $listItem = $(this).closest('.list-group-item');

    // Update UI
    if (isChecked) {
        $titleElement.css('text-decoration', 'line-through');
        $listItem.fadeOut(300, function() {
            $(this).prependTo('#todoList').fadeIn(300);
        });
    } else {
        $titleElement.css('text-decoration', 'none');
        $listItem.fadeOut(300, function() {
            $(this).appendTo('#todoList').fadeIn(300);
        });
    }

    // Kirim ke server
    $.ajax({
        url: 'update_status.php',
        method: 'POST',
        data: { list_id: listId, completed: isChecked ? 1 : 0 },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (!result.success) {
                    // Kembalikan state jika gagal
                    $(this).prop('checked', !isChecked);
                    $titleElement.css('text-decoration', isChecked ? 'none' : 'line-through');
                    showNotification('Failed to update status', 'danger');
                }
            } catch (e) {
                console.error('Error:', e);
                showNotification('Error updating status', 'danger');
            }
        },
        error: function() {
            showNotification('Connection error', 'danger');
        }
    });
});



// Tambahkan CSS untuk animasi yang lebih smooth
</script>

<!-- Add this JavaScript at the end of your existing script section -->
<script>
// Fungsi pencarian yang ditingkatkan dengan pemindahan posisi
// Fungsi pencarian yang sudah diperbaiki
function searchTasks() {
    const searchTerm = $('#searchTask').val().toLowerCase().trim();
    const $todoList = $('#todoList');
    const $items = $todoList.find('.list-group-item').detach(); // Lepaskan semua item
    const sortedItems = [];

    // Urutkan item berdasarkan hasil pencarian
    $items.each(function() {
        const $item = $(this);
        const taskTitle = $item.find('.list-title').text().toLowerCase();
        const isMatch = taskTitle.includes(searchTerm);
        
        // Reset highlight
        const $titleElement = $item.find('.list-title');
        const originalText = $titleElement.text();

        // Jika ada kata yang cocok
        if (isMatch) {
            // Tambahkan highlight jika ada search term
            if (searchTerm) {
                const regex = new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi');
                const highlightedText = originalText.replace(regex, '<mark>$1</mark>');
                $titleElement.html(highlightedText);
            } else {
                $titleElement.html(originalText);
            }
            $item.show();
            // Tambahkan ke awal array untuk ditampilkan di atas
            sortedItems.unshift($item);
        } else {
            $titleElement.html(originalText);
            $item.hide();
            // Tambahkan ke akhir array
            sortedItems.push($item);
        }
    });

    // Terapkan urutan baru
    $todoList.append(sortedItems);

    // Tampilkan pesan jika tidak ada hasil
    updateNoResultsMessage(searchTerm, sortedItems);

    // Update tombol clear
    updateClearButton(searchTerm);
}

// Fungsi untuk escape karakter khusus dalam RegExp
function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

// Fungsi untuk update pesan tidak ada hasil
function updateNoResultsMessage(searchTerm, sortedItems) {
    const $noResults = $('#noResults');
    const hasVisibleItems = sortedItems.some($item => $item.is(':visible'));

    if (!hasVisibleItems && searchTerm) {
        if (!$noResults.length) {
            const noResultsHtml = `
                <div id="noResults" class="alert alert-info text-center my-3">
                    <i class="fas fa-search me-2"></i>
                    <span>No tasks found matching "${searchTerm}"</span>
                </div>`;
            $('#todoList').after(noResultsHtml);
        } else {
            $noResults.show();
        }
    } else {
        $noResults?.remove();
    }
}

// Fungsi untuk update tombol clear
function updateClearButton(searchTerm) {
    const $clearButton = $('#clearSearch');
    if (searchTerm) {
        $clearButton.fadeIn(300);
    } else {
        $clearButton.fadeOut(300);
    }
}

// Event listener untuk input pencarian dengan debounce
$('#searchTask').on('input', debounce(function() {
    searchTasks();
}, 300));

// Event listener untuk tombol clear
$(document).on('click', '#clearSearch', function() {
    $('#searchTask').val('');
    searchTasks();
    $(this).hide();
});

// Fungsi debounce untuk optimasi performa
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// CSS untuk tampilan
const searchStyles = `
    mark {
        background: rgba(102, 204, 255, 0.3);
        padding: 0.2em;
        border-radius: 3px;
        transition: background-color 0.3s ease;
    }

    mark:hover {
        background: rgba(102, 204, 255, 0.5);
    }

    .list-group-item {
        transition: all 0.3s ease;
    }

    #searchTask {
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    #searchTask:focus {
        border-color: #66CCFF;
        box-shadow: 0 0 0 0.2rem rgba(102, 204, 255, 0.25);
    }

    .search-container {
        position: relative;
    }

    .search-icon {
        color: #66CCFF;
    }

    #clearSearch {
        transition: all 0.3s ease;
    }

    #clearSearch:hover {
        background-color: #66CCFF;
        color: white;
    }

    .alert-info {
        background: rgba(102, 204, 255, 0.1);
        border: 1px solid rgba(102, 204, 255, 0.2);
        color: #0099CC;
    }
`;

// Tambahkan style ke document
if (!document.getElementById('searchStyles')) {
    const styleSheet = document.createElement("style");
    styleSheet.id = 'searchStyles';
    styleSheet.textContent = searchStyles;
    document.head.appendChild(styleSheet);
}

// Hero Slider Logic
let slideIndex = 0;
const track = document.getElementById('sliderTrack');
const dots = document.querySelectorAll('.dot');
const totalSlides = 3;

function updateSlider() {
    if (!track) return;
    track.style.transform = `translateX(-${slideIndex * 100}%)`;
    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === slideIndex);
    });
}

window.moveSlide = function(n) {
    slideIndex = (slideIndex + n + totalSlides) % totalSlides;
    updateSlider();
}

window.goToSlide = function(n) {
    slideIndex = n;
    updateSlider();
}

// Auto slide
setInterval(() => {
    moveSlide(1);
}, 3000);
</script>

</body>
</html>
<style> .modal.show { display: block !important; opacity: 1 !important; } .modal.show .modal-content { opacity: 1 !important; transform: none !important; visibility: visible !important; display: block !important; z-index: 1060 !important; } .modal-backdrop { z-index: 1040 !important; } .modal { z-index: 1050 !important; } </style>
