<?php
    
namespace App\Http\Controllers\API;
    
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Validator;
use App\Models\Team;
use Auth;

class UserController extends BaseController
{
    function __construct()
    {
        setPermissionsTeamId('1');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
    * @OA\Get(
    *     path="/users/{id}",
    *     tags={"Users"},
    *     summary="Get list of users or 1 user",
    *     @OA\Parameter(
    *         in="path",
    *         name="id",
    *         required=true,
    *         @OA\Schema(
    *             type="string",
    *             example="19"
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation. If requested without ID, the *data* field will contain a paginator model: ``
    data={
        data: TUser[],
        current_page: number,
        from: number,
        to: number,
        per_page: number,
        total: number,
        last_page: number,
        first_page_url: string,
        last_page_url: string,
        prev_page_url?: string,
        next_page_url?: string
    }``",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(
    *                 property="data",
    *                 type="array",
    *                 @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="created_at", type="string", example="2024-03-20T00:17:38.000000Z"),
    *                     @OA\Property(property="current_team_id", type="number", example=1),
    *                     @OA\Property(property="email", type="string", example="jihad.ismail.8@gmail.com"),
    *                     @OA\Property(property="email_verified_at", type="object", example=null),
    *                     @OA\Property(property="id", type="number", example=2),
    *                     @OA\Property(property="name", type="string", example="jihad"),
    *                     @OA\Property(property="profile_photo_path", type="object", example=null),
    *                     @OA\Property(property="profile_photo_url", type="string", example="https://ui-avatars.com/api/?name=j&color=7F9CF5&background=EBF4FF"),
    *                     @OA\Property(
    *                         property="roles",
    *                         type="array",
    *                         @OA\Items(
    *                             type="object",
    *                             @OA\Property(property="created_at", type="string", example="2024-03-20T00:17:37.000000Z"),
    *                             @OA\Property(property="guard_name", type="string", example="api"),
    *                             @OA\Property(property="id", type="number", example=1),
    *                             @OA\Property(property="name", type="string", example="Super Admin"),
    *                             @OA\Property(
    *                                 property="permissions",
    *                                 type="array",
    *                                 @OA\Items(
    *                                     type="object",
    *                                     @OA\Property(property="created_at", type="string", example="2024-03-20T00:17:37.000000Z"),
    *                                     @OA\Property(property="guard_name", type="string", example="api"),
    *                                     @OA\Property(property="id", type="number", example=1),
    *                                     @OA\Property(property="name", type="string", example="Create User"),
    *                                     @OA\Property(
    *                                         property="pivot",
    *                                         type="object",
    *                                         @OA\Property(property="permission_id", type="number", example=1),
    *                                         @OA\Property(property="role_id", type="number", example=1)
    *                                     ),
    *                                     @OA\Property(property="updated_at", type="string", example="2024-03-20T00:17:37.000000Z")
    *                                 )
    *                             ),
    *                             @OA\Property(
    *                                 property="pivot",
    *                                 type="object",
    *                                 @OA\Property(property="model_id", type="number", example=2),
    *                                 @OA\Property(property="model_type", type="string", example="App\\Models\\User"),
    *                                 @OA\Property(property="role_id", type="number", example=1)
    *                             ),
    *                             @OA\Property(property="team_id", type="number", example=1),
    *                             @OA\Property(property="updated_at", type="string", example="2024-03-20T00:17:37.000000Z")
    *                         )
    *                     ),
    *                     @OA\Property(
    *                         property="teams",
    *                         type="array",
    *                         @OA\Items(
    *                             type="object",
    *                             @OA\Property(property="created_at", type="string", example="2024-03-20T00:17:37.000000Z"),
    *                             @OA\Property(property="id", type="number", example=1),
    *                             @OA\Property(property="name", type="string", example="developers"),
    *                             @OA\Property(
    *                                 property="pivot",
    *                                 type="object",
    *                                 @OA\Property(property="team_id", type="number", example=1),
    *                                 @OA\Property(property="user_id", type="number", example=2)
    *                             ),
    *                             @OA\Property(property="updated_at", type="string", example="2024-03-20T00:17:37.000000Z")
    *                         )
    *                     ),
    *                     @OA\Property(property="two_factor_confirmed_at", type="object", example=null),
    *                     @OA\Property(property="updated_at", type="string", example="2024-03-20T00:17:38.000000Z")
    *                 )
    *             ),
    *             @OA\Property(property="message", type="string", example="users data"),
    *             @OA\Property(property="success", type="boolean", example=true)
    *         )
    *     )
    * )
    */
    public function Users($id=null)
    {
        if(!isset($id)){
            $data = User::orderBy('id','DESC')->with('teams')->paginate(10);
        }else{
            $data = User::where('id',$id)->with('teams')->get();
        }


        return $this->sendResponse($data, 'users data');

        // return view('users.index',compact('data'))
        //     ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
    * @OA\Get(
    *     path="/profile",
    *     tags={"Profile"},
    *     summary="Get current user profile",
    *     @OA\Response(
    *         response=200,
    *         description="successful operation",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(
    *                 property="data",
    *                 type="array",
    *                 @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="created_at", type="string", example="2024-06-20T07:15:46.000000Z"),
    *                     @OA\Property(property="current_team_id", type="number", example=1),
    *                     @OA\Property(property="email", type="string", example="my-user@example.com"),
    *                     @OA\Property(property="email_verified_at", type="object", example=null),
    *                     @OA\Property(property="id", type="number", example=19),
    *                     @OA\Property(property="name", type="string", example="MyUser"),
    *                     @OA\Property(property="profile_photo_path", type="object", example=null),
    *                     @OA\Property(property="profile_photo_url", type="string", example="https://ui-avatars.com/api/?name=A&color=7F9CF5&background=EBF4FF"),
    *                     @OA\Property(
    *                         property="roles",
    *                         type="array",
    *                         @OA\Items(
    *                             type="object",
    *                             @OA\Property(property="created_at", type="string", example="2024-03-20T00:17:37.000000Z"),
    *                             @OA\Property(property="guard_name", type="string", example="api"),
    *                             @OA\Property(property="id", type="number", example=1),
    *                             @OA\Property(property="name", type="string", example="Super Admin"),
    *                             @OA\Property(
    *                                 property="permissions",
    *                                 type="array",
    *                                 @OA\Items(
    *                                     type="object",
    *                                     @OA\Property(property="created_at", type="string", example="2024-03-20T00:17:37.000000Z"),
    *                                     @OA\Property(property="guard_name", type="string", example="api"),
    *                                     @OA\Property(property="id", type="number", example=1),
    *                                     @OA\Property(property="name", type="string", example="Create User"),
    *                                     @OA\Property(
    *                                         property="pivot",
    *                                         type="object",
    *                                         @OA\Property(property="permission_id", type="number", example=1),
    *                                         @OA\Property(property="role_id", type="number", example=1)
    *                                     ),
    *                                     @OA\Property(property="updated_at", type="string", example="2024-03-20T00:17:37.000000Z")
    *                                 )
    *                             ),
    *                             @OA\Property(
    *                                 property="pivot",
    *                                 type="object",
    *                                 @OA\Property(property="model_id", type="number", example=19),
    *                                 @OA\Property(property="model_type", type="string", example="App\\Models\\User"),
    *                                 @OA\Property(property="role_id", type="number", example=1)
    *                             ),
    *                             @OA\Property(property="team_id", type="number", example=1),
    *                             @OA\Property(property="updated_at", type="string", example="2024-03-20T00:17:37.000000Z")
    *                         )
    *                     ),
    *                     @OA\Property(property="two_factor_confirmed_at", type="object", example=null),
    *                     @OA\Property(property="updated_at", type="string", example="2024-06-26T07:41:12.000000Z")
    *                 )
    *             ),
    *             @OA\Property(property="message", type="string", example="profile data"),
    *             @OA\Property(property="success", type="boolean", example=true)
    *         )
    *     )
    * )
    */
    public function profile()
    {
        $id=auth('sanctum')->user()->id;
        $data = User::where('id','=',$id)->get();
        return $this->sendResponse($data, 'profile data');

        // return view('users.index',compact('data'))
        //     ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
    * @OA\Post(
    *     path="/users",
    *     tags={"Users"},
    *     summary="Creates a User",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 type="object",
    *                 @OA\Property(
    *                     property="current_team_ids",
    *                     type="array",
    *                     @OA\Items(
    *                         type="number"
    *                     )
    *                 ),
    *                 @OA\Property(property="email", type="string"),
    *                 @OA\Property(property="name", type="string"),
    *                 @OA\Property(property="password", type="string"),
    *                 @OA\Property(
    *                     property="roles",
    *                     type="array",
    *                     @OA\Items(
    *                         type="string"
    *                     )
    *                 ),
    *                 example={
    *                     "name": "My User",
    *                     "email": "jr6nt.m.lux@q.k5",
    *                     "password": "Jzux713",
    *                     "roles": {
    *                         "Super Admin"
    *                     },
    *                     "current_team_ids": {
    *                         1
    *                     }
    *                 }
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="successful operation",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(
    *                 property="data",
    *                 type="object",
    *                 @OA\Property(property="name", type="string", example="My User")
    *             ),
    *             @OA\Property(property="message", type="string", example="User register successfully."),
    *             @OA\Property(property="success", type="boolean", example=true)
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Bad request",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(
    *                 property="data",
    *                 type="object",
    *                 @OA\AdditionalProperties(
    *                     type="array",
    *                     @OA\Items(
    *                         type="string",
    *                         example="The ... field is required."
    *                     )
    *                 )
    *             ),
    *             @OA\Property(property="message", type="string", example="Validation Error."),
    *             @OA\Property(property="success", type="boolean", example=false)
    *         )
    *     )
    * )
    */
    public function storeUser(Request $request)
    {
        setPermissionsTeamId('1');
        $validator =  Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'roles' => 'required|exists:roles,name|array',
            'current_team_ids' => 'required|array',
            'current_team_ids.*'=>'exists:teams,id',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        // $input['current_team_id']= $input['current_team_id'];
        $user  =User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => bcrypt($input['password']),
            'current_team_id'=> 1,
        ])->assignRole($request->input('roles'));
        $user->teams()->attach('1');
        // $user = User::create($input)->assignRole('Super Admin');
        // $team = Team::Where('id',$input['current_team_id'])->get();
        
        //  $ids = $request->input('current_team_ids');
        //  foreach($ids as $team_id){
        //     $user->teams()->attach($team_id);
        //     setPermissionsTeamId($team_id); 
        //     // $rolesids = Role::WhereIn('name',[$request->input('roles')])->get('id');
        //     try{
        //         $user->syncRoles($request->input('roles'));
        //     }catch(\Throwable $e){// if Role deos not exist in the team , since each Team has sit of roles
        //         // $message = dd('not exists');
        //         // $user->teams()->detach($team_id);
        //     }
            
        // }
        //  foreach($ids as $id){
        //      $user = User::find($id);
        //      $user->teams()->attach($team);
        //  }
        // $user->teams()->attach($ids);
        // $user->assignRole($request->input('roles'));
        $success['name'] =  $request->input('name');
        $name = Auth::user()->name;
        $this->logtochannel('telegram','logs','info',"$name Created New User");
        return $this->sendResponse($success, 'User register successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
    * @OA\Put(
    *     path="/users",
    *     tags={"Users"},
    *     summary="Updates a user",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 type="object",
    *                 @OA\Property(
    *                     property="current_team_ids",
    *                     type="array",
    *                     @OA\Items(
    *                         type="number"
    *                     )
    *                 ),
    *                 @OA\Property(property="email", type="string"),
    *                 @OA\Property(property="id", type="number"),
    *                 @OA\Property(property="name", type="string"),
    *                 @OA\Property(property="password", type="string"),
    *                 @OA\Property(
    *                     property="roles",
    *                     type="array",
    *                     @OA\Items(
    *                         type="string"
    *                     )
    *                 ),
    *                 example={
    *                     "name": "My User",
    *                     "email": "q9g.z@iz5fs.zpgd",
    *                     "password": "Sflr751}",
    *                     "roles": {
    *                         "Administrator",
    *                         "Super Admin"
    *                     },
    *                     "current_team_ids": {
    *                         1
    *                     },
    *                     "id": 27
    *                 }
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="OK",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(
    *                 property="data",
    *                 type="object",
    *                 @OA\Property(property="id", type="number", example=27),
    *                 @OA\Property(property="name", type="string", example="My User")
    *             ),
    *             @OA\Property(property="message", type="string", example="User updated successfully"),
    *             @OA\Property(property="success", type="boolean", example=true)
    *         )
    *     )
    * )
    */
    public function UpdateUser(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'id'=>'required',
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$request->input('id'),
            'password' => '',
            'roles' => 'required|exists:roles,name',
            'current_team_ids' => 'required|array',
            'current_team_ids.*'=>'exists:teams,id',
        ]);
        
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
    
        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = bcrypt($input['password']);
        }

        // $teamtemp = Team::where('id',$request->input('team_id'))->with('UsersList')->first()->toArray();
        // $team = Team::find($request->input('team_id'));
        // foreach($teamtemp['users_list'] as $useraray){
        //     $id = $useraray['id'];
        //     $user = User::find($id);
        //     $user->teams()->detach($team);
        // }
        // }else{
        //     $input = Arr::except($input,array('password'));    
        // }
        $teams_ids = $request->input('current_team_ids');
        $user = User::find($request->input('id'));
        $deatchTeams= DB::table('model_has_roles')
        ->where('model_id',$request->input('id'))
        ->delete();
        $deatchTeams2= DB::table('team_user')
        ->where('user_id',$request->input('id'))
        ->delete();
        // $teamsidavail = model_has_roles::Where('model_id',$request->input('id'))->get();
        // dd($teamsidavail);
        $user->update($input);
        // $user5->teams()->attach($team);
        foreach($teams_ids as $team_id){
            $user->teams()->attach($team_id);
            setPermissionsTeamId($team_id); 
            // $rolesids = Role::WhereIn('name',[$request->input('roles')])->get('id');
            try{
                $user->syncRoles($request->input('roles'));
            }catch(\Throwable $e){// if Role deos not exist in the team , since each Team has sit of roles
                // $message = dd('not exists');
                // $user->teams()->detach($team_id);
            }
            
        }


        // 
        // $user->teams()->attach($ids);

        // DB::table('model_has_roles')->where('model_id',$request->input('id'))->delete();
    


        // $user->teams()->attach($ids);

        $success['id'] =  $request->input('id');
        $success['name'] =  $request->input('name');
        $name = Auth::user()->name;
        $this->logtochannel('telegram','logs','info',"$name Updated User  Data");
        return $this->sendResponse($success, 'User updated successfully');
    }
    
    /**
    * @OA\Put(
    *     path="/profile",
    *     tags={"Profile"},
    *     summary="Updates a profile",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 type="object",
    *                 @OA\Property(property="email", type="string"),
    *                 @OA\Property(property="name", type="string"),
    *                 @OA\Property(property="password", type="string"),
    *                 example={
    *                     "name": "MyUser",
    *                     "email": "my-user@example.com",
    *                     "password": "12345"
    *                 }
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="OK",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(
    *                 property="data",
    *                 type="object",
    *                 @OA\Property(property="id", type="object", example=null),
    *                 @OA\Property(property="name", type="string", example="MyUser")
    *             ),
    *             @OA\Property(property="message", type="string", example="User profile updated successfully"),
    *             @OA\Property(property="success", type="boolean", example=true)
    *         )
    *     )
    * )
    */
    public function profileUpdate(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
    
        $input = $request->except(['profile_photo_url','profile_photo_url']);
        if(!empty($input['password'])){ 
            $input['password'] = bcrypt($input['password']);
        }
        // }else{
        //     $input = Arr::except($input,array('password'));    
        // }
    
        $user = User::where('id','=',$id=auth('sanctum')->user()->id);
        
        $user->update($input);
        $success['id'] =  $request->input('id');
        $success['name'] =  $request->input('name');
        return $this->sendResponse($success, 'User profile updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
    * @OA\Post(
    *     path="/users/delete",
    *     tags={"Users"},
    *     summary="Deletes a user",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 type="object",
    *                 @OA\Property(property="id", type="number"),
    *                 example={
    *                     "id": 30
    *                 }
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="OK",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(
    *                 property="data",
    *                 type="object",
    *                 @OA\Property(property="id", type="number", example=30)
    *             ),
    *             @OA\Property(property="message", type="string", example="User Deleted successfully"),
    *             @OA\Property(property="success", type="boolean", example=true)
    *         )
    *     )
    * )
    */
    public function DeleteUser(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'id' => 'required'
        ]);
        $id=$request->input('id');
        User::find($id)->delete();
        $success['id'] =  $id;
        $name = Auth::user()->name;
        $this->logtochannel('telegram','logs','info',"$name Deleted a User  ");
        return $this->sendResponse($success, 'User Deleted successfully');
    }


    public function getUserfromtoken(Request $request){
        $tokenrec = $request->input('user_token');
        [$id, $token] = explode('|', $tokenrec, 2);
        $tokenBD = DB::table('personal_access_tokens')->where('id', $id)->first();
        if (hash_equals($tokenBD->token, hash('sha256', $token))) {
            $data = User::where('id',$tokenBD->tokenable_id)->with('teams')->get();
            return $this->sendResponse($data, 'users data');
        }
    }
}
