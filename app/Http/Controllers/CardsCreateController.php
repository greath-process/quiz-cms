<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class CardsCreateController extends Controller
{
    public function __invoke(): View
    {
        return view('cards.create');
    }
}
