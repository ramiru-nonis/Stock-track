<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class StaffManagement extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public string $search = '';
    public bool $showModal = false;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public bool $showDeactivateModal = false;
    public ?int $deactivatingUserId = null;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function openCreateModal(): void
    {
        $this->authorize('create', User::class);

        $this->resetValidation();
        $this->reset(['name', 'email', 'password', 'password_confirmation']);
        $this->showModal = true;
    }

    public function createStaff(): void
    {
        $this->authorize('create', User::class);

        $validated = $this->validate();

        User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role' => 'staff', // Forced to staff role strictly
            'is_active' => true,
        ]);

        session()->flash('message', 'Staff account created successfully.');
        $this->showModal = false;
    }

    public function confirmDeactivate(int $id): void
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        if ($user->isOwner()) {
            session()->flash('error', 'Cannot deactivate an owner account.');
            return;
        }

        $this->deactivatingUserId = $user->id;
        $this->showDeactivateModal = true;
    }

    public function toggleUserStatus(int $id): void
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);

        if ($user->isOwner()) {
            session()->flash('error', 'Cannot alter owner account status.');
            return;
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusStr = $user->is_active ? 'activated' : 'deactivated';
        session()->flash('message', "User account {$user->name} has been {$statusStr}.");
    }

    public function deactivateStaff(): void
    {
        if ($this->deactivatingUserId) {
            $user = User::findOrFail($this->deactivatingUserId);
            $this->authorize('delete', $user);

            // Soft delete user to preserve foreign key & historical integrity of stock movements
            $user->is_active = false;
            $user->save();
            $user->delete();

            session()->flash('message', 'Staff account deactivated & soft deleted. Historical stock audit movements remain intact.');
        }

        $this->showDeactivateModal = false;
        $this->deactivatingUserId = null;
    }

    public function render(): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::withTrashed()
            ->where('role', 'staff')
            ->latest();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.staff-management', [
            'staffMembers' => $query->paginate(10),
        ])->layout('layouts.app');
    }
}
