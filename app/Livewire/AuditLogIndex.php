<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Audit Log page — Owner-only view of all activity logs.
 *
 * PRD §3.3: "Audit" — log setiap perubahan data
 * PRD §8.15: "Recent Activity | Log aktivitas terbaru"
 */
class AuditLogIndex extends Component
{
    use WithPagination;

    /** @var string Filter by subject type (model class name) */
    public string $filterSubject = '';

    /** @var string Filter by event type */
    public string $filterEvent = '';

    /** @var string Filter by user ID */
    public string $filterUser = '';

    /** @var int Items per page */
    public int $perPage = 20;

    /**
     * Reset filters and go back to page 1.
     */
    public function resetFilters(): void
    {
        $this->reset(['filterSubject', 'filterEvent', 'filterUser']);
        $this->resetPage();
    }

    /**
     * Updated filter hooks — reset page when filter changes.
     */
    public function updatedFilterSubject(): void
    {
        $this->resetPage();
    }

    public function updatedFilterEvent(): void
    {
        $this->resetPage();
    }

    public function updatedFilterUser(): void
    {
        $this->resetPage();
    }

    /**
     * Render the audit log index page.
     */
    public function render()
    {
        $query = ActivityLog::with('user')
            ->latest();

        // Apply filters
        if ($this->filterSubject) {
            $query->where('subject_type', $this->filterSubject);
        }

        if ($this->filterEvent) {
            $query->where('event', $this->filterEvent);
        }

        if ($this->filterUser) {
            $query->where('user_id', $this->filterUser);
        }

        $logs = $query->paginate($this->perPage);

        // Get distinct subject types for filter dropdown
        $subjectTypes = ActivityLog::distinct()
            ->pluck('subject_type')
            ->sort()
            ->values();

        // Get distinct event types for filter dropdown
        $eventTypes = ActivityLog::distinct()
            ->pluck('event')
            ->sort()
            ->values();

        // Get users who have made changes (for filter dropdown)
        $users = \App\Models\User::whereIn('id', ActivityLog::distinct()->pluck('user_id'))
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        return view('livewire.owner.audit-log.index', [
            'logs' => $logs,
            'subjectTypes' => $subjectTypes,
            'eventTypes' => $eventTypes,
            'users' => $users,
        ]);
    }
}
