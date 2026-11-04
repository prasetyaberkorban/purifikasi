<?php

namespace App\View\Components;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use Illuminate\View\Component;
use Illuminate\View\View;

class StatusBadge extends Component
{
    public string $tone;

    public string $text;

    public function __construct(public AssetStatus|LoanStatus|AssetCondition|string $status)
    {
        [$this->tone, $this->text] = $this->describe($status);
    }

    /** @return array{0: string, 1: string} */
    private function describe(AssetStatus|LoanStatus|AssetCondition|string $status): array
    {
        if (is_string($status)) {
            $status = AssetStatus::tryFrom($status)
                ?? LoanStatus::tryFrom($status)
                ?? AssetCondition::tryFrom($status)
                ?? AssetCondition::BelumDicek;
        }

        return match (true) {
            $status instanceof AssetStatus => [$status->tone(), $status->label()],
            $status instanceof LoanStatus => [$status->tone(), $status->label()],
            $status instanceof AssetCondition => [$this->conditionTone($status), $status->label()],
        };
    }

    private function conditionTone(AssetCondition $condition): string
    {
        return match ($condition) {
            AssetCondition::Baik => 'leaf',
            AssetCondition::BelumDicek => 'muted',
            AssetCondition::RusakRingan => 'olive',
            AssetCondition::Rusak => 'danger',
        };
    }

    public function render(): View
    {
        return view('components.status-badge');
    }
}