<?php declare(strict_types = 1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreTaskRequest;
use App\Http\Requests\API\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Traits\ErrorHandlerTrait;
use App\Traits\HttpResponsesTrait;
use App\Traits\InputHandlingTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{   
    // Traits
    use HttpResponsesTrait, ErrorHandlerTrait, InputHandlingTrait;

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
    public function store(StoreTaskRequest $request): TaskResource|JsonResponse|array
    {
        try {    
            $request->validated($request->all());
            $values = $this->sanitizeString($request->all());

            $task = Auth::user()
                ->tasks()
                ->create($values);
    
            // Returns the created collection as JSON
            return new TaskResource($task);
        } catch(\Throwable $e) {
            return $this->handleThrowable($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): TaskResource|JsonResponse
    {
        try {
            $task = Task::find($id);

            // Executed if there are records
            if(!empty($task)) {
                $this->authorize('view', $task);
                return new TaskResource($task);
            }

            // Executed otherwise
            return $this->error(null, 'No record found.', 404);
        } catch(\Throwable $e) {
            return $this->handleThrowable($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, int $id): TaskResource|JsonResponse
    {
        try {
            $task = Task::find($id);

            // Executed if there are records
            if(!empty($task)) {
                $this->authorize('update', $task);
        
                $request->validated($request->all());
                $values = $this->sanitizeString($request->all());
                $task->update($values);
        
                return new TaskResource($task);    
            }           
            
            // Executed otherwise
            return $this->error(null, 'No record found.', 404);
        } catch(\Throwable $e) {
            return $this->handleThrowable($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): Response|JsonResponse
    {
        try {
            $task = Task::find($id);

            // Executed if there are records
            if(!empty($task)) {
                $this->authorize('delete', $task);

                $task->delete();
                return response(null, 204);
            }
                        
            // Executed otherwise
            return $this->error(null, 'No record found.', 404);
        } catch(\Throwable $e) {
            return $this->handleThrowable($e);
        }
    }
}
