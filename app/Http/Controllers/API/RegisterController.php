<?php

   

namespace App\Http\Controllers\API;

   

use Illuminate\Http\Request;

use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\User;

use Illuminate\Support\Facades\Auth;

use Validator;
use Illuminate\Support\Facades\Hash;

   

class RegisterController extends BaseController

{

    /**

     * Register api

     *

     * @return \Illuminate\Http\Response

     */

    public function register(Request $request)

    {

        $validator = Validator::make($request->all(), [

            'name' => 'required',

            'email' => 'required|email',

            'password' => 'required',


        ]);

   

        if($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors());       

        }

   

        $input = $request->all();

        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);

        $success['token'] =  $user->createToken('MyApp')->plainTextToken;

        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User register successfully.');

    }

   

    /**

     * Login api

     *

     * @return \Illuminate\Http\Response
     *
     **/
    /**
    * @OA\Post(
    *    path="/login",
    *    tags={"Login"},
    *    summary="Login",
    *    @OA\RequestBody(
    *        @OA\MediaType(
    *            mediaType="application/json",
    *            @OA\Schema(
    *                type="object",
    *                @OA\Property(
    *                    property="email",
    *                    type="string"
    *                ),
    *                @OA\Property(
    *                    property="password",
    *                    type="string"
    *                ),
    *                example={
    *                    "email": "my-user@example.com",
    *                    "password": "Password12345"
    *                }
    *            )
    *        )
    *    ),
    *    @OA\Response(
    *        response=200,
    *        description="Valid credentials",
    *        @OA\JsonContent(
    *            type="object",
    *            @OA\Property(
    *                property="data",
    *                type="object",
    *                @OA\Property(
    *                    property="name",
    *                    type="string",
    *                    example="MyUser"
    *                ),
    *                @OA\Property(
    *                    property="permissions",
    *                    type="array",
    *                    @OA\Items(
    *                        type="string",
    *                        example="Create User"
    *                    )
    *                ),
    *                @OA\Property(
    *                    property="roles",
    *                    type="array",
    *                    @OA\Items(
    *                        type="string",
    *                        example="Super Admin"
    *                    )
    *                ),
    *                @OA\Property(
    *                    property="token",
    *                    type="string",
    *                    example="233|EdMAy51xKVHT98aRe4bWqe60P0Rtu9OpOY9uPDQl"
    *                )
    *            ),
    *            @OA\Property(
    *                property="message",
    *                type="string",
    *                example="User login successfully."
    *            ),
    *            @OA\Property(
    *                property="success",
    *                type="boolean",
    *                example=true
    *            )
    *        )
    *    ),
    *    @OA\Response(
    *        response=404,
    *        description="Invalid credentials",
    *        @OA\JsonContent(
    *            type="object",
    *            @OA\Property(
    *                property="message",
    *                type="array",
    *                @OA\Items(
    *                    type="string",
    *                    example="These credentials do not match our records."
    *                )
    *            )
    *        )
    *    )
    * )
    */
    public function login(Request $request)

    {
        setPermissionsTeamId('1');
        // dd(Auth::guard('web')->check());
        if(Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])){ 

            $user = Auth::guard('web')->user();

            $success['token'] =  $user->createToken('auth_token')->plainTextToken; 

            $success['name'] =  $user->name;
            $success['roles']= $user->getRoleNames();
            $success['permissions'] = $user->getPermissionsViaRoles()->pluck('name');
            return $this->sendResponse($success, 'User login successfully.');

        }elseif(Auth::guard('api')){ 

            $user= User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response([
                    'message' => ['These credentials do not match our records.']
                ], 404);
            }
        
            $success['token'] = $user->createToken('auth_token')->plainTextToken;
        
            $success['name'] =  $user->name;
            $success['roles']= $user->getRoleNames();
            $success['permissions'] = $user->getPermissionsViaRoles()->pluck('name');
            return $this->sendResponse($success, 'User login successfully.');
        } 

        else{ 

            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);

        } 

    }

}