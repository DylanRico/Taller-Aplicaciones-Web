<?php
function sanitize(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function formatMoney(float $amount): string {
    return '$' . number_format($amount, 2, ',', '.');
}

function formatDate(string $datetime): string {
    return date('d/m/Y H:i', strtotime($datetime));
}

function generateInvoiceNumber(): string {
    return 'FAC-' . date('Y') . '-' . str_pad(rand(1, 9999), 6, '0', STR_PAD_LEFT);
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function flashMessage(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}