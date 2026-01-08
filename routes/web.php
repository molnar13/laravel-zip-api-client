use App\Http\Controllers\SettlementController;

// Publikus szűrés
Route::get('/settlements/filter', [SettlementController::class, 'filterView'])->name('settlements.filter');

// Alap CRUD (néhányat védhetünk session alapján)
Route::resource('settlements', SettlementController::class);