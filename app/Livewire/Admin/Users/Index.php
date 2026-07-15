<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterRole = '';

    public $user_id, $name, $email, $role = 'viewer', $password;
    public $permissions = [];
    public $isModalOpen = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user_id)],
            'role' => 'required|string|in:admin,team_owner,viewer,sub_admin',
            'permissions' => 'nullable|array',
            'password' => $this->user_id ? 'nullable|string|min:8' : 'required|string|min:8',
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
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterRole, function ($query) {
                $query->where('role', $this->filterRole);
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
        $this->role = 'viewer';
        $this->permissions = [];
        $this->password = '';
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
