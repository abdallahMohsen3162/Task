<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchRandomUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        try {
            $response = Http::get('https://randomuser.me/api/');

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Random User API Results:', $data['results']);
            } else {
                Log::error('Failed to fetch random user data. Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Error fetching random user data: ' . $e->getMessage());
        }
    }
}
