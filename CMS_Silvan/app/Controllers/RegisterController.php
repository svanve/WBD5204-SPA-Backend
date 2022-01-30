<?php

namespace App\Controllers;

use App\Models\FormValidation;
use App\Models\Session;
use App\Models\Email;
use App\Interfaces\BaseController;
use App\Request;
use App\Traits\RouteGuards\GuestOnly;

class RegisterController extends BaseController {
    use GuestOnly;

    public function index(Request $request)
    {
        
        if (!$request->hasInput()) {
            return $this->renderView('register');
        }

        $userData = $request->getInput();

        $validation = new FormValidation($this->db, $userData);

        $validation->setRules([
            'alias' => 'required|min:5|max:32|available:users',
            'email' => 'required|email|available:users',
            'password' => 'required|min:5',
            'passwordAgain' => 'required|matches:password'
        ]);

        $validation->validate();
        
        if ($validation->fails()) {
            return $this->renderView('register', [
                'errors' => $validation->getErrors()
            ]);
        }

        $this->user->register(
            ...array_values($request->only(
                'firstName',
                'lastName',
                'alias',
                'email',
                'password'
            ))
        );

        try {
            $this->user->login($userData['email'], $userData['password']);
            Session::flash('message', 'Du wurdest erfolgreich registriert und bist nun eingeloggt.');
            $this->redirect('/flat');
        } catch (Exception $e) {
            $this->renderView('login', [
                'errors' => [
                    'root' => $e->getMessage()
                ]
                ]);
        }

        Session::get('message', 'Dein Account wurde erfolgreich erstellt.');
        Session::flash('message');
    }
}