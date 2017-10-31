<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\FormEntity;
use Mautic\ReportBundle\Scheduler\Enum\SchedulerEnum;
use Mautic\ReportBundle\Scheduler\Exception\ScheduleNotValidException;
use Mautic\ReportBundle\Scheduler\SchedulerInterface;
use Mautic\ReportBundle\Scheduler\Validator as ReportAssert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Class Report.
 */
class Report extends FormEntity implements SchedulerInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var bool
     */
    private $system = false;

    /**
     * @var string
     */
    private $source;

    /**
     * @var array
     */
    private $columns = [];

    /**
     * @var array
     */
    private $filters = [];

    /**
     * @var array
     */
    private $tableOrder = [];

    /**
     * @var array
     */
    private $graphs = [];

    /**
     * @var array
     */
    private $groupBy = [];

    /**
     * @var array
     */
    private $aggregators = [];

    /**
     * @var array
     */
    private $settings = [];

    /**
     * @var bool
     */
    private $isScheduled = false;

    /**
     * @var null|string
     */
    private $toAddress;

    /**
     * @var null|string
     */
    private $scheduleUnit;

    /**
     * @var null|string
     */
    private $scheduleDay;

    /**
     * @var null|string
     */
    private $scheduleMonthFrequency;

    public function __clone()
    {
        $this->id = null;

        parent::__clone();
    }

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('reports')
            ->setCustomRepositoryClass(ReportRepository::class);

        $builder->addIdColumns();

        $builder->addField('system', 'boolean');

        $builder->addField('source', 'string');

        $builder->createField('columns', 'array')
            ->nullable()
            ->build();

        $builder->createField('filters', 'array')
            ->nullable()
            ->build();

        $builder->createField('tableOrder', 'array')
            ->columnName('table_order')
            ->nullable()
            ->build();

        $builder->createField('graphs', 'array')
            ->nullable()
            ->build();

        $builder->createField('groupBy', 'array')
            ->columnName('group_by')
            ->nullable()
            ->build();

        $builder->createField('aggregators', 'array')
            ->columnName('aggregators')
            ->nullable()
            ->build();

        $builder->createField('settings', 'json_array')
            ->columnName('settings')
            ->nullable()
            ->build();

        $builder->createField('isScheduled', 'boolean')
            ->columnName('is_scheduled')
            ->build();

        $builder->createField('scheduleUnit', 'string')
            ->columnName('schedule_unit')
            ->nullable()
            ->build();

        $builder->createField('toAddress', 'string')
            ->columnName('to_address')
            ->nullable()
            ->build();

        $builder->createField('scheduleDay', 'string')
            ->columnName('schedule_day')
            ->nullable()
            ->build();

        $builder->createField('scheduleMonthFrequency', 'string')
            ->columnName('schedule_month_frequency')
            ->nullable()
            ->build();
    }

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new NotBlank([
            'message' => 'mautic.core.name.required',
        ]));

        $metadata->addConstraint(new ReportAssert\ScheduleIsValid());
    }

    /**
     * Prepares the metadata for API usage.
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('report')
            ->addListProperties(
                [
                    'id',
                    'name',
                    'description',
                    'system',
                ]
            )
            ->addProperties(
                [
                    'source',
                    'columns',
                    'filters',
                    'tableOrder',
                    'graphs',
                    'groupBy',
                    'settings',
                ]
            )
            ->build();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Report
     */
    public function setName($name)
    {
        $this->isChanged('name', $name);
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set system.
     *
     * @param string $system
     *
     * @return Report
     */
    public function setSystem($system)
    {
        $this->isChanged('system', $system);
        $this->system = $system;

        return $this;
    }

    /**
     * Get system.
     *
     * @return int
     */
    public function getSystem()
    {
        return $this->system;
    }

    /**
     * Set source.
     *
     * @param string $source
     *
     * @return Report
     */
    public function setSource($source)
    {
        $this->isChanged('source', $source);
        $this->source = $source;

        return $this;
    }

    /**
     * Get source.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set columns.
     *
     * @param string $columns
     *
     * @return Report
     */
    public function setColumns($columns)
    {
        $this->isChanged('columns', $columns);
        $this->columns = $columns;

        return $this;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Set filters.
     *
     * @param string $filters
     *
     * @return Report
     */
    public function setFilters($filters)
    {
        $this->isChanged('filters', $filters);
        $this->filters = $filters;

        return $this;
    }

    /**
     * Get filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getTableOrder()
    {
        return $this->tableOrder;
    }

    /**
     * @param array $tableOrder
     */
    public function setTableOrder(array $tableOrder)
    {
        $this->tableOrder = $tableOrder;
    }

    /**
     * @return mixed
     */
    public function getGraphs()
    {
        return $this->graphs;
    }

    /**
     * @param array $graphs
     */
    public function setGraphs(array $graphs)
    {
        $this->graphs = $graphs;
    }

    /**
     * @return mixed
     */
    public function getGroupBy()
    {
        return $this->groupBy;
    }

    /**
     * @param array $graphs
     */
    public function setGroupBy(array $groupBy)
    {
        $this->groupBy = $groupBy;
    }

    /**
     * @return mixed
     */
    public function getAggregators()
    {
        return $this->aggregators;
    }

    /**
     * @param array $aggregator
     */
    public function setAggregators(array $aggregators)
    {
        $this->aggregators = $aggregators;
    }

    /**
     * @param array $settings
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return bool
     */
    public function isScheduled()
    {
        return $this->isScheduled;
    }

    /**
     * @param bool $isScheduled
     */
    public function setIsScheduled($isScheduled)
    {
        $this->isScheduled = $isScheduled;
    }

    /**
     * @return null|string
     */
    public function getToAddress()
    {
        return $this->toAddress;
    }

    /**
     * @param null|string $toAddress
     */
    public function setToAddress($toAddress)
    {
        $this->toAddress = $toAddress;
    }

    /**
     * @return null|string
     */
    public function getScheduleUnit()
    {
        return $this->scheduleUnit;
    }

    /**
     * @param null|string $scheduleUnit
     */
    public function setScheduleUnit($scheduleUnit)
    {
        $this->scheduleUnit = $scheduleUnit;
    }

    /**
     * @return null|string
     */
    public function getScheduleDay()
    {
        return $this->scheduleDay;
    }

    /**
     * @param null|string $scheduleDay
     */
    public function setScheduleDay($scheduleDay)
    {
        $this->scheduleDay = $scheduleDay;
    }

    /**
     * @return null|string
     */
    public function getScheduleMonthFrequency()
    {
        return $this->scheduleMonthFrequency;
    }

    /**
     * @param null|string $scheduleMonthFrequency
     */
    public function setScheduleMonthFrequency($scheduleMonthFrequency)
    {
        $this->scheduleMonthFrequency = $scheduleMonthFrequency;
    }

    public function setAsNotScheduled()
    {
        $this->setIsScheduled(false);
        $this->setToAddress(null);
        $this->setScheduleUnit(null);
        $this->setScheduleDay(null);
        $this->setScheduleMonthFrequency(null);
    }

    public function ensureIsDailyScheduled()
    {
        $this->setIsScheduled(true);
        $this->setScheduleUnit(SchedulerEnum::UNIT_DAILY);
        $this->setScheduleDay(null);
        $this->setScheduleMonthFrequency(null);
    }

    /**
     * @throws ScheduleNotValidException
     */
    public function ensureIsMonthlyScheduled()
    {
        if (
            !array_key_exists($this->getScheduleMonthFrequency(), SchedulerEnum::getMonthFrequencyForSelect()) ||
            !array_key_exists($this->getScheduleDay(), SchedulerEnum::getDayEnumForSelect())
        ) {
            throw new ScheduleNotValidException();
        }
        $this->setIsScheduled(true);
        $this->setScheduleUnit(SchedulerEnum::UNIT_MONTHLY);
    }

    /**
     * @throws ScheduleNotValidException
     */
    public function ensureIsWeeklyScheduled()
    {
        if (!array_key_exists($this->getScheduleDay(), SchedulerEnum::getDayEnumForSelect())) {
            throw new ScheduleNotValidException();
        }
        $this->setIsScheduled(true);
        $this->setScheduleUnit(SchedulerEnum::UNIT_WEEKLY);
        $this->setScheduleMonthFrequency(null);
    }

    public function isScheduledDaily()
    {
        return $this->getScheduleUnit() === SchedulerEnum::UNIT_DAILY;
    }

    public function isScheduledWeekly()
    {
        return $this->getScheduleUnit() === SchedulerEnum::UNIT_WEEKLY;
    }

    public function isScheduledMonthly()
    {
        return $this->getScheduleUnit() === SchedulerEnum::UNIT_MONTHLY;
    }

    public function isScheduledWeekDays()
    {
        return $this->getScheduleDay() === SchedulerEnum::DAY_WEEK_DAYS;
    }
}
