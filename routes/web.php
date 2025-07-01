<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\ActualiteController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\VisitorTrackerController;
use App\Http\Controllers\ContactController;


Route::get('/', [HomeController::class,'index'])->name('home');
Route::get('/about', [HomeController::class,'about'])->name('about');
Route::get('/services', [HomeController::class,'services'])->name('services');
Route::get('/products', [HomeController::class,'products'])->name('products');
Route::get('/products/{slug}', [HomeController::class,'productShow'])->name('product.show');
Route::get('/portfolio', [HomeController::class,'portfolio'])->name('portfolio');
Route::get('/team-members/listes', [TeamMemberController::class,'getListeDatas'])->name('api.team-members.listes');
Route::get('/partners/listes', [PartnerController::class,'getListeDatas'])->name('api.team-members.listes');
Route::get('/testimonials/listes', [TestimonialController::class,'getListeDatas'])->name('api.team-members.listes');
Route::get('/blog', [HomeController::class, 'blog'])->name('blog');
Route::get('/{id}/blog', [HomeController::class, 'blogShow'])->name('blog.show');

// Contact routes
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Appointment booking routes
Route::get('/appointment', [AppointmentController::class, 'create'])->name('appointment.create');
Route::post('/appointment', [AppointmentController::class, 'store'])->name('appointment.store');
Route::get('/appointment/{appointment}/confirmation', [AppointmentController::class, 'confirmation'])->name('appointment.confirmation');

Route::get('dashboard', [VisitorTrackerController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/about', [AboutController::class, 'edit'])->name('admin.about.edit');
    Route::get('/admin/about/edit', [AboutController::class, 'edit'])->name('admin.about.edit');
    Route::post('/admin/about', [AboutController::class, 'update'])->name('admin.about.update');

    Route::prefix('admin')->group(function () {


        Route::resource('services', ServiceController::class)
            ->except(['show'])
            ->names([
                'index' => 'admin.services.index',
                'create' => 'admin.services.create',
                'store' => 'admin.services.store',
                'edit' => 'admin.services.edit',
                'update' => 'admin.services.update',
                'destroy' => 'admin.services.destroy',
            ]);
        Route::resource('products', ProductController::class)
            ->except(['show'])
            ->names([
                'index' => 'admin.products.index',
                'create' => 'admin.products.create',
                'store' => 'admin.products.store',
                'edit' => 'admin.products.edit',
                'update' => 'admin.products.update',
                'destroy' => 'admin.products.destroy',
            ]);
        Route::resource('team-members', TeamMemberController::class)
            ->except(['show'])
            ->names([
                'index' => 'admin.team-members.index',
                'create' => 'admin.team-members.create',
                'store' => 'admin.team-members.store',
                'edit' => 'admin.team-members.edit',
                'update' => 'admin.team-members.update',
                'destroy' => 'admin.team-members.destroy',
            ]);

        Route::resource('partners', PartnerController::class)
            ->only(['index','store','update','destroy'])->names([
                'index' => 'admin.partners.index',

                'store' => 'admin.partners.store',

                'update' => 'admin.partners.update',
                'destroy' => 'admin.partners.destroy',
            ]);

        Route::resource('testimonials', TestimonialController::class)
            ->except(['show'])
            ->names([
                'index' => 'admin.testimonials.index',
                'create' => 'admin.testimonials.create',
                'store' => 'admin.testimonials.store',
                'edit' => 'admin.testimonials.edit',
                'update' => 'admin.testimonials.update',
                'destroy' => 'admin.testimonials.destroy',
            ]);

        Route::resource('actualites', ActualiteController::class)
            ->except(['show'])
            ->names([
                'index' => 'admin.actualites.index',
                'create' => 'admin.actualites.create',
                'store' => 'admin.actualites.store',
                'edit' => 'admin.actualites.edit',
                'update' => 'admin.actualites.update',
                'destroy' => 'admin.actualites.destroy',
            ]);

        Route::prefix('albums')->group(function () {
            Route::get('/', [AlbumController::class, 'index'])->name('admin.albums.index');
            Route::get('/create', [AlbumController::class, 'create'])->name('admin.albums.create');
            Route::post('/', [AlbumController::class, 'store'])->name('admin.albums.store');
            Route::get('/{album}/edit', [AlbumController::class, 'edit'])->name('admin.albums.edit');
            Route::post('/{album}', [AlbumController::class, 'update'])->name('admin.albums.update'); // En POST au lieu de PUT/PATCH
            Route::delete('/{album}', [AlbumController::class, 'destroy'])->name('admin.albums.destroy');
        });

        Route::get('albums/{album}',[AlbumController::class,'show'])->name('admin.albums.show');

        // Banner management routes
        Route::get('banner', [BannerController::class, 'index'])->name('admin.banner.index');
        Route::post('banner/update', [BannerController::class, 'update'])->name('admin.banner.update');

        // Settings management routes
        Route::resource('settings', SettingController::class)
            ->except(['show'])
            ->names([
                'index' => 'admin.settings.index',
                'create' => 'admin.settings.create',
                'store' => 'admin.settings.store',
                'edit' => 'admin.settings.edit',
                'update' => 'admin.settings.update',
                'destroy' => 'admin.settings.destroy',
            ]);

        // Contact settings
        Route::get('contact-settings', [SettingController::class, 'contactSettings'])->name('admin.contact-settings');
        Route::post('contact-settings', [SettingController::class, 'updateContactSettings'])->name('admin.contact-settings.update');

        // Color settings
        Route::get('color-settings', [SettingController::class, 'colorSettings'])->name('admin.color-settings');
        Route::post('color-settings', [SettingController::class, 'updateColorSettings'])->name('admin.color-settings.update');

        // Schedules management routes
        Route::resource('schedules', ScheduleController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->names([
                'index' => 'admin.schedules.index',
                'store' => 'admin.schedules.store',
                'update' => 'admin.schedules.update',
                'destroy' => 'admin.schedules.destroy',
            ]);
        Route::post('/schedules/{schedule}/duplicate', [ScheduleController::class, 'duplicate'])->name('admin.schedules.duplicate');
        Route::get('/schedules/listes', [ScheduleController::class, 'getListeDatas'])->name('api.schedules.listes');

        // Appointments management routes
        Route::resource('appointments', AppointmentController::class)
            ->only(['index', 'show', 'update', 'destroy'])
            ->names([
                'index' => 'admin.appointments.index',
                'show' => 'admin.appointments.show',
                'update' => 'admin.appointments.update',
                'destroy' => 'admin.appointments.destroy',
            ]);
        Route::post('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('admin.appointments.confirm');

        // Contacts management routes
        Route::get('contacts', [ContactController::class, 'adminIndex'])->name('admin.contacts.index');
        Route::get('contacts/{contact}', [ContactController::class, 'show'])->name('admin.contacts.show');
        Route::delete('contacts/{contact}', [ContactController::class, 'destroy'])->name('admin.contacts.destroy');

        // Newsletter subscribers management routes
        Route::get('newsletter', [App\Http\Controllers\NewsletterController::class, 'index'])->name('admin.newsletter.index');
        Route::get('newsletter/export', [App\Http\Controllers\NewsletterController::class, 'export'])->name('admin.newsletter.export');

        // Users management routes
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)
            ->except(['show'])
            ->names([
                'index' => 'admin.users.index',
                'create' => 'admin.users.create',
                'store' => 'admin.users.store',
                'edit' => 'admin.users.edit',
                'update' => 'admin.users.update',
                'destroy' => 'admin.users.destroy',
            ]);
    });

    // API routes for authenticated users
    Route::get('/api/banner-photos', [BannerController::class, 'getBannerPhotos'])->name('api.banner-photos');
});

// Public API routes
Route::get('/api/albums/latest', [AlbumController::class, 'latest'])->name('api.albums.latest');
Route::get('/api/services', [ServiceController::class, 'getServices'])->name('api.services');
Route::get('/api/products', [ProductController::class, 'getProducts'])->name('api.products');

// Dynamic CSS route
Route::get('/css/colors.css', [App\Http\Controllers\ColorController::class, 'css'])->name('css.colors');
Route::get('/api/schedules/available', [App\Http\Controllers\Admin\ScheduleController::class, 'getAvailableSchedules'])->name('api.schedules.available');
Route::get('/api/actualites/latest', [ActualiteController::class, 'latest'])->name('api.actualites.latest');
Route::post('/api/newsletter/subscribe', [App\Http\Controllers\NewsletterController::class, 'store'])->name('api.newsletter.subscribe');
Route::post('/api/cookie-consent', [App\Http\Controllers\CookieConsentController::class, 'store'])->name('api.cookie-consent.store');

// Visitor tracking route
Route::post('/api/track-action', [App\Http\Controllers\VisitorTrackerController::class, 'trackAction'])->name('api.track-action');
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
