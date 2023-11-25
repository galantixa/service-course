<?php

use App\Http\Controllers\ChapterController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ImageCourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\MyCourseController;
use App\Http\Controllers\ReviewController;
use App\Models\ImageCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/**
 * Route Group Mentor for CRUD with prefix
 * http://localhost:8000/api/v1/
 */
Route::prefix('v1')->group(function () {
    /**
     * Route for Controller CRUD Mentor
     * http://localhost:8000/api/v1/mentors
     * @return \Illuminate\Http\JsonResponse
     */
    Route::prefix('mentors')->group(function () {
        Route::controller(MentorController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/', 'create');
            Route::patch('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    /**
     * Route for Controller CRUD Course
     * http://localhost:8000/api/v1/courses
     */
    Route::prefix('courses')->group(function () {
        Route::controller(CourseController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/', 'create');
            Route::patch('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    /**
     * Route for Controller CRUD Chapter
     * http://localhost:8000/api/v1/chapters
     */
    Route::prefix('chapters')->group(function () {
        Route::controller(ChapterController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/', 'create');
            Route::patch('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    /**
     * Route for Controller CRUD Lesson
     * http://localhost:8000/api/v1/lessons
     */
    Route::prefix('lessons')->group(function () {
        Route::controller(LessonController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/', 'create');
            Route::patch('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    /**
     * Route for Controller CRUD ImageCourse
     * http://localhost:8000/api/v1/image-course
     */
    Route::prefix('image-course')->group(function () {
        Route::controller(ImageCourseController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/', 'create');
            Route::patch('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    /**
     * Route for Controller MyCourse
     * http://localhost:8000/api/v1/my-course
     */
    Route::prefix('my-course')->group(function () {
        Route::controller(MyCourseController::class)->group(function () {
            Route::post('/', 'create');
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::patch('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    /**
     * Route for Controller Review
     * http://localhost:8000/api/v1/review
     */
     Route::prefix('review')->group(function () {
        Route::controller(ReviewController::class)->group(function () {
            Route::post('/', 'create');
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::patch('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
     });
});
