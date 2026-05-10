<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ValidateJwtToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan, silakan login terlebih dahulu'
            ], 401);
        }

        try {
            $secret = 'INISECRET#KHUSUSTESTINGAJAHFWMA>FA403t3womwjogh';
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));

            // Cek role harus admin-keuangan
            if ($decoded->role_name !== 'admin-keuangan') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak, role tidak sesuai'
                ], 403);
            }

            // Simpan data user ke request
            $request->merge(['auth_user' => (array) $decoded]);

            return $next($request);

        } catch (\Firebase\JWT\ExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token sudah expired, silakan login ulang'
            ], 401);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid'
            ], 401);
        }
    }
}