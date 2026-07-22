<?php

namespace App\Livewire\Admin\Contacts;

use App\Models\ContactMessage;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $messageModal = false;
    public $selectedMessage = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function viewMessage(ContactMessage $message)
    {
        $this->selectedMessage = $message;
        $this->messageModal = true;
        
        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }
    }

    public function closeModal()
    {
        $this->messageModal = false;
        $this->selectedMessage = null;
    }

    public function deleteMessage(ContactMessage $message)
    {
        $message->delete();
        session()->flash('message', 'Message deleted successfully.');
    }

    public function render()
    {
        $messages = ContactMessage::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.contacts.index', [
            'messages' => $messages
        ])->with('header', 'Manage Contacts');
    }
}
