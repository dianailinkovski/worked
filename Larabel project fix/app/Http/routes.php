<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use App\Task;
//use App\Template;
use Illuminate\Http\Request;


//Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);


//***************************************
// previous task
/*
Route::get('/', function () {
    $tasks = Task::orderBy('created_at', 'asc')->get();
	
    return view('tasks', [
        'tasks' => $tasks
    ]);
});
*/   
   



Route::post('/task', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'name' => 'required|max:255',
    ]);

    if ($validator->fails()) {
        return redirect('/')
            ->withInput()
            ->withErrors($validator);
    }

    $task = new Task;
    $task->name = $request->name;
	
	$task->searchVal = env("search");
	
    $task->save();

    return redirect('/');
});

Route::delete('/task/{id}', function ($id) {
    Task::findOrFail($id)->delete();

    return redirect('/');
});

//***************************************

//New  Task controller name is TestController
Route::get('/', function () {
    $tasks = Task::orderBy('created_at', 'asc')->get();
	
    return view('test', [
        'tasks' => $tasks
    ]);
});





