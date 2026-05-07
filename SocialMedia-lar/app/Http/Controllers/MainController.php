<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use App\Models\Followers;
use App\Models\Likes;
use App\Models\PostImage;
use App\Models\Posts;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    public function CreatePost(Request $request){
        try{
            $fields = $request->validate([
                'title'=>['required', 'string', 'max:255'],
                'content'=>['required', 'string'],
                'path'=>['required','mimes:jpg,png,jpeg,gif,svg,webp','max:2048'],
            ]);
            $post = Posts::create([
                'user_id'=>Auth::id(),
                'title'=>$fields['title'],
                'content'=>$fields['content'],
            ]);
            if(isset($fields['path'])){
                $imageName = time().'.'.$fields['path']->extension();
                $fields['path']->move(public_path('assets/images'),$imageName);
                $fields['path'] = $imageName;
                $image = PostImage::create([
                    'post_id'=>$post->id,
                    'path'=>$fields['path'],
                ]);
            }
            return response()->json([
                'response_code'=>201,
                'status'=>'success',
                'message'=>'Post created successfully',
                'post_info'=>[
                    'id'=>$post->id,
                    'user_id'=>$post->user_id,
                    'title'=>$post->title,
                    'content'=>$post->content,
                    'created_at'=>$post->created_at,
                    'updated_at'=>$post->updated_at,
                ],
                'image_info'=>[
                    'id'=>$image->id,
                    'post_id'=>$image->post_id,
                    'path'=>$image->path,
                    'created_at'=>$image->created_at,
                    'updated_at'=>$image->updated_at,
                ],
            ]);
            
        }catch(\Exception $e){
            return response()->json([
                'message'=>$e->getMessage(),
                'line'=>$e->getLine(),
            ], 500);
        }
    }
    function GetPosts(){
        try{
            $posts = Posts::with('images','users')->get();
            return response()->json([
                'response_code'=>200,
                'status'=>'success',
                'message'=>'Posts retrieved successfully',
                'posts'=>$posts,
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message'=>$e->getMessage(),
                'line'=>$e->getLine(),
            ], 500);
        }
    }
    public function GetPost(int $id){
        try{
            $post = Posts::with('images','users')->find($id);
            if(!$post){
                return response()->json([
                    'response_code'=>404,
                    'status'=>'error',
                    'message'=>'Post not found',
                ], 404);
            }
            return response()->json([
                'response_code'=>200,
                'status'=>'success',
                'message'=>'Post retrieved successfully',
                'post'=>$post,
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message'=>$e->getMessage(),
                'line'=>$e->getLine(),
            ], 500);
        }
    }
    public function MyPosts(){
        try{
            $posts = Posts::where('user_id',Auth::id())->with('images','users')->get();
            return response()->json([
                'response_code'=>200,
                'status'=>'success',
                'message'=>'My posts retrieved successfully',
                'posts'=>$posts,
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message'=>$e->getMessage(),
                'line'=>$e->getLine(),
            ], 500);
        }
    }
    public function UpdateProfile(Request $request , int $id){
        $fields = $request->validate([
            'name'=>['required','string','max:255'],
            'email'=>['required','email'],
            'profile_image'=>['nullable','mimes:jpg,png,jpeg,gif,svg,webp','max:2048'],
        ]);
        try{
            $user = User::find($id);
            if(!$user){
                return response()->json([
                    'response_code'=>404,
                    'status'=>'error',
                    'message'=>'User not found',
                ], 404);
            }
            $user->name = $fields['name'];
            $user->email = $fields['email'];
            if(isset($fields['profile_image'])){
                $imageName = time().'.'.$fields['profile_image']->extension();
                $fields['profile_image']->move(public_path('assets/images'),$imageName);
                $fields['profile_image'] = $imageName;
                $user->profile_image = $fields['profile_image'];
            }
            $user->save();
            return response()->json([
                'response_code'=>200,
                'status'=>'success',
                'message'=>'Profile updated successfully',
                'user_info'=>[
                    'id'=>$user->id,
                    'name'=>$user->name,
                    'email'=>$user->email,
                    'profile_image'=>$user->profile_image,
                    'created_at'=>$user->created_at,
                    'updated_at'=>$user->updated_at,
                ],
            ]);
    }catch(\Exception $e){
        return response()->json([
            'message'=>$e->getMessage(),
            'line'=>$e->getLine(),
        ], 500);
    }
}
public function DeletePost(int $id){
    try{
        $post = Posts::find($id);
        if(!$post){
            return response()->json([
                'response_code'=>404,
                'status'=>'error',
                'message'=>'Post not found',
            ], 404);
        }
        $post->delete();
        return response()->json([
            'response_code'=>200,
            'status'=>'success',
            'message'=>'Post deleted successfully',
        ]);
    }catch(\Exception $e){
        return response()->json([
            'message'=>$e->getMessage(),
            'line'=>$e->getLine(),
        ], 500);
    }
}
public function LikePost(User $user, Posts $post){
    try{
        $existingLike = Likes::where('user_id',$user->id)->where('post_id',$post->id)->first();
        if($existingLike){
            $existingLike->delete();
        }
        $like = Likes::create([
            'user_id'=>$user->id,
            'post_id'=>$post->id,
        ]);
        return response()->json([
            'response_code'=>200,
            'status'=>'success',
            'message'=>$existingLike ? 'Post unliked successfully' : 'Post liked successfully',
            'like_info'=>[
                'id'=>$like->id,
                'user_id'=>$like->user_id,
                'post_id'=>$like->post_id,
                'created_at'=>$like->created_at,
                'updated_at'=>$like->updated_at,
            ],
        ]);
    }catch(\Exception $e){
        return response()->json([
            'message'=>$e->getMessage(),
            'line'=>$e->getLine(),
        ], 500);
    };
}
public function GetLikes(Posts $post){
    try{
        $likes = Likes::where('post_id',$post->id)->with('user')->get();
        $count = $likes->count();
        return response()->json([
            'response_code'=>200,
            'status'=>'success',
            'message'=>'Likes retrieved successfully',
            'likes_count'=>$count,
            'likes'=>$likes,
        ]);
    }catch(\Exception $e){
        return response()->json([
            'message'=>$e->getMessage(),
            'line'=>$e->getLine(),
        ], 500);
    }
}
public function Comment(Request $request , User $user, Posts $post){
    $fields = $request->validate([
        'content'=>['required','string'],
    ]);
    try{
        $comment = Comments::create([
            'user_id'=>$user->id,
            'post_id'=>$post->id,
            'content'=>$fields['content'],
        ]);
        return response()->json([
            'response_code'=>201,
            'status'=>'success',
            'message'=>'Comment added successfully',
            'comment_info'=>[
                'id'=>$comment->id,
                'user_id'=>$comment->user_id,
                'post_id'=>$comment->post_id,
                'content'=>$comment->content,
                'created_at'=>$comment->created_at,
                'updated_at'=>$comment->updated_at,
            ],
        ]);
    }catch(\Exception $e){
        return response()->json([
            'message'=>$e->getMessage(),
            'line'=>$e->getLine(),
        ], 500);
}
}
public function GetComments(Posts $post){
    try{
        $comments = Comments::where('post_id',$post->id)->with('user')->get();
        $count = $comments->count();
        return response()->json([
            'response_code'=>200,
            'status'=>'success',
            'message'=>'Comments retrieved successfully',
            'comments_count'=>$count,
            'comments'=>$comments,
        ]);
    }catch(\Exception $e){
        return response()->json([
            'message'=>$e->getMessage(),
            'line'=>$e->getLine(),
        ], 500);
    }
}
public function Follow(User $user){
    try{
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
        $authUser = Auth::user();
        $Follower = Followers::create([
            'follower_id'=>$authUser->id,
            'following_id'=>$user->id
        ]);
        return response()->json([
            'response_code'=>200,
            'status'=>'success',
            'message'=>'Follow request sent successfully',
            'follow_info'=>[
                'id'=>$Follower->id,
                'follower_id'=>$Follower->follower_id,
                'following_id'=>$Follower->following_id,
                'status'=>$Follower->status,
                'created_at'=>$Follower->created_at,
                'updated_at'=>$Follower->updated_at,
            ],
        ]);
    }catch(\Exception $e){
        return response()->json([
            'message'=>$e->getMessage(),
            'line'=>$e->getLine(),
        ], 500);
}
}
public function acceptFollow(User $user)
{
    try {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $authUser = Auth::user();

        $followRequest = Followers::where('follower_id', $user->id)
            ->where('following_id', $authUser->id)
            ->where('status', 'pending')
            ->first();

        if (!$followRequest) {
            return response()->json([
                'message' => 'No pending follow request found'
            ], 404);
        }

        $followRequest->update([
            'status' => 'accepted'
        ]);

        return response()->json([
            'response_code' => 200,
            'status' => 'success',
            'message' => 'Follow request accepted successfully',
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
        ], 500);
    }
}
public function RejectFollow(User $user){
    try{
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $authUser = Auth::user();

        $followRequest = Followers::where('follower_id', $user->id)
            ->where('following_id', $authUser->id)
            ->where('status', 'pending')
            ->first();

        if (!$followRequest) {
            return response()->json([
                'message' => 'No pending follow request found'
            ], 404);
        }

        $followRequest->update([
            'status' => 'rejected'
        ]);

        return response()->json([
            'response_code' => 200,
            'status' => 'success',
            'message' => 'Follow request rejected successfully',
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
        ], 500);
    }
}
public function GetFollowers(){
    try{
        if(!Auth::check()){
            return response()->json([
                'message'=>'Unauthenticated'
            ], 401);
        }
        $authUser = Auth::user();
        $followers = Followers::where('following_id',$authUser->id)
        ->where('status','accepted')
        ->with('followerUser')
        ->get();
        $count = $followers->count();
        return response()->json([
            'response_code'=>200,
            'status'=>'success',
            'message'=>'Followers retrieved successfully',
            'followers_count'=>$count,
            'followers'=>$followers,
        ]);
    }catch(\Exception $e){
        return response()->json([
            'message'=>$e->getMessage(),
            'line'=>$e->getLine(),
        ], 500);
    }
}
public function GetFollowings(){
    try{
        if(!Auth::check()){
            return response()->json([
                'message'=>'Unauthenticated'
            ], 401);
        }
        $authUser = Auth::user();
        $followings = Followers::where('follower_id',$authUser->id)
        ->where('status','accepted')
        ->with('followingUser')
        ->get();
        $count = $followings->count();
        return response()->json([
            'response_code'=>200,
            'status'=>'success',
            'message'=>'Followings retrieved successfully',
            'followings_count'=>$count,
            'followings'=>$followings,
        ]);
    }catch(\Exception $e){
        return response()->json([
            'message'=>$e->getMessage(),
            'line'=>$e->getLine(),
        ], 500);
}
}
public function Unfollow(User $user){
    try{
        if(!Auth::check()){
            return response()->json([
                'message'=>'Unauthenticated'
            ], 401);
        }
        $authUser = Auth::user();
        $follow = Followers::where('follower_id',$authUser->id)
        ->where('following_id',$user->id)
        ->where('status','accepted')
        ->first();
        if(!$follow){
            return response()->json([
                'message'=>'Follow relationship not found'
            ], 404);
        }
        $follow->delete();
        return response()->json([
            'response_code'=>200,
            'status'=>'success',
            'message'=>'Unfollowed successfully',
        ]);
    }catch(\Exception $e){
        return response()->json([
            'message'=>$e->getMessage(),
            'line'=>$e->getLine(),
        ], 500);
}
}
public function GetFollowRequests(){
    try{
        if(!Auth::check()){
            return response()->json([
                'message'=>'Unauthenticated'
            ], 401);
        }
        $authUser = Auth::user();
        $followRequests = Followers::where('following_id',$authUser->id)
        ->where('status','pending')
        ->with('followerUser')
        ->get();
        $count = $followRequests->count();
        return response()->json([
            'response_code'=>200,
            'status'=>'success',
            'message'=>'Follow requests retrieved successfully',
            'follow_requests_count'=>$count,
            'follow_requests'=>$followRequests,
        ]);
    }catch(\Exception $e){
        return response()->json([
            'message'=>$e->getMessage(),
            'line'=>$e->getLine(),
        ], 500);
}
}
}