<?php

namespace App\Http\Controllers;

use App\ViewModels\AccountLikesViewModel;

class AccountLikeController extends Controller
{

    public function index(AccountLikesViewModel $viewModel)
    {
        $likes = $viewModel->receivedLikes();

        return view('account.likes.index', compact('likes'));
    }
}