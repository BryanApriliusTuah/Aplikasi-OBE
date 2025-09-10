<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Kalau belum login
        if (!$session->get('logged_in')) {
            if ($request->isAJAX()) {
                return service('response')->setStatusCode(401)->setBody('Unauthorized');
            }
            return redirect()->to('/auth/login');
        }

        // Session timeout 30 menit
        if ($session->has('last_activity') && (time() - $session->get('last_activity') > 1800)) {
            $session->destroy();
            return redirect()->to('/auth/login')->with('error', 'Sesi Anda telah berakhir. Silakan login ulang.');
        }
        $session->set('last_activity', time());

        //  Tambahan: Khusus akses fitur admin 
        // Kalau filter dipakai dengan ['auth:admin'] pada route
        if ($arguments && in_array('admin', $arguments)) {
            if ($session->get('role') !== 'admin') {
                return redirect()->to('/rps')->with('error', 'Hanya admin yang bisa akses halaman ini.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}
