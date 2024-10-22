<?php declare(strict_types = 1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\LoginUserRequest;
use App\Http\Requests\API\StoreUserRequest;
use App\Models\User;
use App\Traits\ErrorHandlerTrait;
use App\Traits\HttpResponsesTrait;
use App\Traits\InputHandlingTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Traits
    use HttpResponsesTrait, ErrorHandlerTrait, InputHandlingTrait;
    
    // Methods
    /**
     * Method for logging in
    */
    public function Login(LoginUserRequest $request): JsonResponse
    {
        try {
            $request->validated($request->all());
    
            // Executed for invalid credentials
            if(!Auth::attempt($request->only('email', 'password'))) {
                return $this->error(null, 'Invalid Credentials. Please try again.', 401);
            }

            // Executed otherwise
            $user = Auth::user();
    
            return $this->success([
                'user' => $user,
                'token' => $user
                    ->createToken('TOKEN-USER-'.$user->id)
                    ->plainTextToken,
            ]);
        } catch(\Throwable $e) {
            return $this->handleThrowable($e);
        }
    } 

    /**
     * Method for registering a new user
    */
    public function Register(StoreUserRequest $request): JsonResponse|array
    {
        try {
            $request->validated($request->all());

            $stringValues = $this->sanitizeString($request->except(['email']));
            $stringValues['password'] = Hash::make($stringValues['password']);
            $emailValue = $this->sanitizeEmail($request->only(['email']));
            $values = array_merge($stringValues, $emailValue);

            $record = User::create($values);
    
            return $this->success([
                'user' => $record,
                'token' => $record
                    ->createToken('TOKEN-USER-'.$record->id)
                    ->plainTextToken,
            ]);
        } catch(\Throwable $e) {
            return $this->handleThrowable($e);
        }
    }

    /**
     * Method for logging out a user
    */
    public function logout(): JsonResponse
    {
        Auth::user()
            ->currentAccessToken()
            ->delete();
        
        return $this->success([
            'message' => 'User logged out successfully.',
        ]);
    } 
}
