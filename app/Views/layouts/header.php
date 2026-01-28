<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Task Manager' ?></title>
    
    <!-- Bootstrap 5 RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">

    <!-- Biometric Helpers -->
    <script src="<?= BASE_URL ?>js/biometrics.js"></script>
</head>
<body>

<div class="container-custom py-0">
    <!-- Global Notifications -->
    <?php if (\App\Helpers\Session::has('flash_success')): ?>
        <div class="alert-custom alert-success mt-3 mb-0">
            <i class="bi bi-check-circle"></i> <?= \App\Helpers\Session::getFlash('success') ?>
        </div>
    <?php endif; ?>

    <?php if (\App\Helpers\Session::has('flash_error')): ?>
        <div class="alert-custom alert-danger mt-3 mb-0">
            <i class="bi bi-exclamation-circle"></i> <?= \App\Helpers\Session::getFlash('error') ?>
        </div>
    <?php endif; ?>
</div>

