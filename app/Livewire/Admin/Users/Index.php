<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filterRole = '';

    public $user_id, $name, $email, $role = 'viewer', $password;
    public $permissions = [];
    public $isModalOpen = false;
    public $image, $newImage;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user_id)],
            'role' => 'required|string|in:admin,team_owner,viewer,sub_admin',
            'permissions' => 'nullable|array',
            'password' => $this->user_id ? 'nullable|string|min:8' : 'required|string|min:8',
            'newImage' => 'nullable|image|max:2048',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterRole()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::query();

        if (auth()->user()->role === 'sub_admin') {
            $query->where('role', 'team_owner');
        } else {
            $query->when($this->filterRole, function ($q) {
                $q->where('role', $this->filterRole);
            });
        }

        $users = $query->when($this->search, function ($q) {
                $q->where(function($subQ) {
                    $subQ->where('name', 'like', '%' . $this->search . '%')
                         ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.users.index', compact('users'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->user_id = '';
        $this->name = '';
        $this->email = '';
        $this->role = auth()->user()->role === 'sub_admin' ? 'team_owner' : 'viewer';
        $this->permissions = [];
        $this->password = '';
        $this->image = null;
        $this->newImage = null;
    }

    public function store()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'permissions' => $this->role === 'sub_admin' ? $this->permissions : null,
        ];

        if ($this->newImage) {
            if ($this->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->image);
            }
            $data['image'] = $this->newImage->store('users', 'public');
        }

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        User::updateOrCreate(['id' => $this->user_id], $data);

        session()->flash('message', $this->user_id ? 'User Updated Successfully.' : 'User Created Successfully.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->user_id = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->permissions = $user->permissions ?? [];
        $this->image = $user->image;
        $this->newImage = null;
        // Password is left blank unless they want to change it
        
        $this->openModal();
    }

    public function delete($id)
    {
        if(auth()->id() == $id) {
            session()->flash('error', 'You cannot delete yourself.');
            return;
        }

        User::find($id)->delete();
        session()->flash('message', 'User Deleted Successfully.');
    }
}
