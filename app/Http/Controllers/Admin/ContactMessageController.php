<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $archived = $request->query('box') === 'archived';

        return view('admin.messages.index', [
            'archived' => $archived,
            'messages' => ContactMessage::query()
                ->when($archived, fn ($query) => $query->whereNotNull('archived_at'), fn ($query) => $query->whereNull('archived_at'))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function show(ContactMessage $message): View
    {
        if ($message->read_at === null) {
            $message->update(['read_at' => now()]);
        }

        return view('admin.messages.show', ['message' => $message]);
    }

    public function archive(ContactMessage $message): RedirectResponse
    {
        $message->update(['archived_at' => now(), 'read_at' => $message->read_at ?? now()]);
        AuditLog::record('message.archive', $message);

        return redirect()->route('admin.messages.index')->with('status', __('admin.flash.archived'));
    }

    public function unarchive(ContactMessage $message): RedirectResponse
    {
        $message->update(['archived_at' => null]);
        AuditLog::record('message.unarchive', $message);

        return redirect()->route('admin.messages.index', ['box' => 'archived'])->with('status', __('admin.flash.unarchived'));
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        AuditLog::record('message.delete', $message);
        $message->delete();

        return redirect()->route('admin.messages.index')->with('status', __('admin.flash.deleted'));
    }
}
