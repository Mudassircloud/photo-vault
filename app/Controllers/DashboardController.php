<?php

namespace App\Controllers;

use App\Models\PhotoModel;
use CodeIgniter\Controller;

class DashboardController extends Controller
{
    protected $photoModel;

    public function __construct()
    {
        $this->photoModel = new PhotoModel();
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $photos = $this->photoModel->where('user_id', session()->get('user_id'))->findAll();

        $photoDisplay = [];
        foreach ($photos as $photo) {
            $filePath = WRITEPATH . 'uploads/' . $photo['filename'];
            $decrypted = $this->decryptFile($filePath);

            if ($decrypted !== null) {
                $mimeType = mime_content_type($filePath) ?: 'image/jpeg';
                $photo['thumbnail'] = 'data:' . $mimeType . ';base64,' . base64_encode($decrypted);
            }

            $photoDisplay[] = $photo;
        }

        return view('dashboard/index', ['photos' => $photoDisplay]);
    }

    public function upload()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $file = $this->request->getFile('photo');

        if ($file && $file->isValid()) {
            $fileContent = file_get_contents($file->getTempName());

            $vaultKey = hex2bin(session()->get('vault_key'));
            $iv = random_bytes(16);
            $tag = '';

            $encrypted = openssl_encrypt(
                $fileContent,
                'aes-256-gcm',
                $vaultKey,
                OPENSSL_RAW_DATA,
                $iv,
                $tag
            );

            $randomFileName = bin2hex(random_bytes(16)) . '.enc';
            file_put_contents(WRITEPATH . 'uploads/' . $randomFileName, $iv . $tag . $encrypted);

            $this->photoModel->save([
                'user_id' => session()->get('user_id'),
                'filename' => $randomFileName,
                'original_name' => $file->getName(),
            ]);

            return redirect()->to('/dashboard')->with('success', 'Photo uploaded successfully.');
        }

        return redirect()->to('/dashboard')->with('error', 'No file selected or invalid.');
    }

    public function download($id)
    {
        $photo = $this->photoModel->where('id', $id)
            ->where('user_id', session()->get('user_id'))
            ->first();

        if (!$photo) {
            return redirect()->to('/dashboard')->with('error', 'Photo not found.');
        }

        $filePath = WRITEPATH . 'uploads/' . $photo['filename'];
        $decrypted = $this->decryptFile($filePath);

        if ($decrypted === null) {
            return redirect()->to('/dashboard')->with('error', 'Unable to decrypt file.');
        }

        return $this->response->download($photo['original_name'], $decrypted);
    }

    private function decryptFile(string $filePath): ?string
    {
        if (!file_exists($filePath)) {
            return null;
        }

        $data = file_get_contents($filePath);
        $iv = substr($data, 0, 16);
        $tag = substr($data, 16, 16);
        $encryptedContent = substr($data, 32);

        $vaultKey = hex2bin(session()->get('vault_key'));

        return openssl_decrypt(
            $encryptedContent,
            'aes-256-gcm',
            $vaultKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
    }
}
