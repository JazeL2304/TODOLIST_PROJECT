<?php
$file = 'c:\xampp\htdocs\UTS_LAB_WEBPROG\dashboard.php';
$content = file_get_contents($file);

// 1. Fix the newListItem template
$old_template = <<<'HTML'
                    const newListItem = `
                        <li class="list-group-item d-flex justify-content-between align-items-center" id="list-${newList.id}">
                            <div class="d-flex align-items-center">
                                <input type="checkbox" class="form-check-input me-3" data-id="${newList.id}">
                                <span class="list-title" id="title-${newList.id}">${newList.title}</span>
                            </div>
                            <div>
                                <button class="btn-action btn-edit me-2" data-id="${newList.id}" 
                                    data-title="${newList.title}" data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </button>
                                <button class="btn-action btn-delete" data-id="${newList.id}" 
                                    data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash"></i>
                                    <span>Delete</span>
                                </button>
                            </div>
                        </li>
                    `;
HTML;

$new_template = <<<'HTML'
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
HTML;

// Ensure we fix Windows line endings to make str_replace work
$content = str_replace("\r\n", "\n", $content);
$old_template = str_replace("\r\n", "\n", $old_template);
$new_template = str_replace("\r\n", "\n", $new_template);

if (strpos($content, $old_template) !== false) {
    $content = str_replace($old_template, $new_template, $content);
    echo "Successfully replaced the newListItem template inside AJAX.\n";
} else {
    echo "Warning: Could not find the newListItem template to replace.\n";
    // fallback with regex
    $content = preg_replace('/const newListItem = `\s*<li.*?<\/li>\s*`;/s', $new_template, $content);
}

// 2. Remove the erroneous floating block (it causes Uncaught ReferenceError: newList is not defined)
$bad_block_pattern = '/\/\/ Update bagian add new task \(pada bagian success callback\)\s*if \(newList\.success\)\s*\{.*showNotification\(\'Task added successfully!\', \'success\'\);\s*\}/s';

if (preg_match($bad_block_pattern, $content)) {
    $content = preg_replace($bad_block_pattern, '', $content);
    echo "Successfully removed the erroneous floating newList block.\n";
} else {
    echo "Warning: Could not find the erroneous floating newList block to remove.\n";
}

file_put_contents($file, $content);
echo "dashboard.php updated.\n";
?>
