<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Jobitems extends Model
{
    protected $fillable = ['job_id', 'path', 'blob_sha', 'status_id', 'results'];
    protected $table = 'codeanalyzer_job_items';
    protected $with = ['status'];
    protected $casts = ['results' => 'array'];
    public function status()
    {
        return $this->belongsTo(Jobstatus::class);
    }

    public function issues()
    {
        return $this->hasMany(Jobissues::class, "jobitem_id");
    }
    public function job()
    {
        return $this->belongsTo(Jobs::class);
    }
    protected function filteredResults(): Attribute
    {
        return Attribute::make(
            get: fn(): array => $this->hasImprovements($this->results),
        )->shouldCache();
    }
    private function hasImprovements($arr): array
    {
        return Cache::rememberForever('results_' . $this->id, function () use ($arr) {
            if (empty($arr)) {
                return [];
            }

            return array_filter($arr, function ($item) {
                if (is_array($item)) {
                    return true;
                }

                $uitem = strtoupper($item);
                return ! ($uitem === '1' || $uitem === 'OK' || $uitem === 'NOT APPLICABLE');
            });
        });
    }
    private function walkResults(&$value, $key): void
    {
        if (is_array($value)) {
            array_walk($value, [$this, 'walkResults']);
            $value = "{$key}: " . implode("\r\n", $value);
        } else {
            $value = "{$key}: {$value}";
        }
    }
    public function resultsToString(): string
    {
        return Cache::rememberForever('results_string' . $this->id, function () {
            if ($results = $this->filteredResults) {
                array_walk($results, [$this, 'walkResults']);
                return implode("\r\n", $results);
            }

            return '';
        });
    }
}
