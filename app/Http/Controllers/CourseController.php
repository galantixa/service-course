<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Mentor;
use App\Models\MyCourse;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{

    // function for show all with pagination and filter
    public function index (Request $request)
    {
        $courses = Course::query();

        $q = $request->query('q');
        $status = $request->query('status');
        $level = $request->query('level');
        $type = $request->query('type');

        $courses->when($q, function ($query) use ($q) {
            return $query->whereRaw("lower(name) LIKE '%" . strtolower($q) . "%'")
                         ->orWhereRaw("lower(description) LIKE '%" . strtolower($q) . "%'");
        });

        $courses->when($status, function($query) use ($status) {
            return $query->where('status', '=', $status);
        });

        $courses->when($level, function($query) use ($level) {
            return $query->where('level', '=', $level);
        });

        $courses->when($type, function($query) use ($type) {
            return $query->where('type', '=', $type);
        });

        if (!$courses->exists()) {
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $courses->paginate(10)
        ], 200);
    }

    // function for show by id
    public function show ($id)
    {
        $course = Course::with(['chapters.lessons', 'mentor', 'images'])->find($id);
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        // to view reviews on each course
        $reviews = Review::where('course_id', '=', $id)->get()->toArray();

        // to retrieve user data based on userId through the getUserId helper
        if (count($reviews) > 0) {
            $userIds = array_column($reviews, 'user_id');
            $users = getUserById($userIds);
            if($users['status'] === 'error') {
                $reviews = [];
            } else {
                foreach($reviews as $key => $review) {
                    $user_index = array_search($review['user_id'], array_column($users['data'], 'id'));
                    $reviews[$key]['users'] =$users['data'][$user_index];
                }
            }
        }

        // to see how many users have registered on this course
        $totalStudent = MyCourse::where('course_id', '=', $id)->count();

        // to see how many videos on this course
        $totalVideos = Chapter::where('course_id', '=', $id)->withCount('lessons')->get()->toArray();
        $finalTotalVideos = array_sum(array_column($totalVideos, 'lessons_count'));

        $course['reviews'] = $reviews;
        $course['total_videos'] = $finalTotalVideos;
        $course['total_student'] = $totalStudent;

        return response()->json([
            'status' => 'success',
            'data' => $course
        ], 200);
    }

    // function for create or add data course
    public function create (Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'certificate' => 'required|boolean',
            'thumbnail' => 'url',
            'type' => 'required|in:free,premium',
            'status' => 'required|in:draft,published',
            'price' => 'integer',
            'level' => 'required|in:all-level,beginner,intermediate,advance',
            'description' => 'string',
            'mentor_id' => 'required|integer',
        ];

        $data = $request->all();
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $mentorId = $request->input('mentor_id');
        $mentor = Mentor::find($mentorId);
        if(!$mentor) {
            return response()->json([
                'status' => 'error',
                'message' => 'mentor not found'
            ], 404);
        }

        $course = Course::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $course
        ], 201);
    }

    // function for update data course
    public function update (Request $request, $id)
    {
        $rules = [
            'name' => 'string',
            'certificate' => 'boolean',
            'thumbnail' => 'url',
            'type' => 'in:free,premium',
            'status' => 'in:draft,published',
            'price' => 'integer',
            'level' => 'in:all-level,beginner,intermediate,advance',
            'description' => 'string',
            'mentor_id' => 'integer',
        ];

        $data = $request->all();
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'success',
                'message' => $validator->errors()
            ], 400);
        }

        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $mentorId = $request->input('mentor_id');
        if($mentorId) {
            $mentor = Mentor::find($mentorId);
            if(!$mentor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'mentor not found'
                ], 404);
            }
        };

        $course->fill($data);
        $course->save();

        return response()->json([
            'status' => 'success',
            'data' => $course
        ], 200);
    }

    // function for delete data course
    public function destroy ($id)
    {
        $course = Course::find($id);
        if(!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course Not Found!'
            ] , 404);
        }

        $course->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'course deleted'
        ], 200);
    }
}
