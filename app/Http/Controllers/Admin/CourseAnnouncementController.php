<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseAnnouncement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CourseAnnouncementController extends Controller
{
    public function index()
    {
        $courses = Course::select('id', 'title', 'title_ar')->orderBy('title_ar')->get();

        $announcements = CourseAnnouncement::with('course:id,title,title_ar')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (CourseAnnouncement $announcement) {
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'message' => $announcement->message,
                    'image_url' => $announcement->image_path ? route('announcements.image', $announcement->id) : null,
                    'starts_at' => $announcement->starts_at?->format('Y-m-d H:i:s'),
                    'ends_at' => $announcement->ends_at?->format('Y-m-d H:i:s'),
                    'is_active' => $announcement->is_active,
                    'course' => [
                        'id' => $announcement->course->id,
                        'title' => $announcement->course->title,
                        'title_ar' => $announcement->course->title_ar,
                    ],
                ];
            });

        return Inertia::render('Admin/Settings/GeneralMessages', [
            'courses' => $courses,
            'announcements' => $announcements,
            'locale' => app()->getLocale(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('course-announcements', 'public');
        }

        CourseAnnouncement::create([
            'course_id' => $validated['course_id'],
            'title' => $validated['title'],
            'message' => $validated['message'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'image_path' => $imagePath,
            'is_active' => true,
        ]);

        return redirect()->route('admin.settings.general-messages.index')
            ->with('success', 'تم حفظ الرسالة بنجاح');
    }

    public function destroy(CourseAnnouncement $announcement)
    {
        if ($announcement->image_path && Storage::disk('public')->exists($announcement->image_path)) {
            Storage::disk('public')->delete($announcement->image_path);
        }

        $announcement->delete();

        return redirect()->route('admin.settings.general-messages.index')
            ->with('success', 'تم حذف الرسالة بنجاح');
    }

    public function image(CourseAnnouncement $announcement): BinaryFileResponse
    {
        abort_unless($announcement->image_path, 404);
        abort_unless(Storage::disk('public')->exists($announcement->image_path), 404);

        return response()->file(Storage::disk('public')->path($announcement->image_path));
    }
}
