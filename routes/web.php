<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\StatusController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('tickets', TicketController::class)
    ->only(['index', 'store', 'edit', 'update', 'destroy'])
    ->middleware(['auth', 'verified']);

Route::resource('comments', CommentController::class)
    ->only(['index', 'store', 'edit', 'update', 'destroy'])
    ->middleware(['auth', 'verified']);

Route::resource('categories', CategoryController::class)
    ->only(['index', 'store', 'edit', 'update', 'destroy'])
    ->middleware(['auth', 'verified']);

Route::resource('statuses', StatusController::class)
    ->only(['index', 'store', 'edit', 'update', 'destroy'])
    ->middleware(['auth', 'verified']);

Route::get('my_tickets', [TicketController::class,'userTickets'])->name('my_tickets');
Route::get('new_ticket', [App\Http\Controllers\TicketController::class,'create'])->name('new_ticket');
Route::post('new_ticket', [App\Http\Controllers\TicketController::class,'store']);

Route::get('tickets/{ticket_id}', [App\Http\Controllers\TicketController::class, 'show']);
Route::post('comment', [App\Http\Controllers\CommentController::class, 'postComment']);
Route::post('admincomment', [App\Http\Controllers\CommentController::class, 'postAdminComment']);

Route::get('admin_tickets', [App\Http\Controllers\TicketController::class, 'adminTickets']);
Route::post('ticket_status/{ticket_id}', [App\Http\Controllers\TicketController::class, 'status']);

require __DIR__.'/auth.php';
