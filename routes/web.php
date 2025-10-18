<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MailTestController;

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
        
        // Attendance Management Routes
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('index');
            Route::get('/dashboard', [\App\Http\Controllers\Admin\AttendanceController::class, 'dashboard'])->name('dashboard');
            Route::get('/meeting/{meeting}', [\App\Http\Controllers\Admin\AttendanceController::class, 'meetingReport'])->name('meeting-report');
            Route::get('/course/{course}', [\App\Http\Controllers\Admin\AttendanceController::class, 'courseReport'])->name('course-report');
            Route::get('/user/{user}', [\App\Http\Controllers\Admin\AttendanceController::class, 'userReport'])->name('user-report');
            Route::post('/export', [\App\Http\Controllers\Admin\AttendanceController::class, 'export'])->name('export');
        });
        
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

// Student Attendance Tracking Routes
Route::post('/meetings/student-join', [\App\Http\Controllers\ZoomMeetingController::class, 'studentJoin'])
    ->name('meetings.student-join')
    ->middleware(['auth']);
Route::post('/meetings/student-leave', [\App\Http\Controllers\ZoomMeetingController::class, 'studentLeave'])
    ->name('meetings.student-leave')
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
        Route::post('/courses/{course}/end-meeting', [\App\Http\Controllers\Teacher\DashboardController::class, 'endMeeting'])->name('courses.end-meeting');
        Route::get('/courses/{course}', [\App\Http\Controllers\Teacher\DashboardController::class, 'showCourse'])->name('courses.show');
    });

// Assignment Routes (accessible by both teachers and students with proper authorization)
Route::middleware(['auth'])->group(function () {
    // Teacher Assignment Routes
    Route::post('/assignments', [\App\Http\Controllers\AssignmentController::class, 'store'])->name('assignments.store');
    Route::put('/assignments/{assignment}', [\App\Http\Controllers\AssignmentController::class, 'update'])->name('assignments.update');
    Route::delete('/assignments/{assignment}', [\App\Http\Controllers\AssignmentController::class, 'destroy'])->name('assignments.destroy');
    Route::get('/assignments/{assignment}/download', [\App\Http\Controllers\AssignmentController::class, 'download'])->name('assignments.download');
    Route::get('/assignments/{assignment}/view', [\App\Http\Controllers\AssignmentController::class, 'view'])->name('assignments.view');
    Route::get('/assignments/{assignment}/submissions', [\App\Http\Controllers\AssignmentController::class, 'showSubmissions'])->name('assignments.submissions');
    
    // Student Assignment Submission Routes
    Route::post('/assignments/{assignment}/submit', [\App\Http\Controllers\AssignmentSubmissionController::class, 'store'])->name('assignments.submit');
    Route::delete('/submissions/{submission}', [\App\Http\Controllers\AssignmentSubmissionController::class, 'destroy'])->name('submissions.destroy');
    Route::post('/submissions/{submission}/correct', [\App\Http\Controllers\AssignmentSubmissionController::class, 'correct'])->name('submissions.correct');
    Route::get('/submissions/{type}/{submission}/download', [\App\Http\Controllers\AssignmentSubmissionController::class, 'download'])->name('submissions.download');
    Route::get('/submissions/{type}/{submission}/view', [\App\Http\Controllers\AssignmentSubmissionController::class, 'view'])->name('submissions.view');
    Route::get('/assignments/{assignment}/my-submission', [\App\Http\Controllers\AssignmentSubmissionController::class, 'show'])->name('assignments.my-submission');
    
    // Notification Routes
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/recent', [\App\Http\Controllers\NotificationController::class, 'recent'])->name('notifications.recent');
    Route::post('/notifications/{notification}/mark-as-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::delete('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications/delete-read', [\App\Http\Controllers\NotificationController::class, 'deleteRead'])->name('notifications.delete-read');
});

    // Student Routes
    Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/active-meetings', [\App\Http\Controllers\Student\DashboardController::class, 'getActiveMeetings'])->name('active-meetings');
        Route::get('/courses/{course}/active-meeting-status', [\App\Http\Controllers\Student\DashboardController::class, 'getActiveMeetingStatus'])->name('active-meeting-status');
        Route::get('/courses/{course}/active-meeting', [\App\Http\Controllers\Student\DashboardController::class, 'getActiveMeetingForCourse'])->name('active-meeting');
        Route::get('/courses/{course}', [\App\Http\Controllers\Student\DashboardController::class, 'showCourse'])->name('courses.show');
        Route::post('/meetings/{meeting}/guest-join', [\App\Http\Controllers\ZoomMeetingController::class, 'generateGuestJoinUrl'])->name('guest-join');
        
        // Student Attendance Tracking
        Route::post('/leave-meeting', [\App\Http\Controllers\Student\DashboardController::class, 'leaveActiveMeeting'])->name('leave-meeting');
        
        Route::get('/courses', function () {
            return Inertia::render('Student/Courses/Index');
        })->name('courses.index');
        
        Route::get('/courses/browse', function () {
            return Inertia::render('Student/Courses/Browse');
        })->name('courses.browse');
        
        Route::get('/lessons', function () {
            return Inertia::render('Student/Lessons/Index');
        })->name('lessons.index');
        
        Route::get('/assignments', [\App\Http\Controllers\Student\AssignmentController::class, 'index'])->name('assignments.index');
        
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

// Mail Test Routes
Route::prefix('mail-test')->name('mail-test.')->group(function () {
    Route::post('/send', [MailTestController::class, 'sendTestEmail'])->name('send');
    Route::get('/check-settings', [MailTestController::class, 'testMailgunSettings'])->name('check-settings');
});




