<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Audit Log page — Owner-only view of all activity logs.
 *
 * PRD §3.3: "Audit" — log setiap perubahan data
 * PRD §8.15: "Recent Activity | Log aktivitas terbaru"
 */
#[Layout('layouts.admin')]
#[Title('Audit Log — Ting Warehouse')]
class AuditLogIndex extends Component
{
    use WithPagination;

    /** @var string Filter by subject type (model class name) */
    #[Url]
    public string $filterSubject = '';

    /** @var string Filter by event type */
    #[Url]
    public string $filterEvent = '';

    /** @var string Filter by user ID */
    #[Url]
    public string $filterUser = '';

    /** @var string Search query */
    #[Url]
    public string $search = '';

    /** @var string Filter date from */
    #[Url]
    public string $filterDateFrom = '';

    /** @var string Filter date to */
    #[Url]
    public string $filterDateTo = '';

    /** @var int Items per page */
    public int $perPage = 20;

    /** @var int|null Selected log for detail modal */
    public ?int $selectedLogId = null;

    /**
     * Reset filters and go back to page 1.
     */
    public function resetFilters(): void
    {
        $this->reset([
            'filterSubject', 'filterEvent', 'filterUser',
            'search', 'filterDateFrom', 'filterDateTo',
        ]);
        $this->resetPage();
    }

    public function updatedFilterSubject(): void { $this->resetPage(); }
    public function updatedFilterEvent(): void { $this->resetPage(); }
    public function updatedFilterUser(): void { $this->resetPage(); }
    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilterDateFrom(): void { $this->resetPage(); }
    public function updatedFilterDateTo(): void { $this->resetPage(); }

    public function selectLog(int $id): void
    {
        $this->selectedLogId = $id;
    }

    public function closeDetail(): void
    {
        $this->selectedLogId = null;
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

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('event', 'like', "%{$this->search}%")
                  ->orWhere('subject_type', 'like', "%{$this->search}%")
                  ->orWhereHas('user', function ($uq) {
                      $uq->where('name', 'like', "%{$this->search}%");
                  });
            });
        }

        if ($this->filterDateFrom) {
            $query->whereDate('created_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->whereDate('created_at', '<=', $this->filterDateTo);
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

        // Selected log for detail
        $selectedLog = $this->selectedLogId
            ? ActivityLog::with('user')->find($this->selectedLogId)
            : null;

        return view('livewire.owner.audit-log.index', [
            'logs' => $logs,
            'subjectTypes' => $subjectTypes,
            'eventTypes' => $eventTypes,
            'users' => $users,
            'selectedLog' => $selectedLog,
        ]);
    }
}
