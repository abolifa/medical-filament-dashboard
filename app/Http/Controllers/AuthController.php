<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:patients,phone',
            'national_id' => 'required|string|unique:patients,national_id',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $patient = Patient::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'national_id' => $data['national_id'],
            'password' => $data['password'],
        ]);

        $token = $patient->createToken('auth_token')->plainTextToken;

        return response()->json([
            'patient' => $patient,
            'token' => $token,
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $patient = Patient::where('phone', $data['phone'])->first();

        if (!$patient) {
            throw ValidationException::withMessages([
                'message' => ['رقم الهاتف غير مسجل بالنظام'],
            ]);
        }

        if (!Hash::check($data['password'], $patient->password)) {
            throw ValidationException::withMessages([
                'message' => ['كلمة المرور غير صحيحة'],
            ]);
        }

        $token = $patient->createToken('auth_token')->plainTextToken;

        return response()->json([
            'patient' => $patient,
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
