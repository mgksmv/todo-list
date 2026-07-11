<?php

namespace App\Enums\Traits;

trait HasLabels
{
    abstract public function label(): string;

    public static function getAllWithLabels(): array
    {
        $data = [];

        foreach (self::cases() as $case) {
            $data[] = [
                'label' => $case->label(),
                'value' => $case->value,
            ];
        }

        return $data;
    }
}
