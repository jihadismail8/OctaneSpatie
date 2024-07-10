<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\Team;
use App\Models\User;
use DB;
use Validator;


class TeamController extends BaseController
{

    /**
    * @OA\Get(
    *    path="/teams/{id}",
    *    tags={"Teams"},
    *    summary="Get list of teams or 1 team",
    *    @OA\Parameter(
    *        in="path",
    *        name="id",
    *        required=true,
    *        @OA\Schema(
    *            type="string",
    *            example="1"
    *        )
    *    ),
    *    @OA\Response(
    *        response=200,
    *         description="Successful operation. If requested without ID, the *data* field will contain an array of objects",
    *        @OA\JsonContent(
    *            type="object",
    *            @OA\Property(
    *                property="data",
    *                type="object",
    *                @OA\Property(property="created_at", type="string", example="2024-03-20T00:17:37.000000Z"),
    *                @OA\Property(property="id", type="number", example=1),
    *                @OA\Property(property="name", type="string", example="developers"),
    *                @OA\Property(property="updated_at", type="string", example="2024-03-20T00:17:37.000000Z"),
    *                @OA\Property(
    *                    property="users_list",
    *                    type="array",
    *                    @OA\Items(
    *                        type="object",
    *                        @OA\Property(property="created_at", type="string", example="2024-03-20T00:17:38.000000Z"),
    *                        @OA\Property(property="current_team_id", type="number", example=1),
    *                        @OA\Property(property="email", type="string", example="jihad.ismail.8@gmail.com"),
    *                        @OA\Property(property="email_verified_at", type="object", example=null),
    *                        @OA\Property(property="id", type="number", example=2),
    *                        @OA\Property(property="name", type="string", example="jihad"),
    *                        @OA\Property(
    *                            property="pivot",
    *                            type="object",
    *                            @OA\Property(property="team_id", type="number", example=1),
    *                            @OA\Property(property="user_id", type="number", example=2)
    *                        ),
    *                        @OA\Property(property="profile_photo_path", type="object", example=null),
    *                        @OA\Property(property="profile_photo_url", type="string", example="https://ui-avatars.com/api/?name=j&color=7F9CF5&background=EBF4FF"),
    *                        @OA\Property(property="two_factor_confirmed_at", type="object", example=null),
    *                        @OA\Property(property="updated_at", type="string", example="2024-03-20T00:17:38.000000Z")
    *                    )
    *                )
    *            ),
    *            @OA\Property(property="message", type="string", example="Teams data"),
    *            @OA\Property(property="success", type="boolean", example=true)
    *        )
    *    )
    * )
    */
    public function Show($id=null)
    {
        if(!isset($id)){
            $teams = Team::with('UsersList')->get();
        }else{
            $teams = Team::where('id',$id)->with('UsersList')->first();
        }
        return $this->sendResponse($teams, 'Teams data');
        // $roles = Role::orderBy('id','DESC')->paginate(5);    
        // return view('roles.index',compact('roles'))
        //     ->with('i', ($request->input('pa ge', 1) - 1) * 5);
    }


    /**
    * @OA\Post(
    *    path="/teams",
    *    tags={"Teams"},
    *    summary="Create a team",
    *    @OA\RequestBody(
    *        @OA\MediaType(
    *            mediaType="application/json",
    *            @OA\Schema(
    *                type="object",
    *                required={"name"},
    *                @OA\Property(property="name", type="string"),
    *                @OA\Property(
    *                    property="users_ids",
    *                    type="array",
    *                    @OA\Items(
    *                        type="number"
    *                    )
    *                ),
    *                example={
    *                    "name": "Dream Team",
    *                    "users_ids": {
    *                        19
    *                    }
    *                }
    *            )
    *        )
    *    ),
    *    @OA\Response(
    *        response=200,
    *        description="successful operation",
    *        @OA\JsonContent(
    *            type="object",
    *            @OA\Property(
    *                property="data",
    *                type="object",
    *                @OA\Property(property="name", type="string", example="Dream Team")
    *            ),
    *            @OA\Property(property="message", type="string", example="Team created successfully"),
    *            @OA\Property(property="success", type="boolean", example=true)
    *        )
    *    ),
    *    @OA\Response(
    *        response=404,
    *        description="Bad request",
    *        @OA\JsonContent(
    *            type="object",
    *            @OA\Property(
    *                property="data",
    *                type="object",
    *                 @OA\AdditionalProperties(
    *                     type="array",
    *                     @OA\Items(
    *                         type="string",
    *                         example="The ... field is required."
    *                     )
    *                 )
    *            ),
    *            @OA\Property(property="message", type="string", example="Validation Error."),
    *            @OA\Property(property="success", type="boolean", example=false)
    *        )
    *    )
    * )
    */
    public function Create(Request $request)    
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:teams,name',
            'users_ids' => 'nullable|array',
            'users_ids.*'=>'exists:users,id',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $team=Team::create(['name' => $request->input('name')]);
        $ids = $request->input('users_ids');
        foreach($ids as $id){
            $user = User::find($id);
            $user->teams()->attach($team);
        }
        $success['name'] =  $request->input('name');
        return $this->sendResponse($success, 'Team created successfully');
    }

    /**
    * @OA\Put(
    *    path="/teams",
    *    tags={"Teams"},
    *    summary="Updates a team",
    *    @OA\RequestBody(
    *        @OA\MediaType(
    *            mediaType="application/json",
    *            @OA\Schema(
    *                type="object",
    *                required={"team_id", "name"},
    *                @OA\Property(property="team_id", type="number"),
    *                @OA\Property(property="name", type="string"),
    *                @OA\Property(
    *                    property="users_ids",
    *                    type="array",
    *                    @OA\Items(
    *                        type="number"
    *                    )
    *                ),
    *                example={
    *                    "team_id": 73,
    *                    "name": "Dream Team",
    *                    "users_ids": {
    *                        19,
    *                        6
    *                    }
    *                }
    *            )
    *        )
    *    ),
    *    @OA\Response(
    *        response=200,
    *        description="OK",
    *        @OA\JsonContent(
    *            type="object",
    *            @OA\Property(
    *                property="data",
    *                type="object",
    *                @OA\Property(property="name", type="string", example="Dream Team")
    *            ),
    *            @OA\Property(property="message", type="string", example="Team Updated successfully"),
    *            @OA\Property(property="success", type="boolean", example=true)
    *        )
    *    )
    * )
    */
    public function UpdateTeam(Request $request)
    {
        $this->validate($request, [
            'team_id'=>'required',
            'name' => 'required',
            'users_ids' => 'required|array',
            'users_ids.*'=>'exists:users,id',
        ]);
        $teamtemp = Team::where('id',$request->input('team_id'))->with('UsersList')->first()->toArray();
        $team = Team::find($request->input('team_id'));
        foreach($teamtemp['users_list'] as $useraray){
            $id = $useraray['id'];
            $user = User::find($id);
            $user->teams()->detach($team);
        }
        $team->name = $request->input('name');
        $team->save();
        $ids = $request->input('users_ids');
        foreach($ids as $id){
            $user = User::find($id);
            $user->teams()->attach($team);
        }
        $success['name'] =  $request->input('name');
        return $this->sendResponse($success, 'Team Updated successfully');

    }

    /**
    * @OA\Post(
    *    path="/teams/delete",
    *    tags={"Teams"},
    *    summary="Deletes a team",
    *    @OA\RequestBody(
    *        @OA\MediaType(
    *            mediaType="application/json",
    *            @OA\Schema(
    *                type="object",
    *                required={"team_id"},
    *                @OA\Property(property="team_id", type="number"),
    *                example={
    *                    "team_id": 75
    *                }
    *            )
    *        )
    *    ),
    *    @OA\Response(
    *        response=200,
    *        description="OK",
    *        @OA\JsonContent(
    *            type="object",
    *            @OA\Property(
    *                property="data",
    *                type="object",
    *                @OA\Property(property="id", type="number", example=75)
    *            ),
    *            @OA\Property(property="message", type="string", example="team Deleted successfully"),
    *            @OA\Property(property="success", type="boolean", example=true)
    *        )
    *    )
    * )
    */
    public function DeleteTeam(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'team_id' => 'required'
        ]);
        $teamid=$request->input('team_id');
        $teamtemp = Team::where('id',$teamid)->with('UsersList')->first()->toArray();
        $team = Team::find($teamid);
        foreach($teamtemp['users_list'] as $useraray){
            $id = $useraray['id'];
            $user = User::find($id);
            $user->teams()->detach($team);
        }
        Team::find($teamid)->delete();
        $success['id'] =  $teamid;
        return $this->sendResponse($success, 'team Deleted successfully');
    }

}   
