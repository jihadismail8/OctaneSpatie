<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Validator;
use Auth;
use Illuminate\Support\Facades\Log;
class PermissionController extends BaseController
{
    function __construct()
    {
        //  $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
        //  $this->middleware('permission:role-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:role-delete', ['only' => ['destroy']]);
        setPermissionsTeamId('1');
        Log::info("requested permissions");
    }

    /**
    * @OA\Get(
    *     path="/permissions/{id}",
    *     tags={"Permissions"},
    *     summary="Getlist of permissions or 1 Permission",
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
    *                 @OA\Property(property="name", type="string", example="Create User"),
    *                 @OA\Property(
    *                     property="roles",
    *                     type="array",
    *                     @OA\Items(
    *                         type="object",
    *                         @OA\Property(property="created_at", type="string", example="2024-03-20T00:17:37.000000Z"),
    *                         @OA\Property(property="guard_name", type="string", example="api"),
    *                         @OA\Property(property="id", type="number", example=1),
    *                         @OA\Property(property="name", type="string", example="Super Admin"),
    *                         @OA\Property(
    *                             property="pivot",
    *                             type="object",
    *                             @OA\Property(property="permission_id", type="number", example=1),
    *                             @OA\Property(property="role_id", type="number", example=1)
    *                         ),
    *                         @OA\Property(property="team_id", type="number", example=1),
    *                         @OA\Property(property="updated_at", type="string", example="2024-03-20T00:17:37.000000Z")
    *                     )
    *                 ),
    *                 @OA\Property(property="updated_at", type="string", example="2024-03-20T00:17:37.000000Z")
    *             ),
    *             @OA\Property(property="message", type="string", example="Permissions data"),
    *             @OA\Property(property="success", type="boolean", example=true)
    *         )
    *     )
    * )
    */
    public function Show($id=null)
    {
        if(!isset($id)){
            $roles = Permission::where('guard_name','=','api')->with('roles')->get();

        }else{
            $roles = Permission::where('id',$id)->with('roles')->first();
        }



        return $this->sendResponse($roles, 'Permissions data');
    }


    /**
    * @OA\Post(
    *     path="/permissions",
    *     tags={"Permissions"},
    *     summary="Creates a Permission",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 type="object",
    *                 @OA\Property(property="name", type="string"),
    *                 @OA\Property(
    *                     property="roles",
    *                     type="array",
    *                     @OA\Items(
    *                         type="string"
    *                     )
    *                 ),
    *                 example={
    *                     "name": "test permission",
    *                     "roles": {
    *                         "Super Admin"
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
    *                 @OA\Property(property="name", type="string", example="test permission")
    *             ),
    *             @OA\Property(property="message", type="string", example="Permission created successfully"),
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
            'roles' => 'required|exists:roles,name',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $role = Permission::create(['name' => $request->input('name'),'guard_name'=>'api']);
        $role->syncRoles($request->input('roles'));
        $role2 = Permission::create(['name' => $request->input('name'),'guard_name'=>'web']);
        $role2->syncRoles($request->input('roles'));
        $success['name'] =  $request->input('name');


        return $this->sendResponse($success, 'Permission created successfully');
    }

    /**
    * @OA\Put(
    *     path="/permissions",
    *     tags={"Permissions"},
    *     summary="Updates a Permission",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 type="object",
    *                 @OA\Property(property="id", type="number"),
    *                 @OA\Property(property="name", type="string"),
    *                 @OA\Property(
    *                     property="roles",
    *                     type="array",
    *                     @OA\Items(
    *                         type="string"
    *                     )
    *                 ),
    *                 example={
    *                     "name": "test permission",
    *                     "roles": {
    *                         "Super Admin",
    *                         "User"
    *                     },
    *                     "id": 26
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
    *                 @OA\Property(property="name", type="string", example="test permission")
    *             ),
    *             @OA\Property(property="message", type="string", example="Permission Updated successfully"),
    *             @OA\Property(property="success", type="boolean", example=true)
    *         )
    *     )
    * )
    */
    public function UpdatePermission(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'roles' => 'required|exists:roles,name',
        ]);
    
        $role = Permission::where('id',$request->input('id'));
        $role->name = $request->input('name');
        $role->save();
    
        $role->syncRoles($request->input('roles'));
    
        $success['name'] =  $request->input('name');


        return $this->sendResponse($success, 'Permission Updated successfully');
    }

    /**
    * @OA\Post(
    *    path="/permissions/delete",
    *    tags={"Permissions"},
    *    summary="Deletes a permission",
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
    *            @OA\Property(property="message", type="string", example="Permission Deleted successfully"),
    *            @OA\Property(property="success", type="boolean", example=true)
    *        )
    *    )
    * )
    */
    public function DeletePermission(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'id' => 'required'
        ]);
        $id=$request->input('id');
        Permission::where('id',$id)->delete();
        $success['id'] =  $id;


        return $this->sendResponse($success, 'Permission Deleted successfully');
    }

}
