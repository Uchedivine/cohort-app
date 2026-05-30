<?php

namespace App\Services;

use App\Models\ContentRevision;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ContentRevisionService
{
    /**
     * Create a revision snapshot of the current model state
     *
     * @param Model $model
     * @param array $proposedChanges
     * @return ContentRevision
     */
    public function createSnapshot(Model $model, array $proposedChanges): ContentRevision
    {
        return ContentRevision::create([
            'revisable_type' => get_class($model),
            'revisable_id' => $model->id,
            'user_id' => auth()->id(),
            'old_data' => $this->getRelevantAttributes($model),
            'new_data' => $proposedChanges,
            'status' => 'pending',
        ]);
    }

    /**
     * Get the latest revision for a model
     *
     * @param Model $model
     * @return ContentRevision|null
     */
    public function getLatestRevision(Model $model): ?ContentRevision
    {
        return ContentRevision::where('revisable_type', get_class($model))
            ->where('revisable_id', $model->id)
            ->latest()
            ->first();
    }

    /**
     * Get all revisions for a model
     *
     * @param Model $model
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRevisionHistory(Model $model)
    {
        return ContentRevision::where('revisable_type', get_class($model))
            ->where('revisable_id', $model->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Compare original and proposed data to generate a diff
     *
     * @param ContentRevision $revision
     * @return array
     */
    public function generateDiff(ContentRevision $revision): array
    {
        $original = $revision->old_data ?? [];
        $proposed = $revision->new_data ?? [];
        
        $diff = [
            'changed' => [],
            'added' => [],
            'removed' => [],
        ];

        // Find changed and added fields
        foreach ($proposed as $key => $value) {
            if (!array_key_exists($key, $original)) {
                $diff['added'][$key] = $value;
            } elseif ($original[$key] !== $value) {
                $diff['changed'][$key] = [
                    'old' => $original[$key],
                    'new' => $value,
                ];
            }
        }

        // Find removed fields
        foreach ($original as $key => $value) {
            if (!array_key_exists($key, $proposed)) {
                $diff['removed'][$key] = $value;
            }
        }

        return $diff;
    }

    /**
     * Get a formatted diff for display
     *
     * @param ContentRevision $revision
     * @return array
     */
    public function getFormattedDiff(ContentRevision $revision): array
    {
        $diff = $this->generateDiff($revision);
        $formatted = [];

        foreach ($diff['changed'] as $field => $values) {
            $formatted[] = [
                'field' => $this->formatFieldName($field),
                'type' => 'changed',
                'old_value' => $this->formatValue($values['old']),
                'new_value' => $this->formatValue($values['new']),
            ];
        }

        foreach ($diff['added'] as $field => $value) {
            $formatted[] = [
                'field' => $this->formatFieldName($field),
                'type' => 'added',
                'old_value' => null,
                'new_value' => $this->formatValue($value),
            ];
        }

        foreach ($diff['removed'] as $field => $value) {
            $formatted[] = [
                'field' => $this->formatFieldName($field),
                'type' => 'removed',
                'old_value' => $this->formatValue($value),
                'new_value' => null,
            ];
        }

        return $formatted;
    }

    /**
     * Apply a revision to the model
     *
     * @param ContentRevision $revision
     * @return bool
     */
    public function applyRevision(ContentRevision $revision): bool
    {
        $model = $revision->revisable;
        
        if (!$model) {
            return false;
        }

        return DB::transaction(function () use ($model, $revision) {
            $model->fill($revision->new_data);
            $saved = $model->save();

            if ($saved) {
                $revision->update([
                    'status' => 'approved',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]);
            }

            return $saved;
        });
    }

    /**
     * Delete old revisions (cleanup)
     *
     * @param int $daysOld
     * @return int Number of deleted revisions
     */
    public function deleteOldRevisions(int $daysOld = 90): int
    {
        return ContentRevision::where('created_at', '<', now()->subDays($daysOld))
            ->where('status', 'approved')
            ->delete();
    }

    /**
     * Get relevant attributes from model (exclude timestamps, etc.)
     *
     * @param Model $model
     * @return array
     */
    private function getRelevantAttributes(Model $model): array
    {
        $attributes = $model->getAttributes();
        
        // Remove system fields that shouldn't be tracked
        $excludeFields = ['id', 'created_at', 'updated_at', 'deleted_at'];
        
        return array_diff_key($attributes, array_flip($excludeFields));
    }

    /**
     * Format field name for display
     *
     * @param string $field
     * @return string
     */
    private function formatFieldName(string $field): string
    {
        // Convert snake_case to Title Case
        return ucwords(str_replace('_', ' ', $field));
    }

    /**
     * Format value for display
     *
     * @param mixed $value
     * @return string
     */
    private function formatValue($value): string
    {
        if (is_null($value)) {
            return '(empty)';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_PRETTY_PRINT);
        }

        if (is_string($value) && strlen($value) > 200) {
            return substr($value, 0, 200) . '...';
        }

        return (string) $value;
    }

    /**
     * Check if a model has pending revisions
     *
     * @param Model $model
     * @return bool
     */
    public function hasPendingRevisions(Model $model): bool
    {
        return ContentRevision::where('revisable_type', get_class($model))
            ->where('revisable_id', $model->id)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Get pending revisions count for a model
     *
     * @param Model $model
     * @return int
     */
    public function getPendingRevisionsCount(Model $model): int
    {
        return ContentRevision::where('revisable_type', get_class($model))
            ->where('revisable_id', $model->id)
            ->where('status', 'pending')
            ->count();
    }
}
