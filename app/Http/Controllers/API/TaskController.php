<?php declare(strict_types = 1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreTaskRequest;
use App\Http\Requests\API\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Traits\ErrorHandlerTrait;
use App\Traits\HttpResponsesTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{   
    // Traits
    use HttpResponsesTrait, ErrorHandlerTrait;

    // Methods
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        try {    
            return TaskResource::collection(
                Task::where('user_id', Auth::id())
                    ->get()
            );        
        } catch(\Throwable $e) {
            return $this->handleThrowable($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): TaskResource|JsonResponse
    {
        try {    
            $request->validated($request->all());

            $task = Auth::user()
                ->tasks()
                ->create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'priority' => $request->priority,
                ]);
    
            // Returns the created collection as JSON
            return new TaskResource($task);
        } catch(\Throwable $e) {
            return $this->handleThrowable($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): TaskResource|JsonResponse
    {
        try {
            $this->authorize('view', $task);
    
            return new TaskResource($task);   
        } catch(\Throwable $e) {
            return $this->handleThrowable($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): TaskResource|JsonResponse
    {
        try {
            $this->authorize('update', $task);
    
            $request->validated($request->all());
            $task->update($request->all());
    
            return new TaskResource($task);    
        } catch(\Throwable $e) {
            return $this->handleThrowable($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): Response|JsonResponse
    {
        try {
            $this->authorize('delete', $task);
    
            $task->delete();
            return response(null, 204);
        } catch(\Throwable $e) {
            return $this->handleThrowable($e);
        }
    }
}
