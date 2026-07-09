<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Item;
use App\Services\NotificationService;
use Illuminate\Console\Command;

/**
 * Check Deadlines — Scheduled command for payment reminders and storage holds.
 *
 * Revisi §2.10.5: Runs daily to check:
 * 1. Payment reminders (H-3, H-1, H-0)
 * 2. Storage deadline expiry → auto hold
 * 3. 2-week overdue payments → hold + lelang warning
 *
 * Idempotent: Uses reminder_sent JSON array to prevent duplicate notifications.
 */
class CheckDeadlinesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deadlines:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check payment/storage deadlines and send reminders (Revisi §2.10)';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notifService): int
    {
        $this->info('Checking deadlines...');

        $this->checkPaymentReminders($notifService);
        $this->checkStorageDeadlines($notifService);
        $this->checkOverduePayments($notifService);

        $this->info('Deadline check complete.');

        return Command::SUCCESS;
    }

    /**
     * Check payment reminders: H-3, H-1, H-0.
     *
     * Sends reminders to customers with invoices approaching payment_deadline.
     * Each reminder type is tracked independently in reminder_sent JSON array.
     */
    private function checkPaymentReminders(NotificationService $notifService): void
    {
        $today = now()->startOfDay();

        // H-3: 3 days before deadline
        Invoice::where('status', Invoice::STATUS_WAITING_PAYMENT)
            ->whereDate('payment_deadline', $today->copy()->addDays(3))
            ->where(function ($q) {
                $q->whereNull('reminder_sent')
                  ->orWhereJsonDoesntContain('reminder_sent', 'h3');
            })
            ->each(function ($invoice) use ($notifService) {
                $notifService->paymentReminderH3($invoice);
                $invoice->markReminderSent('h3');
                $this->line("  Sent H-3 reminder for invoice {$invoice->invoice_number}");
            });

        // H-1: 1 day before deadline
        Invoice::where('status', Invoice::STATUS_WAITING_PAYMENT)
            ->whereDate('payment_deadline', $today->copy()->addDay())
            ->where(function ($q) {
                $q->whereNull('reminder_sent')
                  ->orWhereJsonDoesntContain('reminder_sent', 'h1');
            })
            ->each(function ($invoice) use ($notifService) {
                $notifService->paymentReminderH1($invoice);
                $invoice->markReminderSent('h1');
                $this->line("  Sent H-1 reminder for invoice {$invoice->invoice_number}");
            });

        // H-0: On deadline day
        Invoice::where('status', Invoice::STATUS_WAITING_PAYMENT)
            ->whereDate('payment_deadline', $today)
            ->where(function ($q) {
                $q->whereNull('reminder_sent')
                  ->orWhereJsonDoesntContain('reminder_sent', 'h0');
            })
            ->each(function ($invoice) use ($notifService) {
                $notifService->paymentReminderH0($invoice);
                $invoice->markReminderSent('h0');
                $this->line("  Sent H-0 reminder for invoice {$invoice->invoice_number}");
            });
    }

    /**
     * Check storage deadlines.
     *
     * When storage_deadline has passed, notify customer and auto-hold items.
     */
    private function checkStorageDeadlines(NotificationService $notifService): void
    {
        Invoice::where('status', Invoice::STATUS_WAITING_PAYMENT)
            ->whereNotNull('storage_deadline')
            ->whereDate('storage_deadline', '<', now())
            ->where(function ($q) {
                $q->whereNull('reminder_sent')
                  ->orWhereJsonDoesntContain('reminder_sent', 'storage_expired');
            })
            ->each(function ($invoice) use ($notifService) {
                $notifService->storageExpired($invoice);
                $invoice->markReminderSent('storage_expired');
                $this->holdItems($invoice);
                $this->line("  Storage expired for invoice {$invoice->invoice_number} — items held");
            });
    }

    /**
     * Check overdue payments (2+ weeks past deadline).
     *
     * Auto-holds items and sends lelang warning notification.
     */
    private function checkOverduePayments(NotificationService $notifService): void
    {
        $twoWeeksAgo = now()->subWeeks(2)->startOfDay();

        Invoice::where('status', Invoice::STATUS_WAITING_PAYMENT)
            ->whereDate('payment_deadline', '<', $twoWeeksAgo)
            ->where(function ($q) {
                $q->whereNull('reminder_sent')
                  ->orWhereJsonDoesntContain('reminder_sent', '2week');
            })
            ->each(function ($invoice) use ($notifService) {
                $notifService->paymentOverdue2Week($invoice);
                $invoice->markReminderSent('2week');
                $this->holdItems($invoice);
                $this->line("  2-week overdue for invoice {$invoice->invoice_number} — items held, lelang warning sent");
            });
    }

    /**
     * Hold items related to an invoice.
     *
     * For box-based invoices: holds items by box_id + customer_id.
     * For flexible invoices: holds items via junction table.
     */
    private function holdItems(Invoice $invoice): void
    {
        if ($invoice->box_id) {
            // Box-based invoice
            Item::where('box_id', $invoice->box_id)
                ->where('customer_id', $invoice->customer_id)
                ->where('status', Item::STATUS_ACTIVE)
                ->update(['status' => Item::STATUS_HOLD]);
        }

        if ($invoice->isFlexible()) {
            // Flexible invoice: hold items via junction table
            $invoice->items()
                ->where('status', Item::STATUS_ACTIVE)
                ->update(['status' => Item::STATUS_HOLD]);
        }
    }
}
