<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "name",
        "email",
        "password",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        "password",
        "remember_token",
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
        ];
    }

    // العلاقات - الكورسات التي يدرسها المعلم
    public function teachingCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    // العلاقات - الكورسات المسجل فيها الطالب
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_enrollments', 'student_id', 'course_id')
                    ->withPivot(['status', 'progress', 'enrolled_at', 'completed_at', 'final_grade'])
                    ->withTimestamps();
    }

    public function courseEnrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class, 'student_id');
    }

    // Scopes
    public function scopeTeachers($query)
    {
        return $query->role('teacher');
    }

    public function scopeStudents($query)
    {
        return $query->role('student');
    }

    public function scopeAdmins($query)
    {
        return $query->role('admin');
    }
}
