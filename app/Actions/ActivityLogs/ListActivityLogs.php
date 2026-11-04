<?php

namespace App\Actions\ActivityLogs;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ListActivityLogs
{
    /** @param array{search?: ?string, date?: ?string, page?: int|string} $filters */
    public function execute(User $user, array $filters): LengthAwarePaginator
    {
        return ActivityLog::query()->with('user:id,name')
            ->where('shop_id', $user->shop_id)
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('description', 'like', '%'.$search.'%')
                        ->orWhere('action', 'like', '%'.$search.'%')
                        ->orWhereHas('user', fn (Builder $actor) => $actor->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->when($filters['date'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', $date))
            ->orderByDesc('created_at')->orderByDesc('id')
            ->paginate(20, ['*'], 'page', (int) ($filters['page'] ?? 1))
            ->through(fn (ActivityLog $log): array => [
                'id' => $log->id,
                'user' => $log->user?->name ?? ($log->user_id ? 'Pegawai tidak tersedia' : 'Sistem / akun dihapus'),
                'action' => $log->action,
                'description' => $log->description,
                'time' => $log->created_at->format('d M Y H:i:s'),
            ]);
    }
}
