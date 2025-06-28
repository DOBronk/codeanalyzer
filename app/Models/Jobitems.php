<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    public function hasImprovements($arr)
    {
        return array_filter($arr, function ($item) {
            if (!is_array($item)) {
                return !($item === "OK" | $item === "N/A");
            } else {
                return true;
            }
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
        $text = "";

        if ($this->results) {
            foreach ($this->results as $name => $value) {
                if (! is_array($value)) {
                    $text .= "{$name}: {$value}\r\n\r\n";
                } else {
                    $text .= "{$name}:\r\n";
                    foreach ($value as $key => $val) {
                        $text .= "{$key}: {$val}\r\n";
                    }
                }
            }
        }

        return $text;
    }

    public function job()
    {
        return $this->belongsTo(Jobs::class);
    }
}
