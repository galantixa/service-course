<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\MyCourse;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::query()->with('course');

        $q = $request->query('q');
        $userId = $request->query('user_id');
        $courseId = $request->query('course_id');

        $reviews->when($q, function ($query) use ($q) {
            return $query->whereRaw("note LIKE '%" . strtolower($q) . "%'");
        });

        $reviews->when($userId, function($query) use($userId) {
            return $query->where('user_id', '=', $userId);
        });

        $reviews->when($courseId, function($query) use($courseId) {
            return $query->where('course_id', '=', $courseId);
        });

        if(!$reviews->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $reviews->get()
        ], 200);
    }

    public function show($id)
    {
        //
    }

    public function create(Request $request)
    {
        $rules = [
            'user_id' => 'required|string',
            'course_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'note' => 'string'
        ];

        $data = $request->all();

        // Convert 'course_id' and 'rating' to integers
        $data['course_id'] = (int) $data['course_id'];
        $data['rating'] = (int) $data['rating'];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }
        $courseId = $request->input('course_id');
        $course = Course::find($courseId);

        if(!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $userId = $request->input('user_id');
        $user = getUser($userId);

        if($user['status'] === 'error') {
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message']
            ], $user['http_code']);
        }

        $isExistsReview = Review::where('course_id', '=', $courseId)->where('user_id', '=', $userId)->exists();

        if($isExistsReview) {
            return response()->json([
                'status' => 'error',
                'message' => 'review already exists'
            ], 409);
        }

        $myCourseExists = MyCourse::where('course_id', '=', $courseId)->where('user_id', '=', $userId)->exists();

        if(!$myCourseExists) {
            return response()->json([
                'status' => 'error',
                'message' => "can't leave a review. you don't have access to this course yet"
            ], 403);
        }

        $myReview = Review::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $myReview
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'rating' => 'integer|min:1|max:5',
            'note' => 'string'
        ];

        $data = $request->except('user_id', 'course_id');

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'review not found'
            ], 404);
        }

        $review->fill($data);
        $review->save();

        return response()->json([
            'status' => 'success',
            'data' => $review
        ], 200);

    }

    public function destroy($id)
    {
        $review = Review::find($id);
        if(!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'Review Not Found!'
            ], 404);
        }

        $review->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'review deleted!'
        ], 200);
    }
}
