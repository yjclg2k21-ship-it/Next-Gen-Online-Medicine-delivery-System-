<?php
/**
 * Mediflow API Routes — 100% Logic Parity Mapping
 */

$router->add('OPTIONS', '/.*', 'BaseController@options'); // For CORS preflight

// --- AUTH ---
$router->add('POST', '/auth/login', 'AuthController@login');
$router->add('POST', '/auth/register', 'AuthController@register');
$router->add('GET', '/auth/me', 'AuthController@me');
$router->add('POST', '/auth/logout', 'AuthController@logout');
$router->add('POST', '/auth/forgot-password', 'AuthController@forgotPassword');
$router->add('POST', '/auth/reset-password', 'AuthController@resetPassword');

// --- USER & PROFILE ---
$router->add('GET', '/user/profile', 'UserController@profile');
$router->add('PUT', '/user/profile', 'UserController@updateProfile');
$router->add('POST', '/user/profile/update', 'UserController@updateProfile');
$router->add('POST', '/user/password', 'UserController@changePassword');
$router->add('POST', '/user/deactivate', 'UserController@deactivateAccount');
$router->add('DELETE', '/user/account', 'UserController@deleteAccount');
$router->add('GET', '/user/addresses', 'UserController@addresses');
$router->add('POST', '/user/addresses', 'UserController@addAddress');
$router->add('PUT', '/user/addresses/{id}', 'UserController@updateAddress');
$router->add('PUT', '/user/addresses/{id}/default', 'UserController@setDefaultAddress');
$router->add('DELETE', '/user/addresses/{id}', 'UserController@deleteAddress');
$router->add('GET', '/user/wallet', 'UserController@walletBalance');
$router->add('GET', '/user/dashboard', 'UserController@dashboard');
$router->add('GET', '/user/wishlist', 'UserController@wishlist');
$router->add('POST', '/user/wishlist/toggle', 'UserController@toggleWishlist');
$router->add('GET', '/user/notifications', 'NotificationController@index');
$router->add('GET', '/notifications', 'NotificationController@index'); // Frontend Alias
$router->add('POST', '/notifications/mark-all-read', 'NotificationController@markAllRead');
$router->add('POST', '/notifications/{id}/read', 'NotificationController@markRead');
$router->add('POST', '/notifications/{id}/unread', 'NotificationController@markUnread');

$router->add('GET', '/user/payments', 'UserController@payments');
$router->add('GET', '/admin/reports/inventory', 'PharmacyInventoryController@expiryReport');
$router->add('GET', '/admin/audit/export', 'AuditController@export');
$router->add('GET', '/admin/logs/system', 'AdminController@activityLogs');
$router->add('GET', '/admin/users/list', 'AdminController@usersList');
$router->add('GET', '/admin/agents', 'AdminController@agents');
$router->add('GET', '/banners', 'BannerController@index');
$router->add('POST', '/banners', 'BannerController@create');
$router->add('POST', '/banners/{id}/toggle', 'BannerController@toggle');
$router->add('POST', '/banners/{id}/delete', 'BannerController@delete');
$router->add('POST', '/admin/brands', 'BrandController@create');
$router->add('GET', '/vendor/reports/inventory', 'PharmacyInventoryController@expiryReport');
$router->add('GET', '/delivery/ratings', 'DeliveryController@ratings'); // Added missing ratings route
$router->add('GET', '/delivery/partner/settings', 'DeliveryController@getSettings'); // Added missing settings GET route
$router->add('PUT', '/delivery/partner/settings', 'DeliveryController@updateSettings');
$router->add('POST', '/payments/initiate', 'PaymentController@initiate');
$router->add('POST', '/payments/webhook', 'PaymentController@webhook');
$router->add('POST', '/wallet/recharge', 'PaymentController@recharge');
$router->add('GET', '/user/recommendations', 'UserController@recommendations');
$router->add('GET', '/user/returns', 'ReturnController@index');
$router->add('POST', '/returns/status/{id}', 'ReturnController@updateStatus');

// --- MEDICINE / SEARCH ---
$router->add('GET', '/medicines', 'MedicineController@index');
$router->add('GET', '/medicines/{id}', 'MedicineController@show');
$router->add('GET', '/medicines/{id}/substitutes', 'MedicineController@getSubstitutes');
$router->add('GET', '/medicines/search', 'SearchController@search');
$router->add('GET', '/search', 'SearchController@search');
$router->add('GET', '/search/suggestions', 'SearchController@suggestions');
$router->add('GET', '/categories', 'MedicineController@categories');

// --- PUBLIC ROUTES ---

// --- AI & COMPARISON ---
$router->add('GET', '/ai/compare', 'AiController@compare');

// --- BRANDS ---
$router->add('GET', '/brands', 'BrandController@index');
$router->add('POST', '/brands', 'BrandController@create');
$router->add('PUT', '/brands/{id}', 'BrandController@update');
$router->add('DELETE', '/brands/{id}', 'BrandController@delete');

// --- CART ---
$router->add('GET', '/cart', 'CartController@index');
$router->add('POST', '/cart/add', 'CartController@addItem');
$router->add('PUT', '/cart/{id}', 'CartController@updateItem');
$router->add('DELETE', '/cart/{id}', 'CartController@removeItem');
$router->add('POST', '/cart/clear', 'CartController@clear');

// --- ORDERS ---
$router->add('GET', '/orders', 'OrderController@index');
$router->add('POST', '/orders', 'OrderController@store');
$router->add('GET', '/orders/{id}/tracking', 'OrderController@tracking');
$router->add('GET', '/orders/{id}/emergency-tracking', 'OrderController@emergencyTracking');
$router->add('POST', '/orders/{id}/cancel', 'OrderController@cancel');
$router->add('GET', '/orders/{id}/invoice', 'OrderController@getInvoice');
$router->add('POST', '/orders/{id}/return', 'ReturnController@requestReturn');

// --- REVIEWS ---
$router->add('GET', '/medicines/{medicineId}/reviews', 'ReviewController@index');
$router->add('POST', '/medicines/{medicineId}/reviews', 'ReviewController@create');


// --- KYC ---
$router->add('GET', '/kyc/status/{id}', 'KycController@status');
$router->add('POST', '/kyc/submit', 'KycController@submit');
$router->add('POST', '/kyc/{id}/verify', 'KycController@verify');
$router->add('POST', '/kyc/{id}/reject', 'KycController@reject');
$router->add('GET', '/kyc/file/{filename}', 'KycController@serveFile');

// --- VENDOR ---
$router->add('GET', '/vendor/dashboard', 'VendorController@dashboard');
$router->add('GET', '/vendor/medicines', 'VendorController@listMedicines');
$router->add('POST', '/vendor/medicines', 'VendorController@storeMedicine');
$router->add('PUT', '/vendor/medicines/{id}', 'VendorController@updateMedicine');
$router->add('DELETE', '/vendor/medicines/{id}', 'VendorController@deleteMedicine');
$router->add('GET', '/vendor/orders', 'VendorController@orders');
$router->add('GET', '/vendor/orders/{id}', 'VendorController@orderDetail');
$router->add('PUT', '/vendor/orders/{id}/status', 'VendorController@updateOrderStatus');
$router->add('GET', '/vendor/prescriptions/pending', 'VendorController@prescriptionReview');
$router->add('POST', '/vendor/prescriptions/{id}/review', 'VendorController@reviewPrescription');
$router->add('POST', '/vendor/payout/request', 'VendorController@requestPayout');
$router->add('GET', '/vendor/analytics/delivery-mix', 'VendorController@deliveryMix');
$router->add('GET', '/vendor/analytics/reports', 'VendorController@analyticalReports');
$router->add('GET', '/vendor/insights/summary', 'VendorController@analyticalReports');
$router->add('GET', '/vendor/inventory-reports', 'VendorController@inventoryReports');
$router->add('GET', '/vendor/catalog', 'VendorController@catalogConfig');
$router->add('POST', '/vendor/catalog/toggle/{id}', 'VendorController@toggleVisibility');
$router->add('GET', '/vendor/taxonomy', 'VendorController@taxonomy');
$router->add('GET', '/vendor/profile', 'VendorController@profile');
$router->add('POST', '/vendor/profile/update', 'VendorController@updateProfile');
$router->add('GET', '/vendor/earnings', 'VendorController@earnings');


// --- VENDOR INVENTORY ---
$router->add('GET', '/vendor/inventory', 'PharmacyInventoryController@index');
$router->add('GET', '/pharmacy/inventory', 'PharmacyInventoryController@index');
$router->add('POST', '/pharmacy/inventory/update/{id}', 'PharmacyInventoryController@update');

// --- VENDOR POS ---
$router->add('POST', '/vendor/pos/sale', 'PosController@createSale');
$router->add('GET', '/vendor/pos/history', 'PosController@getSales');

// --- ADMIN ---
$router->add('GET', '/admin/dashboard', 'AdminController@dashboard');
$router->add('POST', '/admin/medicine/{id}/approve', 'AdminController@approveMedicine');
$router->add('GET', '/admin/reports/revenue', 'AdminController@reports');
$router->add('GET', '/admin/pulse', 'AdminController@pulse');
$router->add('POST', '/admin/pulse/trigger', 'AdminController@triggerPulse');
$router->add('GET', '/admin/logs/activity', 'AuditController@activityLogs');
$router->add('GET', '/admin/logs/system', 'AuditController@systemLogs');
$router->add('GET', '/admin/users', 'AdminController@usersList');
$router->add('GET', '/admin/users/{id}', 'AdminController@userDetails');
$router->add('POST', '/admin/users/create', 'AdminController@createUser');
$router->add('POST', '/admin/users/{id}/block', 'AdminController@blockUser');
$router->add('POST', '/admin/users/{id}/unblock', 'AdminController@unblockUser');
$router->add('GET', '/admin/vendors', 'AdminController@vendors');
$router->add('PUT', '/admin/vendors/{id}', 'AdminController@updateVendor');
$router->add('POST', '/admin/vendors/{id}/approve', 'AdminController@approveVendor');
$router->add('POST', '/admin/vendors/{id}/suspend', 'AdminController@suspendVendor');
$router->add('GET', '/admin/financials', 'AdminController@financials');
$router->add('GET', '/admin/order/pulse', 'AdminController@orderPulse');
$router->add('GET', '/admin/kyc/status/{id}', 'AdminController@kycStatus');
$router->add('GET', '/admin/kyc/pending', 'AdminController@pendingKYC');
$router->add('GET', '/admin/kyc/user/{id}', 'AdminController@userKYC');
$router->add('POST', '/admin/kyc/{id}/status', 'AdminController@updateKYCStatus');
$router->add('GET', '/admin/approval-stats', 'AdminController@approvalStats');
$router->add('GET', '/admin/medicine/pending', 'AdminController@pendingMedicines');
$router->add('GET', '/admin/medicines', 'AdminController@medicines');
$router->add('POST', '/admin/orders/{id}/assign', 'AdminController@assignOrder');
$router->add('GET', '/admin/categories', 'AdminController@categories');
$router->add('POST', '/admin/categories', 'AdminController@createCategory');
$router->add('DELETE', '/admin/categories/{id}', 'AdminController@deleteCategory');
$router->add('POST', '/admin/categories/{id}/sub', 'AdminController@addSubCategory');
$router->add('DELETE', '/admin/categories/{id}/sub/{name}', 'AdminController@deleteSubCategory');

// --- BULK OPERATIONS ---
$router->add('POST', '/admin/bulk/upload', 'BulkController@upload');
$router->add('GET', '/admin/bulk/status/all', 'BulkController@jobs');
$router->add('GET', '/admin/bulk/export/{type}', 'BulkController@export');

// --- BACKUPS ---
$router->add('GET', '/admin/backups', 'BackupController@index');
$router->add('POST', '/admin/backups', 'BackupController@create');
$router->add('POST', '/admin/backups/{id}/restore', 'BackupController@restore');
$router->add('GET', '/admin/backups/{id}/download', 'BackupController@download');

// --- PRESCRIPTIONS ---
$router->add('GET', '/prescriptions', 'PrescriptionController@index');
$router->add('POST', '/prescriptions/upload', 'PrescriptionController@upload');
$router->add('GET', '/prescriptions/{id}/view', 'PrescriptionController@view');
$router->add('POST', '/prescriptions/{id}/approve', 'PrescriptionController@approve');
$router->add('POST', '/prescriptions/{id}/reject', 'PrescriptionController@reject');
$router->add('GET', '/prescriptions/{id}/status', 'PrescriptionController@checkStatus');
$router->add('POST', '/prescriptions/{id}/link-medicines', 'PrescriptionController@linkMedicines');
$router->add('POST', '/prescriptions/{id}/use', 'PrescriptionController@recordUsage');
$router->add('POST', '/prescriptions/{id}/add-to-cart', 'PrescriptionController@addToCart');

// --- SUPPORT TICKETS ---
$router->add('GET', '/admin/tickets', 'SupportTicketController@index');
$router->add('POST', '/admin/tickets', 'SupportTicketController@create');
$router->add('GET', '/admin/tickets/{id}', 'SupportTicketController@show');
$router->add('POST', '/admin/tickets/{id}/reply', 'SupportTicketController@reply');

// --- NEWSLETTER ---
$router->add('POST', '/newsletter/subscribe', 'NewsletterController@subscribe');

// --- CLINICAL ANALYTICS (Section 12 - LIVE) ---
$router->add('GET', '/clinical/metrics', 'ClinicalController@getMetrics');
$router->add('POST', '/clinical/metrics', 'ClinicalController@logMetric');
$router->add('GET', '/clinical/insights', 'ClinicalController@getInsights');

// --- AI SAFETY & CLINICAL ALERTS (Section 13 - LIVE) ---
$router->add('GET', '/ai/substitutes', 'AiController@findSubstitutes');
$router->add('POST', '/ai/check-interactions', 'AiController@checkInteractions');
$router->add('POST', '/ai/analyze-prescription', 'AiController@analyzePrescription');
$router->add('GET', '/ai/alternatives', 'AiController@findGenericAlternatives');

// --- USER REFILLS ---
$router->add('GET', '/user/refills', 'RefillController@index');
$router->add('POST', '/user/refills', 'RefillController@store');

// --- PHASE 2 UNIFICATION ROUTES ---
$router->add('GET', '/admin/roles/permissions', 'AdminController@getPermissions');
$router->add('POST', '/admin/roles/assign', 'AdminController@savePermissions');

$router->add('GET', '/admin/settings', 'SettingController@index');
$router->add('POST', '/admin/settings/update', 'SettingController@update');
$router->add('GET', '/settings/bad-weather', 'SettingController@getBadWeather');

$router->add('GET', '/vendor/commissions', 'PayoutController@getVendorCommissions');
$router->add('GET', '/admin/payouts/pending', 'PayoutController@getPendingPayouts');
$router->add('POST', '/admin/payouts/{id}/approve', 'PayoutController@approvePayout');
$router->add('POST', '/admin/payouts/{id}/reject', 'PayoutController@rejectPayout');
$router->add('GET', '/admin/payouts/delivery', 'PayoutController@getDeliveryPayouts');
$router->add('POST', '/admin/payouts/delivery/{id}/approve', 'PayoutController@approveDeliveryPayout');
$router->add('POST', '/admin/payouts/delivery/{id}/reject', 'PayoutController@rejectDeliveryPayout');

$router->add('GET', '/vendor/returns', 'ReturnController@vendorIndex');
$router->add('POST', '/vendor/returns/{id}/status', 'ReturnController@updateStatus');
$router->add('GET', '/admin/roles', 'RoleController@getRoles');
$router->add('GET', '/admin/permissions', 'RoleController@getPermissions');

// --- PHASE 3 FINAL MODULES ---
$router->add('GET', '/admin/audit', 'AuditController@activityLogs');
$router->add('GET', '/admin/audit/{id}', 'AuditController@auditDetail');

$router->add('GET', '/admin/banners', 'BannerController@index');
$router->add('POST', '/admin/banners', 'BannerController@create');
$router->add('POST', '/admin/banners/{id}/toggle', 'BannerController@toggle');

$router->add('POST', '/admin/bulk/upload', 'BulkOperationController@uploadCsv');
$router->add('GET', '/admin/bulk/status/{id}', 'BulkOperationController@checkStatus');

$router->add('GET', '/admin/reports/financial', 'ReportController@financial');
$router->add('GET', '/admin/reports/inventory', 'ReportController@inventory');
$router->add('GET', '/admin/reports/analytical', 'ReportController@analytical');

// --- WORKERS ---
$router->add('POST', '/worker/sla-check', 'WorkerController@checkSLA');
$router->add('POST', '/worker/expiry-check', 'WorkerController@runDailyTasks');
$router->add('POST', '/worker/weekly-payout', 'WorkerController@weeklyPayout');

// --- DELIVERY ---
$router->add('GET', '/delivery/queue', 'DeliveryController@dispatchQueue'); // Frontend alias
$router->add('GET', '/delivery/history', 'DeliveryController@history');
$router->add('GET', '/delivery/dispatch/queue', 'DeliveryController@dispatchQueue');
$router->add('POST', '/admin/dispatch/auto-assign', 'DeliveryController@autoAssign');
$router->add('GET', '/delivery/partner/settings', 'DeliveryController@getSettings');
$router->add('GET', '/delivery/orders', 'DeliveryController@orders');
$router->add('GET', '/delivery/orders/{id}', 'DeliveryController@orderDetail');
$router->add('POST', '/delivery/orders/{id}/accept', 'DeliveryController@acceptOrder');
$router->add('POST', '/delivery/orders/{id}/reject', 'DeliveryController@rejectOrder');
$router->add('POST', '/delivery/orders/{id}/status', 'DeliveryController@updateStatus');
$router->add('POST', '/delivery/orders/{id}/proof', 'DeliveryController@uploadProof');
$router->add('POST', '/delivery/duty/toggle', 'DeliveryController@toggleDuty');
$router->add('POST', '/delivery/location', 'DeliveryController@updateLocation');
$router->add('POST', '/delivery/payout/request', 'DeliveryController@requestPayout');
$router->add('GET', '/delivery/earnings', 'DeliveryController@earnings');
$router->add('POST', '/admin/returns/{id}/assign', 'ReturnController@assignRider');
$router->add('PUT', '/delivery/partner/settings', 'DeliveryController@updateSettings');

// --- ADMIN EMERGENCY PRIORITY ENDPOINTS ---
$router->add('GET', '/admin/emergency-orders', 'AdminController@emergencyOrders');
$router->add('POST', '/admin/emergency-orders/{id}/reassign', 'AdminController@manualReassign');

// --- ADMIN FLEET TRACKING ---
$router->add('GET', '/admin/fleet/live', 'AdminController@fleetLive');

// --- CRON EMERGENCY SLA MONITORING ---
$router->add('POST', '/cron/emergency-sla-check', 'AdminController@emergencySlaCheck');

// --- VENDOR EMERGENCY PRIORITY ENDPOINTS ---
$router->add('GET', '/vendor/emergency-orders', 'OrderController@vendorEmergencyOrders');
$router->add('POST', '/orders/{id}/acknowledge-emergency', 'OrderController@acknowledgeEmergency');
$router->add('POST', '/orders/{id}/ready-for-pickup', 'OrderController@markReadyForPickup');

// --- DELIVERY EMERGENCY PRIORITY ENDPOINTS ---
$router->add('GET', '/delivery/priority-queue', 'DeliveryController@priorityQueue');
$router->add('POST', '/delivery/emergency/{id}/accept', 'DeliveryController@acceptEmergency');
$router->add('POST', '/delivery/emergency/{id}/pickup', 'DeliveryController@pickupEmergency');
$router->add('POST', '/delivery/emergency/{id}/deliver', 'DeliveryController@deliverEmergency');
