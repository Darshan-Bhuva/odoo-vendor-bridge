<?php

use App\Http\Controllers\Api\V1\AnalyticsController;
use App\Http\Controllers\Api\V1\ApprovalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\SignedUrlController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\PurchaseOrderController;
use App\Http\Controllers\Api\V1\QuotationController;
use App\Http\Controllers\Api\V1\RfqController;

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('staff-register', 'staffRegister');
    Route::post('login', 'login');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('forgot-password-otp-verify', 'forgotPasswordOTPVerify');
    Route::post('reset-password', 'resetPassword');
});



Route::controller(UserController::class)->group(function () {
    Route::get('me', 'me');
    Route::post('me', 'updateProfile');
    Route::post('change-password', 'changePassword');

    // Media route - any authenticated user
    Route::middleware(['auth:api'])->delete('media/{media}', [MediaController::class, 'destroy']);
    // Logout
    Route::middleware(['auth:api'])->post('logout', [AuthController::class, 'logout']);
    // Signed URL - any authenticated user
    Route::middleware(['auth:api'])->post('generate-signed-url', SignedUrlController::class);
    // RFQ routes - procurement or admin
    Route::middleware(['auth:api', 'role:procurement'])->group(function () {
        Route::controller(RfqController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('{id}', 'show');
            Route::post('{id}/close', 'close');
        });
    });
});

Route::controller(NotificationController::class)->group(function () {
    Route::get('notifications', 'index');
    Route::get('notifications/unread-count', 'unreadCount');
    Route::post('notifications/read', 'readAllNotification');
    Route::post('notifications/unread', 'markAsUnread');
    Route::post('onesignal-player-id', 'setOnesignalData');
});


    Route::middleware(['auth:api', 'role:vendor'])->group(function () {
        Route::controller(QuotationController::class)->group(function () {
            // List quotations for current vendor
            Route::get('quotation', 'index');
            // Submit quotation for a specific RFQ (vendor only)
            Route::post('rfq/{rfqId}/quotation', 'store');
            // Compare quotations for an RFQ (procurement/manager/admin)
            Route::get('rfq/{rfqId}/compare-quotation', 'compare');
        });
    });
Route::delete('media/{media}', [MediaController::class, 'destroy']);
Route::post('logout', [AuthController::class, 'logout']);

Route::post('generate-signed-url', SignedUrlController::class);

// Approval routes - manager or admin
Route::middleware(['auth:api', 'role:manager,admin'])->group(function () {
    Route::controller(ApprovalController::class)->group(function () {
        // Approval endpoints
    });
});

// Purchase Order routes - procurement, manager, admin
Route::middleware(['auth:api', 'role:procurement,manager,admin'])->group(function () {
    Route::controller(PurchaseOrderController::class)->group(function () {
        // PO endpoints
    });
});
// Invoice routes - procurement, admin
Route::middleware(['auth:api', 'role:procurement,admin'])->group(function () {
    Route::controller(InvoiceController::class)->group(function () {
        // Invoice endpoints
    });
});
// Analytics routes - manager, admin
Route::middleware(['auth:api', 'role:manager,admin'])->group(function () {
    Route::controller(AnalyticsController::class)->group(function () {
        // Analytics endpoints
    });
});
