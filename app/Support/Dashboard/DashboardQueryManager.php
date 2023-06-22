<?php

namespace App\Support\Dashboard;

use App\Support\Contracts\AbstractQueryConfiguration;
use App\Support\Exceptions\DashboardQueryConfigurationNotFound;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\Debug\Exception\ClassNotFoundException;

class DashboardQueryManager
{
    /**
     * @var Collection
     */
    protected $queryConfigurations;

    /**
     * @var Collection
     */
    protected $statisticsConfigurations;

    /**
     * @var DashboardFiltersBag
     */
    protected $filtersBag;

    /**
     * @var DashboardDataBag
     */
    protected $dataBag;


    protected $filters = [];

    protected $statistics = [];

    protected $widgets = [];


    public function __construct(AbstractQueryConfiguration $defaultDashboardQueryConfiguration)
    {
        $this->filtersBag = new DashboardFiltersBag();

        $this->dataBag = new DashboardDataBag();

        $this->queryConfigurations = Collection::make([
            'default' => $defaultDashboardQueryConfiguration
        ]);

        $this->statisticsConfigurations = Collection::make([
            'default' => DashboardStatisticsConfiguration::make()
        ]);
    }

    /**
     * @throws DashboardQueryConfigurationNotFound
     */
    public function getQueryConfiguration(string $key = 'default'): AbstractQueryConfiguration
    {
        if ($this->queryConfigurations->has($key)) {
            return $this->queryConfigurations->get($key);
        }

        throw new DashboardQueryConfigurationNotFound($key);
    }

    public function setQueryConfiguration(string $key, AbstractQueryConfiguration $queryConfiguration): DashboardQueryManager
    {
        $this->queryConfigurations->put($key, $queryConfiguration);
        return $this;
    }

    public function buildQueryConfigurations(): DashboardQueryManager
    {
        $this->queryConfigurations->map(function (AbstractQueryConfiguration $dashboardQueryConfiguration) {
            $query = $dashboardQueryConfiguration->buildQuery($this->dataBag, $this->filtersBag, $this);

            $dashboardQueryConfiguration->setQuery($query);

            return $dashboardQueryConfiguration;
        });

        return $this;
    }


    public function resolveQueryConfigurations(): DashboardQueryManager
    {
        $this->queryConfigurations->map(function (AbstractQueryConfiguration $dashboardQueryConfiguration, $key) {
            $result = $dashboardQueryConfiguration->resolveQuery($this->getStatisticsConfiguration($key), $this->dataBag, $this);

            $dashboardQueryConfiguration->setResults($result);

            return $dashboardQueryConfiguration;
        });

        return $this;
    }

    /**
     */
    public function getStatisticsConfiguration(string $key = 'default'): DashboardStatisticsConfiguration
    {
        if (!$this->statisticsConfigurations->has($key)) {
            $this->setStatisticsConfiguration($key, DashboardStatisticsConfiguration::make());
        }

        return $this->statisticsConfigurations->get($key);
    }

    public function setStatisticsConfiguration(string $key, DashboardStatisticsConfiguration $statisticsConfiguration): DashboardQueryManager
    {
        $this->statisticsConfigurations->put($key, $statisticsConfiguration);
        return $this;
    }

    public function getFiltersBag(): DashboardFiltersBag
    {
        return $this->filtersBag;
    }

    public function setFiltersBag(DashboardFiltersBag $filterBag): DashboardQueryManager
    {
        $this->filtersBag = $filterBag;

        return $this;
    }

    public function getDataBag(): DashboardDataBag
    {
        return $this->dataBag;
    }

    public function setDataBag(DashboardDataBag $dataBag): DashboardQueryManager
    {
        $this->dataBag = $dataBag;

        return $this;
    }


    /**
     * @param array|string|AbstractQueryConfiguration $queryConfiguration
     * @param array|null $extraQueryConfigurations
     * @return DashboardQueryManager
     * @throws \Throwable
     */
    public static function make($queryConfiguration, array $extraQueryConfigurations = null, $filters = [], $statistics = [], $widgets = []): DashboardQueryManager
    {
        $defaultQueryConfiguration = $queryConfiguration;

        if (is_array($defaultQueryConfiguration)) {
            $defaultQueryConfiguration = $defaultQueryConfiguration['default'];
        }

        $defaultQueryConfiguration = static::buildQueryConfigurationInstance($defaultQueryConfiguration);

        $instance = new static($defaultQueryConfiguration);

        if (!$extraQueryConfigurations) {
            $extraQueryConfigurations = [];
        }

        $extraQueryConfigurations = $extraQueryConfigurations + Arr::except($queryConfiguration, 'default');

        if (count($extraQueryConfigurations) > 0) {
            foreach ($extraQueryConfigurations as $key => $configuration) {
                $instance->setQueryConfiguration($key, static::buildQueryConfigurationInstance($configuration));
            }
        }

        $instance->setFilters($filters);
        $instance->setStatistics($statistics);
        $instance->setWidgets($widgets);

        return $instance;
    }

    /**
     * @throws \Throwable
     */

    protected static function buildQueryConfigurationInstance($queryConfiguration)
    {
        if (is_string($queryConfiguration)) {
            throw_if(!class_exists($queryConfiguration), ClassNotFoundException::class);
            $queryConfiguration = app($queryConfiguration);
        }

        return $queryConfiguration;
    }


    public function setFilters($filters): DashboardQueryManager
    {
        $this->filters = $filters;

        return $this;
    }

    public function setStatistics($statistics): DashboardQueryManager
    {
        $this->statistics = $statistics;

        return $this;
    }

    public function setWidgets($widgets): DashboardQueryManager
    {
        $this->widgets = $widgets;

        return $this;
    }

    public function callFilterHook($hookName): DashboardQueryManager
    {
        /** @var DashboardQueryManager $dashboardQueryManager */
        return app(Pipeline::class)
            ->send($this)
            ->via($hookName)
            ->through($this->filters)
            ->then(function (DashboardQueryManager $dashboardQueryManager) {
                return $dashboardQueryManager;
            });
    }

    public function callStatisticsHook($hookName): DashboardQueryManager
    {
        /** @var DashboardQueryManager $dashboardQueryManager */
        return app(Pipeline::class)
            ->send($this)
            ->via($hookName)
            ->through($this->statistics)
            ->then(function (DashboardQueryManager $dashboardQueryManager) {
                return $dashboardQueryManager;
            });
    }

    public function callWidgetsHook($hookName): DashboardQueryManager
    {
        /** @var DashboardQueryManager $dashboardQueryManager */
        return app(Pipeline::class)
            ->send($this)
            ->via($hookName)
            ->through($this->widgets)
            ->then(function (DashboardQueryManager $dashboardQueryManager) {
                return $dashboardQueryManager;
            });
    }


    public function callHooksBeforeBuildQueryConfigurations(): DashboardQueryManager
    {
        return $this->callFilterHook('preParseFilterValue')
            ->callStatisticsHook('preParseFilterValue')
            ->callFilterHook('parseFilterValue')
            ->callFilterHook('postParseFilterValue')
            ->callFilterHook('preBuildQueryConfigurations')
            ->callStatisticsHook('preBuildQueryConfigurations');
    }

    public function callHooksAfterBuildQueryConfigurations(): DashboardQueryManager
    {
        return $this->callFilterHook('postBuildQueryConfigurations')
            ->callStatisticsHook('postBuildQueryConfigurations')
            ->callFilterHook('parseFilter')
            ->callFilterHook('postParseFilter');
    }

    public function callHooksBeforeResolveQueryConfigurations(): DashboardQueryManager
    {
        return $this->callStatisticsHook('preBuildStatistics')
            ->callStatisticsHook('buildStatistics');
    }

    public function callHooksAfterResolveQueryConfigurations(): DashboardQueryManager
    {
        return $this->callStatisticsHook('preResolveStatistics')
            ->callStatisticsHook('resolveStatistics');
    }
}
