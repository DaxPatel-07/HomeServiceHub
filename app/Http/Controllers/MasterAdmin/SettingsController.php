<?php
namespace App\Http\Controllers\MasterAdmin;
use App\Http\Controllers\Controller;

class SettingsController extends Controller {
    public function index() {
        return view('master-admin.settings');
    }
}
