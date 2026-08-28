<?php

namespace App\Http\Controllers;

use App\Http\Resources\FollowRequestResource;
use App\Models\FollowRequest;
use App\Models\User;
use App\Services\FollowServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowRequestController extends Controller
{
    public function __construct(public FollowServices $followServices){}
    //
    public function follow (Request $request , User $user){
        //
        $this->authorize('view',$user);

        return response()->json($this->followServices->follow($request->user(),$user));
    }

    public function acceptFollowRequests(Request $request ,FollowRequest $follow_request): JsonResponse
    {
        //
        $this->authorize('update',$follow_request);

        return response()->json(
            $this->followServices->acceptFollowRequest($request->user() ,$follow_request)
        );
    }

    public function rejectFollowRequests(FollowRequest $follow_request): JsonResponse
    {
        //
        $this->authorize('update',$follow_request);
        $this->followServices->reject($follow_request);
        return response()->json([
            'message' => 'The follow request has been rejected.'
        ]);
    }

    public function followRequests(Request $request): JsonResponse
    {
        //
        $follow_requests = $request->user()->followRequests()->latest()->paginate(20);

        return response()->json([
            'follow_requests' => FollowRequestResource::collection($follow_requests),
        ]);
    }

    public function changeAccountStatus(Request $request): JsonResponse
    {
        //
        $user = $this->followServices->changeStatus($request->user());

        return response()->json([
            'message' => 'The status of your account is changed to ' . ($user->is_private ? 'private' : 'public'),
        ]);
    }
}
