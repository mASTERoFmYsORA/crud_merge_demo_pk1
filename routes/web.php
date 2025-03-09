<?php

use App\Http\Controllers\ContactCustomFieldController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
Route::get('/contacts/create', [ContactController::class, 'create'])->name('contacts.create');
Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
Route::get('/contacts/{id}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
Route::post('/contacts/{id}/update', [ContactController::class, 'update'])->name('contacts.update');
Route::delete('contacts/{id}', [ContactController::class, 'destroy'])->name('contacts.destroy');

Route::post('/add-field', [ContactCustomFieldController::class, 'addField'])->name('add-field');
Route::get('/get-index-data', [ContactController::class, 'getIndexData'])->name('contacts.get-index-data');
// Route::resource('contacts', ContactController::class);

Route::get('/get-contacts', [ContactController::class, 'getContacts']);
Route::post('/merge-contacts', [ContactController::class, 'mergeContacts'])->name('contacts.merge-data');
