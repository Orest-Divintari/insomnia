<?php

namespace App\Http\Controllers;

use App\ViewModels\AccountLikesViewModel;

class AccountLikeController extends Controller
{

    /**
     * Display a listing of the likes the user has received
     *
     * @param AccountLikesViewModel $viewModel
     * @return \Illuminate\View\View
     */
    public function index(AccountLikesViewModel $viewModel)
    {
        $likes = $viewModel->receivedLikes();

        return view('account.likes.index', compact('likes'));
    }
}