<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    public function index()
    {
        return Cache::remember('stats', now()->addMinutes(60), function () {
            $totalUsers = User::count();
            $totalPosts = Post::count();
            $usersWithNoPosts = User::has('posts', '=', 0)->count();

            return [
                'total_users' => $totalUsers,
                'total_posts' => $totalPosts,
                'users_with_no_posts' => $usersWithNoPosts,
            ];
        });
    }
}
