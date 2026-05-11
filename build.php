<?php
$old_dashboard = file_get_contents('c:\xampp\htdocs\UTS_LAB_WEBPROG\dashboard.php');

// Extract PHP Header
preg_match('/(<\?php.*?\?>)/s', $old_dashboard, $php_matches);
$php_header = $php_matches[1];

// Extract JS
preg_match('/(<script>\s*\/\/ Handle when the "Edit Profile".*)/s', $old_dashboard, $js_matches);
$js_block = $js_matches[1];

// The new HTML template
$new_html = <<<'HTML'

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
    
    /* Hide tailwind reset styles for bootstrap modal */
    .modal {
        --bs-modal-zindex: 1055;
    }
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
            <span class="font-headline-md font-bold text-primary tracking-tight">TaskDo</span>
            <div class="w-1.5 h-1.5 bg-secondary rounded-full mt-1"></div>
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
<main class="pt-8 pb-32 px-md max-w-container-max mx-auto">
    <!-- Dashboard Header -->
    <div class="bg-primary-gradient rounded-3xl p-xl mb-xl shadow-ambient text-center">
        <h1 class="font-display-lg text-on-primary mb-xs">MY TODO LIST</h1>
        <p class="font-body-md text-on-primary/80">Stay focused, maintain clarity, and achieve more.</p>
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
        <div class="modal-content bg-surface-container-lowest rounded-3xl shadow-2xl border-0 overflow-hidden">
            <div class="bg-primary-gradient p-md flex justify-between items-center modal-header border-0 rounded-t-3xl">
                <h3 class="font-headline-sm text-on-primary modal-title m-0">Edit Task: <span id="currentTaskTitle" class="italic opacity-90 font-normal"></span></h3>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-xl space-y-md">
                <form id="editForm">
                    <div>
                        <label class="block font-label-md text-on-surface-variant mb-xs" for="editTitle">Task Title</label>
                        <input class="w-full px-md py-3 bg-surface-container-low border border-outline-variant rounded-xl focus:border-primary focus:ring-0 outline-none font-body-md" type="text" id="editTitle" name="editTitle" required/>
                        <input type="hidden" id="editListId" name="editListId">
                    </div>
                    <div class="flex justify-end gap-md pt-md">
                        <button type="button" class="px-xl py-3 text-on-surface-variant font-label-md hover:bg-surface-container-high rounded-xl transition-colors border-0 bg-transparent" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="px-xl py-3 bg-primary-gradient text-on-primary font-label-md rounded-xl shadow-lg shadow-primary/20 active:scale-95 transition-all border-0">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content bg-surface-container-lowest rounded-3xl shadow-2xl border-0 p-xl text-center">
            <div class="modal-body p-0">
                <div class="w-16 h-16 bg-error-container text-error rounded-full flex items-center justify-center mx-auto mb-md">
                    <span class="material-symbols-outlined text-[32px]" data-icon="delete_forever">delete_forever</span>
                </div>
                <h3 class="font-headline-md text-on-surface mb-xs">Are you sure?</h3>
                <p class="font-body-md text-on-surface-variant mb-xl">This action cannot be undone. This task will be permanently removed.</p>
                <div class="grid grid-cols-2 gap-md">
                    <button type="button" class="py-3 text-on-surface-variant font-label-md border border-outline-variant rounded-xl hover:bg-surface-container-low transition-colors bg-transparent" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="py-3 bg-error text-on-error font-label-md rounded-xl hover:shadow-lg hover:shadow-error/20 active:scale-95 transition-all border-0" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-surface-container-lowest rounded-3xl shadow-2xl border-0 text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-24 bg-primary-gradient"></div>
            <div class="modal-body p-xl relative z-10 pt-8">
                <button type="button" class="btn-close btn-close-white absolute top-4 right-4 z-20" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg mx-auto overflow-hidden bg-white mb-3">
                    <img src="<?= htmlspecialchars($photo) ?>?<?= time() ?>" alt="Profile Picture" class="w-full h-full object-cover">
                </div>
                <h3 class="font-headline-md text-on-surface mt-md mb-0"><?= htmlspecialchars($username) ?></h3>
                <p class="font-body-md text-on-surface-variant mb-xl"><?= htmlspecialchars($email) ?></p>
                <div class="space-y-sm">
                    <a href="edit_profile.php" class="w-full flex items-center justify-center gap-sm py-3 border border-primary text-primary rounded-xl font-label-md hover:bg-primary-fixed transition-colors no-underline">
                        <span class="material-symbols-outlined">person_edit</span>
                        Edit Profile
                    </a>
                    <a href="logout.php" class="w-full flex items-center justify-center gap-sm py-3 bg-surface-container-low text-error rounded-xl font-label-md hover:bg-error-container transition-colors no-underline border-0">
                        <span class="material-symbols-outlined">logout</span>
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
        <div class="modal-content bg-surface-container-lowest rounded-3xl shadow-2xl border-0 overflow-hidden">
            <div class="bg-primary-gradient p-md flex justify-between items-center modal-header border-0 rounded-t-3xl">
                <h3 class="font-headline-sm text-on-primary modal-title m-0">Confirm Your Password</h3>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-xl space-y-md">
                <form id="passwordForm">
                    <div>
                        <label class="block font-label-md text-on-surface-variant mb-xs" for="password">Enter Password</label>
                        <input class="w-full px-md py-3 bg-surface-container-low border border-outline-variant rounded-xl focus:border-primary focus:ring-0 outline-none font-body-md" type="password" id="password" name="password" required/>
                    </div>
                    <p id="passwordError" class="text-error mt-2 text-sm font-label-md" style="display: none;">Incorrect password. Please try again.</p>
                    <div class="flex justify-end gap-md pt-md">
                        <button type="button" class="px-xl py-3 text-on-surface-variant font-label-md hover:bg-surface-container-high rounded-xl transition-colors border-0 bg-transparent" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="px-xl py-3 bg-primary-gradient text-on-primary font-label-md rounded-xl shadow-lg shadow-primary/20 active:scale-95 transition-all border-0">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="fixed bottom-0 w-full z-40 bg-secondary-container min-h-[80px] flex items-center justify-center">
    <div class="absolute top-0 left-0 w-full h-12 wave-bg -translate-y-full transform-gpu"></div>
    <div class="flex flex-col md:flex-row justify-between items-center px-xl py-md w-full max-w-container-max text-on-secondary-container">
        <div class="flex items-center gap-sm">
            <span class="font-headline-sm text-on-secondary-container font-bold">TaskDo</span>
            <span class="opacity-80 font-body-sm">© <?= date('Y') ?> TaskDo. All rights reserved.</span>
        </div>
        <div class="flex gap-xl mt-sm md:mt-0">
            <a class="font-body-sm text-on-secondary-container opacity-80 hover:opacity-100 transition-opacity no-underline" href="#">Privacy Policy</a>
            <a class="font-body-sm text-on-secondary-container opacity-80 hover:opacity-100 transition-opacity no-underline" href="#">Terms of Service</a>
            <a class="font-body-sm text-on-secondary-container opacity-80 hover:opacity-100 transition-opacity no-underline" href="#">Help Center</a>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

HTML;

file_put_contents('c:\xampp\htdocs\UTS_LAB_WEBPROG\dashboard.php', $php_header . $new_html . $js_block);
echo "Dashboard generated successfully.\n";
?>
