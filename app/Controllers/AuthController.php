<?php
declare(strict_types=1);
namespace SellSoft\Controllers;
use SellSoft\Core\Controller;
use SellSoft\Services\AuthService;
use SellSoft\Helpers\Session;
use SellSoft\Helpers\Flash;
use SellSoft\Helpers\Csrf;
class AuthController extends Controller
{
    private $auth;
    public function __construct()
    {
        $this->auth = new AuthService();
        if (!Session::isAuthenticated()) $this->auth->restoreFromCookie();
    }
    public function showLogin(): void
    {
        if (Session::isAuthenticated()) $this->redirect(APP_URL . '/dashboard');
        $this->view('auth.login', [
            'title'    => 'Sign In — ' . APP_NAME,
            'csrf'     => Csrf::token(),
            'messages' => Flash::getAll(),
        ], 'auth');
    }
    public function processLogin(): void
    {
        Csrf::validateOrFail();
        $email    = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
        $password = $this->input('password', '');
        $remember = isset($_POST['remember']);
        if (empty($email) || empty($password)) {
            Flash::warning('Please fill in all fields.');
            $this->redirect(APP_URL . '/login');
        }
        $result = $this->auth->login($email, $password, $remember);
        if ($result['success']) {
            Flash::success($result['message']);
            $intended = Session::get('intended_url', APP_URL . '/dashboard');
            Session::delete('intended_url');
            $this->redirect($intended);
        } else {
            Flash::error($result['message']);
            $this->redirect(APP_URL . '/login');
        }
    }
    public function logout(): void
    {
        $this->auth->logout();
        Flash::info('You have been logged out successfully.');
        $this->redirect(APP_URL . '/login');
    }
    public function switchWarehouse(): void
    {
        if (!Session::isAuthenticated()) $this->json(['error' => 'Unauthenticated'], 401);
        Csrf::validateOrFail();
        $warehouseId = (int)$this->input('warehouse_id', 0);
        if ($warehouseId <= 0) $this->json(['error' => 'Invalid warehouse ID'], 400);
        Session::set('warehouse_id', $warehouseId);
        $this->json(['success' => true]);
    }
}
