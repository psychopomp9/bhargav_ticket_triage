<?php
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\StatsController;

// Rate limit for classification
RateLimiter::for('classify', function (Request $request) {
    $max = (int) env('OPENAI_CLASSIFY_RATE_PER_MINUTE', 30);
    return \Illuminate\Cache\RateLimiting\Limit::perMinute($max)->by('classify-global');
});


Route::prefix('tickets')->group(function () {
    Route::post('/', [TicketController::class, 'store']);
    Route::get('/', [TicketController::class, 'index']);
    Route::get('/{id}', [TicketController::class, 'show']);
    Route::patch('/{id}', [TicketController::class, 'update']);
    Route::middleware('throttle:classify')->post('/{id}/classify', [TicketController::class, 'classify']);
});

Route::get('/stats', [StatsController::class, 'index']);