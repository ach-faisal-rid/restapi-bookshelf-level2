<?php

namespace controllers;

require_once __DIR__ . "/../../model/users.php";
use Model\Users;


require_once __DIR__ . "/../../config/TokenJwt.php";
use Config\TokenJwt;

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
        $users = new Users();
        $validasi_email = $users->findEmail($request['email']);
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
        $result = $users->registrasi($form_data);

        // response
        if ($result) {
            // key password tidak perlu di response
            unset($result['password']);
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

    public function login () {
        // menerima request dari client content-type JSON
        $request = json_decode(file_get_contents('php://input'), true);

        // Validasi input   
        if (empty($request['email']) || empty($request['password'])) {
            echo json_encode(['message' => 'Data tidak lengkap harus diisi']);
            http_response_code(400);
            exit();
        } 

        // validasi email yang SAMA
        $model_users = new Users();
        $verifikasi_email = $model_users->findEmail($request['email']);
        if ($verifikasi_email === false) {
            echo json_encode(['message' => 'login gagal, cek email dan password']);
            http_response_code(400);
            exit();
        }

        // Verifikasi password
        $verifikasi_password = password_verify($request['password'], $verifikasi_email['password']);
        if ($verifikasi_email === false) {
            echo json_encode(['message' => 'login gagal, cek email dan password']);
            http_response_code(400);
            exit();
        }

        // Password cocok, buat token auth (misalnya JWT atau token sederhana)
        $library_token = new TokenJwt();
        $token_baru = $library_token->create($verifikasi_email['id']);
        
        // hapus key password
        unset($verifikasi_email['password']);

        // Kirim respons sukses dengan token
        http_response_code(200);
        echo json_encode([
            'data' => $verifikasi_email,
            'message' => 'login berhasil',
            'token' => $token_baru,
        ]);
        exit();
    }
}