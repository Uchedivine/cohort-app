<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class JobMonitorService
{
    /**
     * Get queue statistics
     *
     * @return array
     */
    public function getQueueStatistics(): array
    {
        $stats = [
            'pending' => $this->getPendingJobsCount(),
            'failed' => $this->getFailedJobsCount(),
            'processed_today' => $this->getProcessedTodayCount(),
            'average_wait_time' => $this->getAverageWaitTime(),
        ];

        return $stats;
    }

    /**
     * Get pending jobs count
     *
     * @return int
     */
    public function getPendingJobsCount(): int
    {
        try {
            return DB::table('jobs')->count();
        } catch (\Exception $e) {
            logger()->error('Failed to get pending jobs count', [
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Get failed jobs count
     *
     * @return int
     */
    public function getFailedJobsCount(): int
    {
        try {
            return DB::table('failed_jobs')->count();
        } catch (\Exception $e) {
            logger()->error('Failed to get failed jobs count', [
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Get processed jobs count for today
     *
     * @return int
     */
    public function getProcessedTodayCount(): int
    {
        $cacheKey = 'jobs_processed_' . now()->format('Y-m-d');
        
        return Cache::remember($cacheKey, 3600, function () {
            // This would require a processed_jobs table to track
            // For now, return 0
            return 0;
        });
    }

    /**
     * Get average wait time for jobs
     *
     * @return float
     */
    public function getAverageWaitTime(): float
    {
        try {
            $jobs = DB::table('jobs')
                ->select('created_at', 'available_at')
                ->limit(100)
                ->get();

            if ($jobs->isEmpty()) {
                return 0;
            }

            $totalWait = 0;
            foreach ($jobs as $job) {
                $totalWait += $job->available_at - $job->created_at;
            }

            return round($totalWait / $jobs->count(), 2);
        } catch (\Exception $e) {
            logger()->error('Failed to calculate average wait time', [
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Get recent failed jobs
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getRecentFailedJobs(int $limit = 10)
    {
        try {
            return DB::table('failed_jobs')
                ->orderBy('failed_at', 'desc')
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            logger()->error('Failed to get recent failed jobs', [
                'error' => $e->getMessage(),
            ]);
            return collect();
        }
    }

    /**
     * Retry a failed job
     *
     * @param string $jobId
     * @return bool
     */
    public function retryFailedJob(string $jobId): bool
    {
        try {
            $job = DB::table('failed_jobs')->where('uuid', $jobId)->first();

            if (!$job) {
                return false;
            }

            // Re-queue the job
            DB::table('jobs')->insert([
                'queue' => $job->queue,
                'payload' => $job->payload,
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => now()->timestamp,
                'created_at' => now()->timestamp,
            ]);

            // Remove from failed jobs
            DB::table('failed_jobs')->where('uuid', $jobId)->delete();

            logger()->info('Failed job retried', ['job_id' => $jobId]);

            return true;
        } catch (\Exception $e) {
            logger()->error('Failed to retry job', [
                'job_id' => $jobId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Clear all failed jobs
     *
     * @return int
     */
    public function clearFailedJobs(): int
    {
        try {
            return DB::table('failed_jobs')->delete();
        } catch (\Exception $e) {
            logger()->error('Failed to clear failed jobs', [
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Get job processing rate (jobs per minute)
     *
     * @return float
     */
    public function getProcessingRate(): float
    {
        $cacheKey = 'job_processing_rate';
        
        return Cache::remember($cacheKey, 60, function () {
            // This would require tracking processed jobs
            // For now, return estimated rate
            return 0;
        });
    }

    /**
     * Check if queue is healthy
     *
     * @return bool
     */
    public function isQueueHealthy(): bool
    {
        $pendingJobs = $this->getPendingJobsCount();
        $failedJobs = $this->getFailedJobsCount();

        // Queue is unhealthy if:
        // - More than 1000 pending jobs
        // - More than 100 failed jobs
        // - Failed job rate > 10%
        
        if ($pendingJobs > 1000) {
            return false;
        }

        if ($failedJobs > 100) {
            return false;
        }

        if ($pendingJobs > 0 && ($failedJobs / $pendingJobs) > 0.1) {
            return false;
        }

        return true;
    }

    /**
     * Get queue health status
     *
     * @return array
     */
    public function getHealthStatus(): array
    {
        $isHealthy = $this->isQueueHealthy();
        $stats = $this->getQueueStatistics();

        return [
            'healthy' => $isHealthy,
            'status' => $isHealthy ? 'healthy' : 'unhealthy',
            'statistics' => $stats,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
