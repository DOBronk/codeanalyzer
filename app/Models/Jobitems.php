<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Jobitems extends Model
{
    protected $fillable = ['job_id', 'path', 'blob_sha', 'status_id', 'results'];
    //  protected $casts = ['results' => 'array'];
    protected $table = 'codeanalyzer_job_items';
    protected $with = ['status'];

    public function status()
    {
        return $this->belongsTo(Jobstatus::class);
    }

    private function hasImprovements($arr, $val = null)
    {
        return Cache::remember('results_' . $this->id, 180000, function () use ($arr) {
            if ($arr == null) {
                return [];
            }

            return array_filter($arr, function ($item) {
                if (! is_array($item)) {
                    return $item != 1;
                } else {
                    return true;
                }
            });
        });
    }
    protected function results(): Attribute
    {
        return Attribute::make(
            get: fn(string $value): array => $this->hasImprovements(json_decode($value, true)),
        )->shouldCache();
    }

    public function resultsToString(): string
    {
        return Cache::remember('results_string_' . $this->id, 180000, function () {
            $text = "";
            if ($this->results) {
                foreach ($this->results as $name => $value) {
                    if (! is_array($value)) {
                        $text .= "{$name}: {$value}\r\n\r\n";
                    } else {
                        try {
                            $text .= "{$name}:\r\n";

                            foreach ($value as $key => $val) {
                                if (is_array($val)) {
                                    $text .= implode(':', $val);
                                } else {
                                    $text .= "{$key}: {$val}\r\n";
                                }
                            }
                        } catch (\Exception $e) {
                            dd($e->getMessage());
                        }
                    }
                }
            }

            return $text;
        });
    }

    public function job()
    {
        return $this->belongsTo(Jobs::class);
    }
}
