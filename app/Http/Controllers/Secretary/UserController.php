<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Models\Organization;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index()
    {
        $users = User::with(['organization', 'roles'])
            ->latest()
            ->paginate(15);

        return view('secretary.users.index', compact('users'));
    }

    public function create()
    {
        $organizations = Organization::published()->get();
        $roles = Role::all();
        
        return view('secretary.users.create', compact('organizations', 'roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        // Generate temporary password if not provided
        $temporaryPassword = $validated['password'] ?? Str::random(12);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($temporaryPassword),
            'organization_id' => $validated['organization_id'] ?? null,
        ]);

        // Assign role
        $user->assignRole($validated['role']);

        // Log activity
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties(['role' => $validated['role']])
            ->log('User account created');

        // Send welcome email with temporary password
        $this->notificationService->sendWelcomeEmail($user, $temporaryPassword);

        return redirect()->route('secretary.users.index')
            ->with('success', 'User created successfully. Welcome email sent.');
    }

    public function show(User $user)
    {
        $user->load(['organization', 'roles', 'submissions']);
        return view('secretary.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $organizations = Organization::published()->get();
        $roles = Role::all();
        
        return view('secretary.users.edit', compact('user', 'organizations', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        // Update user details
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'organization_id' => $validated['organization_id'] ?? null,
        ]);

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        // Sync roles
        $user->syncRoles([$validated['role']]);

        // Log activity
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties(['role' => $validated['role']])
            ->log('User account updated');

        return redirect()->route('secretary.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Log activity before deletion
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties(['user_name' => $user->name, 'user_email' => $user->email])
            ->log('User account deleted');

        $user->delete();

        return redirect()->route('secretary.users.index')
            ->with('success', 'User deleted successfully.');
    }
}