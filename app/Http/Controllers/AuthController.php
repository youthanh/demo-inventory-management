<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validate = $this->validateRequest($request, 'register');
        if ($validate) return $validate;

        try {
            $userRegisted = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if (!empty($userRegisted->id)) {
                $response = [
                    'message' => 'Đăng ký thành công.'
                ];
                $statusCode = 200;
            } else {
                $response = [
                    'message' => 'Đăng ký thất bại.'
                ];
                $statusCode = 500;
            }
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                $response = [
                    'email' => 'Email đã tồn tại.',
                    'message' => 'Email đã tồn tại.',
                ];
                $statusCode = 409;
            } else {
                $response = [
                    'message' => 'Đăng ký thất bại.'
                ];
                $statusCode = 500;
            }
        }

        return response()->json($response, $statusCode);
    }

    public function login(Request $request)
    {
        try {
            $validate = $this->validateRequest($request);
            if ($validate) return $validate;

            $user = User::where('email', $request->email)->first();

            // var_dump($user);die;
            if (!$user || !Hash::check($request->password, $user->password)) {
                $response = [
                    'email' => true,
                    'password' => true,
                    'message' => 'Email hoặc Mật khẩu không chính xác.',
                ];
                $statusCode = 401;
                return response()->json($response, $statusCode);
            }

            $token = $user->createToken('authentication');
            if ($token->plainTextToken) {
                $response = [
                    'access_token' => $token->plainTextToken,
                    'token_type' => 'Bearer',
                    'message' => 'Đăng nhập thành công',
                ];
                $statusCode = 200;
            } else {
                $response = [
                    'message' => 'Đăng nhập thất bại.',
                ];
                $statusCode = 500;
            }
        } catch (\Exception $error) {
            $response = [
                'message' => 'Error in Login',
                'error' => $error,
            ];
            $statusCode = 500;
        }
        return response()->json($response, $statusCode);
    }

    public function logout()
    {
        $result = auth()->user()->currentAccessToken()->delete();

        if ($result) {
            $response = [
                'message' => 'Đăng xuất thành công.',
            ];
            $statusCode = 200;
        } else {
            $response = [
                'message' => 'Đăng xuất thất bại.',
            ];
            $statusCode = 500;
        }
        return response()->json($response, $statusCode);
    }

    private function validateRequest(Request $request, string $action = 'login')
    {
        $validate = [
            'email' => 'email|required',
            'password' => 'required',
        ];
        if ($action == 'register') {
            $validate['name'] = 'required';
        }

        $message = [
            'name.required' => 'Tên không thể bỏ trống.',
            'email.email' => 'Định dạng email không hợp lệ.',
            'email.required' => 'Email không thể bỏ trống.',
            'password.required' => 'Password không thể bỏ trống.',
        ];

        $validate = Validator::make($request->all(), $validate, $message);
        if ($validate->fails()) {
            $message = [];
            $arrError = $validate->errors()->getMessages();
            foreach ($arrError as $errorFfield => $value) {
                $arrError[$errorFfield] = $value[0];
                $message[] = $value[0];
            }
            $response = $arrError;
            $response['message'] = implode("\n", $message);
            return response()->json($response, 400);
        }
    }
}
