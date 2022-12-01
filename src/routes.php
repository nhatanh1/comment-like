<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

Route::post('comments', Config::get('comments.controller') . '@store')->name('comments.store');
Route::delete('comments/{comment}', Config::get('comments.controller') . '@destroy')->name('comments.destroy');
Route::put('comments/{comment}', Config::get('comments.controller') . '@update')->name('comments.update');
Route::post('comments/{comment}', Config::get('comments.controller') . '@reply')->name('comments.reply');
Route::post('/like-comments', [Config::get('comments.likecontroller'), 'like'])->name('like.store');

Route::get('/like-total/{id}', [Config::get('comments.likecontroller'), 'like_total'])->name('like.total');

Route::get('/check-like/{id}', [Config::get('comments.likecontroller'), 'check_like'])->name('like.check');

Route::get('/like-all/{id}', [Config::get('comments.likecontroller'), 'like_all'])->name('like.all');

Route::get('/user-like/{id}', [Config::get('comments.likecontroller'), 'user_like'])->name('like.user');