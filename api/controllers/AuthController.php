<?php

namespace controllers;

require_once __DIR__ . "/../../model/users.php";

use Model\Users;

class AuthController
{
    public function registrasi() {
        // menerima request dari client content-type JSON
        $request = json_decode(file_get_contents('php://input'), true);

        // validasi request client
        if (empty($request['name']) || empty($request['email']) || empty($request['password'])) {
            echo json_encode(['message' => 'Data tidak lengkap harus diisi']);
            http_response_code(400);
            exit();
        }

        // validasi email
        $model_users = new Users();
        $validasi_email = $model_users->findEmail($request['email']);
        if (is_array($validasi_email)) {
            echo json_encode(['message' => 'Email sudah digunakan']);
            http_response_code(409); // Conflict
            exit();
        }

        // enkripsi password
        $hashedPassword = password_hash($request['password'], PASSWORD_DEFAULT);
        $form_data = [
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => $hashedPassword
        ];

        // simpan data
        $result = $model_users->registrasi($form_data);

        // response
        if ($result) {
            unset($result);
            
            echo json_encode([
                'message' => 'Registrasi berhasil',
                'data' => $result
            ]);
            http_response_code(201); // Created
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['message' => 'Registrasi gagal, terjadi kesalahan pada database saat proses registrasi']);
        }
    }
}