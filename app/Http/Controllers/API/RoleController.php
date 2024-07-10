<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use DB;
use Validator;
use Auth;


class RoleController extends BaseController
{
    function __construct()
    {
        //  $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
        //  $this->middleware('permission:role-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:role-delete', ['only' => ['destroy']]);
         setPermissionsTeamId('1');
    }

    /**
    * @OA\Get(
    *     path="/roles/{id}",
    *     tags={"Roles"},
    *     summary="Get list of roles or 1 Role",
    *     @OA\Parameter(
    *         in="path",
    *         name="id",
    *         required=true,
    *         @OA\Schema(
    *             type="string",
    *             example="1"
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation. If requested without ID, the *data* field will contain an array of objects",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(
    *                 property="data",
    *                 type="object",
    *                 @OA\Property(property="created_at", type="string", example="2024-03-20T00:17:37.000000Z"),
    *                 @OA\Property(property="guard_name", type="string", example="api"),
    *                 @OA\Property(property="id", type="number", example=1),
    *                 @OA\Property(property="name", type="string", example="Super Admin"),
    *                 @OA\Property(
    *                     property="permissions",
    *                     type="array",
    *                     @OA\Items(
    *                         type="object",
    *                         @OA\Property(property="created_at", type="string", example="2024-03-20T00:17:37.000000Z"),
    *                         @OA\Property(property="guard_name", type="string", example="api"),
    *                         @OA\Property(property="id", type="number", example=1),
    *                         @OA\Property(property="name", type="string", example="Create User"),
    *                         @OA\Property(
    *                             property="pivot",
    *                             type="object",
    *                             @OA\Property(property="permission_id", type="number", example=1),
    *                             @OA\Property(property="role_id", type="number", example=1)
    *                         ),
    *                         @OA\Property(property="updated_at", type="string", example="2024-03-20T00:17:37.000000Z")
    *                     )
    *                 ),
    *                 @OA\Property(property="team_id", type="number", example=1),
    *                 @OA\Property(property="updated_at", type="string", example="2024-03-20T00:17:37.000000Z")
    *             ),
    *             @OA\Property(property="message", type="string", example="roles data"),
    *             @OA\Property(property="success", type="boolean", example=true)
    *         )
    *     )
    * )
    */
    public function Show($id=null)
    {
        // dd(Auth::guard());
        if(!isset($id)){
            $roles = Role::where('guard_name','=','api')->with('permissions')->get();
            // $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            // ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            // ->all();
        }else{
            $roles = Role::where('id',$id)->with('permissions')->first();
        }


        return $this->sendResponse($roles, 'roles data');
        // $roles = Role::orderBy('id','DESC')->paginate(5);    
        // return view('roles.index',compact('roles'))
        //     ->with('i', ($request->input('page', 1) - 1) * 5);
    }


    /**
    * @OA\Post(
    *     path="/roles",
    *     tags={"Roles"},
    *     summary="Creates a Role",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 type="object",
    *                 @OA\Property(property="name", type="string"),
    *                 @OA\Property(
    *                     property="permissions",
    *                     type="array",
    *                     @OA\Items(
    *                         type="string"
    *                     )
    *                 ),
    *                 example={
    *                     "name": "New Role",
    *                     "permissions": {
    *                         "Create User"
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
    *                 @OA\Property(property="name", type="string", example="New Role")
    *             ),
    *             @OA\Property(property="message", type="string", example="Role created successfully"),
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
    public function Create(Request $request)    
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name',
            'permissions'=>'array',
            'permissions' => 'required|exists:permissions,name',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $role = new Role();
        $role->name = $request->input('name');
        $role->guard_name = "api";
        $role->team_id = 1;
        $role->save();
        $permissions = $request->input('permissions');
        if (is_array($permissions)) {
            $role->syncPermissions($permissions);
        } else {
            return $this->sendError("Invalid permissions format. It must be an array");
        }
        $role->syncPermissions($request->input('permissions'));
        $success['name'] =  $request->input('name');


        return $this->sendResponse($success, 'Role created successfully');

    }

    /**
    * @OA\Put(
    *     path="/roles",
    *     tags={"Roles"},
    *     summary="Updates a Role",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 type="object",
    *                 @OA\Property(property="id", type="number"),
    *                 @OA\Property(property="name", type="string"),
    *                 @OA\Property(
    *                     property="permissions",
    *                     type="array",
    *                     @OA\Items(
    *                         type="string"
    *                     )
    *                 ),
    *                 example={
    *                     "name": "New Role",
    *                     "permissions": {
    *                         "Manage Roles",
    *                         "Create User"
    *                     },
    *                     "id": 20
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
    *                 @OA\Property(property="name", type="string", example="New Role")
    *             ),
    *             @OA\Property(property="message", type="string", example="Role Updated successfully"),
    *             @OA\Property(property="success", type="boolean", example=true)
    *         )
    *     )
    * )
    */
    public function UpdateRole(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'permissions' => 'required|exists:permissions,name',
        ]);
    
        $role = Role::where('id',$request->input('id'));
        $role->name = $request->input('name');
        $role->save();
    
        $role->syncPermissions($request->input('permissions'));
        $success['name'] =  $request->input('name');
        $name = Auth::user()->name;
        $this->logtochannel('telegram','user_actions','info',"$name updated a Role ");
        return $this->sendResponse($success, 'Role Updated successfully');

    }

    /**
    * @OA\Post(
    *    path="/roles/delete",
    *    tags={"Roles"},
    *    summary="Deletes a Role",
    *    @OA\RequestBody(
    *        @OA\MediaType(
    *            mediaType="application/json",
    *            @OA\Schema(
    *                type="object",
    *                required={"id"},
    *                @OA\Property(property="id", type="number"),
    *                example={
    *                    "id": 27
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
    *                @OA\Property(property="id", type="number", example=27)
    *            ),
    *            @OA\Property(property="message", type="string", example="Role Deleted successfully"),
    *            @OA\Property(property="success", type="boolean", example=true)
    *        )
    *    )
    * )
    */
    public function DeleteRole(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'id' => 'required'
        ]);
        $id=$request->input('id');
        Role::where('id',$id)->delete();
        $success['id'] =  $id;


        return $this->sendResponse($success, 'Role Deleted successfully');
    }

}
