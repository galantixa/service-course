<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ImageCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageCourseController extends Controller
{
    public function index (Request $request)
    {
        $imageCourse = ImageCourse::query();

        $q = $request->query('q');
        $courseId = $request->query('course_id');

        $imageCourse->when($q, function ($query) use ($q) {
            return $query->whereRaw("image LIKE '%" . strtolower($q) . "%'");
        });

        $imageCourse->when($courseId, function($query) use ($courseId) {
            return $query->where('course_id', '=', $courseId);
        });

        if(!$imageCourse->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'data not found'
            ], 404);
        };

        return response()->json([
            'status' => 'success',
            'data' => $imageCourse->get()
        ], 200);
    }

    public function show ($id)
    {
        $imageCourse = ImageCourse::find($id);
        if(!$imageCourse) {
            return response()->json([
                'status' => 'error',
                'message' => 'Image Course not found'
            ], 404);
        };

        return response()->json([
            'status' => 'success',
            'data' => $imageCourse
        ], 200);
    }

    public function create (Request $request)
    {
        $rules = [
            'course_id' => 'required|integer',
            'image' => 'required|url'
        ];

        $data = $request->all();
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        };

        $courseId = $request->input('course_id');
        $course = Course::find($courseId);
        if(!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'data course not found'
            ], 404);
        };

        $imageCourse = ImageCourse::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'created image course successfully',
            'data' => $imageCourse
        ], 201);
    }

    public function update (Request $request, $id)
    {
        $rules = [
            'course_id' => 'integer',
            'image' => 'url'
        ];

        $data = $request->all();
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        };

        $imageCourse = ImageCourse::find($id);
        if (!$imageCourse) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Image Course not found'
            ], 404);
        }

        $courseId = $request->input('course_id');
        if ($courseId) {
            $course = Course::find($courseId);
            if (!$course) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'data course not found'
                ], 404);
            }
        }

        $imageCourse->fill($data);
        $imageCourse->save();

        return response()->json([
            'status' => 'success',
            'data' => $imageCourse
        ], 200);
    }

    public function destroy ($id)
    {
        $imageCourse = ImageCourse::find($id);
        if(!$imageCourse) {
            return response()->json([
                'status' => 'error',
                'message' => 'data Image Course not found'
            ], 404);
        };

        $imageCourse->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'deleted successfully'
        ], 200);
    }
}
