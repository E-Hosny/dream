<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;

Route::get("/", function () {
    return redirect()->route('login');
});



Route::get("/language/{locale}", [LanguageController::class, "change"])
    ->name("language.change")
    ->where("locale", "[a-z]{2}");

Route::middleware(['auth', 'role.redirect'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Users management
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
        
        // Teachers management  
        Route::get('/teachers', [App\Http\Controllers\Admin\UserController::class, 'teachers'])->name('teachers.index');
        
        // Students management
        Route::get('/students', [App\Http\Controllers\Admin\UserController::class, 'students'])->name('students.index');
        
        // Courses management
        Route::resource('courses', App\Http\Controllers\Admin\CourseController::class);
        
        // Enrollments management
        Route::resource('enrollments', App\Http\Controllers\Admin\EnrollmentController::class);
        Route::post('/enrollments/bulk', [App\Http\Controllers\Admin\EnrollmentController::class, 'bulkEnroll'])->name('enrollments.bulk');
        
        Route::get('/reports', function () {
            return Inertia::render('Admin/Reports/Index');
        })->name('reports.index');
        
        Route::get('/settings', function () {
            return Inertia::render('Admin/Settings/Index');
        })->name('settings.index');

        // Zoom Meetings Management
        Route::resource('zoom-meetings', \App\Http\Controllers\ZoomMeetingController::class);
        Route::post('/zoom-meetings/{meeting}/start', [\App\Http\Controllers\ZoomMeetingController::class, 'start'])->name('zoom-meetings.start');
        Route::post('/zoom-meetings/{meeting}/end', [\App\Http\Controllers\ZoomMeetingController::class, 'end'])->name('zoom-meetings.end');
        Route::post('/zoom-meetings/start-instant', [\App\Http\Controllers\ZoomMeetingController::class, 'startInstantMeeting'])->name('zoom-meetings.start-instant');
        Route::get('/zoom-meetings/upcoming', [\App\Http\Controllers\ZoomMeetingController::class, 'upcoming'])->name('zoom-meetings.upcoming');
        
        // Zoom Accounts Management
        Route::resource('zoom-accounts', \App\Http\Controllers\Admin\ZoomAccountController::class);
        Route::post('/zoom-accounts/{account}/toggle-status', [\App\Http\Controllers\Admin\ZoomAccountController::class, 'toggleStatus'])->name('zoom-accounts.toggle-status');
        Route::get('/zoom-accounts/available', [\App\Http\Controllers\Admin\ZoomAccountController::class, 'available'])->name('zoom-accounts.available');
        
        // Teacher Zoom Account Linking
        Route::post('/teachers/{teacher}/link-zoom-account', [\App\Http\Controllers\Admin\UserController::class, 'linkZoomAccount'])->name('teachers.link-zoom-account');
        Route::post('/teachers/{teacher}/unlink-zoom-account', [\App\Http\Controllers\Admin\UserController::class, 'unlinkZoomAccount'])->name('teachers.unlink-zoom-account');
        
            // API Routes for Zoom
    Route::get('/api/courses/{course}/schedules', function ($course) {
        return \App\Models\CourseSchedule::where('course_id', $course)
            ->where('is_active', true)
            ->get();
    });
});

// General Zoom Meeting Routes (accessible from anywhere)
Route::post('/zoom-meetings/start-instant', [\App\Http\Controllers\ZoomMeetingController::class, 'startInstantMeeting'])
    ->name('zoom-meetings.start-instant')
    ->middleware(['auth']);

    // Teacher Routes
    Route::prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/courses', function () {
            return Inertia::render('Teacher/Courses/Index');
        })->name('courses.index');
        
        Route::get('/lessons', function () {
            return Inertia::render('Teacher/Lessons/Index');
        })->name('lessons.index');
        
        Route::get('/assessments', function () {
            return Inertia::render('Teacher/Assessments/Index');
        })->name('assessments.index');
        
        Route::get('/students', function () {
            return Inertia::render('Teacher/Students/Index');
        })->name('students.index');
        
        Route::get('/grades', function () {
            return Inertia::render('Teacher/Grades/Index');
        })->name('grades.index');
        
        Route::get('/reports', function () {
            return Inertia::render('Teacher/Reports/Index');
        })->name('reports.index');
        
        Route::get('/calendar', function () {
            return Inertia::render('Teacher/Calendar/Index');
        })->name('calendar.index');
        
        // Zoom Meeting Routes for Teachers
        Route::post('/zoom-meetings/start-instant', [\App\Http\Controllers\ZoomMeetingController::class, 'startInstantMeeting'])->name('zoom-meetings.start-instant');
    });

    // Student Routes
    Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/active-meetings', [\App\Http\Controllers\Student\DashboardController::class, 'getActiveMeetings'])->name('active-meetings');
        Route::post('/meetings/{meeting}/guest-join', [\App\Http\Controllers\ZoomMeetingController::class, 'generateGuestJoinUrl'])->name('guest-join');
        
        Route::get('/courses', function () {
            return Inertia::render('Student/Courses/Index');
        })->name('courses.index');
        
        Route::get('/courses/browse', function () {
            return Inertia::render('Student/Courses/Browse');
        })->name('courses.browse');
        
        Route::get('/lessons', function () {
            return Inertia::render('Student/Lessons/Index');
        })->name('lessons.index');
        
        Route::get('/assignments', function () {
            return Inertia::render('Student/Assignments/Index');
        })->name('assignments.index');
        
        Route::get('/schedule', function () {
            return Inertia::render('Student/Schedule/Index');
        })->name('schedule.index');
        
        Route::get('/grades', function () {
            return Inertia::render('Student/Grades/Index');
        })->name('grades.index');
        
        Route::get('/progress', function () {
            return Inertia::render('Student/Progress/Index');
        })->name('progress.index');
        
        Route::get('/certificates', function () {
            return Inertia::render('Student/Certificates/Index');
        })->name('certificates.index');
        
        Route::get('/library', function () {
            return Inertia::render('Student/Library/Index');
        })->name('library.index');
        
        Route::get('/help', function () {
            return Inertia::render('Student/Help/Index');
        })->name('help.index');
    });
});

require __DIR__."/auth.php";

// Zoom Meeting Routes
Route::prefix('zoom-meetings')->name('zoom-meetings.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\ZoomMeetingController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\ZoomMeetingController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\ZoomMeetingController::class, 'store'])->name('store');
    Route::get('/{meeting}', [\App\Http\Controllers\ZoomMeetingController::class, 'show'])->name('show');
    Route::get('/{meeting}/edit', [\App\Http\Controllers\ZoomMeetingController::class, 'edit'])->name('edit');
    Route::put('/{meeting}', [\App\Http\Controllers\ZoomMeetingController::class, 'update'])->name('update');
    Route::delete('/{meeting}', [\App\Http\Controllers\ZoomMeetingController::class, 'destroy'])->name('destroy');
    
    // بدء وإنهاء الاجتماعات
    Route::post('/{meeting}/start', [\App\Http\Controllers\ZoomMeetingController::class, 'start'])->name('start');
    Route::post('/{meeting}/end', [\App\Http\Controllers\ZoomMeetingController::class, 'end'])->name('end');
    
    // اجتماع فوري
    Route::post('/start-instant', [\App\Http\Controllers\ZoomMeetingController::class, 'startInstantMeeting'])->name('start-instant');
});




