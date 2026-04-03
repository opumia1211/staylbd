<?php

namespace App\Services\Courier;

use App\Models\Courierapi;
use Illuminate\Support\Collection;

class CourierManager
{
    /** @var array<string, CourierDriverInterface> */
    protected array $drivers = [];

    public function __construct()
    {
        $this->registerDefaults();
    }

    protected function registerDefaults(): void
    {
        $this->extend(new Drivers\PathaoDriver());
        $this->extend(new Drivers\SteadfastDriver());
        $this->extend(new Drivers\SundarbanDriver());
        $this->extend(new Drivers\ECourierDriver());
        $this->extend(new Drivers\GenericDriver());
    }

    public function extend(CourierDriverInterface $driver): void
    {
        $this->drivers[$driver->getType()] = $driver;
    }

    public function driver(string $type): ?CourierDriverInterface
    {
        return $this->drivers[$type] ?? null;
    }

    /** @return array<string, CourierDriverInterface> */
    public function getDrivers(): array
    {
        return $this->drivers;
    }

    /** Available driver types (for dropdowns) */
    public function getAvailableTypes(): array
    {
        return array_keys($this->drivers);
    }

    /** All configured and active providers from DB */
    public function getActiveProviders(): Collection
    {
        return Courierapi::active()->orderBy('sort_order')->orderBy('name')->get();
    }

    /** Providers that are both active and configured */
    public function getReadyProviders(): Collection
    {
        return $this->getActiveProviders()->filter(function (Courierapi $api) {
            $driver = $this->driver($api->type);
            return $driver && $driver->isConfigured($api);
        });
    }

    /** Driver types that do not yet have a row in courierapis (for "Add provider") */
    public function getAddableTypes(): array
    {
        $existing = Courierapi::pluck('type')->flip()->all();
        $addable = [];
        foreach ($this->drivers as $type => $driver) {
            if (!isset($existing[$type])) {
                $addable[$type] = $driver->getName();
            }
        }
        return $addable;
    }
}
