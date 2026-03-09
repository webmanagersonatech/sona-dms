<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Models\Transfer;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateSystemReport extends Command
{
    protected $signature = 'report:generate {--type=daily}';
    protected $description = 'Generate system reports';

    public function handle()
    {
        $type = $this->option('type');
        
        switch ($type) {
            case 'daily':
                $this->generateDailyReport();
                break;
            case 'weekly':
                $this->generateWeeklyReport();
                break;
            case 'monthly':
                $this->generateMonthlyReport();
                break;
            default:
                $this->error('Invalid report type. Use: daily, weekly, or monthly');
                return 1;
        }

        return 0;
    }

    protected function generateDailyReport()
    {
        $date = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');

        $report = [
            'date' => $date,
            'summary' => [
                'total_users' => User::count(),
                'total_files' => File::count(),
                'total_transfers' => Transfer::count(),
            ],
            'daily_stats' => [
                'new_users' => User::whereDate('created_at', $date)->count(),
                'new_files' => File::whereDate('created_at', $date)->count(),
                'new_transfers' => Transfer::whereDate('created_at', $date)->count(),
                'otp_sent' => \App\Models\Otp::whereDate('created_at', $date)->count(),
                'activities' => ActivityLog::whereDate('performed_at', $date)->count(),
            ],
            'file_breakdown' => File::selectRaw('extension, count(*) as count')
                ->whereDate('created_at', $date)
                ->groupBy('extension')
                ->get()
                ->toArray(),
            'transfer_status' => Transfer::selectRaw('status, count(*) as count')
                ->whereDate('created_at', $date)
                ->groupBy('status')
                ->get()
                ->toArray(),
        ];

        // Save report
        $filename = "reports/daily_report_{$date}.json";
        Storage::disk('local')->put($filename, json_encode($report, JSON_PRETTY_PRINT));

        $this->info("Daily report generated: {$filename}");
    }

    protected function generateWeeklyReport()
    {
        $startDate = now()->startOfWeek()->format('Y-m-d');
        $endDate = now()->endOfWeek()->format('Y-m-d');

        $report = [
            'period' => "{$startDate} to {$endDate}",
            'summary' => $this->generateWeeklySummary($startDate, $endDate),
        ];

        $filename = "reports/weekly_report_{$startDate}_to_{$endDate}.json";
        Storage::disk('local')->put($filename, json_encode($report, JSON_PRETTY_PRINT));

        $this->info("Weekly report generated: {$filename}");
    }

    protected function generateMonthlyReport()
    {
        $month = now()->format('Y-m');
        $startDate = now()->startOfMonth()->format('Y-m-d');
        $endDate = now()->endOfMonth()->format('Y-m-d');

        $report = [
            'month' => $month,
            'summary' => $this->generateMonthlySummary($month),
            'top_users' => $this->getTopUsers($month),
            'department_stats' => $this->getDepartmentStats($month),
        ];

        $filename = "reports/monthly_report_{$month}.json";
        Storage::disk('local')->put($filename, json_encode($report, JSON_PRETTY_PRINT));

        $this->info("Monthly report generated: {$filename}");
    }

    protected function generateWeeklySummary($startDate, $endDate)
    {
        return [
            'new_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            'new_files' => File::whereBetween('created_at', [$startDate, $endDate])->count(),
            'files_by_type' => File::selectRaw('extension, count(*) as count')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('extension')
                ->get()
                ->toArray(),
            'transfers_by_status' => Transfer::selectRaw('status, count(*) as count')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('status')
                ->get()
                ->toArray(),
            'activity_summary' => ActivityLog::selectRaw('action, count(*) as count')
                ->whereBetween('performed_at', [$startDate, $endDate])
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->toArray(),
        ];
    }

    protected function generateMonthlySummary($month)
    {
        return [
            'total_files' => File::where('created_at', 'like', "{$month}%")->count(),
            'total_transfers' => Transfer::where('created_at', 'like', "{$month}%")->count(),
            'storage_usage' => File::where('created_at', 'like', "{$month}%")->sum('size'),
            'otp_usage' => \App\Models\Otp::where('created_at', 'like', "{$month}%")->count(),
        ];
    }

    protected function getTopUsers($month)
    {
        return User::withCount(['files' => function ($query) use ($month) {
                $query->where('created_at', 'like', "{$month}%");
            }])
            ->withCount(['sentTransfers' => function ($query) use ($month) {
                $query->where('created_at', 'like', "{$month}%");
            }])
            ->orderBy('files_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'files_uploaded' => $user->files_count,
                    'transfers_sent' => $user->sent_transfers_count,
                ];
            });
    }

    protected function getDepartmentStats($month)
    {
        return \App\Models\Department::withCount(['files' => function ($query) use ($month) {
                $query->where('created_at', 'like', "{$month}%");
            }])
            ->withCount(['users'])
            ->orderBy('files_count', 'desc')
            ->get()
            ->map(function ($dept) {
                return [
                    'name' => $dept->name,
                    'file_count' => $dept->files_count,
                    'user_count' => $dept->users_count,
                ];
            });
    }
}